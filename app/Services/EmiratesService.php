<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\HelperService;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Support\Str;

class EmiratesService
{
    protected $helperService;
    protected $randomId;
    protected $agencyName;
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
        $action = 'AirShoppingRQ';
        $soapUrl = $this->url;
        $agencyName = $this->agencyName;

        $origin = $data['arr'];
        $destination = $data['dest'];
        $departureDate = $data['dep'];
        $returnDate = $data['return'];
        $cabinClass = $data['cabinClass'] ?? 'Y';
        $currCode = $data['currCode'] ?? 'PKR';
        $adt = $data['adt'];
        $chd = $data['chd'] ?? null;
        $inf = $data['inf'] ?? null;

        // dd($origin, $destination, $departureDate, $returnDate, $adt, $chd, $inf, $cabinClass);
        $paxId = 1;
        $adtIds = [];
        $paxXml = "<PassengerList>";
        for ($i = 0; $i < $adt; $i++, $paxId++) {
            $paxXml .= '
                <Passenger PassengerID="T'.$paxId.'">
                    <PTC>ADT</PTC>
                </Passenger>';
            $adtIds[] = 'T'.$paxId;
        }
        for ($i = 0; $i < $chd; $i++, $paxId++) {
            $paxXml .= '
                <Passenger PassengerID="T'.$paxId.'">
                    <PTC>CNN</PTC>
                </Passenger>';
        }
        for ($i = 0; $i < $inf; $i++) {
            if (isset($adtIds[$i])) {
                $infId = $adtIds[$i] . '.1';
                $paxXml .= '
                    <Passenger PassengerID="'.$infId.'">
                        <PTC>INF</PTC>
                    </Passenger>';
            } else {
                // If not enough ADTs to assign INF to, skip or handle as needed
                break;
            }
        }
        $paxXml .= "</PassengerList>";
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
                                        <AirportCode>KHI</AirportCode>
                                        <Date>2025-05-20</Date>
                                    </Departure>
                                    <Arrival>
                                        <AirportCode>DXB</AirportCode>
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
        $xmlBody = $this->getSoapEnvelope($body);
        // dd($xmlBody);
        try {
            $cookieJar = new CookieJar();
            $response = Http::withHeaders($this->getSoapHeaders($action))
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
            // dd($response->body());
            if (!$response->successful()) {
                \Log::error('Flight details request failed Emirates', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight details request failed Emirates.', 'details' => $response->body()];
            }
            $responseXml = $response->body();
            $data = $this->helperService->XMLtoJSONEmirate($responseXml);
            dd($data); 
            return $this->helperService->XMLtoJSONEmirate($response->body());
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
}