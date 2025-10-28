<?php

namespace App\Services;

use App\Services\PiaService;
use App\Helpers\HelperFunctions;
use App\Services\EmiratesService;
use App\Services\FlyJinnahService;
use Illuminate\Support\Collection;
// 1) if in bundles have array so show directly bundles of this flight (speacially in emirates)
// 2) if in bundles have key only so match bundle from outside flights bundles tag and show  (speacially in pia)
// 3) if in bundles if null so fetch bundles from api (speacially in flyjinnah)
class FlightAggregatorService
{
    protected $services;

    public function __construct(
        HelperFunctions $helper,
        PiaService $pia,
        EmiratesService $emirate,
        FlyJinnahService $flyjinnah
    ) {
        $this->services = [$pia, $emirate, $flyjinnah];
        // $this->services = [$emirate];
        // $this->services = [$pia];
        $this->helper = $helper;
    }
    public function searchAllFlights($params)
    {
        $outboundFlights = collect();
        $inboundFlights = collect();
        $allBundles = collect();
        $allExtras = collect();
        $allErrors = collect();

        if(!empty($this->services)) {
           foreach ($this->services as $service) {
                $rawFlights = $service->searchFlights($params);
                $normalized = $this->normalizeFlights($rawFlights, $service->getCarrierName());

                // Collect bundles
                if ($service->getCarrierName() === 'pia') {
                    $allBundles = $allBundles->merge($normalized['bundles'] ?? []);
                }
                // elseif ($service->getCarrierName() === 'flyJinnah') {
                //     // Fetch bundles if needed (implement in FlyJinnahService)
                //     $bundles = [];
                //     $allBundles = $allBundles->merge($bundles);
                // }
                // Emirates bundles are per-flight, so no global merge needed here

                // Collect errors
                if (!empty($normalized['errors'])) {
                    $allErrors = $allErrors->merge($normalized['errors']);
                }
                if (!empty($normalized['extras'])) {
                    $allExtras = $allExtras->merge($normalized['extras']);
                }

                // Merge flights (index 0 = outbound, 1 = inbound)
                $outbound = $normalized['flights'][0] ?? collect();
                $inbound = $normalized['flights'][1] ?? collect();
                $outboundFlights = $outboundFlights->merge($outbound);
                $inboundFlights = $inboundFlights->merge($inbound);
            } 
        }

        // Sort by price
        $outboundFlights = $outboundFlights->sortBy('price')->values();
        $inboundFlights = $inboundFlights->sortBy('price')->values();

        $flights = collect([$outboundFlights, $inboundFlights]);
        $departureCount = $outboundFlights->count();
        $returnCount = $inboundFlights->count();
        return [
            'flights' => $flights,
            'total_count' => $departureCount + $returnCount,
            'departure_count' => (int) $departureCount,
            'return_count' => (int) $returnCount,
            'bundles' => $allBundles,
            'extras' => $allExtras,
            'errors' => $allErrors
        ];
    }
    private function normalizeFlights($rawData, $carrier)
    {
        $flightsCollection = collect();
        $bundles = collect();
        $extras = collect();
        $errors = collect();

        if ($carrier === 'pia') {
            $outboundFlights = $rawData['flight'] ?? [];
            $bundles = collect($rawData['bundles'] ?? []);
            foreach ($outboundFlights as $flights) {
                $data = collect();
                if (!empty($flights['flights'])) {
                    \Log::info('PIA Flight Count', ['count' => count($flights['flights'])]);
                    foreach ($flights['flights'] as $flight) {
                        $segments = $flight['segments'] ?? [];
                        if (empty($segments)) continue;

                        $departure = $segments[0]['departure_time'] ?? '';
                        $arrival = $segments[1]['arrival_time'] ?? $segments[0]['arrival_time'] ?? '';
                        $data->push([
                            'carrier' => $carrier,
                            'cabinClass' => $bundles->first()['parameters']['cabin_type'] ?? 'economy',
                            'departure' => [
                                'code' => $segments[0]['origin'] ?? '',
                                'airport' => $this->helper::codeToCountry($segments[0]['origin'] ?? ''),
                                'datetime' => $this->helper::formatDateTimeForFlights($departure),
                                'date' => $this->helper::formatDateForFlights($departure),
                                'time' => $this->helper::formatTimeForFlights($departure),
                            ],
                            'arrival' => [
                                'code' => $segments[1]['destination'] ?? $segments[0]['destination'] ?? '',
                                'airport' => $this->helper::codeToCountry($segments[1]['destination'] ?? $segments[0]['destination'] ?? ''),
                                'datetime' => $this->helper::formatDateTimeForFlights($arrival),
                                'date' => $this->helper::formatDateForFlights($arrival),
                                'time' => $this->helper::formatTimeForFlights($arrival),
                            ],
                            'duration' => $this->helper::calculateDuration($departure, $arrival),
                            'isConnected' => count($segments) > 1,
                            'price' => number_format(($flight['price']['total_amount'] ?? 0), 2),
                            'code' => $flight['price']['currency'] ?? 'PKR',
                            'segments' => $this->normalizeSegment($segments, $carrier),
                            'bundles' => $flight['bundleKey'] ?? null,
                            'status' => 'AVAILABLE',
                            'flightRaw' => null,
                        ]);
                    }
                }
                $flightsCollection->push($data);
            }
        } elseif ($carrier === 'flyJinnah') {
            $outboundFlights = $rawData ?? [];
            foreach ($outboundFlights as $flights) {
                $data = collect();
                if (!empty($flights['flights'])) {
                    \Log::info('FlyJinnah Flight Count', ['count' => count($flights['flights'])]);
                    foreach ($flights['flights'] as $flight) {
                        $segments = $flight['flightSegments'] ?? [];
                        if (empty($segments)) continue;

                        $departure = $segments[0]['departureDateTimeLocal'] ?? '';
                        $arrival = $segments[1]['arrivalDateTimeLocal'] ?? $segments[0]['arrivalDateTimeLocal'] ?? '';
                        $data->push([
                            'carrier' => $carrier,
                            'cabinClass' => $this->helper::getCabinClass($flight['cabinPrices'][0]['cabinClass'] ?? null),
                            'departure' => [
                                'code' => $segments[0]['origin']['airportCode'] ?? '',
                                'airport' => $this->helper::codeToCountry($segments[0]['origin']['airportCode'] ?? ''),
                                'datetime' => $this->helper::formatDateTimeForFlights($departure),
                                'date' => $this->helper::formatDateForFlights($departure),
                                'time' => $this->helper::formatTimeForFlights($departure),
                            ],
                            'arrival' => [
                                'code' => $segments[1]['destination']['airportCode'] ?? $segments[0]['destination']['airportCode'] ?? '',
                                'airport' => $this->helper::codeToCountry($segments[1]['destination']['airportCode'] ?? $segments[0]['destination']['airportCode'] ?? ''),
                                'datetime' => $this->helper::formatDateTimeForFlights($arrival),
                                'date' => $this->helper::formatDateForFlights($arrival),
                                'time' => $this->helper::formatTimeForFlights($arrival),
                            ],
                            'duration' => $this->helper::calculateDuration($departure, $arrival),
                            'isConnected' => count($segments) > 1,
                            'price' => number_format($flight['price'] ?? 0, 2),
                            'code' => 'PKR',
                            'segments' => $this->normalizeSegment($segments, $carrier),
                            'bundles' => null, // Fetch via API in FlyJinnahService if needed
                            'status' => $flight['availabilityStatus'] ?? 'AVAILABLE',
                            'flightRaw' => $flight,
                        ]);
                    }
                }
                $flightsCollection->push($data);
            }
        } elseif ($carrier === 'emirates') {
            if (isset($rawData['error']) && !empty($rawData['error'])) {
                $errors->push([
                    'details' => $rawData['details']['value'] ?? $rawData,
                ]);
            }
            $flightsData = array_filter($rawData, function ($key) {
                return is_int($key);
            }, ARRAY_FILTER_USE_KEY);
            $extras = collect([
                'emirates' => [
                    'responseId' => $flightsData[0]['responseId'] ?? $flightsData['responseId'] ?? '',
                ]
            ]);
            foreach ($flightsData as $flights) {
                // dd($flights);
                $data = collect();
                if (!empty($flights['flights'])) {
                    \Log::info('Emirates Flight Count', ['count' => count($flights['flights'])]);
                    foreach ($flights['flights'] as $flight) {
                        $departure = ($flight['Departure']['Date']['value'] ?? '') . ' ' . ($flight['Departure']['Time']['value'] ?? '');
                        $arrival = $flight['secondFlight']['arrival'] ?? $flight['Arrival'] ?? '';
                        $arrivalDateTime = ($arrival['Date']['value'] ?? '') . ' ' . ($arrival['Time']['value'] ?? '');
                        $data->push([
                            'carrier' => $carrier,
                            'cabinClass' => $this->helper::getCabinClass($rawData['cabinClass'] ?? null),
                            'departure' => [
                                'code' => $flight['Departure']['AirportCode']['value'] ?? '',
                                'airport' => $this->helper::codeToCountry($flight['Departure']['AirportCode']['value'] ?? ''),
                                'datetime' => $this->helper::formatDateTimeForFlights($departure),
                                'date' => $this->helper::formatDateForFlights($departure),
                                'time' => $this->helper::formatTimeForFlights($departure),
                            ],
                            'arrival' => [
                                'code' => $arrival['AirportCode']['value'] ?? '',
                                'airport' => $this->helper::codeToCountry($arrival['AirportCode']['value'] ?? ''),
                                'datetime' => $this->helper::formatDateTimeForFlights($arrivalDateTime),
                                'date' => $this->helper::formatDateForFlights($arrivalDateTime),
                                'time' => $this->helper::formatTimeForFlights($arrivalDateTime),
                            ],
                            'duration' => $this->helper::calculateDuration($departure, $arrivalDateTime),
                            'isConnected' => $flight['flightDetails']['isConnected'] ?? false,
                            'price' => number_format($flight['price']['amount'] ?? 0, 2),
                            'code' => $flight['price']['code'] ?? 'AED',
                            'segments' => $this->normalizeSegment($flight, $carrier),
                            'bundles' => $flight['bundles'] ?? [],
                            'status' => 'AVAILABLE',
                            'flightRaw' => null,
                        ]);
                    }
                }
                $flightsCollection->push($data);
            }
        }

        return [
            'flights' => $flightsCollection,
            'bundles' => $bundles,
            'extras' => $extras,
            'errors' => $errors
        ];
    }
    private function normalizeSegment($segments, $airline)
    {
        if ($airline === 'pia') {
            $data = [];
            foreach ($segments as $segment) {
                $data[] = [
                    'segment_key' => $segment['segment_key'] ?? '',
                    'departure' => [
                        'code' => $segment['origin'] ?? '',
                        'airport' => $this->helper::codeToCountry($segment['origin'] ?? ''),
                        'datetime' => $segment['departure_time'] ?? '',
                        'zuluTime' => null,
                    ],
                    'arrival' => [
                        'code' => $segment['destination'] ?? '',
                        'airport' => $this->helper::codeToCountry($segment['destination'] ?? ''),
                        'datetime' => $segment['arrival_time'] ?? '',
                        'zuluTime' => null,
                    ],
                    'flight_number' => $segment['flight_number'] ?? '',
                    'duration' => $segment['duration'] ?? '',
                    'aircraft' => $segment['aircraft_type'] ?? '',
                    'carrier' => $segment['carrier'] ?? '',
                    'baggage' => $segment['baggage_allowance'] ?? [],
                ];
            }
            return $data;
        } elseif ($airline === 'flyJinnah') {
            $data = [];
            foreach ($segments as $segment) {
                $departure = $segment['departureDateTimeLocal'] ?? null;
                $arrival = $segment['arrivalDateTimeLocal'] ?? null;
                $data[] = [
                    'segment_key' => $segment['flightSegmentRef'] ?? null,
                    'departure' => [
                        'code' => $segment['origin']['airportCode'] ?? '',
                        'airport' => $this->helper::codeToCountry($segment['origin']['airportCode'] ?? ''),
                        'datetime' => $departure,
                        'zuluTime' => $segment['departureDateTimeZulu'] ?? '',
                    ],
                    'arrival' => [
                        'code' => $segment['destination']['airportCode'] ?? '',
                        'airport' => $this->helper::codeToCountry($segment['destination']['airportCode'] ?? ''),
                        'datetime' => $arrival,
                        'zuluTime' => $segment['arrivalDateTimeZulu'] ?? '',
                    ],
                    'flight_number' => $segment['flightNumber'] ?? '',
                    'duration' => $this->helper::calculateDuration($departure, $arrival),
                    'aircraft' => $segment['aircraftModel'] ?? '',
                    'carrier' => 'FJ',
                    'baggage' => [],
                ];
            }
            return $data;
        } elseif ($airline === 'emirates') {
            // dd($segments);
            $segmentsArr = [];
            // $firstArrival = !empty($segments['secondFlight'])
            //     ? $segments['secondFlight']['departure']
            //     : $segments['Arrival'];
            $firstArrival = $segments['Arrival'];

            $segmentsArr[] = [
                'segment_key' => $segments['segmentKey'] ?? null,
                'departure' => [
                    'code' => $segments['Departure']['AirportCode']['value'] ?? '',
                    'airport' => $segments['Departure']['AirportName']['value'] ?? '',
                    'datetime' => ($segments['Departure']['Date']['value'] ?? '') . 'T' . ($segments['Departure']['Time']['value'] ?? ''),
                    'zuluTime' => null,
                ],
                'arrival' => [
                    'code' => $firstArrival['AirportCode']['value'] ?? '',
                    'airport' => $firstArrival['AirportName']['value'] ?? '',
                    'datetime' => ($firstArrival['Date']['value'] ?? '') . 'T' . ($firstArrival['Time']['value'] ?? ''),
                    'zuluTime' => null,
                ],
                'flight_number' => $segments['flightDetails']['marketingCarrier']['FlightNumber']['value'] ?? '',
                'duration' => $segments['flightDetails']['details']['FlightDuration']['Value']['value'] ?? $segments['duration'] ?? null,
                'aircraft' => $segments['flightDetails']['equipment']['Name']['value'] ?? '',
                'carrier' => $segments['flightDetails']['marketingCarrier']['AirlineID']['value'] ?? 'EK',
                'baggage' => $segments['bundles'][0]['baggageAllowance'] ?? [],
                'isConnected' => $segments['flightDetails']['isConnected'] ?? false,
            ];

            if (!empty($segments['secondFlight'])) {
                $sf = $segments['secondFlight'];
                $segmentsArr[] = [
                    'segment_key' => null,
                    'departure' => [
                        'code' => $sf['departure']['AirportCode']['value'] ?? '',
                        'airport' => $sf['departure']['AirportName']['value'] ?? '',
                        'datetime' => ($sf['departure']['Date']['value'] ?? '') . 'T' . ($sf['departure']['Time']['value'] ?? ''),
                        'zuluTime' => null,
                    ],
                    'arrival' => [
                        'code' => $sf['arrival']['AirportCode']['value'] ?? '',
                        'airport' => $sf['arrival']['AirportName']['value'] ?? '',
                        'datetime' => ($sf['arrival']['Date']['value'] ?? '') . 'T' . ($sf['arrival']['Time']['value'] ?? ''),
                        'zuluTime' => null,
                    ],
                    'flight_number' => $sf['marketingCarrier']['FlightNumber']['value'] ?? '',
                    'duration' => $sf['details']['FlightDuration']['Value']['value'] ?? null,
                    'aircraft' => $sf['equipment']['Name']['value'] ?? '',
                    'carrier' => $sf['marketingCarrier']['AirlineID']['value'] ?? 'EK',
                    'baggage' => $segments['bundles'][0]['baggageAllowance'] ?? [],
                    'isConnected' => $sf['isConnected'] ?? false,
                ];
            }

            return $segmentsArr;
        }

        return $segments;
    }
}