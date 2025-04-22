<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\HelperService;
use GuzzleHttp\Cookie\CookieJar;

class FlyJinnahService
{
    protected $agencyName;
    protected $searchUrl;
    protected $username;
    protected $password;
    protected $authenticate;
    protected $helperService;
    protected $flight_details;
    protected $agentCode;
    protected $tempToken;

    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
        $this->agencyName = config('services.agency.name');
        $this->authenticate = config('services.flyjinnah_api.authenticate');
        $this->searchUrl = config('services.flyjinnah_api.search');
        $this->flight_details = config('services.flyjinnah_api.flight_details');

        $this->testUsername = 'TESTS9P';
        $this->testPassword = 'P@ss1234';
        $this->username = config('services.flyjinnah_api.username');
        $this->password = config('services.flyjinnah_api.password');
        $this->agentCode = config('services.flyjinnah_api.agent_code');
        $this->XMLHeader = '
            <soap:Header>
                <wsse:Security soap:mustUnderstand="1"
                    xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <wsse:UsernameToken wsu:Id="UsernameToken-26506823"
                        xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">
                        <wsse:Username>'.$this->testUsername.'</wsse:Username>
                        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->testPassword.'</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soap:Header>
        ';
        $this->tempToken = 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJBQllURVNUUzlQIiwiaXAiOiIxNzguMTI4LjIyMC4wIiwiaWQiOiJkMmVhMTliMy05NmVkLTQ2NzItOGIwMC1lM2NhZGZjMWViOGEiLCJmbiI6IkRlc3RpbmF0aW9ucyBUcmF2ZWwiLCJsbiI6IlRvdXJzIiwib2MiOiJBQUJLSEk4MjY0Iiwic3QiOiIiLCJwcml2aWxlZ2VzIjpbIkxBQUFBQUkiXSwiaWF0IjoxNzQ1MjE4NzIxLCJleHAiOjE3NDUzMDUxMjF9.7qYpP3j-qyHREXMT1qT5KOn0lnKegaJ5-zH4O9U-cV0';
    }
    public function authenticate()
    {
        if(!$this->username){
            dd('env error run config cache cmd :)', $this->username);
        }
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false,
            ])->post($this->authenticate, [
                'login' => $this->username,
                'password' => $this->password,
            ]);
            // dd($response->body());
            if (!$response->successful()) {
                \Log::error('Authentication Failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                // dd('okok');
                return 'Authentication Failed!';
            }

            $data = $response->json();
            $token = $data['tokenPair']['accessToken'] ?? null;

            if (!$token) {
                \Log::error('No token received from API.');
                return 'No token received from API.';
            }

            $decoded = $this->helperService->decodeJWTToken($token);
            $expTime = $decoded['exp'] ?? null;

            if ($expTime) {
                $expiresInSeconds = $expTime - time();
                Cache::put('flyjinnah_token', $token, $expiresInSeconds);
            }

            return $token;

        } catch (\Exception $e) {
            \Log::error('Error in authentication request', ['message' => $e->getMessage()]);
            return null;
        }
    }
    private function getToken()
    {
        $cachedToken = Cache::get('flyjinnah_token');
        if ($cachedToken) {
            $decoded = $this->helperService->decodeJWTToken($cachedToken);
            $expTime = $decoded['exp'] ?? null;

            if ($expTime && $expTime - time() > 300) {
                return $cachedToken;
            }
        }

        return $this->authenticate();
    }
    public function searchFlights($data)
    {
        $origin = $data['arr'];
        $destination = $data['dest'];
        $departureDate = $data['dep'];
        $returnDate = $data['return'];
        $cabinClass = $data['cabinClass'] ?? 'Y';
        $adt = $data['adt'];
        $chd = $data['chd'] ?? null;
        $inf = $data['inf'] ?? null;
        // dd($origin, $destination, $departureDate, $cabinClass, $returnDate, $adt, $chd, $inf);
        session([
            'JSESSIONID' => null,
            'TransactionIdentifier' => null,
            'IdsExpireTime' => null,
        ]);
        $username = $this->username;
        $agentCode = $this->agentCode;
        $token = $this->getToken();
        // $token = $this->tempToken;
        // dd($username, $token);
        if (!$token) {
            \Log::error('Authentication failed while searching flights.');
            return ['error' => 'Authentication failed.'];
        }

        $headers = [
            'X-AERO-SALES-CHANNEL' => "OTA",
            'X-AERO-JOURNEY-TYPE' => $returnDate ? "RETURN" : "ONEWAY",
            'X-AERO-USERID' => "$username",
            'X-AERO-AGENT-CODE' => "$agentCode",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$token}",
        ];
        // dd($headers);
        $payload = [
            "searchOnds" => [
                [
                    "origin" => ["code" => $origin, "locationType" => "AIRPORT"],
                    "destination" => ["code" => $destination, "locationType" => "AIRPORT"],
                    "searchStartDate" => $departureDate,
                    "searchEndDate" => $departureDate,
                    "preferredDate" => $departureDate,
                    "bookingType" => "NORMAL",
                    "cabinClass" => "Y",
                    "ondRef" => "{$origin}/{$destination}",
                    "interlineQuoteDetails" => null
                ]
            ],
            "paxCounts" => [
                ["count" => $adt ?? 1, "paxType" => "ADT"],
                ["count" => $chd, "paxType" => "CHD"],
                ["count" => $inf, "paxType" => "INF"]
            ],
            "isReturn" => $returnDate ? true : false,
            "currencyCode" => "PKR",
            "cabinClass" => "Y",
            "metaData" => [
                "agentCode" => "RBGALY10",
                "country" => "PK",
                "station" => "KHI",
                "salesChannel" => "TravelAgent",
                "otherMetaData" => [
                    ["metaDataKey" => "FLIGHT_CUTOVER_TIME", "metaDataValue" => date('Y-m-d\TH:i:s')],
                    ["metaDataKey" => "SKIP_OND_MERGE", "metaDataValue" => "true"]
                ]
            ]
        ];
        if ($returnDate) {
            $payload["searchOnds"][] = [
                "origin" => ["code" => $destination, "locationType" => "AIRPORT"],
                "destination" => ["code" => $origin, "locationType" => "AIRPORT"],
                "searchStartDate" => $returnDate,
                "searchEndDate" => $returnDate,
                "preferredDate" => $returnDate,
                "bookingType" => "NORMAL",
                "cabinClass" => "Y",
                "ondRef" => "{$destination}/{$origin}",
                "interlineQuoteDetails" => null
            ];
        };
        // dd($payload);
        try {
            $cookieJar = new CookieJar();
            $response = Http::withHeaders($headers)
                ->withOptions([
                    'verify' => false,
                    'cookies' => $cookieJar
                    ])
                ->post($this->searchUrl, $payload);
            // dd($response->json(), $this->searchUrl, $payload, $headers);
            if (!$response->successful()) {
                \Log::error('Flight search failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight search failed.', 'details' => $response->json()];
            }

            return $response->json();

        } catch (\Exception $e) {
            \Log::error('Exception in flight search', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while searching flights.'];
        }
    }
    public function getFlightDetails($data) // getBundles
    {
        $paxCount = $data['paxCount'];
        $firstFlightData = $data['firstFlight'];
        $returnFlightData = $data['returnFlight'] ?? null;
        $connectedFlightData = $data['firstConnectedFlight'] ?? null;
        $returnConnectedFlightData = $data['returnConnectedFlight'] ?? null;

        // dd($paxCount, $firstFlightData, $returnFlightData, $connectedFlightData, $returnConnectedFlightData);
        $adt = $paxCount['adt'] ?? 1;
        $chd = $paxCount['chd'] ?? 0;
        $inf = $paxCount['inf'] ?? 0;
        $directionInd = $returnFlightData ? 'Return' : 'OneWay';

        $flightSegmentsXml = '';

        if ($firstFlightData) {
            $flightSegmentsXml .= $this->addFlightSegments(1, [$firstFlightData, $connectedFlightData]);
        }
        if ($returnFlightData) {
            $flightSegmentsXml .= $this->addFlightSegments(1, [$returnFlightData, $returnConnectedFlightData]);
        }
        

        $soapUrl = $this->flight_details;

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            '.$this->XMLHeader.'
            <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
                <ns1:OTA_AirPriceRQ EchoToken="12662148060105253838426" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" Version="20061.00">
                    <ns1:POS>
                        <ns1:Source TerminalID="TestUser/Test Runner">
                            <ns1:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns1:BookingChannel Type="12"/>
                        </ns1:Source>
                    </ns1:POS>
                    <ns1:AirItinerary DirectionInd="'.$directionInd.'">
                        <ns1:OriginDestinationOptions>
                            ' . $flightSegmentsXml . '
                        </ns1:OriginDestinationOptions>
                    </ns1:AirItinerary>
                    <ns1:TravelerInfoSummary>
                        <ns1:AirTravelerAvail>
                            <ns1:PassengerTypeQuantity Code="ADT" Quantity="'.$adt.'"/>
                            <ns1:PassengerTypeQuantity Code="CHD" Quantity="'.$chd.'"/>
                            <ns1:PassengerTypeQuantity Code="INF" Quantity="'.$inf.'"/>
                        </ns1:AirTravelerAvail>
                        <ns1:SpecialReqDetails>
                            <ns1:SSRRequests/>
                        </ns1:SpecialReqDetails>
                    </ns1:TravelerInfoSummary>
                </ns1:OTA_AirPriceRQ>
            </soap:Body>
        </soap:Envelope>';
        // dd($xmlBody);
        try {
            $cookieJar = new CookieJar();
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);

            // \Log::info('SOAP XML Request:', ['xml' => $xmlBody]);

            if (!$response->successful()) {
                \Log::error('Flight details request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight details request failed.', 'details' => $response->body()];
            }

            $setCookieHeader = $response->header('Set-Cookie');

            if (preg_match('/JSESSIONID=([^;]+)/', $setCookieHeader, $matches)) {
                $jsessionId = $matches[0];
            } else {
                $jsessionId = null;
            }
            $arrayResponse = $this->helperService->XMLtoJSON($response->body());
            $errorResponse = $arrayResponse['Body']['OTA_AirPriceRS']['Errors'];
            // return $arrayResponse;
            if ($errorResponse){
                \Log::error('Flight get price request failed', [
                    'status' => $errorResponse['Error']['@attributes']['code'] ?? 500,
                    'response' => $errorResponse['Error']['@attributes'] ?? ''
                ]);
                // dd($errorResponse['Error']['@attributes']);
                return ['error' => 'Flight get price request failed.', 'details' => $errorResponse['Error']['@attributes']];
            }
            // dd($arrayResponse);
            session([
                'JSESSIONID' => $jsessionId,
                'TransactionIdentifier' => $arrayResponse['Body']['OTA_AirPriceRS']['@attributes']['TransactionIdentifier'] ?? null,
                'IdsExpireTime' => now(),
            ]);
            return([
                'originDestinationOptions' => $arrayResponse['Body']['OTA_AirPriceRS']['PricedItineraries']['PricedItinerary']['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'] ?? 'Not found',
                'bundles' => $arrayResponse['Body']['OTA_AirPriceRS']['PricedItineraries']['PricedItinerary']['AirItinerary']['OriginDestinationOptions']['AABundledServiceExt'] ?? 'Not found',
                'prices' => $arrayResponse['Body']['OTA_AirPriceRS']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo'] ?? 'Not found',
                'error' => null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in fetching flight details', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while fetching flight details.'];
        }
    }
    public function getBundlePrice($data)
    {
        $soapUrl = $this->flight_details;
        $paxCount = $data['data']['paxCount'];
        $segments = $data['data']['segments'];
        // $returnFlight = $data['data']['returnFlight'];
        $firstFlightBundleId = $data['data']['firstFlightBundleId'];
        $returnFlightBundleId = $data['data']['returnFlightBundleId'];
        $returnFlight = $data['data']['returnFlight']['flightSegments'] ?? null;
        $departureFlight = $data['data']['departureFlight']['flightSegments'];
        $cookieJar = new CookieJar();
        $transactionIdentifier = session('TransactionIdentifier');
        if (!$transactionIdentifier) return ['error' => 'Transaction identifier not provided'];
        $jsessionId = session('JSESSIONID');
        if (!$jsessionId) return ['error' => 'Jsession Id not provided'];
        $flightSegmentsXml = '';
        if ($departureFlight) {
            $flightSegmentsXml .= $this->addFlightSegments(1, $departureFlight);
        }
        if ($returnFlight) {
            $flightSegmentsXml .= $this->addFlightSegments(1, $returnFlight);
        }
        $adt = $paxCount['adt'] ?? 1;
        $chd = $paxCount['chd'] ?? 0;
        $inf = $paxCount['inf'] ?? 0;

        $directionInd = $returnFlight ? 'Return' : 'OneWay';
        $bundleIds = '<ns1:OutBoundBunldedServiceId>'.$firstFlightBundleId.'</ns1:OutBoundBunldedServiceId>';
        if($returnFlightBundleId) {
            $bundleIds.= '
                <ns1:InBoundBunldedServiceId>'.$returnFlightBundleId.'</ns1:InBoundBunldedServiceId>';
        }
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            '.$this->XMLHeader.'
            <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
                <ns1:OTA_AirPriceRQ EchoToken="12662148060105253838426" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" TransactionIdentifier="'.$transactionIdentifier.'" Version="20061.00">
                    <ns1:POS>
                        <ns1:Source TerminalID="TestUser/Test Runner">
                            <ns1:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns1:BookingChannel Type="12"/>
                        </ns1:Source>
                    </ns1:POS>
                    <ns1:AirItinerary DirectionInd="'.$directionInd.'">
                        <ns1:OriginDestinationOptions>
                            ' . $flightSegmentsXml . '
                        </ns1:OriginDestinationOptions>
                    </ns1:AirItinerary>
                    <ns1:TravelerInfoSummary>
                        <ns1:AirTravelerAvail>
                            <ns1:PassengerTypeQuantity Code="ADT" Quantity="'.$adt.'"/>
                            <ns1:PassengerTypeQuantity Code="CHD" Quantity="'.$chd.'"/>
                            <ns1:PassengerTypeQuantity Code="INF" Quantity="'.$inf.'"/>
                        </ns1:AirTravelerAvail>
                    </ns1:TravelerInfoSummary>
                    <ns1:BundledServiceSelectionOptions>
                        ' . $bundleIds . '
                    </ns1:BundledServiceSelectionOptions>
                    <ns1:SpecialReqDetails>
                        <ns1:SSRRequests></ns1:SSRRequests>
                    </ns1:SpecialReqDetails>
                </ns1:OTA_AirPriceRQ>
            </soap:Body>
        </soap:Envelope>';
        // dd($xmlBody);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
                'JSESSIONID' => $jsessionId,
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
            if (!$response->successful()) {
                \Log::error('Flight details request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight details request failed.', 'details' => $response->body()];
            }

            return $this->helperService->XMLtoJSON($response->body());

        } catch (\Exception $e) {
            \Log::error('Exception in booking flight', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while booking flight.'];
        }
    }
    public function seatMap($segments)
    {
        // dd($segments['data']);
        $soapUrl = $this->flight_details;
        $cookieJar = new CookieJar();
        $transactionIdentifier = session('TransactionIdentifier');
        if (!$transactionIdentifier) return ['error' => 'Transaction identifier not provided'];
        $jsessionId = session('JSESSIONID');
        if (!$jsessionId) return ['error' => 'Jsession Id not provided'];

        // dd($segments, $transactionIdentifier, $jsessionId);
        $flightSegmentsXml = '';
        $segments = is_array(reset($segments['data'])) ? $segments['data'] : [$segments['data']];
        foreach ($segments as $segment) {
            $flightSegmentsXml .= $this->addFlightSegmentRequest($segment, 'SeatMap');
        }
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:ns="http://www.opentravel.org/OTA/2003/05" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                '.$this->XMLHeader.'
                <soap:Body>
                    <ns:OTA_AirSeatMapRQ EchoToken="11868765275150-1300257933" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" TransactionIdentifier="'.$transactionIdentifier.'" Version="20061.00">
                        <ns:POS>
                            <ns:Source TerminalID="TestUser/Test Runner">
                            <ns:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns:BookingChannel Type="12"/>
                            </ns:Source>
                        </ns:POS>
                        <ns:SeatMapRequests>
                        ' . $flightSegmentsXml . '
                        </ns:SeatMapRequests>
                    </ns:OTA_AirSeatMapRQ>
                </soap:Body>
            </soap:Envelope>
        ';
        // dd($xmlBody);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
                'JSESSIONID' => $jsessionId,
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar,
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
    
            // \Log::info('SOAP Seat XML Request:', ['xml' => $xmlBody]);
            if (!$response->successful()) {
                \Log::error('Flight seat map request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight seat map request failed.', 'details' => $response->body()];
            }
    
            // \Log::info('SOAP Seat XML Request:', ['xml' => $response->body()]);
            // dd($this->helperService->XMLtoJSON($response->body()));
            return $this->helperService->XMLtoJSON($response->body());
        } catch (\Exception $e) {
            \Log::error('Exception in seat map flight', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while seat map flight.'];
        }
    }
    public function mealMap($segments)
    {
        // dd($segments['data']);
        $soapUrl = $this->flight_details;
        $cookieJar = new CookieJar();
        $transactionIdentifier = session('TransactionIdentifier');
        if (!$transactionIdentifier) return ['error' => 'Transaction identifier not provided'];
        $jsessionId = session('JSESSIONID');
        if (!$jsessionId) return ['error' => 'Jsession Id not provided'];

        // dd($segments, $transactionIdentifier, $jsessionId);
        $flightSegmentsXml = '';
        $segments = is_array(reset($segments['data'])) ? $segments['data'] : [$segments['data']];
        foreach ($segments as $segment) {
            $flightSegmentsXml .= $this->addFlightSegmentRequest($segment, 'MealDetails');
        }
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:ns="http://www.opentravel.org/OTA/2003/05" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                '.$this->XMLHeader.'
                <soap:Body>
                    <ns:AA_OTA_AirMealDetailsRQ EchoToken="11868765275150-1300257933" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" TransactionIdentifier="'.$transactionIdentifier.'" Version="20061.00">
                        <ns:POS>
                            <ns:Source TerminalID="TestUser/Test Runner">
                            <ns:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns:BookingChannel Type="12"/>
                            </ns:Source>
                        </ns:POS>
                        <ns:MealDetailsRequests>
                        ' . $flightSegmentsXml . '
                        </ns:MealDetailsRequests>
                    </ns:AA_OTA_AirMealDetailsRQ>
                </soap:Body>
            </soap:Envelope>
        ';
        // dd($xmlBody);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
                'JSESSIONID' => $jsessionId,
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar,
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
    
            // \Log::info('SOAP meal XML Request:', ['xml' => $xmlBody]);
            if (!$response->successful()) {
                \Log::error('Flight meal map request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight meal map request failed.', 'details' => $response->body()];
            }
    
            // \Log::info('SOAP meal XML Request:', ['xml' => $response->body()]);
            // dd($this->helperService->XMLtoJSON($response->body()));
            return $this->helperService->XMLtoJSON($response->body());
        } catch (\Exception $e) {
            \Log::error('Exception in meal map flight', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while meal map flight.'];
        }
    }
    public function baggageMap($segments)
    {
        // dd($segments['data']);
        $soapUrl = $this->flight_details;
        $cookieJar = new CookieJar();
        $transactionIdentifier = session('TransactionIdentifier');
        if (!$transactionIdentifier) return ['error' => 'Transaction identifier not provided'];
        $jsessionId = session('JSESSIONID');
        if (!$jsessionId) return ['error' => 'Jsession Id not provided'];

        // dd($segments, $transactionIdentifier, $jsessionId);
        $flightSegmentsXml = '';
        $segments = is_array(reset($segments['data'])) ? $segments['data'] : [$segments['data']];
        foreach ($segments as $segment) {
            $flightSegmentsXml .= $this->addFlightSegmentRequest($segment, 'BaggageDetails');
        }
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:ns="http://www.opentravel.org/OTA/2003/05" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                '.$this->XMLHeader.'
                <soap:Body>
                    <ns:AA_OTA_AirBaggageDetailsRQ EchoToken="11868765275150-1300257933" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" TransactionIdentifier="'.$transactionIdentifier.'" Version="20061.00">
                        <ns:POS>
                            <ns:Source TerminalID="TestUser/Test Runner">
                            <ns:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns:BookingChannel Type="12"/>
                            </ns:Source>
                        </ns:POS>
                        <ns:BaggageDetailsRequests>
                        ' . $flightSegmentsXml . '
                        </ns:BaggageDetailsRequests>
                    </ns:AA_OTA_AirBaggageDetailsRQ>
                </soap:Body>
            </soap:Envelope>
        ';
        // dd($xmlBody);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
                'JSESSIONID' => $jsessionId,
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar,
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
    
            // \Log::info('SOAP baggage XML Request:', ['xml' => $xmlBody]);
            if (!$response->successful()) {
                \Log::error('Flight baggage map request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight baggage map request failed.', 'details' => $response->body()];
            }
    
            // \Log::info('SOAP baggage XML Request:', ['xml' => $response->body()]);
            // dd($this->helperService->XMLtoJSON($response->body()));
            // dd($response->body());
            return $this->helperService->XMLtoJSON($response->body());
        } catch (\Exception $e) {
            \Log::error('Exception in baggage map flight', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while baggage map flight.'];
        }
    }
    public function finalPrice($data)
    {
        // dd($data);
        $soapUrl = $this->flight_details;
        $cookieJar = new CookieJar();
        $paxCount = $data['data']['paxCount'];
        $segments = $data['data']['segments'];
        $returnFlight = $data['data']['returnFlight']['flightSegments'] ?? null;
        $departureFlight = $data['data']['departureFlight']['flightSegments'];
        $returnFlightData = $data['data']['returnFlight'] ?? null;
        $baggages = $data['baggages'] ?? null;
        $meals = $data['meals'] ?? null;
        $seats = $data['seats'] ?? null;
        $firstFlightBundleId = $data['data']['firstFlightBundleId'] ?? null;
        $returnFlightBundleId = $data['data']['returnFlightBundleId'] ?? null;

        // dd($paxCount, $segments, $returnFlight, $departureFlight, $returnFlightData);
        $transactionIdentifier = session('TransactionIdentifier');
        if (!$transactionIdentifier) return ['error' => 'Transaction identifier not provided'];
        $jsessionId = session('JSESSIONID');
        if (!$jsessionId) return ['error' => 'Jsession Id not provided'];

        $baggageXml = $mealXml = $seatXml = '';

        if (!empty($baggages)) {
            $baggageTag = '';
            foreach ($baggages as $baggage) {
                $baggageTag .= $this->addBaggage($baggage);
            }
            $baggageXml = "<ns1:BaggageRequests>$baggageTag</ns1:BaggageRequests>";
        }
        if (!empty($meals)) {
            $mealTag = '';
            foreach ($meals as $meal) {
                $mealTag .= $this->addAncisTag($meal, 'meal');
            }
            $mealXml = "<ns1:MealRequests>$mealTag</ns1:MealRequests>";
        }

        if (!empty($seats)) {
            $seatTag = '';
            foreach ($seats as $seat) {
                $seatTag .= $this->addAncisTag($seat, 'seat');
            }
            $seatXml = "<ns1:SeatRequests>$seatTag</ns1:SeatRequests>";
        }
        // dd($baggageXml, $mealXml ,$seatXml);
        $adt = $paxCount['adt'] ?? 1;
        $chd = $paxCount['chd'] ?? 0;
        $inf = $paxCount['inf'] ?? 0;
        $directionInd = $returnFlightData ? 'Return' : 'OneWay';

        $flightSegmentsXml = '';
        if ($departureFlight) {
            $flightSegmentsXml .= $this->addFlightSegments(1, $departureFlight, $segments);
        }
        if ($returnFlight) {
            $flightSegmentsXml .= $this->addFlightSegments(1, $returnFlight, $segments);
        }
        $bundleIds = '<ns1:OutBoundBunldedServiceId>'.$firstFlightBundleId.'</ns1:OutBoundBunldedServiceId>';
        if($returnFlightBundleId) {
            $bundleIds.= '
                <ns1:InBoundBunldedServiceId>'.$returnFlightBundleId.'</ns1:InBoundBunldedServiceId>';
        }
        $bundleXml = '';
        // if (empty($baggages) && empty($meals) && empty($seats)){
        $bundleXml = '<ns1:BundledServiceSelectionOptions>
                        ' . $bundleIds . '
                    </ns1:BundledServiceSelectionOptions>';
        // }
        // $flightSegmentsXml = '';
        // foreach ($segments as $segment) {
        //     $flightSegmentsXml .= $this->addFlightSegments(1, $segment);
        // }
        // <ns1:SSRRequests>
        // </ns1:SSRRequests>

        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            '.$this->XMLHeader.'
            <soap:Body xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
                <ns1:OTA_AirPriceRQ EchoToken="12662148060105253838426" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" Version="20061.00" TransactionIdentifier="'.$transactionIdentifier.'">
                    <ns1:POS>
                        <ns1:Source TerminalID="TestUser/Test Runner">
                            <ns1:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns1:BookingChannel Type="12"/>
                        </ns1:Source>
                    </ns1:POS>
                    <ns1:AirItinerary DirectionInd="'.$directionInd.'">
                        <ns1:OriginDestinationOptions>
                            ' . $flightSegmentsXml . '
                        </ns1:OriginDestinationOptions>
                    </ns1:AirItinerary>
                    <ns1:TravelerInfoSummary>
                        <ns1:AirTravelerAvail>
                            <ns1:PassengerTypeQuantity Code="ADT" Quantity="'.$adt.'"/>
                            <ns1:PassengerTypeQuantity Code="CHD" Quantity="'.$chd.'"/>
                            <ns1:PassengerTypeQuantity Code="INF" Quantity="'.$inf.'"/>
                        </ns1:AirTravelerAvail>
                        <ns1:SpecialReqDetails>
                            ' . $baggageXml . '
                            ' . $mealXml . '
                            ' . $seatXml . '
                        </ns1:SpecialReqDetails>
                    </ns1:TravelerInfoSummary>
                        ' . $bundleXml . '
                </ns1:OTA_AirPriceRQ>
            </soap:Body>
        </soap:Envelope>';
        // dd($xmlBody);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
                'JSESSIONID' => $jsessionId,
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar,
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
            // dd($this->helperService->XMLtoJSON($response->body()));
            // \Log::info('SOAP Final Price XML Request:', ['xml' => $xmlBody]);
            if (!$response->successful()) {
                \Log::error('Flight Final Price request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight Final Price request failed.', 'details' => $response->body()];
            }
            // \Log::info('SOAP Final Price XML Request:', ['xml' => $response->body()]);
            return $this->helperService->XMLtoJSON($response->body());
        } catch (\Exception $e) {
            \Log::error('Exception in Final Price flight', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while Final Price flight.'];
        }
    }
    public function bookFlight($data)
    {
        $user = $data['data']['user'];
        $passengers = $data['data']['passengers'];
        $paymentOnHold = filter_var($data['data']['paymentOnHold'], FILTER_VALIDATE_BOOLEAN);
        $finalPriceTag = $data['data']['finalPriceTag'];
        $segments = $data['data']['data']['segments'];
        $departureFlight = $data['data']['data']['departureFlight']['flightSegments'];
        $returnFlight = $data['data']['data']['returnFlight']['flightSegments'] ?? null;
        $soapUrl = $this->flight_details;
        $cookieJar = new CookieJar();
        $transactionIdentifier = session('TransactionIdentifier');
        if (!$transactionIdentifier) return ['error' => 'Transaction identifier not provided'];
        $jsessionId = session('JSESSIONID');
        if (!$jsessionId) return ['error' => 'Jsession Id not provided'];
        // dd($paymentOnHold, $finalPriceTag, $transactionIdentifier, $jsessionId);
        $passengerXml = '';
        $adultIndexes = [];
        $adultCounter = 1;

        foreach ($passengers as $index => $passenger)
        {
            $rph = ($passenger['type'] === 'Adult') ? 'A' : (($passenger['type'] === 'Child') ? 'C' : 'I');
            $passengerTypeCode = ($passenger['type'] === 'Adult') ? 'ADT' : (($passenger['type'] === 'Child') ? 'CHD' : 'INF');
            $rphNumber = $index + 1;
            if ($passenger['type'] === 'Adult') {
                $adultIndexes[] = $rphNumber;
            }
            $infantAssociation = '';
            if ($passenger['type'] === 'Infant' && !empty($adultIndexes)) {
                $assignedAdult = $adultIndexes[$adultCounter - 1] ?? end($adultIndexes);
                $infantAssociation = "/A{$assignedAdult}";
                $adultCounter++;
            }
            // <ns2:Telephone AreaCityCode=\"{$passenger['areaCode']}\" CountryAccessCode=\"{$passenger['countryCode']}\" PhoneNumber=\"{$passenger['phone']}\"/>
            $passengerXml .= "
                <ns2:AirTraveler BirthDate=\"{$passenger['dob']}T00:00:00\" PassengerTypeCode=\"" . $passengerTypeCode . "\">
                    <ns2:PersonName>
                        <ns2:NameTitle>{$passenger['title']}</ns2:NameTitle>
                        <ns2:GivenName>{$passenger['name']}</ns2:GivenName>
                        <ns2:Surname>{$passenger['surname']}</ns2:Surname>
                    </ns2:PersonName>
                    <ns2:Address>
                        <ns2:CountryName Code=\"{$passenger['nationality']}\"/>
                    </ns2:Address>
                    <ns2:Document DocHolderNationality=\"{$passenger['nationality']}\"/>
                    <ns2:TravelerRefNumber RPH=\"{$rph}{$rphNumber}{$infantAssociation}\"/>
                </ns2:AirTraveler>";
        }
        $flightSegmentsXml = '';
        if ($departureFlight) {
            $flightSegmentsXml .= $this->addFlightSegments(2, $departureFlight, $segments);
        }
        if ($returnFlight) {
            $flightSegmentsXml .= $this->addFlightSegments(2, $returnFlight, $segments);
        }

        $paymentTag = '';
        if(!$paymentOnHold && $finalPriceTag){
            $paymentTag = '
            <ns2:Fulfillment>
                <ns2:PaymentDetails>
                    <ns2:PaymentDetail>
                        <ns2:DirectBill>
                            <ns2:CompanyName Code="'.$this->agentCode.'">'.$this->agencyName.'</ns2:CompanyName>
                        </ns2:DirectBill>
                        <ns2:PaymentAmount Amount="'.$finalPriceTag['Amount'].'" CurrencyCode="'.$finalPriceTag['CurrencyCode'].'" DecimalPlaces="'.$finalPriceTag['DecimalPlaces'].'"/>
                    </ns2:PaymentDetail>
                </ns2:PaymentDetails>
            </ns2:Fulfillment>';
        };
        $loggedInUser = '';
        // $loggedInUser = '<ns1:ContactInfo>
        //     <ns1:PersonName>
        //     <ns1:Title>'.$user['userTitle'].'</ns1:Title>
        //     <ns1:FirstName>'.$user['userFirstName'].'</ns1:FirstName>
        //     <ns1:LastName>'.$user['userLastName'].'</ns1:LastName>
        //     </ns1:PersonName>
        //     <ns1:Telephone>
        //     <ns1:PhoneNumber>'.$user['userPhone'].'</ns1:PhoneNumber>
        //     <ns1:CountryCode>'.$user['userPhoneCode'].'</ns1:CountryCode>
        //     <ns1:AreaCode>'.$user['userAreaCode'].'</ns1:AreaCode>
        //     </ns1:Telephone>
        //     <ns1:Email>'.$user['userEmail'].'</ns1:Email>
        //     <ns1:Address>
        //     <ns1:CountryName>
        //         <ns1:CountryName>'.$user['country'].'</ns1:CountryName>
        //         <ns1:CountryCode>'.$user['countryCode'].'</ns1:CountryCode>
        //     </ns1:CountryName>
        //     <ns1:CityName>'.$user['userCity'].'</ns1:CityName>
        //     </ns1:Address>
        // </ns1:ContactInfo>';
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
            <soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                '.$this->XMLHeader.'
                <soap:Body xmlns:ns1="http://www.isaaviation.com/thinair/webservices/OTA/Extensions/2003/05" xmlns:ns2="http://www.opentravel.org/OTA/2003/05">
                    <ns2:OTA_AirBookRQ EchoToken="11868765275150-1300257933" PrimaryLangID="en-us" SequenceNmbr="1" TimeStamp="' . date('Y-m-d\TH:i:s') . '" TransactionIdentifier="'.$transactionIdentifier.'" Version="20061.00">
                        <ns2:POS>
                            <ns2:Source TerminalID="TestUser/Test Runner">
                            <ns2:RequestorID Type="4" ID="'.$this->testUsername.'"/>
                            <ns2:BookingChannel Type="12"/>
                            </ns2:Source>
                        </ns2:POS>
                        <ns2:AirItinerary>
                            <ns2:OriginDestinationOptions>
                            ' . $flightSegmentsXml . '
                            </ns2:OriginDestinationOptions>
                        </ns2:AirItinerary>
                        <ns2:TravelerInfo>
                            ' . $passengerXml . '
                        </ns2:TravelerInfo>
                        ' . $paymentTag . '
                    </ns2:OTA_AirBookRQ>
                    <ns1:AAAirBookRQExt>
                        ' . $loggedInUser . '
                    </ns1:AAAirBookRQExt>
                </soap:Body>
            </soap:Envelope>
        ';
        // dd($xmlBody);
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => '',
                'JSESSIONID' => $jsessionId,
            ])
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar,
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
            // \Log::info('SOAP XML Booking Request:', ['xml' => $xmlBody]);
            if (!$response->successful()) {
                \Log::error('Flight booking request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight booking request failed.', 'details' => $response->body()];
            }
            return $this->helperService->XMLtoJSON($response->body());
        } catch (\Exception $e) {
            \Log::error('Exception in booking flight', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while booking flight.'];
        }
    }
    // ------------------  Helper Functions  ---------------------
    private function addFlightSegments($tag, $flights, $segments = null)
    {
        $segmentsXml = '';
        if (isset($flights['flightNumber'])) {
            $flights = [$flights];
        }
        foreach ($flights as $flightData) {
            if (!$flightData) continue;
            $departureCode = $flightData['origin']['airportCode'] ?? $flightData['destination'];
            $departureTerminal = $flightData['origin']['terminal'] ?? $flightData['depTerminal'] ?? '';
            $arrivalCode = $flightData['destination']['airportCode'] ?? $flightData['origin'];
            $arrivalTerminal = $flightData['destination']['terminal'] ?? $flightData['arrTerminal'] ?? '';
            $departureDate = $flightData['departure'] ?? $flightData['departureDateTimeLocal'];
            $arrivalDate = $flightData['arrival'] ?? $flightData['arrivalDateTimeLocal'];
            $flightId = $flightData['flightNumber'];
            $airlineCode = substr($flightId, 0, 2);
            $segment = $segments ? $this->getSegmentAttributes($flightId, $segments) : null;
            $rph = $flightData['rph'] ?? ($segment['rph'] ?? '');
            $segmentsXml .= '<ns'.$tag.':FlightSegment ArrivalDateTime="' . $arrivalDate . '" DepartureDateTime="' . $departureDate . '" FlightNumber="' . $flightId . '" RPH="' . $rph . '">
                                <ns'.$tag.':DepartureAirport LocationCode="' . $departureCode . '" Terminal="' . $departureTerminal . '"/>
                                <ns'.$tag.':ArrivalAirport LocationCode="' . $arrivalCode . '" Terminal="' . $arrivalTerminal . '"/>
                                <ns'.$tag.':OperatingAirline Code="' . $airlineCode . '"/>
                            </ns'.$tag.':FlightSegment>';
        }
        return $segmentsXml ? '<ns'.$tag.':OriginDestinationOption>' . $segmentsXml . '</ns'.$tag.':OriginDestinationOption>' : '';
    }
    private function addFlightSegmentRequest($flightData, $type)
    {
        if (!$flightData || !in_array($type, ['SeatMap', 'MealDetails', 'BaggageDetails'])) {
            return '';
        }

        $flightId = $flightData['flightNumber'] ?? '';
        $airlineCode = substr($flightId, 0, 2);

        $requestTag = "ns:{$type}Request";

        return '<' . $requestTag . ' TravelerRefNumberRPHs="">' .
                    '<ns:FlightSegmentInfo ' .
                        'ArrivalDateTime="' . htmlspecialchars($flightData['arrival']) . '" ' .
                        'DepartureDateTime="' . htmlspecialchars($flightData['departure']) . '" ' .
                        'FlightNumber="' . htmlspecialchars($flightData['flightNumber']) . '" ' .
                        'RPH="' . htmlspecialchars($flightData['rph']) . '" returnFlag="false">' .
                        '<ns:DepartureAirport LocationCode="' . htmlspecialchars($flightData['destination']) . '" Terminal="' . htmlspecialchars($flightData['arrTerminal']) . '"/>' .
                        '<ns:ArrivalAirport LocationCode="' . htmlspecialchars($flightData['origin']) . '" Terminal="' . htmlspecialchars($flightData['depTerminal']) . '"/>' .
                        '<ns:OperatingAirline Code="' . htmlspecialchars($airlineCode) . '"/>' .
                    '</ns:FlightSegmentInfo>' .
                '</' . $requestTag . '>';
    }
    private function addAncisTag($data, $type)
    {
        if (!$data || !$type) return '';
        $tagMap = [
            'baggage' => ['tag' => 'BaggageRequest', 'code' => 'BaggageCode', 'key' => 'baggageCode'],
            'meal'    => ['tag' => 'MealRequest',    'code' => 'mealCode',    'key' => 'mealCode'],
            'seat'    => ['tag' => 'SeatRequest',    'code' => 'SeatNumber',  'key' => 'seatId'],
        ];
        if (!isset($tagMap[$type])) return '';
        $tagName = $tagMap[$type]['tag'];
        $codeAttr = $tagMap[$type]['code'];
        $codeKey = $tagMap[$type]['key'];
        $extraAttr = $type === 'meal' ? ' mealQuantity="1"' : '';
        $xml = '';
        foreach ($data as $item) {
            $code = $item[$codeKey] ?? '';
            $traveler = htmlspecialchars($item['passenger'] ?? '');
            if (!$traveler) continue;
            $rph = htmlspecialchars($item['rph'] ?? '');
            $date = htmlspecialchars($item['depDate'] ?? '');
            $flight = htmlspecialchars($item['flightNo'] ?? '');

            $xml .= "
            <ns1:$tagName $codeAttr=\"$code\"$extraAttr TravelerRefNumberRPHList=\"$traveler\" FlightRefNumberRPHList=\"$rph\" DepartureDate=\"$date\" FlightNumber=\"$flight\"/>
            ";
        }
        return $xml;
    }
    private function addBaggage($data)
    {
        if (empty($data)) return '';
        $xml = '';
        $baggages = isset($data[0]) ? $data : [$data];
        foreach ($baggages as $baggageData) {
            $passenger   = $baggageData['passenger'] ?? '';
            $baggageCode = $baggageData['baggageCode'] ?? '';
            $rphs        = $baggageData['rph'] ?? [];
            $flightNos   = $baggageData['flightNo'] ?? [];
            $depDates    = $baggageData['depDate'] ?? [];
            foreach ($rphs as $i => $rph) {
                $flightNumber   = $flightNos[$i] ?? '';
                $departureDate  = $depDates[$i] ?? '';

                $xml .= '<ns1:BaggageRequest '
                    . 'baggageCode="' . htmlspecialchars($baggageCode) . '" '
                    . 'TravelerRefNumberRPHList="' . htmlspecialchars($passenger) . '" '
                    . 'FlightRefNumberRPHList="' . htmlspecialchars($rph) . '" '
                    . 'DepartureDate="' . htmlspecialchars($departureDate) . '" '
                    . 'FlightNumber="' . htmlspecialchars($flightNumber) . '"'
                    . ' />
                    ';
            }
        }
        return $xml;
    }
    private function getSegmentAttributes($flightNo, $segments)
    {
        $segmentArray = isset($segments[0]) ? $segments : [$segments];
        foreach ($segmentArray as $segment) {
            if (($segment['flightNumber'] ?? null) === $flightNo) {
                return $segment;
            }
        }
        return null;
    }

}
