<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\HelperService;
use GuzzleHttp\Cookie\CookieJar;

class PiaService
{
    protected $helperService;
    protected $agencyName;
    protected $agencyEmail;
    protected $url;
    protected $username;
    protected $password;

    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;

        $this->agencyName = config('services.agency.name');
        $this->agencyEmail = config('services.agency.email');
        
        $this->url = config('services.pia_api.url');
        $this->username = config('services.pia_api.username');
        $this->password = config('services.pia_api.password');

        // $this->testUsername = 'TESTS9P';
        // $this->testPassword = 'P@ss1234';
    }
    public function searchFlights($data)
    {
        $tagType = 'AirShopping';
        $soapUrl = $this->url;
        $agencyName = $this->agencyName;
        $agencyEmail = $this->agencyEmail;
        $username = $this->username;

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
        $paxXml = "<Paxs>";
        foreach (['ADT' => $adt, 'CHD' => $chd ?? 0, 'INF' => $inf ?? 0] as $type => $qty) {
            for ($i = 0; $i < $qty; $i++, $paxId++) {
                $paxXml .= "
                <Pax>
                    <PaxID>SH{$paxId}</PaxID>
                    <PTC>{$type}</PTC>
                </Pax>";
            }
        }
        $paxXml .= "</Paxs>";
        $returnXml = '';
        if ($returnDate) {
            $returnXml = '<OriginDestCriteria>
                <DestArrivalCriteria>
                    <IATA_LocationCode>'.$origin.'</IATA_LocationCode>
                </DestArrivalCriteria>
                <OriginDepCriteria>
                    <Date>'.$returnDate.'</Date>
                    <IATA_LocationCode>'.$destination.'</IATA_LocationCode>
                </OriginDepCriteria>
                <PreferredCabinType>
                    <CabinTypeCode>'.$cabinClass.'</CabinTypeCode>
                </PreferredCabinType>
            </OriginDestCriteria>';
        }
        $xmlBody = '<?xml version="1.0" encoding="utf-8"?>
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.iata.org/IATA/2015/00/2020.1/IATA_'.$tagType.'RQ">
            <soapenv:Header/>
                <soapenv:Body>
                    <IATA_'.$tagType.'RQ>
                        <MessageDoc>
                            <RefVersionNumber>20.1</RefVersionNumber>
                        </MessageDoc>
                        <Party>
                            <Sender>
                                <TravelAgency>
                                    <AgencyID>'.$username.'</AgencyID>
                                    <ContactInfo>
                                        <EmailAddress>
                                            <EmailAddressText>'.$agencyEmail.'</EmailAddressText>
                                        </EmailAddress>
                                    </ContactInfo>
                                    <Name>'.$agencyName.'</Name>
                                </TravelAgency>
                            </Sender>
                        </Party>
                        <Request>
                            <FlightRequest>
                                <OriginDestCriteria>
                                    <DestArrivalCriteria>
                                        <IATA_LocationCode>'.$destination.'</IATA_LocationCode>
                                    </DestArrivalCriteria>
                                    <OriginDepCriteria>
                                        <IATA_LocationCode>'.$origin.'</IATA_LocationCode>
                                        <Date>'.$departureDate.'</Date>
                                    </OriginDepCriteria>
                                    <PreferredCabinType>
                                        <CabinTypeCode>'.$cabinClass.'</CabinTypeCode>
                                    </PreferredCabinType>
                                </OriginDestCriteria>
                                ' . $returnXml . '
                            </FlightRequest>
                            ' . $paxXml . '
                            <ResponseParameters>
                                <CurParameter>
                                    <RequestedCurCode>'.$currCode.'</RequestedCurCode>
                                </CurParameter>
                                <LangUsage>
                                    <LangCode>EN</LangCode>
                                </LangUsage>
                            </ResponseParameters>
                            <ShoppingCriteria>
                                <ConnectionCriteria>
                                    <ConnectionPrefID/>
                                    <InterlineInd>false</InterlineInd>
                                    <StationCriteria/>
                                </ConnectionCriteria>
                            </ShoppingCriteria>
                        </Request>
                    </IATA_'.$tagType.'RQ>
                </soapenv:Body>
            </soapenv:Envelope>';
        // dd($xmlBody);
        try {
            $cookieJar = new CookieJar();
            $response = Http::withHeaders($this->getSoapHeaders($tagType))
            ->withOptions([
                'verify' => false,
                'cookies' => $cookieJar
            ])
            ->withBody($xmlBody, 'text/xml')
            ->post($soapUrl);
            // \Log::info('SOAP XML Request PIA:', ['xml' => $xmlBody]);

            if (!$response->successful()) {
                \Log::error('Flight details request failed PIA', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return ['error' => 'Flight details request failed PIA.', 'details' => $response->body()];
            }
            // dd($this->helperService->XMLtoJSON($response->body()));
            return $this->helperService->XMLtoJSON($response->body());
        } catch (\Exception $e) {
            \Log::error('Exception in fetching flight details', ['message' => $e->getMessage()]);
            return ['error' => 'Exception occurred while fetching flight details.'];
        }
    }



    // ------------------  Helper Functions  ---------------------

    private function getSoapHeaders($tagType)
    {
        return [
            'Username' => $this->username,
            'Password' => $this->password,
            'Content-Type' => 'text/xml;charset=UTF-8',
            'SOAPAction' => "cranendc/do" . $tagType
        ];
    }
}