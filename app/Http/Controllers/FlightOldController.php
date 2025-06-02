<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FlyJinnahService;

class FlightOldController extends Controller
{
    protected $flyJinnahService;

    public function __construct(FlyJinnahService $flyJinnahService)
    {
        $this->flyJinnahService = $flyJinnahService;
    }
    public function search(Request $request)
    {
        // $perPage = $request->input('per_page', 10);
        // $sortOption = $request->input('sort_option', 'asc');
        // dd($request->input('arr'));
        try {
            // $validatedData = $request->validate([
            //     'from' => 'required|string',
            //     'to' => 'required|string',
            //     'departureDate' => 'required|date',
            //     'returnDate' => 'nullable|date',
            //     'adult' => 'nullable|integer',
            //     'child' => 'nullable|integer|min:0',
            //     'infant' => 'nullable|integer|min:0',
            // ]);
    
            $flights = $this->flyJinnahService->searchFlights([
                // $validatedData['from'],
                // $validatedData['to'],
                // $validatedData['departureDate'],
                // $validatedData['returnDate'] ?? null,
                // $validatedData['adult'] ?? 1,
                // $validatedData['child'] ?? 0,
                // $validatedData['infant'] ?? 0
                'arr' => $request->input('arr'),
                'dest' => $request->input('dest'),
                'dep' => $request->input('dep'),
                'return' => $request->input('return'),
                'adt' => $request->input('adt'),
                'chd' => $request->input('chd'),
                'inf' => $request->input('inf'),
            ]);
            $paxCount = [
                'adt' => $validatedData['adult'] ?? 1,
                'chd' => $validatedData['child'] ?? 0,
                'inf' => $validatedData['infant'] ?? 0
            ];
            // dd($flights);

            $flightsData = $flights['ondWiseFlightCombinations'] ?? [];
            // dd($flightsData);
            $isRoundTrip = count($flightsData) > 1 ? true : false;
            $data = [];
            foreach ($flightsData as $route => $flightData) {
                if (!isset($flightData['dateWiseFlightCombinations']) || !is_array($flightData['dateWiseFlightCombinations'])) {
                    continue;
                }

                foreach ($flightData['dateWiseFlightCombinations'] as $date => $details) {
                    foreach ($details['flightOptions'] as &$option) {
                        $firstFlightData = $this->getFlightData($option['flightSegments'][0]);
                        $connectedFlightData = $this->getFlightData($option['flightSegments'][1] ?? null);
                        // $option['bundles'] = $this->flyJinnahService->getFlightDetails($paxCount, $firstFlightData, null, $connectedFlightData, null);
                        $option['bundles'] = [];
                        // dd($option['flightSegments'][0]);
                        // foreach ($option['flightSegments'] as &$segment) {
                        //     dd($bundles);
                        // }
                    }
                    $data[] = [
                        'route' => $route,
                        'date' => $date,
                        'flights' => $details['flightOptions']
                    ];
                }
            }
            // dd($data);
            if (empty($data)) {
                return redirect()->back()->with('error', 'No flights found for the given criteria. Or something else');
            }
            // $bundles = $this->flyJinnahService->getFlightDetails( $paxCount, '$firstFlightData', '$returnFlightData', '$connectedFlightData', '$returnConnectedFlightData');
            // dd($bundles);


            // $routes = array_keys($flightsData);

            // $flight1 = $routes[0] ?? null;
            // $flight2 = $routes[1] ?? null;
            
            // $flight1Data = $flight1 ? ($flightsData[$flight1]['dateWiseFlightCombinations'] ?? null) : null;
            // $flight2Data = $flight2 ? ($flightsData[$flight2]['dateWiseFlightCombinations'] ?? null) : null;
            return view('flights', compact('paxCount', 'data', 'isRoundTrip'));
        } catch (\Exception $e) {
            \Log::error('Flight search failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while searching for flights. Please try again.');
        }
    }
    public function bookingPage(Request $request)
    {
        // dd($request->all());
        // $data = [
        //     'paxCount' => $request->paxCount ?? null,
        //     'firstFlightData' => $request->firstFlightData ?? null,
        //     'firstFlightBundleId' => $request->firstFlightBundleId ?? null,
        //     'returnFlightBundleId' => $request->returnFlightBundleId ?? null,
        //     'returnFlightData' => $request->returnFlightData ?? null,
        //     'connectedFlightData' => $request->connectedFlightData ?? null,
        //     'returnConnectedFlightData' => $request->returnConnectedFlightData ?? null,
        // ];
        $data = [
            'paxCount' => $request->paxCount ?? null,
            'segments' => $request->segments ?? null,
            'firstFlightBundleId' => $request->firstBundleId ?? null,
            'returnFlightBundleId' => $request->secondBundleId ?? null,
        ];
        session([
            'data' => $data
        ]);
        return response()->json([
            'redirect' => route('bookingPage')
        ]);
    }
    public function booking()
    {
        return view('booking', [
            'data' => session('data', [])
        ]);
    }
    public function bookFlight(Request $request)
    {
        $booking = $this->flyJinnahService->bookFlight([
            'data' => $request->data ?? null,
            'passengers' => $request->passengers ?? null,
        ]);
        $errorMessage = $booking['Body']['OTA_AirBookRS']['Errors']['Error']['@attributes']['ShortText'] ?? null;
        if ($errorMessage) {
            $filteredMessage = strtok($errorMessage, '[');
            return response()->json([
                'status' => 'error',
                'message' => trim($errorMessage)
            ], 400);
        }
        $bookingData = $booking['Body']['OTA_AirBookRS']['AirReservation']['TravelerInfo'] ?? 'Unknown TravelerInfo';
        $bookingRefID = $booking['Body']['OTA_AirBookRS']['AirReservation']['BookingReferenceID']['@attributes']['ID'] ?? 'Unknown bookingRefID';
        $data = [];

        $airTravelers = isset($bookingData['AirTraveler'][0]) ? $bookingData['AirTraveler'] : [$bookingData['AirTraveler']];

        foreach ($airTravelers as $item) {
            $data[] = [
                'name' => $item['PersonName']['GivenName'] ?? 'Unknown',
                'surName' => $item['PersonName']['Surname'] ?? 'Unknown',
                'phoneNumber' => $item['Telephone']['@attributes']['PhoneNumber'] ?? 'Unknown',
                'type' => $item['@attributes']['PassengerTypeCode'] ?? 'Unknown',
                'travelerRefNumber' => $item['TravelerRefNumber']['@attributes']['RPH'] ?? 'Unknown',
            ];
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Flight booking successful.',
            'data' => $data,
            'bookingRefID' => $bookingRefID
        ], 200);
    }
    public function getBundles(Request $request)
    {
        return response()->json(
            $this->flyJinnahService->getFlightDetails($request->only([
                'paxCount', 'firstFlight', 'returnFlight', 
                'firstConnectedFlight', 'returnConnectedFlight'
            ])), 
            200
        );
    }
    private function getFlightData($data)
    {
        if (!$data) return null;
        return [
            'departure' => $data['departureDateTimeLocal'],
            'arrival' => $data['arrivalDateTimeLocal'],
            'origin' => $data['origin'],
            'destination' => $data['destination'],
            'flightNumber' => $data['flightNumber']
        ];
    }
}
