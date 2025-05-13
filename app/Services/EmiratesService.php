<?php

namespace App\Services;

use Carbon\Carbon;
use SimpleXMLElement;
use Illuminate\Support\Str;
use App\Services\HelperService;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class EmiratesService
{
    protected $helperService;
    protected $randomId;
    protected $url;
    protected $user;
    protected $password;
    protected $agencyId;
    protected $pcc;
    protected $subscriptionKey;

    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
        $this->randomId = Str::random(30);

        $this->agencyName = config('services.agency.name');
        
        $this->url = config('services.emirates_api.url');
        $this->user = config('services.emirates_api.user');
        $this->password = config('services.emirates_api.password');
        $this->agencyId = config('services.emirates_api.agency_id');
        $this->subscriptionKey = config('services.emirates_api.subscription_key');
        $this->pcc = config('services.emirates_api.pcc');
    }
    public function searchFlights($data)
    {
        $origin = $data['arr'];
        $destination = $data['dest'];
        $departureDate = $data['dep'];
        $returnDate = $data['return'];
        $cabinClass = $data['cabinClass'] ?? 'Y';
        $currCode = $data['currCode'] ?? 'PKR';
        session([
            'responseId' => null,
            'IdsExpireTimeEmi' => null,
        ]);
        $paxXml = $this->getPaxTag(['adt' => $data['adt'], 'chd' => $data['chd'] ?? null, 'inf' => $data['inf'] ?? null]);
        $returnXml = '';
        $returnODR = '';
        if ($returnDate) {
            $returnXml = '
            <OriginDestination OriginDestinationKey="OD2">
                <Departure>
                    <AirportCode>'.$destination.'</AirportCode>
                    <Date>'.$returnDate.'</Date>
                </Departure>
                <Arrival>
                    <AirportCode>'.$origin.'</AirportCode>
                </Arrival>
            </OriginDestination>';
            $returnODR = '<OriginDestinationReferences>OD2</OriginDestinationReferences>';
        }
        $farePrefrences = '<FarePreferences>
                                <Types>
                                    <Type>70J</Type>
                                    <Type>749</Type>
                                </Types>
                            </FarePreferences>';
        $body = '<AirShoppingRQ Version="17.2" TransactionIdentifier="'.$this->randomId.'">
                        <Document id="document"/>
                        <Party>
                            <Sender>
                                <TravelAgencySender>
                                    <PseudoCity>'.$this->pcc.'</PseudoCity>
                                    <AgencyID>'.$this->agencyId.'</AgencyID>
                                </TravelAgencySender>
                            </Sender>
                        </Party>
                        <CoreQuery>
                            <OriginDestinations>
                                <OriginDestination OriginDestinationKey="OD1">
                                    <Departure>
                                        <AirportCode>'.$origin.'</AirportCode>
                                        <Date>'.$departureDate.'</Date>
                                    </Departure>
                                    <Arrival>
                                        <AirportCode>'.$destination.'</AirportCode>
                                    </Arrival>
                                </OriginDestination>
                                ' . $returnXml . '
                            </OriginDestinations>
                        </CoreQuery>
                        <Preference>
                            <CabinPreferences>
                                <CabinType>
                                    <Code>'.$cabinClass.'</Code>
                                    <OriginDestinationReferences>OD1</OriginDestinationReferences>
                                    ' . $returnODR . '
                                </CabinType>
                            </CabinPreferences>
                        </Preference>
                        <DataLists>
                            ' . $paxXml . '
                        </DataLists>
                    </AirShoppingRQ>';
        // $xmlBody = $this->getSoapEnvelope($body);
        // dd($xmlBody);
        try {
            $response = $this->helperService->postXml($this->url, $this->getSoapHeaders('AirShoppingRQ'), $this->getSoapEnvelope($body));
            // dd($response->body());
            if (!$response->successful()) {
                \Log::error('Flight details request failed Emirates', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight search request failed Emirates.', 'details' => $response->body()];
            }

            $responseXml = $response->body();
            $data = $this->helperService->XMLtoJSONEmirate($responseXml);
            // dd($data);
            $airShoppingRS = $data['SOAP-ENV:Body']['XXTransactionResponse']['RSP']['AirShoppingRS'];

            if(isset($airShoppingRS['Errors'])){
                return ['error' => 'Flight search failed.', 'details' => $airShoppingRS['Errors']['Error']];
            }

            $flightData = [
                'offers' => $airShoppingRS['OffersGroup']['AirlineOffers']['Offer'] ?? '',
                'passengers' => $airShoppingRS['DataLists']['PassengerList']['Passenger'] ?? '',
                'baggageList' => $airShoppingRS['DataLists']['BaggageAllowanceList']['BaggageAllowance'] ?? '',
                'fares' => $airShoppingRS['DataLists']['FareList']['FareGroup'] ?? '',
                'flightSegments' => $airShoppingRS['DataLists']['FlightSegmentList']['FlightSegment'] ?? '',
                'flights' => $airShoppingRS['DataLists']['FlightList']['Flight'] ?? '',
                'destinationList' => $airShoppingRS['DataLists']['OriginDestinationList']['OriginDestination'] ?? '',
                'priceClass' => $airShoppingRS['DataLists']['PriceClassList']['PriceClass'] ?? '',
                'serviceList' => $airShoppingRS['DataLists']['ServiceDefinitionList']['ServiceDefinition'] ?? '',
                'responseId' => $airShoppingRS['ShoppingResponseID']['ResponseID']['value'] ?? '',
                'request' => 1,
            ];
            // dd($flightData);
            session([
                'responseId' => $airShoppingRS['ShoppingResponseID']['ResponseID']['value'] ?? '',
            ]);
            // dd($this->getFlights($flightData));
            return $this->getFlights($flightData);
        } catch (\Exception $e) {
            \Log::error('Exception in fetching flight details', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while fetching flight details.'];
        }
    }
    public function getBundlePrice($data)
    {
        // dd($data);
        $data = $data['data'];
        if(empty($data)) return '';
        $offer = $this->formatOfferTag($data['depOfferIds'], $data['firstFlightBundleId'], $data['responseId']);
        if($data['rtnOfferIds']) {
            $offer .= $this->formatOfferTag($data['rtnOfferIds'], $data['returnFlightBundleId'], $data['responseId']);
        }
        $paxXml = $this->getPaxTag($data['paxCount']);
        $farePrefrences = '
                    <Preference>
                        <FarePreferences>
                            <Types>
                                <Type>70J</Type>
                                <Type>749</Type>
                            </Types>
                            <Exclusion>
                                <NoMinStayInd>false</NoMinStayInd>
                                <NoMaxStayInd>false</NoMaxStayInd>
                                <NoAdvPurchaseInd>false</NoAdvPurchaseInd>
                                <NoPenaltyInd>false</NoPenaltyInd>
                            </Exclusion>
                        </FarePreferences>
                        <PricingMethodPreference>
                            <BestPricingOption>Y</BestPricingOption>
                        </PricingMethodPreference>
                        <ServicePricingOnlyPreference>
                            <ServicePricingOnlyInd>false</ServicePricingOnlyInd>
                        </ServicePricingOnlyPreference>
                    </Preference>';
        $body = '<OfferPriceRQ Version="17.2" TransactionIdentifier="'.$this->randomId.'">
                    <Document id="document"/>
                    <Party>
                        <Sender>
                            <TravelAgencySender>
                                <PseudoCity>'.$this->pcc.'</PseudoCity>
                                <AgencyID>'.$this->agencyId.'</AgencyID>
                            </TravelAgencySender>
                        </Sender>
                    </Party>
                    <Query>
                        ' . $offer . '
                    </Query>
                    <DataLists>
                        ' . $paxXml . '
                    </DataLists>
                </OfferPriceRQ>';
        // dd($this->getSoapEnvelope($body));
        try {
            $response = $this->helperService->postXml($this->url, $this->getSoapHeaders('OfferPriceRQ'), $this->getSoapEnvelope($body));
            // dd($response->body());
            if (!$response->successful()) {
                \Log::error('Flight bundle request failed Emirates', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight bundle request failed Emirates.', 'details' => $response->body()];
            }
            $responseXml = $response->body();
            $data = $this->helperService->XMLtoJSONEmirate($responseXml);
            // dd($responseXml);
            $offerPriceRS = $data['SOAP-ENV:Body']['XXTransactionResponse']['RSP']['OfferPriceRS'];

            if(isset($offerPriceRS['Errors'])){
                return ['error' => 'Flight bundle failed.', 'details' => $offerPriceRS['Errors']['Error']];
            }
            $flightData = [
                'offers' => $offerPriceRS['PricedOffer'] ?? '',
                'passengers' => $offerPriceRS['DataLists']['PassengerList']['Passenger'] ?? '',
                'baggageList' => $offerPriceRS['DataLists']['BaggageAllowanceList']['BaggageAllowance'] ?? '',
                'fares' => $offerPriceRS['DataLists']['FareList']['FareGroup'] ?? '',
                'flightSegments' => $offerPriceRS['DataLists']['FlightSegmentList']['FlightSegment'] ?? '',
                'flights' => $offerPriceRS['DataLists']['FlightList']['Flight'] ?? '',
                'destinationList' => $offerPriceRS['DataLists']['OriginDestinationList']['OriginDestination'] ?? '',
                'priceClass' => $offerPriceRS['DataLists']['PriceClassList']['PriceClass'] ?? '',
                'serviceList' => $offerPriceRS['DataLists']['ServiceDefinitionList']['ServiceDefinition'] ?? '',
                'responseId' => $offerPriceRS['ShoppingResponseID']['ResponseID']['value'] ?? '',
                'request' => 2,
            ];
            // dd($flightData);
            session([
                'responseId' => $offerPriceRS['ShoppingResponseID']['ResponseID']['value'] ?? '',
            ]);
            // dd($this->getFlights($flightData));
            return $this->getFlights($flightData);
        } catch (\Exception $e) {
            \Log::error('Exception in fetching flight details', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while fetching flight details.'];
        }
    }
    public function bookFlight($data)
    {
        if(empty($data)) return '';
        $offer = $this->formatOfferTag($data['offerIds'], $data['bundleId'], $data['responseId']);
        // dd($offer);
        $paxXml = $this->getPaxContactTag($data['paxCount'], $data['passengers']);
        // dd($paxXml);
        $farePrefrences = '
            <Preference>
                <FarePreferences>
                    <Types>
                        <Type>70J</Type>
                        <Type>749</Type>
                    </Types>
                    <Exclusion>
                        <NoMinStayInd>false</NoMinStayInd>
                        <NoMaxStayInd>false</NoMaxStayInd>
                        <NoAdvPurchaseInd>false</NoAdvPurchaseInd>
                        <NoPenaltyInd>false</NoPenaltyInd>
                    </Exclusion>
                </FarePreferences>
                <PricingMethodPreference>
                    <BestPricingOption>Y</BestPricingOption>
                </PricingMethodPreference>
                <ServicePricingOnlyPreference>
                    <ServicePricingOnlyInd>false</ServicePricingOnlyInd>
                </ServicePricingOnlyPreference>
            </Preference>';
        $loggedInTag = '
            <ContactList>
                <ContactInformation ContactID="CID1">
                    <PostalAddress>
                        <Label>AddressAtDestination</Label>
                        <Street>123 STREET</Street>
                        <PostalCode>33160</PostalCode>
                        <CityName>MIAMI</CityName>
                        <CountrySubdivisionName>FL</CountrySubdivisionName>
                        <CountryCode>US</CountryCode>
                    </PostalAddress>
                    <ContactProvided>
                        <EmailAddress>
                            <Label>Personal</Label>
                            <EmailAddressValue>KYOUNG@FARELOGIX.COM</EmailAddressValue>
                        </EmailAddress>
                    </ContactProvided>
                    <ContactProvided>
                        <Phone>
                            <Label>Home</Label>
                            <CountryDialingCode>1</CountryDialingCode>
                            <PhoneNumber>7865554433</PhoneNumber>
                        </Phone>
                    </ContactProvided>
                </ContactInformation>
            </ContactList>';
        $body = '<OrderCreateRQ Version="17.2" TransactionIdentifier="'.$this->randomId.'" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns1="http://ndc.farelogix.com/aug">
                    <Document id="document"/>
                    <Party>
                        <Sender>
                            <TravelAgencySender>
                                <PseudoCity>'.$this->pcc.'</PseudoCity>
                                <AgencyID>'.$this->agencyId.'</AgencyID>
                            </TravelAgencySender>
                        </Sender>
                    </Party>
                    <Query>
                        <Order>
                            ' . $offer . '
                        </Order>
                        <DataLists>
                            ' . $paxXml . '
                            ' . $loggedInTag . '
                        </DataLists>
                    </Query>
                </OrderCreateRQ>';
        // dd($this->getSoapEnvelope($body));
        try {
            $response = [];
            if (App::environment('local')) {
                $cacheKey = 'orderComp_GG_' . md5(json_encode($body));
                $response = Cache::get($cacheKey);
                if (!$response) {
                    $response = $this->helperService->postXml(
                        $this->url,
                        $this->getSoapHeaders('OrderCreateRQ'),
                        $this->getSoapEnvelope($body)
                    );
                    if ($response && $response->successful()) {
                        Cache::put($cacheKey, $response, now()->addHours(30));
                    }
                }
            } elseif (App::environment('production')) {
                $response = $this->helperService->postXml($this->url, $this->getSoapHeaders('OrderCreateRQ'), $this->getSoapEnvelope($body));
            }
            if (!$response || !$response->successful()) {
                \Log::error('Flight booking request failed Emirates', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight booking request failed Emirates.', 'details' => $response->body()];
            }
            $data = $this->helperService->XMLtoJSONEmirate($response->body());
            $orderViewRS = $data['SOAP-ENV:Body']['XXTransactionResponse']['RSP']['OrderViewRS'];
            if(isset($orderViewRS['Errors'])){
                return ['error' => 'Flight booking failed.', 'details' => $orderViewRS['Errors']['Error']];
            }
            $flightData = [
                'offers' => $orderViewRS['Response']['Order'] ?? '',
                'passengers' => $orderViewRS['Response']['DataLists']['PassengerList']['Passenger'] ?? '',
                'baggageList' => $orderViewRS['Response']['DataLists']['BaggageAllowanceList']['BaggageAllowance'] ?? '',
                'fares' => $orderViewRS['Response']['DataLists']['FareList']['FareGroup'] ?? '',
                'flightSegments' => $orderViewRS['Response']['DataLists']['FlightSegmentList']['FlightSegment'] ?? '',
                'flights' => $orderViewRS['Response']['DataLists']['FlightList']['Flight'] ?? '',
                'destinationList' => $orderViewRS['Response']['DataLists']['OriginDestinationList']['OriginDestination'] ?? '',
                'priceClass' => $orderViewRS['Response']['DataLists']['PriceClassList']['PriceClass'] ?? '',
                'serviceList' => $orderViewRS['Response']['DataLists']['ServiceDefinitionList']['ServiceDefinition'] ?? '',
                'transactionId' => $orderViewRS['@attributes']['TransactionIdentifier'] ?? '',
                'request' => 3,
            ];
            // dd($this->getFlights($flightData));
            session([
                'responseId' => null,
            ]);
            return $this->getFlights($flightData);
        } catch (\Exception $e) {
            \Log::error('Exception in fetching flight details', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while fetching flight details.'];
        }
    }


    // ------------------  Helper Functions  ---------------------

    private function getSoapEnvelope($body)
    {
        return '<?xml version="1.0" encoding="utf-8"?>
            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://farelogix.com/ns1" xmlns:t="http://farelogix.com/flx/t">
                <SOAP-ENV:Header>
                    <t:TransactionControl>
                        <tc>
                            <iden u="'.$this->user.'" p="'.$this->password.'" pseudocity="'.$this->pcc.'" agt="'.$this->user.'" agtpwd="'.$this->password.'" agy="'.$this->agencyId.'"/>
                            <agent user="'.$this->user.'"/>
                            <trace>'.$this->pcc.'_ek</trace>
                            <script engine="FLXDM" name="'.$this->agencyName.'-ek-dispatch.flxdm"/>
                        </tc>
                    </t:TransactionControl>
                </SOAP-ENV:Header>
                <SOAP-ENV:Body>
                    <ns1:XXTransaction>
                        <REQ>
                            '.$body.'
                        </REQ>
                    </ns1:XXTransaction>
                </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';
    }
    private function getSoapHeaders($action)
    {
        return [
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type' => 'text/xml;charset=UTF-8',
            'SOAPAction' => $action,
            'Agency' => $this->agencyName,
            'IATA' => $this->agencyId,
            'PCC' => $this->pcc,
        ];
    }
    private function getPaxTag($data)
    {
        if(empty($data) || empty($data['adt'])) return '';
        $paxId = 1;
        $adtIds = [];
        $paxXml = "<PassengerList>";
        for ($i = 0; $i < $data['adt']; $i++, $paxId++) {
            $paxXml .= '
                <Passenger PassengerID="T'.$paxId.'">
                    <PTC>ADT</PTC>
                </Passenger>';
            $adtIds[] = 'T'.$paxId;
        }
        for ($i = 0; $i < $data['chd']; $i++, $paxId++) {
            $paxXml .= '
                <Passenger PassengerID="T'.$paxId.'">
                    <PTC>CNN</PTC>
                </Passenger>';
        }
        for ($i = 0; $i < $data['inf']; $i++) {
            if (isset($adtIds[$i])) {
                $infId = $adtIds[$i] . '.1';
                $paxXml .= '
                    <Passenger PassengerID="'.$infId.'">
                        <PTC>INF</PTC>
                    </Passenger>';
            } else {
                break;
            }
        }
        $paxXml .= "</PassengerList>";
        return $paxXml;
    }
    // private function getFlights($data)
    // {
    //     // dd($data);
    //     if (empty($data) || empty($data['destinationList']) || empty($data['flights']) || empty($data['flightSegments']) || empty($data['offers']) || empty($data['baggageList']) || empty($data['priceClass'])) return 'Something missing';

    //     $destinations = isset($data['destinationList'][0]) ? $data['destinationList'] : [$data['destinationList']];
    //     $flights = collect(isset($data['flights'][0]) ? $data['flights'] : [$data['flights']]);
    //     $segments = collect(isset($data['flightSegments'][0]) ? $data['flightSegments'] : [$data['flightSegments']]);
    //     $offers = collect(isset($data['offers'][0]) ? $data['offers'] : [$data['offers']]);
    //     $baggages = collect(isset($data['baggageList'][0]) ? $data['baggageList'] : [$data['baggageList']]);
    //     $priceClass = collect(isset($data['priceClass'][0]) ? $data['priceClass'] : [$data['priceClass']]);
    //     $passengers = collect(isset($data['passengers'][0]) ? $data['passengers'] : [$data['passengers']]);
    //     $serviceList = collect($data['serviceList']) ?? null;
    //     $responseId = $data['responseId'] ?? null;
    //     $request = $data['request'];
    //     // dd($destinations, $flights, $segments, $offers, $baggages, $priceClass, $responseId);
    //     $timeZone = config('variables.setting.timezone') ?? 'Asia/Karachi';
    //     $data = [];
    //     $tax = config('variables.flyjinnah_api.tax') ?? 0;
    //     $matchedOffers = '';
    //     foreach ($destinations as $item) {
    //         $flightIds = explode(' ', $item['FlightReferences']['value'] ?? ''); // fetch bundle with this Aliiiiiiiiiii);
    //         $matchedFlights = $flights->filter(fn($flight) => in_array($flight['@attributes']['FlightKey'] ?? null, $flightIds))->values();
    //         $flightSegmentReferences = $matchedFlights->map(fn($flight) => explode(' ', $flight['SegmentReferences']['value'] ?? ''))->values();
    //         // dd($flightSegmentReferences);
    //         $segmentDetails=[];
    //         foreach ($flightSegmentReferences as $segmentIds) {
    //             $flightSegments = collect($segmentIds)
    //                 ->map(fn($id) => $segments->firstWhere('@attributes.SegmentKey', $id))
    //                 ->filter()
    //                 ->values();

    //             $relatedFlightKeys = $matchedFlights
    //                 ->filter(fn($flight) => !array_diff($segmentIds, explode(' ', $flight['SegmentReferences']['value'] ?? '')))
    //                 ->pluck('@attributes.FlightKey')
    //                 ->all();
    //             // dd($relatedFlightKeys);

    //             $matchedOffers = $offers
    //                 ->filter(function ($offer) use ($relatedFlightKeys) {
    //                     return isset($offer['FlightsOverview']['FlightRef']['value'])
    //                         ? in_array($offer['FlightsOverview']['FlightRef']['value'], $relatedFlightKeys)
    //                         : true;
    //                 })
    //                 ->map(function ($offer) use ($baggages, $priceClass, $serviceList, $passengers) {
    //                     $offer['BaggageAllowance'] = collect($offer['BaggageAllowance'] ?? [])
    //                         ->map(fn($allowance) => $this->updateBaggageAllowance($allowance, $baggages))
    //                         ->all();
    //                     // dd($offer);
    //                     // $item = isset($offer['OfferItem'][0]) ? $offer['OfferItem'][0] : $offer['OfferItem'];
    //                     $priceClassRef = $offer['FlightsOverview']['FlightRef']['@attributes']['PriceClassRef'] ?? null;
    //                     $offer['priceClass'] = $priceClassRef ? $priceClass->where('@attributes.PriceClassID', $priceClassRef)->values()->first() : '';
    //                     return [
    //                         'offerID' => $offer['@attributes'] ?? null,
    //                         'parameters' => $offer['Parameters'] ?? null,
    //                         'timeLimits' => $offer['TimeLimits'] ?? null,
    //                         'totalPrice' =>  [
    //                             'code' => $offer['TotalPrice']['DetailCurrencyPrice']['Total']['@attributes']['Code'] ?? '',
    //                             'amount' => $offer['TotalPrice']['DetailCurrencyPrice']['Total']['value'] ?? '',
    //                         ],
    //                         'offerItem' => $this->formatOfferItems($offer['OfferItem'], $serviceList, $passengers),
    //                         'baggageAllowance' => $offer['BaggageAllowance'],
    //                         'priceClass' => $offer['priceClass'],
    //                     ];
    //                 })->values();
    //             $expTime = isset($matchedOffers->first()['timeLimits']['OfferExpiration']['@attributes']['DateTime']) ? Carbon::parse($matchedOffers->first()['timeLimits']['OfferExpiration']['@attributes']['DateTime'])->setTimezone($timeZone) : now()->addMinutes(20);
    //             session([
    //                 'IdsExpireTimeEmi' => $expTime,
    //             ]);
    //             // dd($matchedOffers);
    //             $lowestPrice = [
    //                 'code' => $matchedOffers->min(fn($offer) => data_get($offer, 'totalPrice.code', 'PKR')),
    //                 'amount' => $matchedOffers->min(fn($offer) => (float) data_get($offer, 'totalPrice.amount', 0)) + $tax
    //             ];
    //             if ($flightSegments->isNotEmpty()) {
    //                 $secondFlight = [];
    //                 if (count($flightSegments) > 1) {
    //                     $last = $flightSegments->last();
    //                     $secondFlight = [
    //                         'isConnected' => filter_var($last['@attributes']['ConnectInd'], FILTER_VALIDATE_BOOLEAN),
    //                         'details' => $last['FlightDetail'] ?? [],
    //                         'equipment' => $last['Equipment'] ?? [],
    //                         'marketingCarrier' => $last['MarketingCarrier'] ?? [],
    //                     ];
    //                 }
    //                 $first = $flightSegments->first();
    //                 $totalMinutes = 0;
    //                 try {
    //                     $d1 = new \DateInterval($first['FlightDetail']['FlightDuration']['Value']['value'] ?? 'PT0M');
    //                     $totalMinutes += ($d1->h * 60) + $d1->i;
    //                 } catch (\Exception $e) {
    //                     \Log::error('Exception in calculate duration', ['message' => $e->getMessage()]);
    //                 }

    //                 if (!empty($secondFlight) && isset($secondFlight['details']['FlightDuration']['Value']['value'])) {
    //                     try {
    //                         $d2 = new \DateInterval($secondFlight['details']['FlightDuration']['Value']['value']);
    //                         $totalMinutes += ($d2->h * 60) + $d2->i;
    //                     } catch (\Exception $e) {
    //                         \Log::error('Exception in calculate duration', ['message' => $e->getMessage()]);
    //                     }
    //                 }

    //                 $hours = floor($totalMinutes / 60);
    //                 $minutes = $totalMinutes % 60;
    //                 $duration = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
    //                 $segmentDetails[] = [
    //                     'Departure' => $first['Departure'] ?? [],
    //                     'Arrival' => $flightSegments->last()['Arrival'] ?? [],
    //                     'segmentKey' => $first['@attributes']['SegmentKey'] ?? [],
    //                     'flightDetails' => [
    //                         'isConnected' => filter_var($first['@attributes']['ConnectInd'], FILTER_VALIDATE_BOOLEAN),
    //                         'details' => $first['FlightDetail'] ?? [],
    //                         'equipment' => $first['Equipment'] ?? [],
    //                         'marketingCarrier' => $first['MarketingCarrier'] ?? [],
    //                     ],
    //                     'secondFlight' => $secondFlight,
    //                     'duration' => $duration ?? '',
    //                     'price' => $lowestPrice,
    //                     'bundles' => $request === 1 ? $matchedOffers->all() : [],
    //                 ];
    //             }
    //         }
    //         if($request === 2) {
    //             $data['segments'][] = [
    //                 'departureCode' => $this->helperService->codeToCountry($item['DepartureCode']),
    //                 'arrivalCode' => $this->helperService->codeToCountry($item['ArrivalCode']),
    //                 'flights' => collect($segmentDetails)->first(),
    //                 'responseId' => $responseId,
    //             ];
    //         } else {
    //             $data[] = [
    //                 'departureCode' => $this->helperService->codeToCountry($item['DepartureCode']),
    //                 'arrivalCode' => $this->helperService->codeToCountry($item['ArrivalCode']),
    //                 'flights' => $segmentDetails,
    //                 'responseId' => $responseId,
    //             ];
    //         }
    //     }
    //     if($request === 2) {
    //         $data['bundle'] = $matchedOffers->first();
    //         $expTime = isset($data['bundle']['timeLimits']['OfferExpiration']['@attributes']['DateTime']) ? Carbon::parse($data['bundle']['timeLimits']['OfferExpiration']['@attributes']['DateTime'])->setTimezone($timeZone) : now()->addMinutes(20);
    //         session([
    //             'IdsExpireTimeEmi' => $expTime,
    //         ]);
    //     }
    //     // dd($data);
    //     return $data;
    // }
    private function getFlights($data) //New One
    {
        // dd($data);
        if (empty($data) || empty($data['destinationList']) || empty($data['flights']) || empty($data['flightSegments']) || empty($data['offers']) || empty($data['baggageList']) || empty($data['priceClass'])) return 'Something missing';

        $destinations = isset($data['destinationList'][0]) ? $data['destinationList'] : [$data['destinationList']];
        $flights = collect(isset($data['flights'][0]) ? $data['flights'] : [$data['flights']]);
        $segments = collect(isset($data['flightSegments'][0]) ? $data['flightSegments'] : [$data['flightSegments']]);
        $offers = collect(isset($data['offers'][0]) ? $data['offers'] : [$data['offers']]);
        $baggages = collect(isset($data['baggageList'][0]) ? $data['baggageList'] : [$data['baggageList']]);
        $priceClass = collect(isset($data['priceClass'][0]) ? $data['priceClass'] : [$data['priceClass']]);
        $passengers = collect(isset($data['passengers'][0]) ? $data['passengers'] : [$data['passengers']]);
        $serviceList = collect($data['serviceList']) ?? null;
        $responseId = $data['responseId'] ?? null;
        $transactionId = $data['transactionId'] ?? null;
        $request = $data['request'];
        // dd($destinations, $flights, $segments, $offers, $baggages, $priceClass, $responseId);
        $timeZone = config('variables.setting.timezone') ?? 'Asia/Karachi';
        $data = [];
        $tax = config('variables.flyjinnah_api.tax') ?? 0;
        $matchedOffers = '';
        foreach ($destinations as $item) {
            $flightIds = explode(' ', $item['FlightReferences']['value'] ?? ''); // fetch bundle with this Aliiiiiiiiiii);
            $matchedFlights = $flights->filter(fn($flight) => in_array($flight['@attributes']['FlightKey'] ?? null, $flightIds))->values();
            $flightSegmentReferences = $matchedFlights->map(fn($flight) => explode(' ', $flight['SegmentReferences']['value'] ?? ''))->values();
            // dd($flightSegmentReferences);
            $segmentDetails=[];
            foreach ($flightSegmentReferences as $segmentIds) {
                $flightSegments = collect($segmentIds)
                    ->map(fn($id) => $segments->firstWhere('@attributes.SegmentKey', $id))
                    ->filter()
                    ->values();

                $relatedFlightKeys = $matchedFlights
                    ->filter(fn($flight) => !array_diff($segmentIds, explode(' ', $flight['SegmentReferences']['value'] ?? '')))
                    ->pluck('@attributes.FlightKey')
                    ->all();
                // dd($relatedFlightKeys);

                $matchedOffers = $offers
                    ->filter(function ($offer) use ($relatedFlightKeys) {
                        return isset($offer['FlightsOverview']['FlightRef']['value'])
                            ? in_array($offer['FlightsOverview']['FlightRef']['value'], $relatedFlightKeys)
                            : true;
                    })
                    ->map(function ($offer) use ($baggages, $priceClass, $serviceList, $passengers) {
                        $offer['BaggageAllowance'] = collect($offer['BaggageAllowance'] ?? [])
                            ->map(fn($allowance) => $this->updateBaggageAllowance($allowance, $baggages))
                            ->all();
                        // dd($offer);
                        // $item = isset($offer['OfferItem'][0]) ? $offer['OfferItem'][0] : $offer['OfferItem'];
                        $priceClassRef = $offer['FlightsOverview']['FlightRef']['@attributes']['PriceClassRef'] ?? null;
                        $offer['priceClass'] = $priceClassRef ? $priceClass->where('@attributes.PriceClassID', $priceClassRef)->values()->first() : '';
                        $refs = $offer['BookingReferences']['BookingReference'] ?? [];
                        // dd($refs);

                        $bookingReferences = [
                            'bookingId' => ($refs[0]['OtherID']['value'] ?? '') . ' ' . ($refs[0]['ID']['value'] ?? ''),
                            'airlineID' => ($refs[1]['AirlineID']['value'] ?? '') . ' ' . ($refs[1]['ID']['value'] ?? ''),
                            'airline' => $refs[1]['AirlineID']['@attributes']['Name'] ?? '',
                        ];
                        $formattedItems = $this->formatOfferItems((isset($offer['OrderItems']) ? $offer['OrderItems'] : $offer['OfferItem']), $serviceList, $passengers);
                        return [
                            'offerID' => $offer['@attributes'] ?? null,
                            'bookingReferences' => !empty($bookingReferences) ? $bookingReferences : null,
                            'parameters' => $offer['Parameters'] ?? null,
                            // 'timeLimits' => $offer['TimeLimits'] ?? null,
                            'timeLimits' => isset($offer['TimeLimits']) 
                                ? $offer['TimeLimits'] : (!empty($formattedItems) && isset($formattedItems[0]['timeLimits']) ? $formattedItems[0]['timeLimits'] : null),
                            'totalPrice' =>  [
                                'code' => $offer['TotalPrice']['DetailCurrencyPrice']['Total']['@attributes']['Code'] ?? ($offer['TotalOrderPrice']['DetailCurrencyPrice']['Total']['@attributes']['Code'] ?? ''),
                                'amount' => $offer['TotalPrice']['DetailCurrencyPrice']['Total']['value'] ?? ($offer['TotalOrderPrice']['DetailCurrencyPrice']['Total']['value'] ?? ''),
                            ],
                            'offerItem' => $formattedItems,
                            'baggageAllowance' => $offer['BaggageAllowance'],
                            'priceClass' => $offer['priceClass'],
                        ];
                    })->values();
                $expTime = isset($matchedOffers->first()['timeLimits']['OfferExpiration']['@attributes']['DateTime']) ? Carbon::parse($matchedOffers->first()['timeLimits']['OfferExpiration']['@attributes']['DateTime'])->setTimezone($timeZone) : now()->addMinutes(20);
                session([
                    'IdsExpireTimeEmi' => $expTime,
                ]);
                // dd($matchedOffers);
                $lowestPrice = [
                    'code' => $matchedOffers->min(fn($offer) => data_get($offer, 'totalPrice.code', 'PKR')),
                    'amount' => $matchedOffers->min(fn($offer) => (float) data_get($offer, 'totalPrice.amount', 0)) + $tax
                ];
                if ($flightSegments->isNotEmpty()) {
                    $secondFlight = [];
                    if (count($flightSegments) > 1) {
                        $last = $flightSegments->last();
                        $secondFlight = [
                            'departure' => $last['Departure'] ?? [],
                            'arrival' => $last['Arrival'] ?? [],
                            'isConnected' => isset($last['@attributes']['ConnectInd']) ? filter_var($last['@attributes']['ConnectInd'], FILTER_VALIDATE_BOOLEAN) : null,
                            'details' => $last['FlightDetail'] ?? [],
                            'equipment' => $last['Equipment'] ?? [],
                            'marketingCarrier' => $last['MarketingCarrier'] ?? [],
                        ];
                    }
                    $first = $flightSegments->first();
                    $totalMinutes = 0;
                    try {
                        $d1 = new \DateInterval($first['FlightDetail']['FlightDuration']['Value']['value'] ?? 'PT0M');
                        $totalMinutes += ($d1->h * 60) + $d1->i;
                    } catch (\Exception $e) {
                        \Log::error('Exception in calculate duration', ['message' => $e->getMessage()]);
                    }

                    if (!empty($secondFlight) && isset($secondFlight['details']['FlightDuration']['Value']['value'])) {
                        try {
                            $d2 = new \DateInterval($secondFlight['details']['FlightDuration']['Value']['value']);
                            $totalMinutes += ($d2->h * 60) + $d2->i;
                        } catch (\Exception $e) {
                            \Log::error('Exception in calculate duration', ['message' => $e->getMessage()]);
                        }
                    }

                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    $duration = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
                    $segmentDetails[] = [
                        'Departure' => $first['Departure'] ?? [],
                        'Arrival' => $flightSegments->last()['Arrival'] ?? [],
                        'segmentKey' => $first['@attributes']['SegmentKey'] ?? [],
                        'flightDetails' => [
                            'isConnected' => isset($first['@attributes']['ConnectInd']) ? filter_var($first['@attributes']['ConnectInd'], FILTER_VALIDATE_BOOLEAN) : null,
                            'details' => $first['FlightDetail'] ?? [],
                            'equipment' => $first['Equipment'] ?? [],
                            'marketingCarrier' => $first['MarketingCarrier'] ?? [],
                        ],
                        'secondFlight' => $secondFlight,
                        'duration' => $duration ?? '',
                        'price' => $lowestPrice,
                        'bundles' => $request === 1 ? $matchedOffers->all() : [],
                    ];
                }
            }
            if($request === 2 || $request === 3) {
                $data['segments'][] = [
                    'departureCode' => $this->helperService->codeToCountry($item['DepartureCode']),
                    'arrivalCode' => $this->helperService->codeToCountry($item['ArrivalCode']),
                    'flights' => collect($segmentDetails)->first(),
                    'responseId' => $responseId,
                ];
            } else {
                $data[] = [
                    'departureCode' => $this->helperService->codeToCountry($item['DepartureCode']),
                    'arrivalCode' => $this->helperService->codeToCountry($item['ArrivalCode']),
                    'flights' => $segmentDetails,
                    'responseId' => $responseId,
                ];
            }
        }
        if($request === 2 || $request === 3) {
            $data['bundle'] = $matchedOffers->first();
            $expTime = isset($data['bundle']['timeLimits']['OfferExpiration']['@attributes']['DateTime']) ? Carbon::parse($data['bundle']['timeLimits']['OfferExpiration']['@attributes']['DateTime'])->setTimezone($timeZone) : now()->addMinutes(20);
            session([
                'IdsExpireTimeEmi' => $expTime,
            ]);
        }
        if($request === 3) {
            $data['passengers'] = [];
            foreach ($passengers as $pax) {
                $data['passengers'][] = [
                    'id' => $pax['@attributes']['PassengerID'] ?? '',
                    'type' => $pax['PTC']['value'] ?? '',
                    'birthdate' => $pax['Birthdate']['value'] ?? '',
                    'gender' => $pax['Individual']['Gender']['value'] ?? '',
                    'givenName' => $pax['Individual']['GivenName']['value'] ?? '',
                    'surname' => $pax['Individual']['Surname']['value'] ?? '',
                    'title' => $pax['Individual']['NameTitle']['value'] ?? '',
                    'contactRef' => $pax['ContactInfoRef']['value'] ?? null,
                    'infantRef' => $pax['InfantRef']['value'] ?? null,
                ];
            }
        }
        $data['transactionId'] = $transactionId;
        // dd($data);
        return $data;
    }
    // private function formatOfferItems($data, $serviceItems = null, $passengers)
    // {
    //     // dd($data);
    //     if (empty($data)) return [];
    //     $data = isset($data[0]) ? $data : [$data];
    //     $offers = [];
    //     foreach ($data as $item) {
    //         // dd($item);
    //         $offerItemID = $item['@attributes']['OfferItemID'] ?? '';
    //         $currency = $item['TotalPriceDetail']['TotalAmount']['DetailCurrencyPrice']['Total']['@attributes']['Code'] ?? '';
    //         $price = $item['TotalPriceDetail']['TotalAmount']['DetailCurrencyPrice']['Total']['value'] ?? '';
    //         $fareComponent = isset($item['FareDetail']['FareComponent'][0]) ? $item['FareDetail']['FareComponent'] : [$item['FareDetail']['FareComponent']];
    //         $refs = explode(' ', $item['FareDetail']['PassengerRefs']['value'] ?? '');
    //         $fareDetail = [
    //             'passengerRef' => $item['FareDetail']['PassengerRefs'] ?? '',
    //             'passengers' => collect($refs)
    //                 ->map(fn($ref) => collect($passengers)->firstWhere('@attributes.PassengerID', $ref)['PTC']['value'] ?? null)
    //                 ->filter()->countBy()
    //                 ->map(fn($count, $type) => "$type x $count")
    //                 ->implode(', ') ?: 'N/A',
    //             'baseAmount' => [
    //                 'code' => $item['FareDetail']['Price']['BaseAmount']['@attributes']['Code'] ?? '',
    //                 'amount' => $item['FareDetail']['Price']['BaseAmount']['value'] ?? '',
    //             ],
    //             'taxes' => [
    //                 'total' => [
    //                     'code' => $item['FareDetail']['Price']['Taxes']['Total']['@attributes']['Code'] ?? '',
    //                     'amount' => $item['FareDetail']['Price']['Taxes']['Total']['value'] ?? '',
    //                 ],
    //                 'tax' => collect($item['FareDetail']['Price']['Taxes']['Breakdown']['Tax'])->map(function ($tax) {
    //                     return [
    //                         'price' => [
    //                             'code' => $tax['Amount']['@attributes']['Code'] ?? '',
    //                             'amount' => $tax['Amount']['value'] ?? '',
    //                         ],
    //                         'taxCode' => $tax['TaxCode']['value'] ?? '',
    //                         'description' => $tax['Description']['value'] ?? '',
    //                     ];
    //                 })->values()->all()
    //             ],
    //             'penalties' => collect($fareComponent)->map(function ($fare) {
    //                 return [
    //                     'arrival' => $fare['SegmentRefs']['@attributes']['ON_Point'] ?? '',
    //                     'destination' => $fare['SegmentRefs']['@attributes']['OFF_Point'] ?? '',
    //                     'cabinType' => $fare['FareBasis']['CabinType']['CabinTypeName']['value'] ?? '',
    //                     'fareRules' => $fare['FareRules']['Penalty']['@attributes'] ?? '',
    //                 ];
    //             })->values()->all()
    //         ];
    //         $services = isset($item['Service'][0]) ? $item['Service'] : [$item['Service']];
    //         $formattedServices = null;
    //         // if (!empty($serviceItems) && $serviceItems->filter()->isNotEmpty()) {
    //             $formattedServices = collect($services)->map(function ($service) use ($serviceItems) {
    //                 $serviceID = $service['@attributes']['ServiceID'] ?? '';
    //                 $passengerRefs = $service['PassengerRefs']['value'] ?? '';
    //                 if (!isset($service['ServiceDefinitionRef'])) {
    //                     return [
    //                         'id' => $serviceID,
    //                         'passengerRefs' => $passengerRefs,
    //                         'details' => null,
    //                     ];
    //                 }
    //                 $definitionID = $service['ServiceDefinitionRef']['value'] ?? null;
    //                 $details = null;
    //                 if ($definitionID) {
    //                     $matched = $serviceItems->firstWhere('@attributes.ServiceDefinitionID', $definitionID);
    //                     if ($matched) {
    //                         $descriptionsRaw = $matched['Descriptions']['Description'] ?? [];
    //                         $descriptions = isset($descriptionsRaw[0]) ? $descriptionsRaw : [$descriptionsRaw];
    //                         $descriptionsCollection = collect($descriptions);
    //                         $typeEntry = $descriptionsCollection->firstWhere('Application.value', 'Type');
    //                         $name = $typeEntry['Text']['value'] ?? '';
    //                         $application = $typeEntry['Application']['value'] ?? '';
    //                         $detailsList = $descriptionsCollection
    //                             ->filter(fn($desc) => ($desc['Application']['value'] ?? '') === 'Details')
    //                             ->pluck('Text.value')->filter()->values()->first();
    //                         $details = [
    //                             $application => $name,
    //                             'details' => $detailsList,
    //                         ];
    //                     }
    //                 }
    //                 return [
    //                     'id' => $serviceID,
    //                     'passengerRefs' => $passengerRefs,
    //                     'details' => $details,
    //                 ];
    //             })->values()->all();
    //         // } else {
    //         //     $formattedServices = [
    //         //         'id' => $service['@attributes']['ServiceID'] ?? '',
    //         //         'passengerRefs' => $passengerRefs,
    //         //         'details' => null,
    //         //     ];
    //         //     dd('ggs');
    //         // }
    //         $offers[] = [
    //             'id' => $offerItemID,
    //             'totalPrice' => ['code' => $currency, 'amount' => $price],
    //             'services' => $formattedServices ?? $services ?? '',
    //             'fareDetail' => $fareDetail ?? '',
    //         ];
    //     }
    //     // dd($offers);
    //     return $offers;
    // }
    private function formatOfferItems($data, $serviceItems = null, $passengers)
    {
        $data = isset($data['OrderItem']) ? $data['OrderItem'] : $data;
        // dd($data);
        if (empty($data)) return [];
        $data = isset($data[0]) ? $data : [$data];
        $offers = [];
        foreach ($data as $item) {
            // $item = isset($item['OrderItem']) ? $item['OrderItem'] : $item;
            // dd($item);
            $offerItemID = $item['@attributes']['OfferItemID'] ?? ($item['@attributes']['OrderItemID'] ?? '');
            $priceTag = $item['TotalPriceDetail'] ?? ($item['PriceDetail'] ?? '');
            // dd($item, $offerItemID);
            $currency = $priceTag['TotalAmount']['DetailCurrencyPrice']['Total']['@attributes']['Code'] ?? '';
            $price = $priceTag['TotalAmount']['DetailCurrencyPrice']['Total']['value'] ?? '';
            $fareComponent = isset($item['FareDetail']['FareComponent'][0]) ? $item['FareDetail']['FareComponent'] : [$item['FareDetail']['FareComponent']];
            $refs = explode(' ', $item['FareDetail']['PassengerRefs']['value'] ?? '');
            // dd($refs);
            // dd($currency, $price, $fareComponent, $refs);
            $fareDetail = [
                'passengerRef' => $item['FareDetail']['PassengerRefs'] ?? '',
                'passengers' => collect($refs)
                    ->map(fn($ref) => collect($passengers)->firstWhere('@attributes.PassengerID', $ref)['PTC']['value'] ?? null)
                    ->filter()->countBy()
                    ->map(fn($count, $type) => "$type x $count")
                    ->implode(', ') ?: 'N/A',
                'taxes' => [
                    'baseAmount' => [
                        'code' => $item['FareDetail']['Price']['BaseAmount']['@attributes']['Code'] ?? '',
                        'amount' => $item['FareDetail']['Price']['BaseAmount']['value'] ?? '',
                    ],
                    'tax' => collect($item['FareDetail']['Price']['Taxes']['Breakdown']['Tax'])->map(function ($tax) {
                        return [
                            'price' => [
                                'code' => $tax['Amount']['@attributes']['Code'] ?? '',
                                'amount' => $tax['Amount']['value'] ?? '',
                            ],
                            'taxCode' => $tax['TaxCode']['value'] ?? '',
                            'description' => $tax['Description']['value'] ?? '',
                        ];
                    })->values()->all(),
                    'total' => [
                        'code' => $item['FareDetail']['Price']['Taxes']['Total']['@attributes']['Code'] ?? '',
                        'amount' => $item['FareDetail']['Price']['Taxes']['Total']['value'] ?? '',
                    ]
                ],
                'penalties' => collect($fareComponent)->map(function ($fare) {
                    return [
                        'arrival' => $fare['SegmentRefs']['@attributes']['ON_Point'] ?? '',
                        'destination' => $fare['SegmentRefs']['@attributes']['OFF_Point'] ?? '',
                        'cabinType' => $fare['FareBasis']['CabinType']['CabinTypeName']['value'] ?? '',
                        'fareRules' => $fare['FareRules']['Penalty']['@attributes'] ?? '',
                    ];
                })->values()->all()
            ];
            $services = isset($item['Service'][0]) ? $item['Service'] : [$item['Service']];
            $formattedServices = null;
            // if (!empty($serviceItems) && $serviceItems->filter()->isNotEmpty()) {
                $formattedServices = collect($services)->map(function ($service) use ($serviceItems) {
                    $serviceID = $service['@attributes']['ServiceID'] ?? '';
                    $passengerRefs = $service['PassengerRefs']['value'] ?? $service['PassengerRef']['value'] ?? '';
                    if (!isset($service['ServiceDefinitionRef'])) {
                        return [
                            'id' => $serviceID,
                            'passengerRefs' => $passengerRefs,
                            'details' => null,
                        ];
                    }
                    $definitionID = $service['ServiceDefinitionRef']['value'] ?? null;
                    $details = [];
                    if ($definitionID) {
                        $matched = $serviceItems->firstWhere('@attributes.ServiceDefinitionID', $definitionID);
                        if ($matched) {
                            $descriptionsRaw = $matched['Descriptions']['Description'] ?? [];
                            $descriptions = isset($descriptionsRaw[0]) ? $descriptionsRaw : [$descriptionsRaw];
                            $descriptionsCollection = collect($descriptions);
                            $typeEntry = $descriptionsCollection->firstWhere('Application.value', 'Type');
                            $name = $typeEntry['Text']['value'] ?? '';
                            $application = $typeEntry['Application']['value'] ?? '';
                            $detailsList = $descriptionsCollection
                                ->filter(fn($desc) => ($desc['Application']['value'] ?? '') === 'Details')
                                ->pluck('Text.value')->filter()->values()->first();
                            if ($application && $name) {
                                $details[$application] = $name;
                            }
                
                            if ($detailsList) {
                                $details['details'] = $detailsList;
                            }
                            
                        }
                    }
                    if (empty($details)) {
                        return null;
                    }
                
                    return [
                        'id' => $serviceID,
                        'passengerRefs' => $passengerRefs,
                        'details' => $details,
                    ];
                })->filter()->values()->all();
            // } else {
            //     $formattedServices = [
            //         'id' => $service['@attributes']['ServiceID'] ?? '',
            //         'passengerRefs' => $passengerRefs,
            //         'details' => null,
            //     ];
            //     dd('ggs');
            // }
            $offer = [
                'id' => $offerItemID,
                'totalPrice' => ['code' => $currency, 'amount' => $price],
                'services' => $formattedServices ?? ($services ?? ''),
                'fareDetail' => $fareDetail ?? '',
            ];
            if (isset($item['TimeLimits'])) {
                $offer['timeLimits'] = [
                    'paymentTimeLimit' => $item['TimeLimits']['PaymentTimeLimit']['@attributes']['Timestamp'] ?? null,
                    'ticketingTimeLimit' => $item['TimeLimits']['TicketingTimeLimits']['@attributes']['Timestamp'] ?? null
                ];
            }
            $offers[] = $offer;
        }
        // dd($offers);
        return $offers;
    }
    private function updateBaggageAllowance($allowance, $baggages)
    {
        $refId = $allowance['BaggageAllowanceRef']['value'] ?? null;
        if ($refId) {
            $baggageDetail = $baggages->firstWhere('@attributes.BaggageAllowanceID', $refId);
            if ($baggageDetail) {
                $allowance['baggage_detail'] = $baggageDetail;
            }
        }
        return $allowance;
    }
    private function formatOfferTag($data, $offerId, $responseId)
    {
        // dd($data, $offerId, $responseId);
        $resId = $responseId ?? session('responseId') ?? '';
        $offer = '<Offer OfferID="' . $offerId['OfferID'] . '" Owner="' . $offerId['Owner'] . '" ResponseID="' . $resId . '">';
        foreach ($data as $item) {
            $offer .= '<OfferItem OfferItemID="' . $item['id'] . '">
                            <PassengerRefs>' . $item['PassengerRef'] . '</PassengerRefs>
                    </OfferItem>';
        }
        $offer .= '</Offer>';
        return $offer;
    }
    private function getPaxContactTag(array $paxCount, array $passengers): string
    {
        $xml = "<PassengerList>";
        $adtIds = [];
        $paxId = 1;
        $infantIndex = 0;

        foreach ($passengers as $pax) {
            $type = strtolower($pax['type']);
            $gender = ($pax['title'] === 'Mr') ? 'Male' : 'Female';
            $title = strtoupper($pax['title']);
            $name = strtoupper(preg_replace("/[^a-zA-Z]/", '', $pax['name']));
            $surname = strtoupper(preg_replace("/[^a-zA-Z]/", '', $pax['surname']));
            $dob = $pax['dob'];
            $country = $pax['nationality'] ?? 'PK';
            $ref = 'CID1';
    
            if ($type === 'adult') {
                $id = "T$paxId";
                $adtIds[] = $id;
    
                $xml .= "<Passenger PassengerID=\"$id\">
                            <PTC>ADT</PTC>
                            <ResidenceCountryCode>$country</ResidenceCountryCode>
                            <Individual>
                                <Birthdate>$dob</Birthdate>
                                <Gender>$gender</Gender>
                                <NameTitle>$title</NameTitle>
                                <GivenName>$name</GivenName>
                                <Surname>$surname</Surname>
                            </Individual>
                            <ContactInfoRef>$ref</ContactInfoRef>";
    
                if ($infantIndex < ($paxCount['inf'] ?? 0)) {
                    $xml .= "<InfantRef>{$id}.1</InfantRef>";
                    $infantIndex++;
                }
    
                $xml .= "</Passenger>";
                $paxId++;
            }
    
            elseif ($type === 'child') {
                $id = "T$paxId";
                $xml .= "<Passenger PassengerID=\"$id\">
                            <PTC>CNN</PTC>
                            <ResidenceCountryCode>$country</ResidenceCountryCode>
                            <Individual>
                                <Birthdate>$dob</Birthdate>
                                <Gender>$gender</Gender>
                                <NameTitle>$title</NameTitle>
                                <GivenName>$name</GivenName>
                                <Surname>$surname</Surname>
                            </Individual>
                            <ContactInfoRef>$ref</ContactInfoRef>
                        </Passenger>";
                $paxId++;
            }
        }
    
        $infantIndex = 0;
        foreach ($passengers as $pax) {
            $type = strtolower($pax['type']);
            if ($type !== 'infant') continue;
    
            if (!isset($adtIds[$infantIndex])) {
                continue;
            }
    
            $gender = ($pax['title'] === 'Mr') ? 'Male' : 'Female';
            $title = strtoupper($pax['title']);
            $name = strtoupper(preg_replace("/[^a-zA-Z]/", '', $pax['name']));
            $surname = strtoupper(preg_replace("/[^a-zA-Z]/", '', $pax['surname']));
            $dob = $pax['dob'];
            $country = $pax['nationality'] ?? 'PK';
            $ref = 'CID1';
    
            $id = $adtIds[$infantIndex] . '.1';
            $xml .= "<Passenger PassengerID=\"$id\">
                        <PTC>INF</PTC>
                        <ResidenceCountryCode>$country</ResidenceCountryCode>
                        <Individual>
                            <Birthdate>$dob</Birthdate>
                            <Gender>$gender</Gender>
                            <NameTitle>$title</NameTitle>
                            <GivenName>$name</GivenName>
                            <Surname>$surname</Surname>
                        </Individual>
                        <ContactInfoRef>$ref</ContactInfoRef>
                    </Passenger>";
            $infantIndex++;
        }
    
        return $xml . "</PassengerList>";
    }
    private function formatBookFlight($data)
    {
        // dd($data);
        if (empty($data) || empty($data['destinationList']) || empty($data['flights']) || empty($data['flightSegments']) || empty($data['offers']) || empty($data['baggageList']) || empty($data['priceClass'])) return 'Something missing';

        $destinations = isset($data['destinationList'][0]) ? $data['destinationList'] : [$data['destinationList']];
        $flights = collect(isset($data['flights'][0]) ? $data['flights'] : [$data['flights']]);
        $segments = collect(isset($data['flightSegments'][0]) ? $data['flightSegments'] : [$data['flightSegments']]);
        $offers = collect(isset($data['offers'][0]) ? $data['offers'] : [$data['offers']]);
        $baggages = collect(isset($data['baggageList'][0]) ? $data['baggageList'] : [$data['baggageList']]);
        $priceClass = collect(isset($data['priceClass'][0]) ? $data['priceClass'] : [$data['priceClass']]);
        $passengers = collect(isset($data['passengers'][0]) ? $data['passengers'] : [$data['passengers']]);
        // dd($passengers);
        $serviceList = collect($data['serviceList']) ?? null;
        $responseId = $data['responseId'] ?? null;
        $request = $data['request'];
        // dd($destinations, $flights, $segments, $offers, $baggages, $priceClass, $responseId);
        $timeZone = config('variables.setting.timezone') ?? 'Asia/Karachi';
        $data = [];
        $tax = config('variables.flyjinnah_api.tax') ?? 0;
        $matchedOffers = '';
        foreach ($destinations as $item) {
            $flightIds = explode(' ', $item['FlightReferences']['value'] ?? ''); // fetch bundle with this Aliiiiiiiiiii);
            $matchedFlights = $flights->filter(fn($flight) => in_array($flight['@attributes']['FlightKey'] ?? null, $flightIds))->values();
            $flightSegmentReferences = $matchedFlights->map(fn($flight) => explode(' ', $flight['SegmentReferences']['value'] ?? ''))->values();
            // dd($flightSegmentReferences);
            $segmentDetails=[];
            foreach ($flightSegmentReferences as $segmentIds) {
                $flightSegments = collect($segmentIds)
                    ->map(fn($id) => $segments->firstWhere('@attributes.SegmentKey', $id))
                    ->filter()
                    ->values();

                $relatedFlightKeys = $matchedFlights
                    ->filter(fn($flight) => !array_diff($segmentIds, explode(' ', $flight['SegmentReferences']['value'] ?? '')))
                    ->pluck('@attributes.FlightKey')
                    ->all();
                // dd($relatedFlightKeys);

                $matchedOffers = $offers
                    ->filter(function ($offer) use ($relatedFlightKeys) {
                        return isset($offer['FlightsOverview']['FlightRef']['value'])
                            ? in_array($offer['FlightsOverview']['FlightRef']['value'], $relatedFlightKeys)
                            : true;
                    })
                    ->map(function ($offer) use ($baggages, $priceClass, $serviceList, $passengers) {
                        $offer['BaggageAllowance'] = collect($offer['BaggageAllowance'] ?? [])
                            ->map(fn($allowance) => $this->updateBaggageAllowance($allowance, $baggages))
                            ->all();
                        // dd($offer);
                        // $item = isset($offer['OfferItem'][0]) ? $offer['OfferItem'][0] : $offer['OfferItem'];
                        $priceClassRef = $offer['FlightsOverview']['FlightRef']['@attributes']['PriceClassRef'] ?? null;
                        $offer['priceClass'] = $priceClassRef ? $priceClass->where('@attributes.PriceClassID', $priceClassRef)->values()->first() : '';
                        return [
                            'offerID' => $offer['@attributes'] ?? null,
                            'parameters' => $offer['Parameters'] ?? null,
                            'timeLimits' => $offer['TimeLimits'] ?? null,
                            'totalPrice' =>  [
                                'code' => $offer['TotalPrice']['DetailCurrencyPrice']['Total']['@attributes']['Code'] ?? '',
                                'amount' => $offer['TotalPrice']['DetailCurrencyPrice']['Total']['value'] ?? '',
                            ],
                            'offerItem' => $this->formatOfferItems2($offer['OrderItems'], $serviceList, $passengers),
                            'baggageAllowance' => $offer['BaggageAllowance'],
                            'priceClass' => $offer['priceClass'],
                        ];
                    })->values();
                $expTime = isset($matchedOffers->first()['timeLimits']['OfferExpiration']['@attributes']['DateTime']) ? Carbon::parse($matchedOffers->first()['timeLimits']['OfferExpiration']['@attributes']['DateTime'])->setTimezone($timeZone) : now()->addMinutes(20);
                session([
                    'IdsExpireTimeEmi' => $expTime,
                ]);
                // dd($matchedOffers);
                $lowestPrice = [
                    'code' => $matchedOffers->min(fn($offer) => data_get($offer, 'totalPrice.code', 'PKR')),
                    'amount' => $matchedOffers->min(fn($offer) => (float) data_get($offer, 'totalPrice.amount', 0)) + $tax
                ];
                if ($flightSegments->isNotEmpty()) {
                    $secondFlight = [];
                    if (count($flightSegments) > 1) {
                        $last = $flightSegments->last();
                        $secondFlight = [
                            'departure' => $last['Departure'] ?? [],
                            'arrival' => $last['Arrival'] ?? [],
                            'isConnected' => isset($last['@attributes']['ConnectInd']) ? filter_var($last['@attributes']['ConnectInd'], FILTER_VALIDATE_BOOLEAN) : null,
                            'details' => $last['FlightDetail'] ?? [],
                            'equipment' => $last['Equipment'] ?? [],
                            'marketingCarrier' => $last['MarketingCarrier'] ?? [],
                        ];
                    }
                    $first = $flightSegments->first();
                    $totalMinutes = 0;
                    try {
                        $d1 = new \DateInterval($first['FlightDetail']['FlightDuration']['Value']['value'] ?? 'PT0M');
                        $totalMinutes += ($d1->h * 60) + $d1->i;
                    } catch (\Exception $e) {
                        \Log::error('Exception in calculate duration', ['message' => $e->getMessage()]);
                    }

                    if (!empty($secondFlight) && isset($secondFlight['details']['FlightDuration']['Value']['value'])) {
                        try {
                            $d2 = new \DateInterval($secondFlight['details']['FlightDuration']['Value']['value']);
                            $totalMinutes += ($d2->h * 60) + $d2->i;
                        } catch (\Exception $e) {
                            \Log::error('Exception in calculate duration', ['message' => $e->getMessage()]);
                        }
                    }

                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;
                    $duration = ($hours > 0 ? $hours . 'h ' : '') . ($minutes > 0 ? $minutes . 'm' : '');
                    $segmentDetails[] = [
                        'Departure' => $first['Departure'] ?? [],
                        'Arrival' => $flightSegments->last()['Arrival'] ?? [],
                        'segmentKey' => $first['@attributes']['SegmentKey'] ?? [],
                        'flightDetails' => [
                            'isConnected' => filter_var($first['@attributes']['ConnectInd'], FILTER_VALIDATE_BOOLEAN),
                            'details' => $first['FlightDetail'] ?? [],
                            'equipment' => $first['Equipment'] ?? [],
                            'marketingCarrier' => $first['MarketingCarrier'] ?? [],
                        ],
                        'secondFlight' => $secondFlight,
                        'duration' => $duration ?? '',
                        'price' => $lowestPrice,
                        'bundles' => $request === 1 ? $matchedOffers->all() : [],
                    ];
                }
            }
            if($request === 2 || $request === 3) {
                $data['segments'][] = [
                    'departureCode' => $this->helperService->codeToCountry($item['DepartureCode']),
                    'arrivalCode' => $this->helperService->codeToCountry($item['ArrivalCode']),
                    'flights' => collect($segmentDetails)->first(),
                    'responseId' => $responseId,
                ];
            } else {
                $data[] = [
                    'departureCode' => $this->helperService->codeToCountry($item['DepartureCode']),
                    'arrivalCode' => $this->helperService->codeToCountry($item['ArrivalCode']),
                    'flights' => $segmentDetails,
                    'responseId' => $responseId,
                ];
            }
        }
        if($request === 2 || $request === 3) {
            $data['bundle'] = $matchedOffers->first();
            $expTime = isset($data['bundle']['timeLimits']['OfferExpiration']['@attributes']['DateTime']) ? Carbon::parse($data['bundle']['timeLimits']['OfferExpiration']['@attributes']['DateTime'])->setTimezone($timeZone) : now()->addMinutes(20);
            session([
                'IdsExpireTimeEmi' => $expTime,
            ]);
        }
        if($request === 3) {
            $data['passengers'] = [];
            foreach ($passengers as $pax) {
                $data['passengers'][] = [
                    'id' => $pax['@attributes']['PassengerID'] ?? '',
                    'type' => $pax['PTC']['value'] ?? '',
                    'birthdate' => $pax['Birthdate']['value'] ?? '',
                    'gender' => $pax['Individual']['Gender']['value'] ?? '',
                    'givenName' => $pax['Individual']['GivenName']['value'] ?? '',
                    'surname' => $pax['Individual']['Surname']['value'] ?? '',
                    'title' => $pax['Individual']['NameTitle']['value'] ?? '',
                    'contactRef' => $pax['ContactInfoRef']['value'] ?? null,
                    'infantRef' => $pax['InfantRef']['value'] ?? null,
                ];
            }
        }
        // dd($data);
        return $data;
    }
}

// destinationList > flights > flightSegments