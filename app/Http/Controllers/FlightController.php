<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\SendMail;
use App\Models\BookingId;
use App\Services\PiaService;
use Illuminate\Http\Request;
use App\Services\HelperService;
use App\Services\EmiratesService;
use App\Services\FlyJinnahService;
use Illuminate\Support\Facades\App;
use App\Services\UserBookingService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class FlightController extends Controller
{
    public function __construct(
        protected FlyJinnahService $flyJinnahService,
        protected PiaService $piaService,
        protected EmiratesService $emiratesService,
        protected HelperService $helperService,
        protected UserBookingService $bookingService
    ) {}
    public function search(Request $request)
    {
        try {
            $tax = config('variables.flyjinnah_api.tax') ?? 0;
            $validatedData = $request->only(['arr', 'dest', 'dep', 'return', 'adt', 'chd', 'inf']);
            // $piaFlights = $this->piaService->searchFlights($validatedData);
            // dd($piaFlights);
            $emirateFlights = $this->emiratesService->searchFlights($validatedData);
            // dd($emirateFlights);
            $flyjinnahFlights = $this->flyJinnahService->searchFlights($validatedData);
            // dd($flyjinnahFlights);
            // if ($flights['error']) {
            //     return back()->with('error', $flights['error']);
            // }
            $paxCount = [
                'adt' => $validatedData['adt'] ?? 1,
                'chd' => $validatedData['chd'] ?? 0,
                'inf' => $validatedData['inf'] ?? 0
            ];
            return view('home.flights', [
                'paxCount' => $paxCount,
                'isRoundTrip' => isset($validatedData['return']) ? true : false,
                'data' => $flyjinnahFlights,
                'emirates' => $emirateFlights,
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Flight search failed: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while searching for flights. Please try again.');
        }
    }
    public function getBundles(Request $request) // skip in emirates
    {
        return response()->json(
            $this->flyJinnahService->getFlightDetails($request->only([
                'paxCount', 'firstFlight', 'returnFlight', 
                'firstConnectedFlight', 'returnConnectedFlight'
            ])),
            200
        );
    }
    public function bookingDetails(Request $request)
    {
        $airline = $request->airline ?? '';
        if (empty($airline)) return;
        $data = [];
        $passengerTypes = [
            'adt' => 'Adult',
            'chd' => 'Child',
            'inf' => 'Infant'
        ];
        // dd($request->all());
        if ($airline === 'emirate') {
            $data = [
                'airline' => $airline,
                'logo' => 'emirates.png',
                'paxCount' => $request->paxCount ?? null,
                'firstFlightBundleId' => $request->firstBundleId ?? null,
                'returnFlightBundleId' => $request->secondBundleId ?? null,
                'responseId' => $request->responseId ?? null,
                'passengerTypes' => $passengerTypes,
                'depOfferIds' => $request->depOfferIds ?? null,
                'rtnOfferIds' => $request->rtnOfferIds ?? null,
            ];
            $flightDetails = $this->emiratesService->getBundlePrice([
                'data' => $data ?? null
            ]);
            if (!empty($flightDetails['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $flightDetails['error'],
                    'details' => $flightDetails['details'],
                ], 400);
            }
            $data['flightDetails'] = $flightDetails;
            // dd($data);
            session([
                'data' => $data,
                'totalFare' => $totalFarePrice ?? null
            ]);
            return response()->json([
                'status' => 'success',
                'redirect' => route('flightBooking')
            ], 200);
        }
        // dd('flyjinnah');
        $isDirectBooking = filter_var($request->isDirectBooking, FILTER_VALIDATE_BOOLEAN);
        // dd($isDirectBooking);
        $flightTotalFare = $request->flightTotalFare ?? null;
        $data = [
            'airline' => $airline,
            'logo' => 'Fly_Jinnah_logo.png',
            'paxCount' => $request->paxCount ?? null,
            'segments' => $request->segments ?? null,
            'firstFlightBundleId' => $request->firstBundleId ?? null,
            'returnFlightBundleId' => $request->secondBundleId ?? null,
            'returnFlight' => $request->rtnSelectedFlight ?? null,
            'departureFlight' => $request->depSelectedFlight ?? null,
            'isDirectBooking' => $isDirectBooking,
            'passengerTypes' => $passengerTypes,
        ];
        if ($isDirectBooking) {
            session([
                'data' => $data,
                'totalFare' => $flightTotalFare ?? null
            ]);
            return response()->json([
                'status' => 'success',
                'redirect' => route('flightBooking')
            ], 200);
        };

        // merge bundle with transaction id and now add ancis (required) :)
        $farePrice = $this->flyJinnahService->getBundlePrice([
            'data' => $data ?? null
        ]);
        // dd($farePrice);
        $errorMessage = $farePrice['Body']['OTA_AirPriceRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($farePrice['error'] ?? null);
        if ($errorMessage) {
            $filteredMessage = strtok($errorMessage, '[');
            return response()->json([
                'status' => 'error',
                'message' => $filteredMessage
            ], 400);
        }
        $totalFarePrice = $farePrice['Body']['OTA_AirPriceRS']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['ItinTotalFare'] ?? '';
        // dd($totalFarePrice);
        session([
            'data' => $data,
            'totalFare' => $totalFarePrice ?? null
        ]);
        return response()->json([
            'status' => 'success',
            'redirect' => route('flightBooking')
        ], 200);
    }
    public function booking()
    {
        // dd(session('IdsExpireTime'));
        // dd(session('totalFare', []));
        if (session('data.airline') === 'emirate') {
            return view('home.booking', [
                'data' => session('data', []),
                'totalFare' => session('totalFare', []),
                'tax' => config('variables.flyjinnah_api.tax') ?? 0
            ]);
        }
        return view('home.booking', [
            'data' => session('data', []),
            'totalFare' => session('totalFare', []),
            'tax' => config('variables.flyjinnah_api.tax') ?? 0
        ]);
    }
    public function getSeat(Request $request)
    {
        $seatMap = $this->flyJinnahService->seatMap([
            'data' => $request->data ?? null,
        ]);
        // dd($seatMap);
        $errorMessage = $seatMap['Body']['OTA_AirSeatMapRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($seatMap['error'] ?? null);
        if ($errorMessage) {
            $filteredMessage = strtok($errorMessage, '[');
            return response()->json([
                'status' => 'error',
                'message' => $filteredMessage
            ], 400);
        }
        $seatXml = isset($seatMap['Body']['OTA_AirSeatMapRS']['SeatMapResponses']['SeatMapResponse'][0]) ? $seatMap['Body']['OTA_AirSeatMapRS']['SeatMapResponses']['SeatMapResponse'] : [$seatMap['Body']['OTA_AirSeatMapRS']['SeatMapResponses']['SeatMapResponse']];
        // $seatXml = $seatMap['Body']['OTA_AirSeatMapRS']['SeatMapResponses']['SeatMapResponse'];
        // dd($seatXml);
        foreach ($seatXml as &$item) {
            $item['FlightSegmentInfo']['ArrivalAirport']['City'] = $this->helperService->codeToCountry($item['FlightSegmentInfo']['ArrivalAirport']['@attributes']['LocationCode']) ?? null;
            $item['FlightSegmentInfo']['DepartureAirport']['City'] = $this->helperService->codeToCountry($item['FlightSegmentInfo']['DepartureAirport']['@attributes']['LocationCode']) ?? null;
        }
        return response()->json([
            'status' => 'success',
            'data' => $seatXml
        ], 200);
    }
    public function getMeal(Request $request)
    {
        // return response()->json([
        //     'status' => 'success',
        //     'data' => null
        // ], 200);
        $mealMap = $this->flyJinnahService->mealMap([
            'data' => $request->data ?? null,
        ]);
        // dd($mealMap);
        $errorMessage = $mealMap['Body']['AA_OTA_AirMealDetailsRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($mealMap['error'] ?? null);
        if ($errorMessage) {
            $filteredMessage = strtok($errorMessage, '[');
            return response()->json([
                'status' => 'error',
                'message' => $filteredMessage
            ], 400);
        }
        $mealXml = isset($mealMap['Body']['AA_OTA_AirMealDetailsRS']['MealDetailsResponses']['MealDetailsResponse'][0]) ? $mealMap['Body']['AA_OTA_AirMealDetailsRS']['MealDetailsResponses']['MealDetailsResponse'] : [$mealMap['Body']['AA_OTA_AirMealDetailsRS']['MealDetailsResponses']['MealDetailsResponse']];
        // dd($mealXml);
        foreach ($mealXml as &$item) {
            $item['FlightSegmentInfo']['ArrivalAirport']['City'] = $this->helperService->codeToCountry($item['FlightSegmentInfo']['ArrivalAirport']['@attributes']['LocationCode']) ?? null;
            $item['FlightSegmentInfo']['DepartureAirport']['City'] = $this->helperService->codeToCountry($item['FlightSegmentInfo']['DepartureAirport']['@attributes']['LocationCode']) ?? null;
        }
        return response()->json([
            'status' => 'success',
            'data' => $mealXml
        ], 200);
    }
    public function getBaggage(Request $request)
    {
        $baggageMap = $this->flyJinnahService->baggageMap([
            'data' => $request->data ?? null,
        ]);
        // dd($baggageMap);
        $errorMessage = $baggageMap['Body']['AA_OTA_AirBaggageDetailsRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($baggageMap['error'] ?? null);
        if ($errorMessage) {
            $filteredMessage = strtok($errorMessage, '[');
            return response()->json([
                'status' => 'error',
                'message' => $filteredMessage
            ], 400);
        }
        $baggageXml = isset($baggageMap['Body']['AA_OTA_AirBaggageDetailsRS']['BaggageDetailsResponses']['OnDBaggageDetailsResponse'][0]) ? $baggageMap['Body']['AA_OTA_AirBaggageDetailsRS']['BaggageDetailsResponses']['OnDBaggageDetailsResponse'] : [$baggageMap['Body']['AA_OTA_AirBaggageDetailsRS']['BaggageDetailsResponses']['OnDBaggageDetailsResponse']];
        // dd($baggageXml);
        foreach ($baggageXml as &$item) { 
            $loop = isset($item['OnDFlightSegmentInfo'][0]) ? $item['OnDFlightSegmentInfo'] : [&$item['OnDFlightSegmentInfo']]; // Ensure it's always an array

            foreach ($loop as &$value) { // Reference to modify the array
                if (isset($value['ArrivalAirport']['@attributes']['LocationCode'])) continue;
                if (isset($value['DepartureAirport']['@attributes']['LocationCode'])) continue;
                $value['ArrivalAirport']['City'] = $this->helperService->codeToCountry($value['ArrivalAirport']['@attributes']['LocationCode']) ?? null;
                $value['DepartureAirport']['City'] = $this->helperService->codeToCountry($value['DepartureAirport']['@attributes']['LocationCode']) ?? null;
            }
        }
        unset($item);
        unset($value);   
        // dd($baggageXml);
        return response()->json([
            'status' => 'success',
            'data' => $baggageXml
        ], 200);
    }
    public function getFinalPrice(Request $request)
    {
        // dd($request->all());
        $data = [
            'data' => $request->data ?? null,
            'meals' => $request->meals ?? null,
            'baggages' => $request->baggages ?? null,
            'seats' => $request->seats ?? null
        ];
        $finalPrice = $this->flyJinnahService->finalPrice($data);
        // dd($finalPrice);
        $errorMessage = $finalPrice['Body']['OTA_AirPriceRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($finalPrice['error'] ?? null);
        if ($errorMessage) {
            $filteredMessage = strtok($errorMessage, '[');
            return response()->json([
                'status' => 'error',
                'message' => $filteredMessage
            ], 400);
        }
        $totalFarePrice = $finalPrice['Body']['OTA_AirPriceRS']['PricedItineraries']['PricedItinerary']['AirItineraryPricingInfo']['ItinTotalFare'] ?? '';
        // dd($totalFarePrice);
        return response()->json([
            'status' => 'success',
            'data' => $totalFarePrice
        ], 200);
    }
    public function payment(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'payment is in progress',
        ], 200);
    }
    public function bookFlight(Request $request)
    {
        $airline = $request->airline ?? '';
        // dd($request->all());
        if ($airline === 'emirate') {
            $passengers = $request->passengers ?? null;
            $data = [
                'user' => $request->user ?? null,
                'paymentOnHold' => $request->paymentOnHold ?? null,
                'offerIds' => $request->offerIds ?? null,
                'bundleId' => $request->bundleId ?? null,
                'responseId' => $request->responseId ?? null,
                'paxCount' => $request->paxCount ?? null,
                'passengers' => $passengers,
            ];
            $bookFlight = $this->emiratesService->bookFlight($data ?? null);
            // dd($bookFlight);
            if (!empty($bookFlight['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $bookFlight['error'],
                    'details' => $bookFlight['details'],
                ], 400);
            }
            $bookingRefID = $bookFlight['bundle']['bookingReferences']['bookingId'] ?? null;
            $ticketMsg = 'Booking is OnHold';
            // dd($bookFlight['bundle']['timeLimits']);
            $userData = [
                'user' => $request->user,
                'bookingRefID' => $bookingRefID,
                'ticketStatusMsg' => $ticketMsg,
                'ticketLimit' => $bookFlight['bundle']['timeLimits']['ticketingTimeLimit'] ?? null,
                'paymentLimit' => $bookFlight['bundle']['timeLimits']['paymentTimeLimit'] ?? null,
                'airlineIds' => $bookFlight['bundle']['bookingReferences']['airlineID'] ?? null,
                'airline' => $bookFlight['bundle']['bookingReferences']['airline'] ?? null,
            ];
            $userDetails = [];
            if (App::environment('local')) {
                $cacheKey = 'useremi';
                $userDetails = Cache::remember($cacheKey, now()->addHours(30), function () use ($userData) {
                    return $this->bookingService->createUser($userData);
                });
            } elseif (App::environment('production')) {
                $userDetails = $this->bookingService->createUser($userData);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Success! Your flight is booked. Safe travels!.',
                'data' => $bookFlight,
                'passengers' => $passengers,
                'bookingRefID' => $bookingRefID,
                'ticketMsg' => $ticketMsg,
                'userDetails' => ['name' => $userDetails['user']['name'], 'email' => $userDetails['user']['email']],
                'totalPrice' => $bookFlight['bundle']['totalPrice'] ?? [],
                'emailStatus' => $userDetails['emailMessage'] ?? 'Failed to send email'
            ], 200);
        } elseif ($airline === 'flyjinnah') {
            $data = [
                'data' => $request->data ?? null,
                'user' => $request->user ?? null,
                'passengers' => $request->passengers ?? null,
                'paymentOnHold' => $request->paymentOnHold ?? null,
                'finalPriceTag' => $request->finalPriceTag ?? null
            ];
            // dd($data);
            $bookingResponse = $this->flyJinnahService->bookFlight([
                'data' => $data ?? null
            ]);
            // dd($booking);
            $errorMessage = $bookingResponse['Body']['OTA_AirBookRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($booking['error'] ?? null);
            if ($errorMessage) {
                return response()->json([
                    'status' => 'error',
                    'message' => strtok($errorMessage, '[')
                ], 400);
            }
            $booking = $bookingResponse['Body']['OTA_AirBookRS']['AirReservation'];
            $bookingData = $booking['TravelerInfo'] ?? [];
            $bookingRefID = $booking['BookingReferenceID']['@attributes']['ID'] ?? '--';
            $paxPriceArray = $booking['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'] ?? [];
            $ticketMsg = $booking['Ticketing'] ?? [];
            $userData = [
                'user' => $request->user,
                'bookingRefID' => $bookingRefID,
                'ticketStatusMsg' => $ticketMsg['TicketAdvisory'],
                'ticketLimit' => null,
                'paymentLimit' => null,
                'airlineIds' => null,
                'airline' => 'flyjinnah',
            ];
            $userDetails = $this->bookingService->createUser($userData);
    
            $data = [];
            $airTravelers = isset($bookingData['AirTraveler'][0]) ? $bookingData['AirTraveler'] : [$bookingData['AirTraveler'] ?? []];
    
            foreach ($airTravelers as $item) {
                if (empty($item)) continue;
                $eTicketInfo = [];
                $ticketArray = $item['ETicketInfo']['ETicketInfomation'] ?? [];
    
                if (isset($ticketArray[0])) {
                    foreach ($ticketArray as $ticket) {
                        $eTicketInfo[] = [
                            'couponNo' => $ticket['@attributes']['couponNo'] ?? '',
                            'eTicketNo' => $ticket['@attributes']['eTicketNo'] ?? '',
                            'flightSegmentCode' => str_replace('/', ' to ', $ticket['@attributes']['flightSegmentCode'] ?? ''),
                            'usedStatus' => $ticket['@attributes']['usedStatus'] ?? '',
                        ];
                    }
                } elseif (!empty($ticketArray)) {
                    $eTicketInfo[] = [
                        'couponNo' => $ticketArray['@attributes']['couponNo'] ?? '',
                        'eTicketNo' => $ticketArray['@attributes']['eTicketNo'] ?? '',
                        'flightSegmentCode' => str_replace('/', ' to ', $ticketArray['@attributes']['flightSegmentCode'] ?? ''),
                        'usedStatus' => $ticketArray['@attributes']['usedStatus'] ?? '',
                    ];
                }
                $data[] = [
                    'name' => $item['PersonName']['GivenName'] ?? 'Unknown',
                    'surName' => $item['PersonName']['Surname'] ?? 'Unknown',
                    // 'phoneNumber' => $item['Telephone']['@attributes']['PhoneNumber'] ?? 'Unknown',
                    'type' => $item['@attributes']['PassengerTypeCode'] ?? 'Unknown',
                    'travelerRefNumber' => $item['TravelerRefNumber']['@attributes']['RPH'] ?? 'Unknown',
                    'eTicketInfo' => $eTicketInfo
                ];
            }
            $paxPricingArray = is_array($paxPriceArray) && isset($paxPriceArray[0]) ? $paxPriceArray : [$paxPriceArray];
            $paxPricing = [];
            foreach ($paxPricingArray as $pax) {
                if (empty($pax)) continue;
                $paxPricing[] = [
                    'code' => $pax['PassengerTypeQuantity']['@attributes']['Code'] ?? '-',
                    'price' => $pax['PassengerFare']['TotalFare']['@attributes'] ?? '-',
                    'travelerRefNumber' => $pax['TravelerRefNumber']['@attributes'] ?? '-',
                ];
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Success! Your flight is booked. Safe travels!.',
                'data' => $data,
                'bookingRefID' => $bookingRefID,
                'ticketMsg' => $ticketMsg,
                'userDetails' => ['name' => $userDetails['user']['name'], 'email' => $userDetails['user']['email']],
                'paxPricing' => $paxPricing,
                'totalPrice' => $booking['PriceInfo']['ItinTotalFare']['TotalFare']['@attributes'] ?? '--',
                'emailStatus' => $userDetails['emailMessage']
            ], 200);
        }
    }
}