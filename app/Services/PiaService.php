<?php
namespace App\Services;

use DateInterval;
use Carbon\Carbon;
use SimpleXMLElement;
use GuzzleHttp\Client;
use App\Services\HelperService;
use App\Helpers\HelperFunctions;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Exception\RequestException;

class PiaService
{
    protected $helperService;
    protected $client;
    protected $config;
    protected $agencyName;
    protected $agencyEmail;
    protected $url;
    protected $username;
    protected $password;
    protected $logPath;
    protected $regenerateLogs;

    public function __construct(HelperService $helperService)
    {
        $this->regenerateLogs = true;
        $logDir = storage_path('logs/pia');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $this->logPath = $logDir . '/' . now()->format('Y_m_d') . '.log';
        $this->logPathBooking = $logDir . '/bookings_' . now()->format('Y_m_d') . '.log';

        $this->helperService = $helperService;
        $this->config = config('services.pia_api');

        $this->agencyName = $this->config['username'];
        $this->agencyEmail = $this->config['email'];

        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
        $this->url = $this->config['url'];
    }
    public function sendRequest($endpoint, $xmlBody, $isBooking = false)
    {
        // dd($endpoint, $xmlBody);
        // dd($this->getSoapHeaders($endpoint));
        try {
            if ($this->regenerateLogs) {
                $formattedRequest = $this->helperService->formatXml((string) $xmlBody);
                file_put_contents($this->logPath, "{$endpoint} Request:\n{$formattedRequest}\n\n\n", FILE_APPEND);
                if ($isBooking) {
                    file_put_contents($this->logPathBooking, "{$endpoint} Request:\n{$formattedRequest}\n\n\n", FILE_APPEND);
                }
            }

            $response = $this->helperService->postXml($this->url, $this->getSoapHeaders($endpoint), $xmlBody);

            if ($this->regenerateLogs) {
                $formattedResponse = $this->helperService->formatXml((string) $response);
                file_put_contents($this->logPath, "{$endpoint} Response:\n{$formattedResponse}\n\n\n\n\n\n", FILE_APPEND);
                if ($isBooking) {
                    file_put_contents($this->logPathBooking, "{$endpoint} Response:\n{$formattedResponse}\n\n\n\n\n\n", FILE_APPEND);
                }
            }

            if (!$response || !$response->successful()) {
                \Log::error('Flight booking request failed PIA', [
                    'status' => $response?->status(),
                    'response' => $response?->body()
                ]);
                return ['error' => "Flight booking request failed PIA ({$endpoint}).", 'details' => $response?->body()];
            }
            // dd($response->body());
            $parsed = $this->helperService->XMLtoJSON($response->body());

            $body = $parsed['Body'] ?? null;
            if ($body && is_array($body)) {
                $firstKey = array_key_first($body);
                $payload = $body[$firstKey] ?? null;

                if (isset($payload['Error'])) {
                    return [
                        'error' => $payload['Error']['Code'] ?? 'UNKNOWN_ERROR',
                        'message' => $payload['Error']['DescText'] ?? 'No description provided',
                        'raw' => $payload
                    ];
                }
                return $payload ?? $parsed;
            }
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Log::error('PIA API Request Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            throw new \Exception('API request failed: ' . $e->getMessage());
        }
    }
    public function getPartyBlock()
    {
        return <<<XML
        <Party>
            <Sender>
                <TravelAgency>
                    <AgencyID>{$this->config['username']}</AgencyID>
                    <ContactInfo>
                        <EmailAddress>
                            <EmailAddressText>{$this->config['email']}</EmailAddressText>
                        </EmailAddress>
                    </ContactInfo>
                    <Name>{$this->config['name']}</Name>
                </TravelAgency>
            </Sender>
        </Party>
        XML;
    }
    public function searchFlights($data) // AirShopping
    {
        // Prepare segments
        $segments = [
            [
                'origin' => $data['arr'],
                'destination' => $data['dest'],
                'departure_date' => $data['dep'],
            ],
        ];
        // Add return segment if provided
        if (!empty($data['return'])) {
            $segments[] = [
                'origin' => $data['dest'],
                'destination' => $data['arr'],
                'departure_date' => $data['return'],
            ];
        }
        // Prepare passengers
        $passengers = [];
        for ($i = 0; $i < $data['adt']; $i++) {
            $passengers[] = ['type' => 'ADT'];
        }
        for ($i = 0; $i < $data['chd']; $i++) {
            $passengers[] = ['type' => 'CHD'];
        }
        for ($i = 0; $i < $data['inf']; $i++) {
            $passengers[] = ['type' => 'INF'];
        }
        // Default parameters
        $cabinType = $data['cabinClass'] ?? 'Y';
        $currency = 'PKR'; // Default currency
        $useCitySearch = true; // Enable city search as per document recommendation
        $originDestCriteria = '';
        foreach ($segments as $index => $segment) {
            $originDestCriteria .= <<<XML
            <OriginDestCriteria>
                <DestArrivalCriteria>
                    <IATA_LocationCode>{$segment['destination']}</IATA_LocationCode>
                </DestArrivalCriteria>
                <OriginDepCriteria>
                    <Date>{$segment['departure_date']}</Date>
                    <IATA_LocationCode>{$segment['origin']}</IATA_LocationCode>
                </OriginDepCriteria>
                <PreferredCabinType>
                    <CabinTypeCode>{$cabinType}</CabinTypeCode>
                </PreferredCabinType>
            </OriginDestCriteria>
            XML;
        }
        $paxList = '';
        $counter = 1;
        foreach ($passengers as $pax) {
            $paxList .= <<<XML
            <Pax>
                <PaxID>SH{$counter}</PaxID>
                <PTC>{$pax['type']}</PTC>
            </Pax>
            XML;
            $counter++;
        }
        $specialNeeds = $useCitySearch ? '<SpecialNeedsCriteria>USE_CITY_SEARCH</SpecialNeedsCriteria>' : '';
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.iata.org/IATA/2015/00/2020.1/IATA_AirShoppingRQ">
            <soapenv:Header/>
            <soapenv:Body>
                <IATA_AirShoppingRQ>
                    <MessageDoc>
                        <Name>NDC GATEWAY</Name>
                        <RefVersionNumber>20.1</RefVersionNumber>
                    </MessageDoc>
                    {$this->getPartyBlock()}
                    <Request>
                        <FlightRequest>
                            {$originDestCriteria}
                        </FlightRequest>
                        <Paxs>
                            {$paxList}
                        </Paxs>
                        <ResponseParameters>
                            <CurParameter>
                                <RequestedCurCode>{$currency}</RequestedCurCode>
                            </CurParameter>
                            <LangUsage>
                                <LangCode>EN</LangCode>
                            </LangUsage>
                        </ResponseParameters>
                    </Request>
                </IATA_AirShoppingRQ>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
        // dd($xmlRequest);
        $response = $this->sendRequest('doAirShopping', $xmlRequest);
        // \Log::info(['response' => $response]);
        
        if (!$response || isset($response['error'])) {
            \Log::error('AirShopping request failed', ['response' => $response]);
            return ['error' => 'Flight booking request failed PIA (AirShopping).', 'details' => $response];
        }

        // if (isset($orderViewRS['Errors'])) return ['error' => 'Flight booking failed.', 'details' => $orderViewRS['Errors']['Error']];
        // dd($response);

        // dd($this->parseAirShoppingResponse($response));
        return $this->parseAirShoppingResponse($response);
    }
    public function bookFlight($data) // OrderCreate
    {
        $user = $data['user'] ?? [];
        $passengers = $data['passengers'] ?? [];
        $offerID = $data['data']['offerID'] ?? [];
        $paxRefIDs = $data['data']['PaxRefID'] ?? [];
        $ownerCode = $data['data']['ownerCode'] ?? [];
        $offerItemID = $data['data']['offerItemID'] ?? [];
        $totalAmount = str_replace(',', '', $data['data']['totalAmount']);
        $currency = $data['data']['currency'] ?? 'PKR';

        if(empty($user) || empty($passengers) || empty($offerID) || empty($paxRefIDs) || empty($ownerCode) || !$totalAmount) {
            return ['error' => 'DATA_MISSING', 'message' => 'data is missing for create order please refetch details'];
        }

        // ✅ Contact info (simplified)
        $contactInfoXML = <<<XML
        <ContactInfo>
            <ContactInfoID>Contact-1</ContactInfoID>
            <EmailAddress>
                <EmailAddressText>{$user['userEmail']}</EmailAddressText>
            </EmailAddress>
            <Phone>
                <CountryDialingCode>{$user['userPhoneCode']}</CountryDialingCode>
                <PhoneNumber>{$user['userPhone']}</PhoneNumber>
            </Phone>
        </ContactInfo>
        XML;

        $selectedOfferItemXML = '';
        if (empty($paxRefIDs)) {
            $selectedOfferItemXML = <<<XML
                    <PaxRefID>PAX-ADT1</PaxRefID>
                XML;
        } else {
            foreach ($paxRefIDs as $paxRef) {
                $selectedOfferItemXML .= <<<XML
                    <PaxRefID>{$paxRef}</PaxRefID>
                XML;
            }
        }
        // ✅ Offer
        $selectedOfferXML = <<<XML
        <SelectedOffer>
            <OfferRefID>{$offerID}</OfferRefID>
            <OwnerCode>{$ownerCode}</OwnerCode>
            <SelectedOfferItem>
                <OfferItemRefID>{$offerItemID}</OfferItemRefID>
                {$selectedOfferItemXML}
            </SelectedOfferItem>
            <TotalOfferPriceAmount CurCode="{$currency}">{$totalAmount}</TotalOfferPriceAmount>
        </SelectedOffer>
        XML;

        // ✅ Passenger List (no IdentityDoc, no unnecessary tags)
        $adtPaxIDs = [];
        foreach ($passengers as $idx => $pax) {
            $type = strtoupper(substr($pax['type'], 0, 3));
            if ($type === 'ADU') $type = 'ADT';
            if ($type === 'ADT') {
                $adtPaxIDs[] = "PAX-{$type}" . ($idx + 1);
            }
        }
        $infCounter = 0;
        $paxListXML = '';
        foreach ($passengers as $index => $pax) {
            $rawType = $pax['type'] ?? '';
            $paxType = strtoupper(substr($rawType, 0, 3));
            if ($paxType === 'ADU') $paxType = 'ADT';
            if ($paxType === 'CHI') $paxType = 'CHD';
            $paxID = "PAX-{$paxType}" . ($index + 1);
            $individualID = "IND-{$paxType}" . ($index + 1);
            $gender = (isset($pax['title']) && (strtolower($pax['title']) === 'mr' || strtolower($pax['title']) === 'm')) ? 'M' : 'F';
            $contactRef = "Contact-" . ($index + 1);

            $paxRefForThis = $paxID;
            if ($paxType === 'INF') {
                if (!empty($adtPaxIDs)) {
                    $mappedAdult = $adtPaxIDs[$infCounter] ?? end($adtPaxIDs);
                    $paxRefForThis = $mappedAdult;
                } else {
                    $paxRefForThis = $paxID;
                }
                $infCounter++;
            }
            $paxListXML .= <<<XML
                <Pax>
                    <Birthdate>{$pax['dob']}</Birthdate>
                    <CitizenshipCountryCode>{$pax['nationality']}</CitizenshipCountryCode>
                    <ContactInfoRefID>{$contactRef}</ContactInfoRefID>
                    <Individual>
                        <GenderCode>{$gender}</GenderCode>
                        <GivenName>{$pax['name']}</GivenName>
                        <IndividualID>{$individualID}</IndividualID>
                        <Surname>{$pax['surname']}</Surname>
                        <TitleName>{$pax['title']}</TitleName>
                    </Individual>
                    <PaxID>{$paxID}</PaxID>
                    <PaxRefID>{$paxRefForThis}</PaxRefID>
                    <PTC>{$paxType}</PTC>
                </Pax>
            XML;
        }

        // ✅ Final XML body
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.iata.org/IATA/2015/00/2020.1/IATA_OrderCreateRQ">
            <soapenv:Header/>
            <soapenv:Body>
                <IATA_OrderCreateRQ>
                    {$this->getPartyBlock()}
                    <PayloadAttributes>
                        <PrimaryLangID>EN</PrimaryLangID>
                    </PayloadAttributes>
                    <Request>
                        <CreateOrder>
                            {$selectedOfferXML}
                        </CreateOrder>
                        <DataLists>
                            <ContactInfoList>
                                {$contactInfoXML}
                            </ContactInfoList>
                            <PaxList>
                                {$paxListXML}
                            </PaxList>
                        </DataLists>
                        <OrderCreateParameters>
                            <CurParameter>
                                <CurCode>{$currency}</CurCode>
                            </CurParameter>
                        </OrderCreateParameters>
                    </Request>
                </IATA_OrderCreateRQ>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

        // dd($xmlRequest);

        // ✅ Send request to API
        $response = $this->sendRequest('doOrderCreate', $xmlRequest, true);

        if (!$response || isset($response['error'])) {
            \Log::error('OrderCreate request failed', ['response' => $response]);
            return $response;
        }

        // dd($response);
        return $this->parseOrderViewResponse($response);
    }
    public function orderChange($data) // OrderChange
    {
        $orderId = $data['orderId'] ?? '';
        $ownerCode = $data['ownerCode'] ?? 'PK';
        $amount = number_format(($data['amount'] ?? 0), 2, '.', '');
        $code = $data['code'] ?? '';

        if(!$amount || !$orderId) {
            return ['error' => 'DATA_MISSING', 'message' => 'data is missing for change order please refetch details'];
        }
        // ✅ Final XML body
        $xmlRequest = <<<XML
        <S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
            <S:Body>
                <IATA_OrderChangeRQ xmlns="http://www.iata.org/IATA/2015/00/2020.1/IATA_OrderChangeRQ" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="xmldsig-core-schema.xsd">
                    {$this->getPartyBlock()}
                    <PayloadAttributes>
                        <PrimaryLangID>EN</PrimaryLangID>
                    </PayloadAttributes>
                    <Request>
                        <Order>
                            <OrderID>{$orderId}</OrderID>
                            <OwnerCode>{$ownerCode}</OwnerCode>
                        </Order>
                        <OrderChangeParameters>
                            <CurParameter>
                                <CurCode>{$code}</CurCode>
                            </CurParameter>
                        </OrderChangeParameters>
                        <PaymentFunctions>
                            <PaymentProcessingDetails>
                                <Amount CurCode="{$code}">{$amount}</Amount>
                                <PaymentMethod>
                                    <AccountableDoc>
                                        <DocType>{$this->config['doc_type']}</DocType>
                                        <TicketID>{$this->config['inv_no']}</TicketID>
                                    </AccountableDoc>
                                </PaymentMethod>
                                <PaymentRefID>PaymentInfo1</PaymentRefID>
                                <TypeCode>{$this->config['doc_type']}</TypeCode>
                            </PaymentProcessingDetails>
                        </PaymentFunctions>
                    </Request>
                </IATA_OrderChangeRQ>
            </S:Body>
        </S:Envelope>
        XML;

        // dd($xmlRequest);

        $response = $this->sendRequest('doOrderChange', $xmlRequest, true);
        // dd($response);

        if (!$response || isset($response['error'])) {
            \Log::error('OrderChange request failed', ['response' => $response]);
            return $response;
        }
        return $this->parseOrderViewResponse($response);
    }
    public function orderCancel($data) // OrderCancelCommit
    {
        $orderId = $data['orderId'] ?? '';
        $ownerCode = $data['ownerCode'] ?? 'PK';
        $code = $data['code'] ?? 'PKR';

        if(!$orderId) {
            return ['error' => 'DATA_MISSING', 'message' => 'data is missing for change order please refetch details'];
        }
        // ✅ Final XML body
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.iata.org/IATA/2015/00/2020.1/IATA_OrderChangeRQ">
            <soapenv:Header/>
            <soapenv:Body>
                <IATA_OrderChangeRQ>
                    {$this->getPartyBlock()}
                    <PayloadAttributes>
                        <PrimaryLangID>EN</PrimaryLangID>
                    </PayloadAttributes>
                    <Request>
                        <ChangeOrder>
                            <CancelOrder>
                                <OrderRefID>{$orderId}</OrderRefID>
                            </CancelOrder>
                        </ChangeOrder>
                        <Order>
                            <OrderID>{$orderId}</OrderID>
                            <OwnerCode>{$ownerCode}</OwnerCode>
                        </Order>
                        <OrderChangeParameters>
                            <CurParameter>
                                <CurCode>{$code}</CurCode>
                            </CurParameter>
                        </OrderChangeParameters>
                    </Request>
                </IATA_OrderChangeRQ>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

        // dd($xmlRequest);

        $response = $this->sendRequest('doOrderCancelCommit', $xmlRequest, true);
        // dd($response);

        if (!$response || isset($response['error'])) {
            $errorMsg = $response['error'] ?? ($response['raw']['Error']['Code'] ?? '') ?? '';

            if (str_contains(strtolower($errorMsg), 'already closed')) {
                return ['warnings' => [
                    'details' => $response['error'] ?? 'Order is already closed.',
                    'raw' => $response['raw'] ?? []
                ]];
            }
            \Log::error('OrderCancelCommit request failed', ['response' => $response]);
            return $response;
        }
        return $response;
    }
    public function doTicketPreview($data) // TicketPreview
    {
        // $orderId = '800Q5P';
        $orderId = $data['orderId'] ?? '';
        $ownerCode = $data['owner'] ?? 'PK';
        $priceCode = $data['price_code'] ?? 'PKR';

        if(!$orderId) return ['error' => 'DATA_MISSING', 'message' => 'data is missing for change order please refetch details'];
        $xmlRequest = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:iata="http://www.iata.org/IATA/2015/00/2020.1/IATA_OrderChangeRQ">
            <soapenv:Header/>
            <soapenv:Body>
                <IATA_OrderChangeRQ xmlns="http://www.iata.org/IATA/2015/00/2020.1/IATA_OrderChangeRQ" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="xmldsig-core-schema.xsd">
                    {$this->getPartyBlock()}
                    <PayloadAttributes>
                        <PrimaryLangID>EN</PrimaryLangID>
                    </PayloadAttributes>
                    <Request>
                        <Order>
                            <OrderID>{$orderId}</OrderID>
                            <OwnerCode>{$ownerCode}</OwnerCode>
                        </Order>
                        <OrderChangeParameters>
                            <CurParameter>
                                <CurCode>{$priceCode}</CurCode>
                            </CurParameter>
                        </OrderChangeParameters>
                    </Request>
                </IATA_OrderChangeRQ>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;
        // dd($xmlRequest);

        $response = $this->sendRequest('doTicketPreview', $xmlRequest, true);
        // dd($response);

        if (!$response || isset($response['error'])) {
            \Log::error('OrderChange request failed', ['response' => $response]);
            return $response;
        }
        return $this->parseOrderViewResponse($response);
    }






    // ----------------------------------------------------------- Helpers -------------------------------------------------------- \\

    public function parseAirShoppingResponse($response)
    {
        // dd($response);
        $flights = [];
        $bundles = [];

        // Check if essential data is present
        if (!isset($response['Response']['DataLists']['PaxJourneyList']['PaxJourney']) ||
            !isset($response['Response']['DataLists']['PaxSegmentList']['PaxSegment']) ||
            !isset($response['Response']['DataLists']['OriginDestList']['OriginDest'])) {
            Log::warning('Missing required data in AirShopping response', ['response' => $response]);
            return $flights;
        }

        $offers = isset($response['Response']['OffersGroup']['CarrierOffers']['Offer'])
            ? (is_array($response['Response']['OffersGroup']['CarrierOffers']['Offer'])
                ? $response['Response']['OffersGroup']['CarrierOffers']['Offer']
                : [$response['Response']['OffersGroup']['CarrierOffers']['Offer']])
            : [];
        if (!empty($offers)) {
            foreach ($offers as $offer) {
                $offerItems = isset($offer['OfferItem'][0]) ? $offer['OfferItem'] : [$offer['OfferItem']];

                $baggageAllowances = [];
                foreach ($offerItems as $item) {
                    // dd($offerItems, $item, $offers);
                    // Handle FareDetail, which may have multiple FareComponents
                    // dd($offerItems, $item);
                    $fareDetails = isset($item['FareDetail'][0]) ? $item['FareDetail'] : [$item['FareDetail']];
                    $paxRefs = [];
                    foreach ($fareDetails as $fareDetail) {
                        $paxRefIDs = isset($fareDetail['PaxRefID']) ? (is_array($fareDetail['PaxRefID']) ? $fareDetail['PaxRefID'] : [$fareDetail['PaxRefID']]) : [];
                        $paxRefs = array_merge($paxRefs, $paxRefIDs);
                    }
                    $paxRefs = array_unique($paxRefs);

                    // Process services for baggage allowances
                    $services = is_array($item['Service']) ? $item['Service'] : [$item['Service']];
                    foreach ($services as $service) {
                        $serviceDefId = $service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionRefID'] 
                        ?? null;
                        
                        if ($serviceDefId) {
                            $baggageDetails = $this->getBaggageDetailsByServiceDef($serviceDefId, $response);
                            if ($baggageDetails) {
                                $baggageAllowances[] = $baggageDetails;
                            }
                        }
                        // if (isset($service['ServiceAssociations']['ServiceDefinitionRefID'])) {
                        //     $serviceDefId = (string)$service['ServiceAssociations']['ServiceDefinitionRefID'];
                        //     $baggageDetails = $this->getBaggageDetailsByServiceDef($serviceDefId, $response);
                        //     if ($baggageDetails) {
                        //         $baggageAllowances[] = $baggageDetails;
                        //     }
                        // }
                    }

                    $offerItemDetails = [];
                    // Build offer item details
                    foreach ($paxRefs as $paxRef) {
                        $offerItemDetails[] = [
                            'pax_id' => (string)$paxRef,
                            'type' => $this->getPassengerType($paxRef, $response),
                            'price' => [
                                'base_amount' => number_format((float)($item['Price']['BaseAmount'] ?? 0), 2),
                                'equiv_amount' => number_format((float)($item['Price']['EquivAmount'] ?? 0), 2),
                                'total_amount' => number_format((float)($item['Price']['TotalAmount'] ?? 0), 2),
                                'currency' => (string)($item['Price']['TotalAmount']['CurCode'] ?? 'PKR'),
                                'taxes' => number_format((float)($item['Price']['TaxSummary']['TotalTaxAmount'] ?? 0), 2),
                                'surcharges' => number_format((float)($item['Price']['Surcharge']['TotalAmount'] ?? 0), 2),
                            ],
                            'fare_components' => array_map(function ($fareDetail) {
                                return [
                                    'pax_ref_id' => is_array($fareDetail['PaxRefID']) ? $fareDetail['PaxRefID'] : [(string)$fareDetail['PaxRefID']],
                                    'fare_component' => $fareDetail['FareComponent'] ?? [],
                                    'fare_price_type' => $fareDetail['FarePriceType'] ?? [],
                                ];
                            }, $fareDetails),
                            'services' => array_map(function ($service) {
                                return [
                                    'pax_ref_id' => is_array($service['PaxRefID']) ? $service['PaxRefID'] : [(string)$service['PaxRefID']],
                                    'service_id' => (string)$service['ServiceID'],
                                    'associations' => $service['ServiceAssociations'] ?? [],
                                ];
                            }, $services),
                        ];
                    }
                }
                $journeyPriceClass = $offer['JourneyOverview']['JourneyPriceClass'];
                $paxJourneyRefIDs = [];
                if (isset($journeyPriceClass[0]) && is_array($journeyPriceClass[0])) {
                    foreach ($journeyPriceClass as $item) {
                        $paxJourneyRefIDs[] = $item['PaxJourneyRefID'];
                    }
                } elseif (isset($journeyPriceClass['PaxJourneyRefID'])) {
                    $paxJourneyRefIDs[] = $journeyPriceClass['PaxJourneyRefID'];
                }
                $flightKey = $paxJourneyRefIDs;
                $totalAmount = (float)($offer['TotalPrice']['TotalAmount'] ?? 0);
                // dd($offer, $offerItemDetails, $offer['JourneyOverview'], $offer['JourneyOverview']);
                $bundles[] = $totalAmount > 0 ? [
                    'offerID' => (string)$offer['OfferID'],
                    'flightKey' => $flightKey,
                    'ownerCode' => (string)$offer['OwnerCode'],
                    'parameters' => [
                        'priceClassRef' => $offer['JourneyOverview']['JourneyPriceClass']['PriceClassRefID']
                            ?? $offer['JourneyOverview']['JourneyPriceClass']['PaxJourneyRefID']
                            ?? $offer['JourneyOverview']['JourneyPriceClass']
                            ?? '',
                        'cabin_type' => $this->getCabinType($offer['OfferItem']),
                    ],
                    'timeLimits' => $offerItems[0]['OfferItemPaymentTimeLimit']['PaymentTimeLimitDate']['PaymentTimeLimitDateTime'] ?? '',
                    'totalPrice' => [
                        'base_amount' => number_format((float)($offer['TotalPrice']['BaseAmount'] ?? 0), 2),
                        'discount' => number_format((float)($offer['TotalPrice']['Discount']['DiscountAmount'] ?? 0), 2),
                        'fee' => number_format((float)($offer['TotalPrice']['Fee']['Amount'] ?? 0), 2),
                        'total_amount' => number_format((float)($offer['TotalPrice']['TotalAmount'] ?? 0), 2),
                        'currency' => (string)($offer['TotalPrice']['TotalAmount']['CurCode'] ?? 'PKR'),
                        'taxes' => number_format((float)($offer['TotalPrice']['TaxSummary']['TotalTaxAmount'] ?? 0), 2),
                        'surcharges' => number_format((float)($offer['TotalPrice']['Surcharge']['TotalAmount'] ?? 0), 2),
                    ],
                    'offerItem' => $this->getOfferItem($offer['OfferItem'], $response),
                    'baggageAllowance' => $baggageAllowances,
                    'journeyOverview' => [
                        'id' => $offer['JourneyOverview']['PriceClassRefID'] ?? '--',
                        'priceClass' => (array)($offer['JourneyOverview']['JourneyPriceClass'] ?? []),
                    ],
                ] : null;
            }
        }
        // Normalize PaxSegment to an array of segments
        $rawPaxSegments = $response['Response']['DataLists']['PaxSegmentList']['PaxSegment'];
        $paxSegments = (is_array($rawPaxSegments) && isset($rawPaxSegments['PaxSegmentID']))
            ? [$rawPaxSegments]
            : (is_array($rawPaxSegments) ? $rawPaxSegments : [$rawPaxSegments]);

        // Normalize PaxJourney to an array of journeys
        $rawPaxJourneys = $response['Response']['DataLists']['PaxJourneyList']['PaxJourney'];
        if (is_string($rawPaxJourneys)) {
            $paxJourneys = [['PaxJourneyID' => $rawPaxJourneys, 'PaxSegmentRefID' => []]];
        } elseif (is_array($rawPaxJourneys) && isset($rawPaxJourneys['PaxJourneyID'])) {
            $paxJourneys = [$rawPaxJourneys];
        } else {
            $paxJourneys = is_array($rawPaxJourneys) ? $rawPaxJourneys : [$rawPaxJourneys];
        }
        // Normalize OriginDest to an array of origin-destination entries
        $rawOriginDests = $response['Response']['DataLists']['OriginDestList']['OriginDest'];
        if (is_array($rawOriginDests) && isset($rawOriginDests['OriginCode']) && isset($rawOriginDests['DestCode'])) {
            $originDests = [$rawOriginDests];
        } else {
            $originDests = is_array($rawOriginDests) ? $rawOriginDests : [$rawOriginDests];
        }

        // Create a map of PaxSegmentID to segment details
        $paxSegmentMap = [];
        foreach ($paxSegments as $segment) {
            if (is_array($segment) && isset($segment['PaxSegmentID'])) {
                $paxSegmentMap[(string)$segment['PaxSegmentID']] = $segment;
            } else {
                Log::warning('Invalid segment structure in PaxSegmentList', ['segment' => $segment]);
            }
        }

        // Create a map of PaxJourneyID to journey details
        $paxJourneyMap = [];
        foreach ($paxJourneys as $journey) {
            if (is_array($journey) && isset($journey['PaxJourneyID'])) {
                $paxJourneyMap[(string)$journey['PaxJourneyID']] = $journey;
            } else {
                Log::warning('Invalid journey structure in PaxJourneyList', ['journey' => $journey]);
            }
        }

        // Group journeys by origin-destination
        $originDestGroups = [];
        foreach ($originDests as $od) {
            if (!is_array($od) || !isset($od['OriginCode']) || !isset($od['DestCode'])) {
                Log::warning('Invalid OriginDest structure', ['originDest' => $od]);
                continue;
            }

            $origin = (string)$od['OriginCode'];
            $destination = (string)$od['DestCode'];
            $originDestId = (string)($od['OriginDestID'] ?? '');
            $paxJourneyRefs = is_array($od['PaxJourneyRefID']) ? $od['PaxJourneyRefID'] : [$od['PaxJourneyRefID']];

            $flightsForOd = [];
            foreach ($paxJourneyRefs as $journeyRef) {
                $journey = $paxJourneyMap[$journeyRef] ?? null;
                if (!$journey) {
                    Log::warning('Journey not found in PaxJourneyMap', ['journeyRef' => $journeyRef]);
                    continue;
                }

                $segmentRefs = is_array($journey['PaxSegmentRefID'])
                    ? $journey['PaxSegmentRefID']
                    : [$journey['PaxSegmentRefID']];
                $segments = [];
                $keys = [];

                foreach ($segmentRefs as $segmentRef) {
                    $segment = $paxSegmentMap[(string)$segmentRef] ?? null;
                    $keys[] = (string)$segment['PaxSegmentID'];
                    if ($segment) {
                        $segments[] = [
                            'segment_key' => (string)$segment['PaxSegmentID'],
                            'origin' => (string)$segment['Dep']['IATA_LocationCode'],
                            'origin_name' => (string)$segment['Dep']['StationName'],
                            'destination' => (string)$segment['Arrival']['IATA_LocationCode'],
                            'destination_name' => (string)$segment['Arrival']['StationName'],
                            'departure_time' => (string)$segment['Dep']['AircraftScheduledDateTime'],
                            'arrival_time' => (string)$segment['Arrival']['AircraftScheduledDateTime'],
                            'flight_number' => (string)$segment['MarketingCarrierInfo']['MarketingCarrierFlightNumberText'],
                            'carrier' => (string)$segment['MarketingCarrierInfo']['CarrierDesigCode'],
                            'carrier_name' => (string)($segment['MarketingCarrierInfo']['CarrierName'] ?? 'Unknown'),
                            'duration' => (string)$segment['Duration'],
                            'aircraft_type' => (string)($this->getAircraftType($segment['DatedOperatingLeg']) ?? 'Unknown'),
                            'baggage_allowance' => $this->getBaggageAllowance($segmentRef, $response),
                        ];
                    }
                }
                $paxJourneys = $response['Response']['DataLists']['PaxJourneyList']['PaxJourney'];
                
                $matchedJourneyId = null;
                foreach ($paxJourneys as $journey) {
                    $segmentsRefId = (array) $journey['PaxSegmentRefID']; // normalize to array
                    if (count($segmentsRefId) === count($keys) && !array_diff($segmentsRefId, $keys)) {
                        $matchedJourneyId = $journey['PaxJourneyID'];
                        break;
                    }
                }
                
                $flight = [
                    'segments' => $segments,
                    'price' => [],
                    'bundleKey' => $matchedJourneyId,
                    // 'bundles' => [],
                ];
                foreach ($offers as $offer) {
                    $journeyPriceClasses = isset($offer['JourneyOverview']['JourneyPriceClass'][0])
                        ? $offer['JourneyOverview']['JourneyPriceClass']
                        : [$offer['JourneyOverview']['JourneyPriceClass']];
                    $journeyRefs = array_map(function ($priceClass) {
                        return (string)(is_array($priceClass) ? ($priceClass['PaxJourneyRefID'] ?? '') : $priceClass);
                    }, $journeyPriceClasses);

                    if (in_array((string)$journey['PaxJourneyID'], $journeyRefs)) {
                        if (empty($flight['price']) || (float)$offer['TotalPrice']['TotalAmount'] < (float)$flight['price']['total_amount']) {
                            $flight['price'] = [
                                'total_amount' => (float)($offer['TotalPrice']['TotalAmount'] ?? 0),
                                'currency' => (string)($offer['TotalPrice']['TotalAmount']['CurCode'] ?? 'PKR'),
                            ];
                        }
                    }
                }

                $flightsForOd[] = $flight;
            }
            // $bundleKey = null;
            // dd($matchedJourneyId, $keys, $response['Response']['DataLists']['PaxJourneyList']);

            $originDestGroups[] = [
                'departureCode' => $origin,
                'arrivalCode' => $destination,
                'flights' => $flightsForOd,
                'responseId' => (string)($response['Response']['ShoppingResponse']['ShoppingResponseRefID'] ?? ''),
            ];
        }

        $result['flight'] = $originDestGroups;
        $result['bundles'] = $bundles;
        $result['transactionId'] = (string)($response['PayloadAttributes']['EchoTokenText'] ?? null);

        return count($bundles) > 1 ? $result : null;
    }
    public function parseOrderViewResponse($response)
    {
        $result = [
            'transaction_id' => null,
            'paymentLimit' => null,
            'order' => [],
            'passengers' => [],
            'journeys' => [],
            'segments' => [],
            'baggage_allowances' => [],
            'services' => [],
            'tickets' => [],
            'totalPrice' => null,
        ];
        // dd($response);
        $responseData = $response['Response'];
        $dataLists = $responseData['DataLists'];
        $order = $responseData['Order'] ?? null;
        // Check if essential data is present
        if (!isset($responseData['DataLists'])) {
            Log::warning('Missing required DataLists in OrderViewRS response', ['response' => $responseData]);
            return $result;
        }

        if ($order) {
            $result['order'] = [
                'orderID' => $order['OrderID'],
                'creationDate' => $order['CreationDateTime'],
                'ownerCode' => $order['OwnerCode'],
                'ownerType' => $order['OwnerTypeCode'],
                'statusCode' => $order['StatusCode'] ?? null,
            ];
            $result['totalPrice'] = isset($order['TotalPrice']['TotalAmount'])
                ? (string)$order['TotalPrice']['TotalAmount']
                : null;
            // $result['totalPrice'] = $order['TotalPrice']['TotalAmount'] ?? '';
        }


        // Extract transaction ID
        $result['transaction_id'] = isset($response['PayloadAttributes']['EchoTokenText'])
            ? (string)$response['PayloadAttributes']['EchoTokenText']
            : null;

        // Parse BaggageAllowanceList
        if (isset($dataLists['BaggageAllowanceList']['BaggageAllowance'])) {
            $baggageAllowances = isset($dataLists['BaggageAllowanceList']['BaggageAllowance'][0])
                ? $dataLists['BaggageAllowanceList']['BaggageAllowance']
                : [$dataLists['BaggageAllowanceList']['BaggageAllowance']];

            foreach ($baggageAllowances as $baggage) {
                $result['baggage_allowances'][] = [
                    'baggage_allowance_id' => (string)($baggage['BaggageAllowanceID'] ?? ''),
                    'type' => (string)($baggage['TypeCode'] ?? 'Unknown'),
                    'piece_allowance' => [
                        'applicable_party' => (string)($baggage['PieceAllowance']['ApplicablePartyText'] ?? ''),
                        'total_quantity' => (int)($baggage['PieceAllowance']['TotalQty'] ?? 0),
                        'max_weight' => [
                            'value' => (float)($baggage['PieceAllowance']['PieceWeightAllowance']['MaximumWeightMeasure'] ?? 0),
                            'unit' => (string)($baggage['PieceAllowance']['PieceWeightAllowance']['MaximumWeightMeasure']['UnitCode'] ?? 'KG'),
                        ],
                    ],
                ];
            }
        }

        // Parse PaxSegmentList (if available)
        $paxSegmentMap = [];
        if (isset($dataLists['PaxSegmentList']['PaxSegment'])) {
            $paxSegments = isset($dataLists['PaxSegmentList']['PaxSegment'][0])
                ? $dataLists['PaxSegmentList']['PaxSegment']
                : [$dataLists['PaxSegmentList']['PaxSegment']];

            foreach ($paxSegments as $segment) {
                if (isset($segment['PaxSegmentID'])) {
                    $paxSegmentMap[(string)$segment['PaxSegmentID']] = $segment;
                    $result['segments'][] = [
                        'segment_id' => (string)($segment['PaxSegmentID'] ?? ''),
                        'origin' => (string)($segment['Dep']['IATA_LocationCode'] ?? ''),
                        'origin_name' => (string)($segment['Dep']['StationName'] ?? ''),
                        'destination' => (string)($segment['Arrival']['IATA_LocationCode'] ?? ''),
                        'destination_name' => (string)($segment['Arrival']['StationName'] ?? ''),
                        'departure_time' => (string)($segment['Dep']['AircraftScheduledDateTime'] ?? ''),
                        'arrival_time' => (string)($segment['Arrival']['AircraftScheduledDateTime'] ?? ''),
                        'flight_number' => (string)($segment['MarketingCarrierInfo']['MarketingCarrierFlightNumberText'] ?? ''),
                        'carrier' => (string)($segment['MarketingCarrierInfo']['CarrierDesigCode'] ?? ''),
                        'carrier_name' => (string)($segment['MarketingCarrierInfo']['CarrierName'] ?? ''),
                        'duration' => (string)($segment['Duration'] ?? ''),
                        'aircraft_type' => (string)($this->getAircraftType($segment['DatedOperatingLeg'] ?? []) ?? ''),
                    ];
                } else {
                    Log::warning('Invalid segment structure in PaxSegmentList', ['segment' => $segment]);
                }
            }
        }

        // Parse PaxJourneyList
        if (isset($dataLists['PaxJourneyList']['PaxJourney'])) {
            $paxJourneys = isset($dataLists['PaxJourneyList']['PaxJourney'][0])
                ? $dataLists['PaxJourneyList']['PaxJourney']
                : [$dataLists['PaxJourneyList']['PaxJourney']];

            foreach ($paxJourneys as $journey) {
                if (!isset($journey['PaxJourneyID'])) {
                    Log::warning('Invalid journey structure in PaxJourneyList', ['journey' => $journey]);
                    continue;
                }

                // $segmentRefs = isset($journey['PaxSegmentRefID'][0]) ? $journey['PaxSegmentRefID'] : [$journey['PaxSegmentRefID']];
                $segmentRefs = isset($journey['PaxSegmentRefID']) ? (is_array($journey['PaxSegmentRefID']) ? $journey['PaxSegmentRefID']
                    : [$journey['PaxSegmentRefID']]) : [];

                // dd($segmentRefs, $journey);
                $journeySegments = [];
                foreach ($segmentRefs as $segmentRef) {
                    if (isset($paxSegmentMap[(string)$segmentRef])) {
                        $journeySegments[] = (string)$segmentRef;
                    }
                }

                $result['journeys'][] = [
                    'journey_id' => (string)$journey['PaxJourneyID'],
                    'segment_refs' => $journeySegments,
                    'origin_dest_id' => null, // To be filled in OriginDestList parsing
                    'origin' => null,
                    'destination' => null,
                ];
            }
        }

        // Parse OriginDestList
        if (isset($dataLists['OriginDestList']['OriginDest'])) {
            $originDests = isset($dataLists['OriginDestList']['OriginDest'][0])
                ? $dataLists['OriginDestList']['OriginDest']
                : [$dataLists['OriginDestList']['OriginDest']];

            foreach ($originDests as $od) {
                if (!isset($od['OriginDestID']) || !isset($od['OriginCode']) || !isset($od['DestCode'])) {
                    Log::warning('Invalid OriginDest structure', ['originDest' => $od]);
                    continue;
                }

                $paxJourneyRefs = isset($od['PaxJourneyRefID'][0])
                    ? $od['PaxJourneyRefID']
                    : [$od['PaxJourneyRefID']];

                $result['journeys'] = array_map(function ($journey) use ($od, $paxJourneyRefs) {
                    $paxJourneyRefs = (array) $paxJourneyRefs;
                    if (in_array($journey['journey_id'], $paxJourneyRefs)) {
                        $journey['origin_dest_id'] = (string)$od['OriginDestID'];
                        $journey['origin'] = (string)$od['OriginCode'];
                        $journey['destination'] = (string)$od['DestCode'];
                    }
                    return $journey;
                }, $result['journeys']);
            }
        }

        // Parse ServiceList (if available)
        $serviceMap = [];
        if (isset($responseData['Order']['OrderItem'])) {
            $orderItems = isset($responseData['Order']['OrderItem'][0])
                ? $responseData['Order']['OrderItem']
                : [$responseData['Order']['OrderItem']];
            $result['paymentLimit'] = $orderItems[0]['PaymentTimeLimitDateTime'] ?? now()->addHours(24)->toDateTimeString();

            foreach ($orderItems as $item) {
                if (isset($item['Service'])) {
                    $services = isset($item['Service'][0]) ? $item['Service'] : [$item['Service']];
                    foreach ($services as $service) {
                        $paxSegmentRefs = [];
                        if (isset($service['ServiceAssociations']['PaxSegmentRef']['PaxSegmentRefID'])) {
                            $paxSegmentRefID = $service['ServiceAssociations']['PaxSegmentRef']['PaxSegmentRefID'];
                            $paxSegmentRefs = is_array($paxSegmentRefID) ? $paxSegmentRefID : [$paxSegmentRefID];
                        } elseif (isset($service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionFlightAssociations']['PaxSegmentRef']['PaxSegmentRefID'])) {
                            $paxSegmentRefID = $service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionFlightAssociations']['PaxSegmentRef']['PaxSegmentRefID'];
                            $paxSegmentRefs = is_array($paxSegmentRefID) ? $paxSegmentRefID : [$paxSegmentRefID];
                        }

                        $serviceData = [
                            'service_id' => (string)($service['ServiceID'] ?? ''),
                            'pax_ref_id' => (string)($service['PaxRefID'] ?? ''),
                            'status_code' => (string)($service['StatusCode'] ?? ''),
                            'segment_refs' => array_map('strval', $paxSegmentRefs),
                            'service_definition_id' => isset($service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionRefID'])
                                ? $service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionRefID']
                                : '',
                            // 'baggage_allowance_ref' => $this->getBaggageDetailsByServiceDefOC(
                            //     (string)($service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionRefID'] ?? ''),
                            //     $responseData
                            // ),
                        ];
                        $result['services'][] = $serviceData;
                        $serviceMap[$serviceData['pax_ref_id']][] = $serviceData;
                    }
                }
            }
        }

        // Parse TicketList (if available)
        if (isset($responseData['TicketDocInfo'])) {
            $tickets = HelperFunctions::normalizeToArray($responseData['TicketDocInfo'] ?? []);
            foreach ($tickets as $ticket) {
                $result['tickets'][] = [
                    'statusCode' => $ticket['Ticket']['Coupon']['CouponStatusCode'] ?? '',
                    'pax_id' => $ticket['PaxRefID'] ?? '',
                    'ticketNumber' => $ticket['Ticket']['TicketNumber'] ?? '',
                    'bookingId' => $ticket['BookingRef']['BookingID'] ?? '',
                    'paymentInfoRefID' => $ticket['PaymentInfoRefID'] ?? '',
                ];
            }
        }
        $penaltyMap = [];
        if (isset($dataLists['PenaltyList']['Penalty'])) {
            $penalties = isset($dataLists['PenaltyList']['Penalty'][0])
                ? $dataLists['PenaltyList']['Penalty']
                : [$dataLists['PenaltyList']['Penalty']];

            foreach ($penalties as $penalty) {
                if (isset($penalty['PenaltyID']) && $penalty['TypeCode'] === 'Cancellation') {
                    $paxRefId = implode('-', array_slice(explode('-', $penalty['PenaltyID']), 0, 2)); // Extract PaxRefID from PenaltyID
                    $penaltyMap[$paxRefId] = [
                        'penalty_id' => $penalty['PenaltyID'] ?? null,
                        'type_code' => $penalty['TypeCode'] ?? null,
                        'cancel_fee' => (float)($penalty['Price']['TotalAmount'] ?? 0),
                    ];
                }
            }
        }
        if (isset($dataLists['PaxList']['Pax'])) {
            $passengers = isset($dataLists['PaxList']['Pax'][0])
                ? $dataLists['PaxList']['Pax']
                : [$dataLists['PaxList']['Pax']];

            foreach ($passengers as $pax) {
                $paxId = (string)($pax['PaxID'] ?? '');
                $paxData = [
                    'pax_id' => $paxId,
                    'ptc' => (string)($pax['PTC'] ?? ''),
                    'given_name' => (string)($pax['Individual']['GivenName'] ?? ''),
                    'surname' => (string)($pax['Individual']['Surname'] ?? ''),
                    'gender' => (string)($pax['Individual']['GenderCode'] ?? ''),
                    'birthdate' => (string)($pax['Birthdate'] ?? ''),
                    'citizenship' => (string)($pax['CitizenshipCountryCode'] ?? ''),
                    'individual_id' => (string)($pax['Individual']['IndividualID'] ?? ''),
                    'title' => (string)($pax['Individual']['TitleName'] ?? ''),
                    'ticket' => [],
                    'fare_details' => [],
                    'services' => [],
                    'cancel_fee' => $penaltyMap[$paxId] ?? [],
                ];

                // Map ticket to passenger
                foreach ($result['tickets'] as $ticket) {
                    if ($ticket['pax_id'] === $paxData['pax_id']) {
                        $paxData['ticket'] = $ticket;
                    }
                }

                // Map fare details from OrderItem
                if (isset($responseData['Order']['OrderItem'])) {
                    $orderItems = HelperFunctions::normalizeToArray($responseData['Order']['OrderItem'] ?? []);
                    foreach ($orderItems as $item) {
                        $fareDetail = $item['FareDetail'] ?? $item ?? null;
                        $farePaxId = $fareDetail['PaxRefID'] ?? $item['PaxRefID'] ?? null;
                        if ((string)$farePaxId === (string)$paxData['pax_id']) {
                            $fareComponents = [];
                            if (isset($fareDetail['FareComponent'])) {
                                $fareComponents = HelperFunctions::normalizeToArray($fareDetail['FareComponent'] ?? []);
                            }
                            // dd($farePaxId, $paxData['pax_id'], $item, $fareComponents, $fareDetail);
                            $price = $fareDetail['FarePriceType']['Price'] ?? [];
                            // $fareComponents = is_array($fareDetail['FareComponent'])
                            //     ? $fareDetail['FareComponent']
                            //     : [$fareDetail['FareComponent']];
                            $paxData['fare_details'] = [
                                'pricing_code' => (string)($fareDetail['FareCalculationInfo']['PricingCodeText'] ?? ''),
                                // 'fare_components' => array_map(function ($component) {
                                //     return [
                                //         'cabin_type' => [
                                //             'code' => (string)($component['CabinType']['CabinTypeCode'] ?? ''),
                                //             'name' => (string)($component['CabinType']['CabinTypeName'] ?? ''),
                                //         ],
                                //         'fare_basis_city_pair' => (string)($component['FareBasisCityPairText'] ?? ''),
                                //         'fare_basis_code' => (string)($component['FareBasisCode'] ?? ''),
                                //         'fare_type_code' => (string)($component['FareTypeCode'] ?? ''),
                                //         'pax_segment_ref_id' => (string)($component['PaxSegmentRefID'] ?? ''),
                                //         'rbd_code' => (string)($component['RBD']['RBD_Code'] ?? ''),
                                //     ];
                                // }, $fareComponents),
                                'fare_price_type' => [
                                    'code' => (string)($fareDetail['FarePriceType']['FarePriceTypeCode'] ?? ''),
                                    'price' => [
                                        'base_amount' => (float)($price['BaseAmount'] ?? 0),
                                        'equiv_amount' => (float)($price['EquivAmount'] ?? 0),
                                        'currency' => (string)($price['TotalAmount']['CurCode'] ?? 'PKR'),
                                        'discount' => (float)($price['Discount']['DiscountAmount'] ?? 0),
                                        'fee' => (float)($price['Fee']['Amount'] ?? 0),
                                        'surcharge' => (float)($price['Surcharge']['TotalAmount'] ?? 0),
                                        'taxes' => array_map(function ($tax) {
                                            return [
                                                'amount' => (float)($tax['Amount'] ?? 0),
                                                'tax_code' => (string)($tax['TaxCode'] ?? ''),
                                                'refund_ind' => (string)($tax['RefundInd'] ?? 'false'),
                                            ];
                                        }, $price['TaxSummary']['Tax'] ?? []),
                                        'total_tax_amount' => (float)($price['TaxSummary']['TotalTaxAmount'] ?? 0),
                                        'total_amount' => (float)($price['TotalAmount'] ?? 0),
                                    ],
                                ],

                                'order_item_id' => (string)($item['OrderItemID'] ?? ''),
                                'owner_code' => (string)($item['OwnerCode'] ?? ''),
                                'payment_time_limit' => (string)($item['PaymentTimeLimitDateTime'] ?? ''),
                            ];
                            if (isset($fareDetail['FareComponent'])) {
                                $fareComponents = HelperFunctions::normalizeToArray($fareDetail['FareComponent'] ?? []);

                                $paxData['fare_details']['fare_components'] = array_map(function ($component) {
                                    return [
                                        'cabin_type' => [
                                            'code' => (string)($component['CabinType']['CabinTypeCode'] ?? ''),
                                            'name' => (string)($component['CabinType']['CabinTypeName'] ?? ''),
                                        ],
                                        'fare_basis_city_pair' => (string)($component['FareBasisCityPairText'] ?? ''),
                                        'fare_basis_code' => (string)($component['FareBasisCode'] ?? ''),
                                        'fare_type_code' => (string)($component['FareTypeCode'] ?? ''),
                                        'pax_segment_ref_id' => (string)($component['PaxSegmentRefID'] ?? ''),
                                        'rbd_code' => (string)($component['RBD']['RBD_Code'] ?? ''),
                                    ];
                                }, $fareComponents);
                            }
                        }
                    }
                }

                $paxData['services'] = $serviceMap[$paxData['pax_id']] ?? [];

                $result['passengers'][] = $paxData;
            }
        }
        return $result;
    }


    // ------------------------------------------------------------------ Helper's helper ---------------------------------------------------------------------------------------------------
    private function getBaggageDetailsByServiceDef($serviceDefId, $responseData)
    {
        $serviceDefinitions = isset($responseData['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition'][0])
            ? $responseData['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition']
            : [$responseData['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition']];

        foreach ($serviceDefinitions as $service) {
            if ((string)$service['ServiceDefinitionID'] === $serviceDefId && isset($service['ServiceDefinitionAssociation']['BaggageAllowanceRef']['BaggageAllowanceRefID'])) {
                $baggageRef = (string)$service['ServiceDefinitionAssociation']['BaggageAllowanceRef']['BaggageAllowanceRefID'];
                return $this->getBaggageDetails($baggageRef, $responseData);
            }
        }
        return null;
    }
    private function getServiceDetailsByServiceDef($serviceDefId, $response)
    {
        $serviceDefinitions = isset($response['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition'][0])
            ? $response['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition']
            : [$response['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition']];

        foreach ($serviceDefinitions as $service) {
            if ((string)$service['ServiceDefinitionID'] === $serviceDefId && isset($service['ServiceDefinitionAssociation']['BaggageAllowanceRef']['BaggageAllowanceRefID'])) {
                return [
                    'id' => $service['ServiceDefinitionID'] ?? '',
                    'name' => $service['Name'] ?? 'Unknown',
                    'code' => $service['ServiceCode'] ?? '',
                ];
            }
        }
        return null;
    }
    private function getOfferItem($item, $response)
    {
        if (empty($item)) return $item;
        $items = (isset($item[0])) ? $item : [$item];
        $offers = [];
        foreach($items as $offer) {
            $baggageAllowances = [];
            $serviceArray = [];
            $services = isset($offer['Service'][0]) ? $offer['Service'] : [$offer['Service']];
            foreach ($services as $service) {
                $serviceDefId = $service['ServiceAssociations']['ServiceDefinitionRef']['ServiceDefinitionRefID'] 
                ?? null;
                
                if ($serviceDefId) {
                    $baggageDetails = $this->getBaggageDetailsByServiceDef($serviceDefId, $response);
                    $serviceDetails = $this->getServiceDetailsByServiceDef($serviceDefId, $response);
                    if ($baggageDetails) {
                        $baggageAllowances[] = $baggageDetails;
                    }
                    if ($serviceDetails) {
                        $serviceArray[] = $serviceDetails;
                    }
                }
            }
            // dd($baggageAllowances, $services, $item, $response);
            $offers[] = [
                'id' => $offer['OfferItemID'] ?? '',
                'fareDetail' => $offer['FareDetail'] ?? [],
                'mandatoryInd' => $offer['MandatoryInd'] ?? '',
                'paymentLimit' => $offer['OfferItemPaymentTimeLimit']['PaymentTimeLimitDate']['PaymentTimeLimitDateTime'] ?? '',
                'price' => [
                    'base_amount' => number_format((float)($offer['Price']['BaseAmount'] ?? 0), 2),
                    'discount' => number_format((float)($offer['Price']['Discount']['DiscountAmount'] ?? 0), 2),
                    'fee' => number_format((float)($offer['Price']['Fee']['Amount'] ?? 0), 2),
                    'surcharge' => $offer['Price']['Surcharge'] ?? [],
                    'taxSummary' => $offer['Price']['TaxSummary'] ?? [],
                    'total_amount' => number_format((float)($offer['Price']['TotalAmount'] ?? 0), 2),
                ],
                'service' => $serviceArray,
                'baggage' => $baggageAllowances,
            ];
        }
        return $offers;
    }
    private function formatDuration($duration)
    {
        try {
            $interval = new DateInterval($duration);

            $hours = $interval->h + ($interval->d * 24) + ($interval->m * 30 * 24); // Approximate months/days if needed
            $minutes = $interval->i;

            return sprintf('%dh %dm', $hours, $minutes);
        } catch (Exception $e) {
            return $duration; // Return raw value if parsing fails
        }
    }
    private function getCabinType($offerItem)
    {
        // dd($offer);
        $fareDetails = isset($offerItem[0]) ? $offerItem : [$offerItem];
        foreach ($fareDetails as $fareDetail) {
            $fareComponents = isset($fareDetail['FareDetail'][0]) ? $fareDetail['FareDetail'] : (isset($fareDetail['FareDetail']) ? [$fareDetail['FareDetail']] : []);
            foreach ($fareComponents as $fareComponent) {
                return $fareComponent['FareComponent']['CabinType']['CabinTypeName'] ?? 'EconomY';
                // $paxSegmentRefs = isset($fareComponent['FareComponent']['PaxSegmentRefID']) ? (is_array($fareComponent['FareComponent']['PaxSegmentRefID']) ? $fareComponent['FareComponent']['PaxSegmentRefID'] : [$fareComponent['FareComponent']['PaxSegmentRefID']]) : [];
                // // dd($segmentRef, $fareComponent, $paxSegmentRefs);
                // dd($segmentRef, $paxSegmentRefs, $fareComponent);
                // if (in_array((string)$segmentRef, $paxSegmentRefs)) {
                //     // dd($fareComponent['FareComponent']['CabinType']['CabinTypeName'] ?? 'EconomY');
                //     return $fareComponent['FareComponent']['CabinType']['CabinTypeName'] ?? 'EconomY';
                // }
            }
        }
        return 'EconomY';
    }
    private function getBaggageAllowance($segmentRef, $response)
    {
        $baggageAllowances = [];

        $serviceDefinitionsRaw = $response['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition'] ?? [];

        // Ensure it's an array of service definitions
        $serviceDefinitions = is_array($serviceDefinitionsRaw) && isset($serviceDefinitionsRaw[0])
            ? $serviceDefinitionsRaw
            : [$serviceDefinitionsRaw];

        foreach ($serviceDefinitions as $service) {
            // Check if the service is a baggage service
            if (
                isset($service['ServiceCode']) && $service['ServiceCode'] === 'BAG' &&
                isset($service['ServiceDefinitionAssociation']['BaggageAllowanceRef'])
            ) {
                $baggageRefData = $service['ServiceDefinitionAssociation']['BaggageAllowanceRef'];

                // Handle one-way (single object) vs return (array of objects)
                $baggageRefs = isset($baggageRefData[0])
                    ? $baggageRefData
                    : [$baggageRefData];

                foreach ($baggageRefs as $ref) {
                    if (isset($ref['BaggageAllowanceRefID'])) {
                        $baggageRef = (string)$ref['BaggageAllowanceRefID'];
                        $details = $this->getBaggageDetails($baggageRef, $response);

                        if ($details) {
                            $baggageAllowances[] = $details;
                        }
                    }
                }
            }
        }

        return $baggageAllowances;
    }
    private function getBaggageDetails($baggageRef, $response)
    {
        $baggageAllowancesRaw = $response['Response']['DataLists']['BaggageAllowanceList']['BaggageAllowance'] ?? [];

        $baggageAllowances = is_array($baggageAllowancesRaw) && isset($baggageAllowancesRaw[0])
            ? $baggageAllowancesRaw
            : [$baggageAllowancesRaw];


        foreach ($baggageAllowances as $allowance) {
            if ((string)$allowance['BaggageAllowanceID'] === $baggageRef) {
                return [
                    'type' => (string)$allowance['TypeCode'],
                    'weight' => (float)($allowance['PieceAllowance']['PieceWeightAllowance']['MaximumWeightMeasure'] ?? 0),
                    'unit' => (string)($allowance['PieceAllowance']['PieceWeightAllowance']['MaximumWeightMeasure']['UnitCode'] ?? 'KG'),
                ];
            }
        }
        return null;
    }
    private function getPassengerType($paxRef, $response)
    {
        $passengers = isset($response['Response']['DataLists']['PaxList']['Pax'][0])
            ? $response['Response']['DataLists']['PaxList']['Pax']
            : [$response['Response']['DataLists']['PaxList']['Pax']];

        foreach ($passengers as $pax) {
            if ((string)$pax['PaxID'] === $paxRef) {
                return (string)$pax['PTC'];
            }
        }
        return 'Unknown';
    }
    private function getAircraftType($datedOperatingLeg)
    {
        if (!is_array($datedOperatingLeg)) {
            return null;
        }

        // If DatedOperatingLeg is a single array
        if (isset($datedOperatingLeg['CarrierAircraftType']['CarrierAircraftTypeName'])) {
            return $datedOperatingLeg['CarrierAircraftType']['CarrierAircraftTypeName'];
        }

        // If DatedOperatingLeg is an array of arrays (multiple legs)
        if (isset($datedOperatingLeg[0]) && is_array($datedOperatingLeg[0])) {
            foreach ($datedOperatingLeg as $leg) {
                if (isset($leg['CarrierAircraftType']['CarrierAircraftTypeName'])) {
                    return $leg['CarrierAircraftType']['CarrierAircraftTypeName'];
                }
            }
        }

        return null;
    }
    private function determineTripType($journeys, $response)
    {
        $originDests = is_array($response['Response']['DataLists']['OriginDestList']['OriginDest'])
            ? $response['Response']['DataLists']['OriginDestList']['OriginDest']
            : [$response['Response']['DataLists']['OriginDestList']['OriginDest']];

        $journeyCount = count($journeys);
        if ($journeyCount === 1) {
            return 'One-way';
        }

        $cities = [];
        foreach ($journeys as $journey) {
            foreach ($journey['segments'] as $segment) {
                $cities[] = $segment['origin'];
                $cities[] = $segment['destination'];
            }
        }
        $uniqueCities = array_unique($cities);

        if ($journeyCount === 2) {
            $firstJourney = $journeys[0]['segments'][0];
            $returnJourney = $journeys[1]['segments'][0];
            if ($firstJourney['origin'] === $returnJourney['destination'] && 
                $firstJourney['destination'] === $returnJourney['origin']) {
                return 'Round-trip';
            }
        }

        return count($uniqueCities) > 2 ? 'Multi-city' : 'Connecting';
    }
    private function getSoapHeaders($action)
    {
        return [
            'Username' => $this->username,
            'Password' => $this->password,
            'Content-Type' => 'text/xml;charset=UTF-8',
            'Accept' => '*/*',
            'SOAPAction' => "cranendc/{$action}",
        ];
    }
    public function getCarrierName()
    {
        return 'pia';
    }

    // Order Create Helpers
    private function getAircraftTypeOC($datedOperatingLeg)
    {
        return isset($datedOperatingLeg['AircraftCode'])
            ? (string)$datedOperatingLeg['AircraftCode']
            : null;
    }

    // private function getBaggageDetailsByServiceDefOC($serviceDefId, $response)
    // {
    //     dd($serviceDefId, $response);
    //     if (isset($response['DataLists']['BaggageAllowanceList']['BaggageAllowance'])) {
    //         $baggageAllowances = is_array($response['DataLists']['BaggageAllowanceList']['BaggageAllowance'])
    //             ? $response['DataLists']['BaggageAllowanceList']['BaggageAllowance']
    //             : [$response['DataLists']['BaggageAllowanceList']['BaggageAllowance']];

    //         foreach ($baggageAllowances as $baggage) {
    //             if ((string)$baggage['BaggageAllowanceID'] === $serviceDefId) {
    //                 return [
    //                     'baggage_allowance_id' => (string)$baggage['BaggageAllowanceID'],
    //                     'type' => (string)($baggage['TypeCode'] ?? 'Unknown'),
    //                     'piece_allowance' => [
    //                         'applicable_party' => (string)($baggage['PieceAllowance']['ApplicablePartyText'] ?? ''),
    //                         'total_quantity' => (int)($baggage['PieceAllowance']['TotalQty'] ?? 0),
    //                         'max_weight' => [
    //                             'value' => (float)($baggage['PieceAllowance']['PieceWeightAllowance']['MaximumWeightMeasure'] ?? 0),
    //                             'unit' => (string)($baggage['PieceAllowance']['PieceWeightAllowance']['MaximumWeightMeasure']['UnitCode'] ?? 'KG'),
    //                         ],
    //                     ],
    //                 ];
    //             }
    //         }
    //     }
    //     return null;
    // }
    // FLOW: doAirShopping > doOrderCreate > DoOrderRetrieve > doOrderChange > DoVoidTicket > DoAddAncillary
    //       DoServiceList > DoSeatAvailability > DoBaggageServiceList > DoTicketPreview > doOfferPrice > doOrderCancelCommit
    // connected flight route KHI - LHE - DXB
    // http://127.0.0.1:8000/flights?arr=KHI&dest=DXB&dep=2025-11-25&return=2025-11-28&cabinClass=Y&adt=2&chd=1&inf=1
}