<?php
namespace App\Services;

use DateInterval;
use Carbon\Carbon;
use SimpleXMLElement;
use GuzzleHttp\Client;
use App\Services\HelperService;
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
        $this->logPath = storage_path('logs/pia_logs.txt');
        $this->helperService = $helperService;
        $this->config = config('services.pia_api');

        $this->agencyName = $this->config['username'];
        $this->agencyEmail = $this->config['email'];

        $this->username = $this->config['username'];
        $this->password = $this->config['password'];
        $this->url = $this->config['url'];
    }
    /**
     * Send API request and parse XML response
     *
     * @param string $method HTTP method (POST)
     * @param string $endpoint API endpoint
     * @param string $xmlBody XML request body
     * @return SimpleXMLElement Parsed response
     * @throws \Exception
     */
    public function sendRequest($endpoint, $xmlBody)
    {
        // dd($endpoint, $xmlBody);
        // dd($this->getSoapHeaders($endpoint));
        try {
        if ($this->regenerateLogs) {file_put_contents($this->logPath, "{$endpoint} Request:\n" . (string) $xmlBody . "\n\n\n");}

        $response = $this->helperService->postXml($this->url, $this->getSoapHeaders($endpoint), $xmlBody);

        if ($this->regenerateLogs) {file_put_contents($this->logPath, "{$endpoint} Response:\n" . (string) $response . "\n\n\n\n\n\n", FILE_APPEND);}
        return $this->helperService->XMLtoJSON($response->body());
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

    /**
     * Generate Party block for XML requests
     *
     * @return string
     */
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
                    <Name>HITIT COMPUTER SERVICES</Name>
                </TravelAgency>
            </Sender>
        </Party>
        XML;
    }

    /**
     * DoAirShopping for one-way, round-trip, or multi-city flights
     *
     * @param array $segments Array of [origin, destination, departure_date]
     * @param array $passengers Array of [type => ADT/CHD/INF]
     * @param string $cabinType Cabin type (e.g., Y for Economy)
     * @param string $currency Currency code (e.g., PKR)
     * @param bool $useCitySearch Whether to use city codes
     * @return SimpleXMLElement
     */
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
                    <IATA_LocationCode>{$segment['origin']}</IATA_LocationCode>
                </DestArrivalCriteria>
                <OriginDepCriteria>
                    <Date>{$segment['departure_date']}</Date>
                    <IATA_LocationCode>{$segment['destination']}</IATA_LocationCode>
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

        $response = $this->sendRequest('doAirShopping', $xmlRequest);
        // \Log::info(['response' => $response]);
        
        if (!$response || !$response->successful()) {
            \Log::error('Flight booking request failed Emirates', [
                'status' => $response?->status(),
                'response' => $response?->body()
            ]);
            return ['error' => 'Flight booking request failed Emirates (orderChange).', 'details' => $response?->body()];
        }

        // if (isset($orderViewRS['Errors'])) return ['error' => 'Flight booking failed.', 'details' => $orderViewRS['Errors']['Error']];
        dd($response);

        dd($this->parseAirShoppingResponse($response));
        return $this->parseAirShoppingResponse($response);
    }

    /**
     * DoOrderCreate for creating a PNR
     *
     * @param string $offerId Offer ID from DoAirShopping response
     * @param array $passengers Array of passenger details [birthdate, citizenship, email, phone, etc.]
     * @param array|null $payment Payment details [currency, amount, method, etc.]
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function createOrder($offerId, $passengers, $payment = null, $currency = 'PKR')
    {
        $contactList = '';
        $paxList = '';
        foreach ($passengers as $index => $pax) {
            $contactId = "Contact-{$index}";
            $contactList .= <<<XML
            <ContactInfo>
                <ContactInfoID>{$contactId}</ContactInfoID>
                <EmailAddress>
                    <EmailAddressText>{$pax['email']}</EmailAddressText>
                </EmailAddress>
                <Phone>
                    <AreaCodeNumber>{$pax['area_code']}</AreaCodeNumber>
                    <CountryDialingCode>{$pax['country_code']}</CountryDialingCode>
                    <PhoneNumber>{$pax['phone']}</PhoneNumber>
                </Phone>
            </ContactInfo>
            XML;

            $paxList .= <<<XML
            <Pax>
                <Birthdate>{$pax['birthdate']}</Birthdate>
                <CitizenshipCountryCode>{$pax['citizenship']}</CitizenshipCountryCode>
                <ContactInfoRefID>{$contactId}</ContactInfoRefID>
                <IdentityDoc>
                    <IdentityDocID>{$pax['identity_doc_id']}</IdentityDocID>
                    <IdentityDocTypeCode>NATIONAL_ID</IdentityDocTypeCode>
                </IdentityDoc>
                <Individual>
                    <GenderCode>{$pax['gender']}</GenderCode>
                    <GivenName>{$pax['given_name']}</GivenName>
                    <IndividualID>IND-{$index}</IndividualID>
                    <Surname>{$pax['surname']}</Surname>
                    <TitleName>{$pax['title']}</TitleName>
                </Individual>
                <PaxID>PAX-{$index}</PaxID>
                <PTC>{$pax['type']}</PTC>
            </Pax>
            XML;
        }

        $paymentBlock = $payment ? <<<XML
        <PaymentFunctions>
            <PaymentProcessingDetails>
                <Amount CurCode="{$payment['currency']}">{$payment['amount']}</Amount>
                <PaymentMethod>
                    <AccountableDoc>
                        <DocType>{$payment['method']}</DocType>
                        <TicketID>{$payment['ticket_id']}</TicketID>
                    </AccountableDoc>
                </PaymentMethod>
                <PaymentRefID>PaymentInfo1</PaymentRefID>
                <TypeCode>{$payment['method']}</TypeCode>
            </PaymentProcessingDetails>
        </PaymentFunctions>
        XML : '';

        $xmlRequest = <<<XML
        <DoOrderCreateRQ>
            {$this->getPartyBlock()}
            <Query>
                <SelectedOffer>
                    <OfferRefID>{$offerId}</OfferRefID>
                    <SelectedOfferItem>
                        <OfferItemID>OfferItem-1</OfferItemID>
                        <PaxRefID>PAX-0</PaxRefID>
                    </SelectedOfferItem>
                </SelectedOffer>
                <DataLists>
                    <ContactInfoList>
                        {$contactList}
                    </ContactInfoList>
                    <PaxList>
                        {$paxList}
                    </PaxList>
                </DataLists>
                {$paymentBlock}
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
            </Preference>
        </DoOrderCreateRQ>
        XML;

        return $this->sendRequest('/DoOrderCreate', $xmlRequest);
    }

    /**
     * DoOrderChange to complete ticketing
     *
     * @param string $orderId PNR number
     * @param array $payment Payment details [currency, amount, method, ticket_id]
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function orderChange($orderId, $payment, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoOrderChangeRQ>
            {$this->getPartyBlock()}
            <Query>
                <Order>
                    <OrderID>{$orderId}</OrderID>
                    <ValidatingCarrierCode>VF</ValidatingCarrierCode>
                </Order>
                <PaymentFunctions>
                    <PaymentProcessingDetails>
                        <Amount CurCode="{$currency}">{$payment['amount']}</Amount>
                        <PaymentMethod>
                            <AccountableDoc>
                                <DocType>{$payment['method']}</DocType>
                                <TicketID>{$payment['ticket_id']}</TicketID>
                            </AccountableDoc>
                        </PaymentMethod>
                        <PaymentRefID>PaymentInfo1</PaymentRefID>
                        <TypeCode>{$payment['method']}</TypeCode>
                    </PaymentProcessingDetails>
                </PaymentFunctions>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
            </Preference>
        </DoOrderChangeRQ>
        XML;

        return $this->sendRequest('/DoOrderChange', $xmlRequest);
    }

    /**
     * DoServiceList to retrieve ancillary services
     *
     * @param string $orderId PNR number
     * @param array $passenger Passenger details [citizenship, gender, given_name, surname, title]
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function serviceList($orderId, $passenger, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoServiceListRQ>
            {$this->getPartyBlock()}
            <Query>
                <Order>
                    <OrderID>{$orderId}</OrderID>
                </Order>
                <Pax>
                    <CitizenshipCountryCode>{$passenger['citizenship']}</CitizenshipCountryCode>
                    <Individual>
                        <GenderCode>{$passenger['gender']}</GenderCode>
                        <GivenName>{$passenger['given_name']}</GivenName>
                        <IndividualID>IND-1</IndividualID>
                        <Surname>{$passenger['surname']}</Surname>
                        <TitleName>{$passenger['title']}</TitleName>
                    </Individual>
                    <PaxID>ADT-PAX1</PaxID>
                    <PTC>ADT</PTC>
                </Pax>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
                <TripPurposePref>
                    <TripPurposeCode>SL</TripPurposeCode>
                </TripPurposePref>
                <LanguagePref LangCode="EN"/>
            </Preference>
        </DoServiceListRQ>
        XML;

        return $this->sendRequest('/DoServiceList', $xmlRequest);
    }

    /**
     * DoBaggageServiceList to retrieve baggage options
     *
     * @param string $orderId PNR number
     * @param array $passenger Passenger details
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function baggageServiceList($orderId, $passenger, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoBaggageServiceListRQ>
            {$this->getPartyBlock()}
            <Query>
                <Order>
                    <OrderID>{$orderId}</OrderID>
                </Order>
                <Pax>
                    <CitizenshipCountryCode>{$passenger['citizenship']}</CitizenshipCountryCode>
                    <Individual>
                        <GenderCode>{$passenger['gender']}</GenderCode>
                        <GivenName>{$passenger['given_name']}</GivenName>
                        <IndividualID>IND-1</IndividualID>
                        <Surname>{$passenger['surname']}</Surname>
                        <TitleName>{$passenger['title']}</TitleName>
                    </Individual>
                    <PaxID>ADT-PAX1</PaxID>
                    <PTC>ADT</PTC>
                </Pax>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
                <TripPurposePref>
                    <TripPurposeCode>SL</TripPurposeCode>
                </TripPurposePref>
                <LanguagePref LangCode="EN"/>
            </Preference>
        </DoBaggageServiceListRQ>
        XML;

        return $this->sendRequest('/DoBaggageServiceList', $xmlRequest);
    }

    /**
     * DoAddAncillary to add extra baggage
     *
     * @param string $orderId PNR number
     * @param string $paxId Passenger ID
     * @param string $serviceCode Service code (e.g., XBAG-15KG)
     * @param string $segmentId Flight segment ID
     * @param array $passenger Passenger details
     * @param array $segment Segment details [origin, destination, departure_time, arrival_time, etc.]
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function addAncillaryWeightbag($orderId, $paxId, $serviceCode, $segmentId, $passenger, $segment, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoAddAncillaryRQ>
            {$this->getPartyBlock()}
            <Query>
                <ChangeOrder>
                    <SelectedALaCarteOfferItem>
                        <OfferItemID>OfferItem-{$serviceCode}</OfferItemID>
                        <Quantity>1</Quantity>
                        <Service>
                            <ServiceDefinitionRefID>ServiceDef-{$serviceCode}</ServiceDefinitionRefID>
                            <ServiceID>SELECTEDSERVICES2</ServiceID>
                        </Service>
                    </SelectedALaCarteOfferItem>
                    <FlightAssociations>
                        <PaxSegmentRef>
                            <PaxSegmentRefID>{$segmentId}</PaxSegmentRefID>
                        </PaxSegmentRef>
                    </FlightAssociations>
                </ChangeOrder>
                <DataLists>
                    <OriginDestList>
                        <OriginDest>
                            <DestCode>{$segment['destination']}</DestCode>
                            <OriginCode>{$segment['origin']}</OriginCode>
                            <OriginDestID>OD-{$segment['origin']}{$segment['destination']}</OriginDestID>
                            <PaxJourneyRefID>{$segment['origin']}-{$segment['destination']}-J1</PaxJourneyRefID>
                        </OriginDest>
                    </OriginDestList>
                    <PaxJourneyList>
                        <PaxJourney>
                            <PaxJourneyID>{$segment['origin']}-{$segment['destination']}-J1</PaxJourneyID>
                            <PaxSegmentRefID>{$segmentId}</PaxSegmentRefID>
                        </PaxJourney>
                    </PaxJourneyList>
                    <PaxList>
                        <Pax>
                            <Birthdate>{$passenger['birthdate']}</Birthdate>
                            <Individual>
                                <GivenName>{$passenger['given_name']}</GivenName>
                                <Surname>{$passenger['surname']}</Surname>
                                <GenderCode>{$passenger['gender']}</GenderCode>
                            </Individual>
                            <PaxID>{$paxId}</PaxID>
                            <PTC>ADT</PTC>
                        </Pax>
                    </PaxList>
                    <PaxSegmentList>
                        <PaxSegment>
                            <Arrival>
                                <AircraftScheduledDateTime>{$segment['arrival_time']}</AircraftScheduledDateTime>
                                <IATA_LocationCode>{$segment['destination']}</IATA_LocationCode>
                                <StationName>{$segment['destination_name']}</StationName>
                            </Arrival>
                            <CabinType>
                                <CabinTypeCode>Y</CabinTypeCode>
                                <CabinTypeName>ECONOMY</CabinTypeName>
                            </CabinType>
                            <Dep>
                                <AircraftScheduledDateTime>{$segment['departure_time']}</AircraftScheduledDateTime>
                                <IATA_LocationCode>{$segment['origin']}</IATA_LocationCode>
                                <StationName>{$segment['origin_name']}</StationName>
                            </Dep>
                            <Duration>PT1H55M</Duration>
                            <MarketingCarrierInfo>
                                <CarrierDesigCode>PK</CarrierDesigCode>
                                <MarketingCarrierFlightNumberText>{$segment['flight_number']}</MarketingCarrierFlightNumberText>
                            </MarketingCarrierInfo>
                            <OperatingCarrierInfo>
                                <CarrierDesigCode>PK</CarrierDesigCode>
                                <OperatingCarrierFlightNumberText>{$segment['flight_number']}</OperatingCarrierFlightNumberText>
                            </OperatingCarrierInfo>
                            <PaxSegmentID>{$segmentId}</PaxSegmentID>
                        </PaxSegment>
                    </PaxSegmentList>
                    <ServiceDefinitionList>
                        <ServiceDefinition>
                            <Name>BAG</Name>
                            <ServiceCode>XBAG</ServiceCode>
                            <ServiceDefinitionID>ServiceDef-{$serviceCode}</ServiceDefinitionID>
                        </ServiceDefinition>
                    </ServiceDefinitionList>
                </DataLists>
                <Order>
                    <OrderID>{$orderId}</OrderID>
                    <ValidatingCarrierCode>PK</ValidatingCarrierCode>
                </Order>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
            </Preference>
        </DoAddAncillaryRQ>
        XML;

        return $this->sendRequest('/DoAddAncillary', $xmlRequest);
    }

    /**
     * DoReissuePreview to preview ticket reissue
     *
     * @param string $orderId PNR number
     * @param string $offerId Offer ID from DoAirShopping
     * @param string $paxId Passenger ID
     * @param string $segmentId Segment ID to reissue
     * @param array $newSegment New segment details [origin, destination, etc.]
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function reissuePreview($orderId, $offerId, $paxId, $segmentId, $newSegment, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoReissuePreviewRQ>
            {$this->getPartyBlock()}
            <Query>
                <ExistingOrderCriteria>
                    <Order>
                        <OrderID>{$orderId}</OrderID>
                        <ValidatingCarrierCode>VF</ValidatingCarrierCode>
                    </Order>
                    <OrderItem>
                        <OrderItemID>MGFiNjJiOWI4ZmQ3NWNjY2U2NDJkZTc3NjdhYzJiMzZiY2YzODgxYThmNDk4NTA3OTk4YWVkMDJmMjA0YTc1NDBiMmY3OTExMDZkMGE3ZGVjNDU3ZWE1YWM1NGYxMGI3MjkyZjcyZjA0Yjc5YTlhMTg2MDM2NmU5OTBkNzRjNGUxNzYwMzcyNzRhNTVlNjNlOGI2NTY4NmI2N2QyNTgwNTMxMTVmZDkwYWZkMWQxM2I1MWM4ODY3MjYwMWVlMzlkNjkwMzE0MGJmZjYwODA2ZTI1NWZmOGFhYjVlODFjPlVPVzFTTT5TTUFSVD5LSEk+SVNCPjIwMjQtMDgtMTkgMDc6MDA6MDA+MjAyNC0wOC0xOSAwODo1NTowMD5QSz4zMDA+VT45PkVDT05PTVk+MzIwPlMwaEpTVk5DTXpBdz4tMT4zYWUzOTgyYy0zOTY5LTRjMTMtYWRlZi1jMWNiMWFjMDhhZWMjMA==</OrderItemID>
                        <PaxRefID>{$paxId}</PaxRefID>
                    </OrderItem>
                    <DeleteOrderItem>
                        <OrderItemRefID>{$segmentId}</OrderItemRefID>
                    </DeleteOrderItem>
                </ExistingOrderCriteria>
                <SpecificOriginDestCriteria>
                    <OriginDestCriteria>
                        <DestCode>{$newSegment['destination']}</DestCode>
                        <OriginCode>{$newSegment['origin']}</OriginCode>
                    </OriginDestCriteria>
                </SpecificOriginDestCriteria>
                <SelectedOffer>
                    <OfferRefID>{$offerId}</OfferRefID>
                </SelectedOffer>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
                <LanguagePref LangCode="EN"/>
                <TripPurposePref>
                    <TripPurposeCode>SL</TripPurposeCode>
                </TripPurposePref>
            </Preference>
        </DoReissuePreviewRQ>
        XML;

        return $this->sendRequest('/DoReissuePreview', $xmlRequest);
    }

    /**
     * DoReissueCommit to finalize ticket reissue
     *
     * @param string $orderId PNR number
     * @param string $offerId Offer ID
     * @param array $segment Segment details
     * @param array $payment Payment details
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function reissueCommit($orderId, $offerId, $segment, $payment, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoReissueCommitRQ>
            {$this->getPartyBlock()}
            <Query>
                <SelectedOffer>
                    <OfferRefID>{$offerId}</OfferRefID>
                </SelectedOffer>
                <DataLists>
                    <PaxSegmentList>
                        <PaxSegment>
                            <Arrival>
                                <AircraftScheduledDateTime>{$segment['arrival_time']}</AircraftScheduledDateTime>
                                <IATA_LocationCode>{$segment['destination']}</IATA_LocationCode>
                                <StationName>{$segment['destination_name']}</StationName>
                            </Arrival>
                            <CabinType>
                                <CabinTypeCode>Y</CabinTypeCode>
                                <CabinTypeName>ECONOMY</CabinTypeName>
                            </CabinType>
                            <Dep>
                                <AircraftScheduledDateTime>{$segment['departure_time']}</AircraftScheduledDateTime>
                                <IATA_LocationCode>{$segment['origin']}</IATA_LocationCode>
                                <StationName>{$segment['origin_name']}</StationName>
                            </Dep>
                            <Duration>PT1H55M</Duration>
                            <MarketingCarrierInfo>
                                <CarrierDesigCode>VF</CarrierDesigCode>
                                <MarketingCarrierFlightNumberText>{$segment['flight_number']}</MarketingCarrierFlightNumberText>
                            </MarketingCarrierInfo>
                            <OperatingCarrierInfo>
                                <CarrierDesigCode>VF</CarrierDesigCode>
                                <OperatingCarrierFlightNumberText>{$segment['flight_number']}</OperatingCarrierFlightNumberText>
                            </OperatingCarrierInfo>
                            <PaxSegmentID>{$segment['segment_id']}</PaxSegmentID>
                        </PaxSegment>
                    </PaxSegmentList>
                </DataLists>
                <Order>
                    <OrderID>{$orderId}</OrderID>
                    <ValidatingCarrierCode>PK</ValidatingCarrierCode>
                </Order>
                <PaymentFunctions>
                    <PaymentProcessingDetails>
                        <Amount CurCode="{$currency}">{$payment['amount']}</Amount>
                        <PaymentMethod>
                            <AccountableDoc>
                                <DocType>{$payment['method']}</DocType>
                                <TicketID>{$payment['ticket_id']}</TicketID>
                            </AccountableDoc>
                        </PaymentMethod>
                        <PaymentRefID>PaymentInfo1</PaymentRefID>
                        <TypeCode>{$payment['method']}</TypeCode>
                    </PaymentProcessingDetails>
                </PaymentFunctions>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
            </Preference>
        </DoReissueCommitRQ>
        XML;

        return $this->sendRequest('/DoReissueCommit', $xmlRequest);
    }

    /**
     * DoOrderRetrieve to fetch PNR details
     *
     * @param string $orderId PNR number
     * @param string $carrierCode Carrier code (e.g., PK)
     * @return SimpleXMLElement
     */
    public function orderRetrieve($orderId, $carrierCode = 'PK')
    {
        $xmlRequest = <<<XML
        <DoOrderRetrieveRQ>
            {$this->getPartyBlock()}
            <Query>
                <OrderFilterCriteria>
                    <Order>
                        <OrderID>{$orderId}</OrderID>
                        <ValidatingCarrierCode>{$carrierCode}</ValidatingCarrierCode>
                    </Order>
                </OrderFilterCriteria>
            </Query>
            <Preference>
                <LanguagePref LangCode="EN"/>
            </Preference>
        </DoOrderRetrieveRQ>
        XML;

        return $this->sendRequest('/DoOrderRetrieve', $xmlRequest);
    }

    /**
     * DoAirShopping for reissue
     *
     * @param array $segment Segment details [origin, destination, departure_date]
     * @param array $passenger Passenger details [type]
     * @param string $currency Currency code
     * @return SimpleXMLElement
     */
    public function airShoppingReissue($segment, $passenger, $currency = 'PKR')
    {
        $xmlRequest = <<<XML
        <DoAirShoppingRQ>
            <Version>20.1</Version>
            {$this->getPartyBlock()}
            <Query>
                <OriginDestCriteria>
                    <DestCode>{$segment['destination']}</DestCode>
                    <OriginCode>{$segment['origin']}</OriginCode>
                    <Departure>
                        <Date>{$segment['departure_date']}</Date>
                    </Departure>
                    <CabinType>
                        <CabinTypeCode>Y</CabinTypeCode>
                    </CabinType>
                </OriginDestCriteria>
                <Paxs>
                    <Pax>
                        <PaxID>SH1</PaxID>
                        <PTC>{$passenger['type']}</PTC>
                    </Pax>
                </Paxs>
            </Query>
            <Preference>
                <CurrencyPref CurCode="{$currency}"/>
                <LanguagePref LangCode="EN"/>
            </Preference>
        </DoAirShoppingRQ>
        XML;

        return $this->sendRequest('/DoAirShopping', $xmlRequest);
    }
    /**
     * Parse and format flight data from the API response
     */
    public function parseAirShoppingResponse($response)
    {
        $flights = [];

        // Check if essential data is present
        if (!isset($response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxJourneyList']['PaxJourney']) ||
            !isset($response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxSegmentList']['PaxSegment']) ||
            !isset($response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['OriginDestList']['OriginDest'])) {
            Log::warning('Missing required data in AirShopping response', ['response' => $response]);
            return $flights;
        }

        // Normalize PaxSegment to an array of segments
        $rawPaxSegments = $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxSegmentList']['PaxSegment'];
        $paxSegments = (is_array($rawPaxSegments) && isset($rawPaxSegments['PaxSegmentID']))
            ? [$rawPaxSegments]
            : (is_array($rawPaxSegments) ? $rawPaxSegments : [$rawPaxSegments]);

        // Normalize PaxJourney to an array of journeys
        $rawPaxJourneys = $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxJourneyList']['PaxJourney'];
        if (is_string($rawPaxJourneys)) {
            $paxJourneys = [['PaxJourneyID' => $rawPaxJourneys, 'PaxSegmentRefID' => []]];
        } elseif (is_array($rawPaxJourneys) && isset($rawPaxJourneys['PaxJourneyID'])) {
            $paxJourneys = [$rawPaxJourneys];
        } else {
            $paxJourneys = is_array($rawPaxJourneys) ? $rawPaxJourneys : [$rawPaxJourneys];
        }

        // Normalize OriginDest to an array of origin-destination entries
        $rawOriginDests = $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['OriginDestList']['OriginDest'];
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

                foreach ($segmentRefs as $segmentRef) {
                    $segment = $paxSegmentMap[(string)$segmentRef] ?? null;
                    if ($segment) {
                        $segments[] = [
                            'segment_key' => (string)$segment['PaxSegmentID'],
                            'origin' => (string)$segment['Dep']['IATA_LocationCode'],
                            'origin_name' => (string)$segment['Dep']['StationName'],
                            'destination' => (string)$segment['Arrival']['IATA_LocationCode'],
                            'destination_name' => (string)$segment['Arrival']['StationName'],
                            'departure_time' => $this->formatDateTime((string)$segment['Dep']['AircraftScheduledDateTime']),
                            'arrival_time' => $this->formatDateTime((string)$segment['Arrival']['AircraftScheduledDateTime']),
                            'flight_number' => (string)$segment['MarketingCarrierInfo']['MarketingCarrierFlightNumberText'],
                            'carrier' => (string)$segment['MarketingCarrierInfo']['CarrierDesigCode'],
                            'carrier_name' => (string)($segment['MarketingCarrierInfo']['CarrierName'] ?? 'Unknown'),
                            'duration' => $this->formatDuration((string)$segment['Duration']),
                            'aircraft_type' => (string)($this->getAircraftType($segment['DatedOperatingLeg']) ?? 'Unknown'),
                            'baggage_allowance' => $this->getBaggageAllowance($segmentRef, $response),
                        ];
                    }
                }

                $flight = [
                    'segments' => $segments,
                    'price' => [],
                    'bundles' => [],
                ];

                $offers = isset($response['Body']['IATA_AirShoppingRS']['Response']['OffersGroup']['CarrierOffers']['Offer'])
                    ? (is_array($response['Body']['IATA_AirShoppingRS']['Response']['OffersGroup']['CarrierOffers']['Offer'])
                        ? $response['Body']['IATA_AirShoppingRS']['Response']['OffersGroup']['CarrierOffers']['Offer']
                        : [$response['Body']['IATA_AirShoppingRS']['Response']['OffersGroup']['CarrierOffers']['Offer']])
                    : [];

                foreach ($offers as $offer) {
                    $journeyPriceClasses = is_array($offer['JourneyOverview']['JourneyPriceClass'])
                        ? $offer['JourneyOverview']['JourneyPriceClass']
                        : [$offer['JourneyOverview']['JourneyPriceClass']];
                    $journeyRefs = array_map(function ($priceClass) {
                        return (string)(is_array($priceClass) ? ($priceClass['PaxJourneyRefID'] ?? '') : $priceClass);
                    }, $journeyPriceClasses);

                    if (in_array((string)$journey['PaxJourneyID'], $journeyRefs)) {
                        $offerItems = is_array($offer['OfferItem']) ? $offer['OfferItem'] : [$offer['OfferItem']];

                        $baggageAllowances = [];
                        foreach ($offerItems as $item) {
                            dd($offerItems, $item, $offers);
                            // Handle FareDetail, which may have multiple FareComponents
                            $fareDetails = isset($item['FareDetail']) ? (is_array($item['FareDetail']) && isset($item['FareDetail'][0]) && is_array($item['FareDetail'][0]) ? $item['FareDetail'] : [$item['FareDetail']]) : [];
                            $paxRefs = [];
                            foreach ($fareDetails as $fareDetail) {
                                $paxRefIDs = isset($fareDetail['PaxRefID']) ? (is_array($fareDetail['PaxRefID']) ? $fareDetail['PaxRefID'] : [$fareDetail['PaxRefID']]) : [];
                                $paxRefs = array_merge($paxRefs, $paxRefIDs);
                            }
                            $paxRefs = array_unique($paxRefs);

                            // Process services for baggage allowances
                            $services = is_array($item['Service']) ? $item['Service'] : [$item['Service']];
                            foreach ($services as $service) {
                                if (isset($service['ServiceAssociations']['ServiceDefinitionRefID'])) {
                                    $serviceDefId = (string)$service['ServiceAssociations']['ServiceDefinitionRefID'];
                                    $baggageDetails = $this->getBaggageDetailsByServiceDef($serviceDefId, $response);
                                    if ($baggageDetails) {
                                        $baggageAllowances[] = $baggageDetails;
                                    }
                                }
                            }

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

                        $flight['bundles'][] = [
                            'offerID' => (string)$offer['OfferID'],
                            'ownerCode' => (string)$offer['OwnerCode'],
                            'parameters' => [
                                'price_class_ref' => (string)($offer['JourneyOverview']['PriceClassRefID'] ?? ''),
                                'cabin_type' => $this->getCabinType($segmentRefs[0], $offer),
                            ],
                            'timeLimits' => $this->formatDateTime($offerItems[0]['OfferItemPaymentTimeLimit']['PaymentTimeLimitDate']['PaymentTimeLimitDateTime'] ?? ''),
                            'totalPrice' => [
                                'base_amount' => number_format((float)($offer['TotalPrice']['BaseAmount'] ?? 0), 2),
                                'discount' => number_format((float)($offer['TotalPrice']['Discount']['DiscountAmount'] ?? 0), 2),
                                'fee' => number_format((float)($offer['TotalPrice']['Fee']['Amount'] ?? 0), 2),
                                'total_amount' => number_format((float)($offer['TotalPrice']['TotalAmount'] ?? 0), 2),
                                'currency' => (string)($offer['TotalPrice']['TotalAmount']['CurCode'] ?? 'PKR'),
                                'taxes' => number_format((float)($offer['TotalPrice']['TaxSummary']['TotalTaxAmount'] ?? 0), 2),
                                'surcharges' => number_format((float)($offer['TotalPrice']['Surcharge']['TotalAmount'] ?? 0), 2),
                            ],
                            'offerItem' => $this->getOfferItem($offer['OfferItem']),
                            'baggageAllowance' => $baggageAllowances,
                            'journeyOverview' => [
                                'id' => (string)($offer['JourneyOverview']['PriceClassRefID'] ?? '--'),
                                'priceClass' => (array)($offer['JourneyOverview']['JourneyPriceClass'] ?? []),
                            ],
                        ];

                        if (empty($flight['price']) || (float)$offer['TotalPrice']['TotalAmount'] < (float)$flight['price']['total_amount']) {
                            $flight['price'] = [
                                'total_amount' => number_format((float)($offer['TotalPrice']['TotalAmount'] ?? 0), 2),
                                'currency' => (string)($offer['TotalPrice']['TotalAmount']['CurCode'] ?? 'PKR'),
                            ];
                        }
                    }
                }

                $flightsForOd[] = $flight;
            }

            $originDestGroups[] = [
                'departureCode' => $origin,
                'arrivalCode' => $destination,
                'flights' => $flightsForOd,
                'responseId' => (string)($response['Body']['IATA_AirShoppingRS']['Response']['ShoppingResponse']['ShoppingResponseRefID'] ?? ''),
            ];
        }

        $result = $originDestGroups;
        $result['transactionId'] = (string)($response['Body']['IATA_AirShoppingRS']['PayloadAttributes']['EchoTokenText'] ?? null);

        return $result;
    }
    private function getBaggageDetailsByServiceDef($serviceDefId, $response)
    {
        $serviceDefinitions = is_array($response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition'])
            ? $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition']
            : [$response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition']];

        foreach ($serviceDefinitions as $service) {
            if ((string)$service['ServiceDefinitionID'] === $serviceDefId && isset($service['ServiceDefinitionAssociation']['BaggageAllowanceRef']['BaggageAllowanceRefID'])) {
                $baggageRef = (string)$service['ServiceDefinitionAssociation']['BaggageAllowanceRef']['BaggageAllowanceRefID'];
                return $this->getBaggageDetails($baggageRef, $response);
            }
        }
        return null;
    }
    private function getOfferItem($item)
    {
        if (empty($item)) return $item;
        $items = (is_array($item)) ? $item : [$item];
        $offers = [];
        foreach($items as $offer) {
            $offers[] = [
                'id' => $offer['OfferItemID'] ?? '',
                'fareDetail' => $offer['FareDetail'] ?? [],
                'mandatoryInd' => $offer['MandatoryInd'] ?? '',
                'paymentLimit' => $this->formatDateTime($offer['OfferItemPaymentTimeLimit']['PaymentTimeLimitDate']['PaymentTimeLimitDateTime'] ?? ''),
                'price' => [
                    'base_amount' => number_format((float)($offer['Price']['BaseAmount'] ?? 0), 2),
                    'discount' => number_format((float)($offer['Price']['Discount']['DiscountAmount'] ?? 0), 2),
                    'fee' => number_format((float)($offer['Price']['Fee']['Amount'] ?? 0), 2),
                    'surcharge' => $offer['Price']['Surcharge'] ?? [],
                    'taxSummary' => $offer['Price']['TaxSummary'] ?? [],
                    'total_amount' => number_format((float)($offer['Price']['TotalAmount'] ?? 0), 2),
                    'service' => $offer['Service'] ?? [],
                ]
            ];
        }
        return $offers;
    }
    /**
     * Format date and time for display
     */
    private function formatDateTime($dateTime)
    {
        return Carbon::parse($dateTime)->format('D, M d Y H:i');
    }
    /**
     * Format duration (e.g., PT1H55M to 1h 55m)
     */
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
    /**
     * Get cabin type for a segment
     */
    private function getCabinType($segmentRef, $offer)
    {
        // dd($offer);
        $fareDetails = is_array($offer['OfferItem']) && isset($offer['OfferItem'][0]) && is_array($offer['OfferItem'][0]) ? $offer['OfferItem'] : [$offer['OfferItem']];
        foreach ($fareDetails as $fareDetail) {
            $fareComponents = is_array($fareDetail['FareDetail']) && isset($fareDetail['FareDetail'][0]) && is_array($fareDetail['FareDetail'][0]) ? $fareDetail['FareDetail'] : (isset($fareDetail['FareDetail']) ? [$fareDetail['FareDetail']] : []);
            foreach ($fareComponents as $fareComponent) {
                $paxSegmentRefs = isset($fareComponent['FareComponent']['PaxSegmentRefID']) ? (is_array($fareComponent['FareComponent']['PaxSegmentRefID']) ? $fareComponent['FareComponent']['PaxSegmentRefID'] : [$fareComponent['FareComponent']['PaxSegmentRefID']]) : [];
                // dd($segmentRef, $paxSegmentRefs, $fareComponent);
                if (in_array((string)$segmentRef, $paxSegmentRefs)) {
                    return (string)($fareComponent['FareComponent']['CabinType']['CabinTypeName'] ?? 'EconomY');
                }
            }
        }
        return 'EconomY';
    }
    /**
     * Get baggage allowance for a segment
     */
    private function getBaggageAllowance($segmentRef, $response)
    {
        $baggageAllowances = [];

        $serviceDefinitionsRaw = $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition'] ?? [];

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
    /**
     * Get baggage details by reference ID
     */
    private function getBaggageDetails($baggageRef, $response)
    {
        $baggageAllowancesRaw = $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['BaggageAllowanceList']['BaggageAllowance'] ?? [];

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
    /**
     * Get passenger type
     */
    private function getPassengerType($paxRef, $response)
    {
        $passengers = is_array($response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxList']['Pax'])
            ? $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxList']['Pax']
            : [$response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['PaxList']['Pax']];

        foreach ($passengers as $pax) {
            if ((string)$pax['PaxID'] === $paxRef) {
                return (string)$pax['PTC'];
            }
        }
        return 'Unknown';
    }
    /**
     * Get aircraft type from DatedOperatingLeg, handling single or multiple legs
     */
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
    /**
     * Determine trip type
     */
    private function determineTripType($journeys, $response)
    {
        $originDests = is_array($response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['OriginDestList']['OriginDest'])
            ? $response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['OriginDestList']['OriginDest']
            : [$response['Body']['IATA_AirShoppingRS']['Response']['DataLists']['OriginDestList']['OriginDest']];

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
            'Content-Type' => 'application/xml',
            'Accept' => 'application/xml',
            'SOAPAction' => "cranendc/{$action}.",
        ];
    }
    // FLOW: doAirShopping > doOrderCreate > DoOrderRetrieve > doOrderChange > DoVoidTicket > DoAddAncillary
    //       DoServiceList > DoSeatAvailability > DoBaggageServiceList > DoTicketPreview > doOfferPrice > doOrderCancelCommit
    // connected flight route KHI - LHE - DXB
}