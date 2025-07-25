<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Mail\SendMail;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\ErrorLog;
use App\Models\BookingId;
use App\Services\PiaService;
use Illuminate\Http\Request;
use App\Models\CancelResponse;
use App\Services\HelperService;
use App\Services\EmiratesService;
use App\Services\FlyJinnahService;
use Illuminate\Support\Facades\App;
use App\Services\UserBookingService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Services\FlightBookingService;

class FlightController extends Controller
{
    // Y Economy
    // W Premium Economy
    // C Business
    // J Business
    // P First
    // F First
    public function __construct(
        protected FlyJinnahService $flyJinnahService,
        protected PiaService $piaService,
        protected EmiratesService $emiratesService,
        protected HelperService $helperService,
        protected UserBookingService $bookingService
    ) {}
    public function search(Request $request)
    {
        // try {
            $tax = config('variables.flyjinnah_api.tax') ?? 0;
            $validatedData = $request->validate([
                'arr' => 'required|string',
                'dest' => 'required|string',
                'dep' => 'required|date',
                'return' => 'nullable|date',
                'cabinClass' => 'nullable|string|in:Y,W,C,J,P,F',
                'adt' => 'required|numeric',
                'chd' => 'nullable|numeric',
                'inf' => 'nullable|numeric',
            ]);
            session(['cabinClass' => $validatedData['cabinClass']]);
            // dd($validatedData);
            // $validatedData = $request->only(['arr', 'dest', 'dep', 'return', 'cabinClass','adt', 'chd', 'inf']);
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
    
        // } catch (\Exception $e) {
        //     \Log::error('Flight search failed: ' . $e->getMessage());
        //     return back()->with('error', 'An error occurred while searching for flights. Please try again.');
        // }
    }
    public function getBundles(Request $request) // skip in emirates
    {
        return response()->json(
            $this->flyJinnahService->getFlightDetails($request->only([
                'paxCount', 'firstFlight', 'returnFlight', 
                'firstConnectedFlight', 'returnConnectedFlight'
            ])), 200
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
            // dd($data['flightDetails']);
            $totalFarePrice = $flightDetails['bundle']['totalPrice'] ?? null;
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
        return response()->json(['status' => 'success', 'message' => 'payment is in progress']);
        // return response()->json(['status' => 'error', 'message' => 'payment error'], 400);
    }
    public function bookFlight(Request $request)
    {
        $airline = $request->airline ?? '';
        // dd($request->all(), session('data'), session('totalFare'));
        if ($airline === 'emirate') {
            $cabinClass = session('cabinClass') ?? 'Y';
            $data = [
                'user' => $request->user ?? null,
                'paymentOnHold' => $request->paymentOnHold ?? null,
                'offerIds' => $request->offerIds ?? null,
                'bundleId' => $request->bundleId ?? null,
                'responseId' => $request->responseId ?? null,
                'paxCount' => $request->paxCount ?? null,
                'passengers' => $request->passengers ?? null,
            ];
            $bookFlight = $this->emiratesService->bookFlight($data ?? null);
            // dd($bookFlight);
            if (!empty($bookFlight['error'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $bookFlight['error'],
                    'details' => $bookFlight['details'] ?? null,
                ], 400);
            }

            // 1. Create User
            $client = $this->bookingService->createUser($request->user);

            // 2. Create Passengers
            $passengers = $this->bookingService->createPassengers($request->passengers, $client->id);

            // 3. Create Flights and Segments
            $flights = app(FlightBookingService::class)->handleEmiratesBooking($bookFlight, $client->id, $cabinClass);
            // dd($bookFlight);
            return response()->json($flights);
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
    // book flight wala btn create data kryga sirf phr admin approve kryga booking
    // public function confirmBooking(Request $request)
    // {
    //     $airline = $request->airline ?? '';
    //     // dd($request->all(), session('data'), session('totalFare'));
    //     if ($airline === 'emirate') {
    //         $passengers = $request->passengers ?? null;
    //         $data = [
    //             'user' => $request->user ?? null,
    //             'paymentOnHold' => $request->paymentOnHold ?? null,
    //             'offerIds' => $request->offerIds ?? null,
    //             'bundleId' => $request->bundleId ?? null,
    //             'responseId' => $request->responseId ?? null,
    //             'paxCount' => $request->paxCount ?? null,
    //             'passengers' => $passengers,
    //         ];
    //         $bookFlight = $this->emiratesService->bookFlight($data ?? null);
    //         // dd($bookFlight);
    //         if (!empty($bookFlight['error'])) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => $bookFlight['error'],
    //                 'details' => $bookFlight['details'],
    //             ], 400);
    //         }
    //         $bookingRefID = $bookFlight['bundle']['bookingReferences']['bookingId'] ?? null;
    //         $ticketMsg = 'Booking is OnHold';
    //         // dd($bookFlight['bundle']['timeLimits']);
    //         $userData = [
    //             'user' => $request->user,
    //             'bookingRefID' => $bookingRefID,
    //             'ticketStatusMsg' => $ticketMsg,
    //             'ticketLimit' => $bookFlight['bundle']['timeLimits']['ticketingTimeLimit'] ?? null,
    //             'paymentLimit' => $bookFlight['bundle']['timeLimits']['paymentTimeLimit'] ?? null,
    //             'airlineIds' => $bookFlight['bundle']['bookingReferences']['airlineID'] ?? null,
    //             'airline' => $bookFlight['bundle']['bookingReferences']['airline'] ?? null,
    //         ];
    //         $userDetails = [];
    //         if (App::environment('local')) {
    //             $cacheKey = 'useremi';
    //             $userDetails = Cache::remember($cacheKey, now()->addHours(30), function () use ($userData) {
    //                 return $this->bookingService->createUser($userData);
    //             });
    //         } elseif (App::environment('production')) {
    //             $userDetails = $this->bookingService->createUser($userData);
    //         }
    //         // $flightDetails = [
    //         //     'paxCount' => $request->paxCount ?? null,
    //         //     'ticketLimit' => $bookFlight['bundle']['timeLimits']['ticketingTimeLimit'] ?? null,
    //         //     'paymentLimit' => $bookFlight['bundle']['timeLimits']['paymentTimeLimit'] ?? null,
    //         //     'airline' => $airline,
    //         // ];
    //         // $userDetails = $this->bookingService->createUser($request->user);
    //         // $userPassengers = $this->bookingService->createPassengers($passengers, $userDetails->id);
    //         // $userFlight = $this->bookingService->createFlight($passengers, $userDetails->id);
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Success! Your flight is booked. Safe travels!.',
    //             'data' => $bookFlight,
    //             'passengers' => $passengers,
    //             'bookingRefID' => $bookingRefID,
    //             'ticketMsg' => $ticketMsg,
    //             'userDetails' => ['name' => $userDetails['user']['name'], 'email' => $userDetails['user']['email']],
    //             'totalPrice' => $bookFlight['bundle']['totalPrice'] ?? [],
    //             'emailStatus' => $userDetails['emailMessage'] ?? 'Failed to send email'
    //         ], 200);
    //     } elseif ($airline === 'flyjinnah') {
    //         $data = [
    //             'data' => $request->data ?? null,
    //             'user' => $request->user ?? null,
    //             'passengers' => $request->passengers ?? null,
    //             'paymentOnHold' => $request->paymentOnHold ?? null,
    //             'finalPriceTag' => $request->finalPriceTag ?? null
    //         ];
    //         // dd($data);
    //         $bookingResponse = $this->flyJinnahService->bookFlight([
    //             'data' => $data ?? null
    //         ]);
    //         // dd($booking);
    //         $errorMessage = $bookingResponse['Body']['OTA_AirBookRS']['Errors']['Error']['@attributes']['ShortText'] ?? ($booking['error'] ?? null);
    //         if ($errorMessage) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => strtok($errorMessage, '[')
    //             ], 400);
    //         }
    //         $booking = $bookingResponse['Body']['OTA_AirBookRS']['AirReservation'];
    //         $bookingData = $booking['TravelerInfo'] ?? [];
    //         $bookingRefID = $booking['BookingReferenceID']['@attributes']['ID'] ?? '--';
    //         $paxPriceArray = $booking['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'] ?? [];
    //         $ticketMsg = $booking['Ticketing'] ?? [];
    //         $userData = [
    //             'user' => $request->user,
    //             'bookingRefID' => $bookingRefID,
    //             'ticketStatusMsg' => $ticketMsg['TicketAdvisory'],
    //             'ticketLimit' => null,
    //             'paymentLimit' => null,
    //             'airlineIds' => null,
    //             'airline' => 'flyjinnah',
    //         ];
    //         $userDetails = $this->bookingService->createUser($userData);
    
    //         $data = [];
    //         $airTravelers = isset($bookingData['AirTraveler'][0]) ? $bookingData['AirTraveler'] : [$bookingData['AirTraveler'] ?? []];
    
    //         foreach ($airTravelers as $item) {
    //             if (empty($item)) continue;
    //             $eTicketInfo = [];
    //             $ticketArray = $item['ETicketInfo']['ETicketInfomation'] ?? [];
    
    //             if (isset($ticketArray[0])) {
    //                 foreach ($ticketArray as $ticket) {
    //                     $eTicketInfo[] = [
    //                         'couponNo' => $ticket['@attributes']['couponNo'] ?? '',
    //                         'eTicketNo' => $ticket['@attributes']['eTicketNo'] ?? '',
    //                         'flightSegmentCode' => str_replace('/', ' to ', $ticket['@attributes']['flightSegmentCode'] ?? ''),
    //                         'usedStatus' => $ticket['@attributes']['usedStatus'] ?? '',
    //                     ];
    //                 }
    //             } elseif (!empty($ticketArray)) {
    //                 $eTicketInfo[] = [
    //                     'couponNo' => $ticketArray['@attributes']['couponNo'] ?? '',
    //                     'eTicketNo' => $ticketArray['@attributes']['eTicketNo'] ?? '',
    //                     'flightSegmentCode' => str_replace('/', ' to ', $ticketArray['@attributes']['flightSegmentCode'] ?? ''),
    //                     'usedStatus' => $ticketArray['@attributes']['usedStatus'] ?? '',
    //                 ];
    //             }
    //             $data[] = [
    //                 'name' => $item['PersonName']['GivenName'] ?? 'Unknown',
    //                 'surName' => $item['PersonName']['Surname'] ?? 'Unknown',
    //                 // 'phoneNumber' => $item['Telephone']['@attributes']['PhoneNumber'] ?? 'Unknown',
    //                 'type' => $item['@attributes']['PassengerTypeCode'] ?? 'Unknown',
    //                 'travelerRefNumber' => $item['TravelerRefNumber']['@attributes']['RPH'] ?? 'Unknown',
    //                 'eTicketInfo' => $eTicketInfo
    //             ];
    //         }
    //         $paxPricingArray = is_array($paxPriceArray) && isset($paxPriceArray[0]) ? $paxPriceArray : [$paxPriceArray];
    //         $paxPricing = [];
    //         foreach ($paxPricingArray as $pax) {
    //             if (empty($pax)) continue;
    //             $paxPricing[] = [
    //                 'code' => $pax['PassengerTypeQuantity']['@attributes']['Code'] ?? '-',
    //                 'price' => $pax['PassengerFare']['TotalFare']['@attributes'] ?? '-',
    //                 'travelerRefNumber' => $pax['TravelerRefNumber']['@attributes'] ?? '-',
    //             ];
    //         }
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Success! Your flight is booked. Safe travels!.',
    //             'data' => $data,
    //             'bookingRefID' => $bookingRefID,
    //             'ticketMsg' => $ticketMsg,
    //             'userDetails' => ['name' => $userDetails['user']['name'], 'email' => $userDetails['user']['email']],
    //             'paxPricing' => $paxPricing,
    //             'totalPrice' => $booking['PriceInfo']['ItinTotalFare']['TotalFare']['@attributes'] ?? '--',
    //             'emailStatus' => $userDetails['emailMessage']
    //         ], 200);
    //     }
    // }
    public function fetchDetails(Request $request) // OrderRetrieveRQ
    {
        $validatedData = $request->validate([
            'bookingId' => 'required|exists:bookings,id',
            'clientId' => 'required|exists:clients,id',
        ]);
        $booking = Booking::with('bookingRequest')->find($validatedData['bookingId']);
        if (!$booking) return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        if ($booking->client_id !== (int)$validatedData['clientId']) return response()->json(['status' => 'error', 'message' => 'Client does not match this booking.'], 403);

        $airline = strtolower($booking->airline);
        if ($airline === 'emirates') {
            $orderRetrieve = $this->emiratesService->orderRetrieve([
                'amount'  => $booking->price,
                'code'    => $booking->price_code,
                'orderId' => $booking->order_id,
            ]);

            if (!empty($orderRetrieve['error'])) {
                return response()->json([
                    'status'  => 'error',
                    'message' => $orderRetrieve['error'],
                    'details' => $orderRetrieve['details'] ?? null,
                ], 400);
            }
            // Use the robust extractor
            $latest = $this->extractTotalPriceAndCode($orderRetrieve, (float) $booking->price, $booking->price_code);
            $skipComparison = $latest['price_code'] === null || $latest['price'] <= 0;

            if ($skipComparison) {
                // You can decide to not update booking at all in this case
                return response()->json([
                    'status'   => 'success',
                    'message'  => 'Fetched latest order details. Could not find a valid total price in the response.',
                    'note'     => 'Price comparison skipped due to missing/invalid amounts in OrderRetrieve response.',
                    'latest'   => $latest,
                    'data'     => $orderRetrieve,
                ]);
            }

            // Compare
            $comparison = $this->buildPriceComparison(
                (float) $booking->price,
                $booking->price_code,
                (float) $latest['price'],
                $latest['price_code']
            );
            $updatedBooking = app(FlightBookingService::class)->updateBookingFieldsFromResponse($orderRetrieve, $booking->id);
            return response()->json([
                'status'      => 'success',
                'message'     => 'Fetched latest order details.',
                'price_source'=> $latest['source'],
                'comparison'  => $comparison,
                'booking_old' => [
                    'price'      => (float) $booking->price,
                    'price_code' => $booking->price_code,
                ],
                'booking_new' => [
                    'price'      => (float) $updatedBooking->price,
                    'price_code' => $updatedBooking->price_code,
                ],
            ]);
        }
        return response()->json(['status' => 'error', 'message' => 'Airline Missing!.'], 401);
    }
    public function confirmBooking(Request $request)
    {
        $validatedData = $request->validate([
            'bookingId' => 'required|exists:bookings,id',
            'clientId' => 'required|exists:clients,id',
        ]);
        $booking = Booking::with('bookingRequest', 'payment')->find($validatedData['bookingId']);
        if (!$booking) return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        if ($booking->client_id !== (int)$validatedData['clientId']) return response()->json(['status' => 'error', 'message' => 'Client does not match this booking.'], 403);

        // ✅ Check if payment exists
        // if (!$booking->payment) return response()->json(['status' => 'error', 'message' => 'No payment found for this booking.'], 400);

        $airline = strtolower($booking->airline);

        if ($airline === 'emirates') {
            $orderChange = $this->emiratesService->orderChange([
                'amount' => $booking->price,
                'code' => $booking->price_code,
                'orderId' => $booking->order_id,
            ]);
            if (!empty($orderChange['error'])) {
                $details = $orderChange['details'] ?? [];
                if (is_string($details)) {
                    $details = json_decode($details, true) ?? [];
                }
                // $messages = collect($details)->pluck('value')->filter()->values()->all();
                $messages = collect($details)
                    ->map(function ($item) {
                        $value = is_array($item) && isset($item['value']) ? $item['value'] : $item;
                        return is_string($value) ? trim($value) : null;
                    })
                    ->filter()->values()->all();
                $alreadyTicketed = collect($messages)->contains(function ($message) {
                    return str_contains(strtolower($message), 'already ticketed');
                });
                // Only set status to ERROR if it's not already ticketed
                if (!$alreadyTicketed) {
                    $booking->update(['status' => Booking::STATUS_ERROR]);
                    ErrorLog::create([
                        'client_id' => $booking->client_id,
                        'booking_id' => $booking->id,
                        'error_type' => 'ticketing',
                        'error_message' => json_encode($messages),
                        'details' => json_encode($details),
                    ]);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => $alreadyTicketed
                        ? 'This flight was already ticketed.'
                        : 'Flight booking failed.',
                    'code' => $alreadyTicketed ? 409 : 400,
                    'details' => $messages,
                ], $alreadyTicketed ? 409 : 400);
            }
            $ticketTimeLimit = $orderChange['bundle']['timeLimits']['ticketingTimeLimit'] ?? $booking->ticket_limit;
            $paymentTimeLimit = $orderChange['bundle']['timeLimits']['paymentTimeLimit'] ?? $booking->payment_limit;
            // ----------------------------------------- Update Booking -----------------------------------------
            $booking->update([
                'status' => Booking::STATUS_ISSUED,
                'only_search' => false,
                'ticket_limit' => $ticketTimeLimit,
                'payment_limit' => $paymentTimeLimit,
            ]);
            // ----------------------------------------- Update Booking Request Body -----------------------------------------
            if ($booking->bookingRequest) {
                $booking->bookingRequest()->update([
                    'status' => 'change',
                    'ticket_limit' => $ticketTimeLimit,
                    'payment_limit' => $paymentTimeLimit,
                    'xml_body' => json_encode($orderChange ?? []),
                ]);
            }
            // ----------------------------------------- Create Tickets -----------------------------------------
            $tickets = [];
            foreach ($orderChange['ticketInfos'] ?? [] as $ticketInfo) {
                $tickets[] = [
                    'airline' => $ticketInfo['issuingAirlineInfo']['airline'] ?? null,
                    'passenger_reference' => $ticketInfo['passengerReference'] ?? null,
                    'place' => $ticketInfo['issuingAirlineInfo']['place'] ?? null,
                    'ticket_no' => $ticketInfo['ticketDocument']['ticketDocNbr'] ?? null,
                    'type' => $ticketInfo['ticketDocument']['type'] ?? null,
                    'issue_date' => isset($ticketInfo['ticketDocument']['dateOfIssue'], $ticketInfo['ticketDocument']['timeOfIssue'])
                        ? Carbon::parse($ticketInfo['ticketDocument']['dateOfIssue'] . ' ' . $ticketInfo['ticketDocument']['timeOfIssue'])
                        : now(),
                    'price_code' => $ticketInfo['price']['total']['code'] ?? null,
                    'price' => $ticketInfo['price']['total']['value'] ?? null,
                    'price_reference' => $ticketInfo['price']['refs'] ?? null,
                    'ticket_details' => json_encode($ticketInfo),
                    'client_id' => $booking->client_id,
                    'booking_id' => $booking->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($tickets)) Ticket::insert($tickets);
            $booking->load('bookingItems.penalties', 'client', 'tickets');
            return response()->json(['status' => 'success', 'message' => 'Success! Your flight is booked. Safe travels!.', 'booking' => $booking]);
        }
        return response()->json(['status' => 'missing_airline', 'message' => 'Airline not supported'], 400);
    }
    public function orderCancel(Request $request) // OrderCancelRQ
    {
        $validatedData = $request->validate([
            'bookingId' => 'required|exists:bookings,id',
            'clientId' => 'required|exists:clients,id',
        ]);
        $booking = Booking::with('tickets')->find($validatedData['bookingId']);
        if (!$booking) return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
        if ($booking->client_id !== (int)$validatedData['clientId']) return response()->json(['status' => 'error', 'message' => 'Client does not match this booking.'], 403);

        $airline = strtolower($booking->airline);
        if ($airline === 'emirates') {
            $data = [
                'owner' => $booking->order_owner ?? null,
                'orderId' => $booking->order_id ?? null,
            ];
            $orderCancel = $this->emiratesService->orderCancel($data ?? []);
            if (!empty($orderCancel['error'])) {
                $booking->update(['status' => Booking::STATUS_ERROR]);
                ErrorLog::create([
                    'client_id' => $booking->client_id,
                    'booking_id' => $booking->id,
                    'error_type' => 'cancellation',
                    'error_message' => 'Error Cancel Order',
                    'details' => json_encode($orderCancel),
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => $orderCancel['error'] ?? 'Error Cancel Order',
                    'details' => $orderCancel['details'] ?? '',
                ], 400);
            }
            if (!empty($orderCancel['warnings'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order already cancelled',
                    'details' => $orderCancel['warnings']['details'] ?? 'Cannot perform cancel - Order already cancelled',
                ], 400);
            }
            $booking->update(['status' => Booking::STATUS_CANCEL]);
            $booking->tickets()->update(['status' => 'cancel']);
            CancelResponse::create([
                'xml_body' => json_encode($orderCancel),
                'booking_id' => $booking->id
            ]);
            return response()->json(['status' => 'success', 'message' => 'Success! Flight cancelled successfully!.', 'data' => $orderCancel]);
        }
        return response()->json(['status' => 'warning', 'message' => 'Missing Airline'], 400);
    }
    public function verifyClient(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $user = auth()->guard('client')->user();
        if ($user) {
            if ($user->email === $request->email) {
                return response()->json(['message' => 'Logged in user', 'status' => 'success']);
            }
        }
        $client = Client::where('email', $request->email)->first();
        if ($client) {
            return response()->json(['message' => 'Email is already exist please login', 'status' => 'warning'], 400);
        } else {
            return response()->json(['message' => 'Email is new', 'status' => 'success']);
        }
    }

    // Helper Emirates
    protected function buildPriceComparison(float $oldPrice, ?string $oldCode, float $newPrice, ?string $newCode): array
    {
        $delta      = $newPrice - $oldPrice;
        $direction  = $delta > 0 ? 'increased' : ($delta < 0 ? 'decreased' : 'same');
        $absDelta   = abs($delta);
        $pctChange  = $oldPrice > 0 ? round(($absDelta / $oldPrice) * 100, 2) : null;
        // {
        // "old_price": 262926,
        // "old_price_code": "PKR",
        // "new_price": 270000,
        // "new_price_code": "PKR",
        // "difference": 7074,
        // "difference_label": "increased",
        // "percent_change": 2.69,
        // "same_currency": true
        // }
        return [
            'old_price'        => $oldPrice,
            'old_price_code'   => $oldCode,
            'new_price'        => $newPrice,
            'new_price_code'   => $newCode,
            'difference'       => $absDelta,
            'difference_label' => $direction,
            'percent_change'   => $pctChange,   // null when old price = 0
            'same_currency'    => $oldCode === $newCode,
        ];
    }
    protected function extractTotalPriceAndCode(array $response, float $fallbackPrice, ?string $fallbackCode): array
    {
        // Try all likely places for total price in different payloads
        $candidates = [
            ['path' => 'bundle.totalPrice',                               'amount' => 'amount', 'code' => 'code'],
            ['path' => 'totalPrice',                                      'amount' => 'amount', 'code' => 'code'],
            ['path' => 'OrderViewRS.Response.TotalOrderPrice',            'amount' => 'Amount.value', 'code' => 'CurrencyCode.value'],
            ['path' => 'OrderViewRS.Response.Order.TotalAmount',          'amount' => 'Amount.value', 'code' => 'CurrencyCode.value'],
            ['path' => 'data.order.totalPrice',                           'amount' => 'amount', 'code' => 'code'],
            ['path' => 'order.totalPrice',                                'amount' => 'amount', 'code' => 'code'],
            ['path' => 'payment.totalPrice',                              'amount' => 'amount', 'code' => 'code'],
            // Add more if you know the exact Emirates structure
        ];

        foreach ($candidates as $c) {
            $amount = data_get($response, "{$c['path']}.{$c['amount']}");
            $code   = data_get($response, "{$c['path']}.{$c['code']}");

            if (!is_null($amount)) {
                // normalize
                $amount = (float) $amount;
                return [
                    'price'      => $amount,
                    'price_code' => $code,
                    'source'     => $c['path'], // for logging/debug
                ];
            }
        }

        // If nothing matched, return fallback (the old booking price)
        return [
            'price'      => $fallbackPrice,
            'price_code' => $fallbackCode,
            'source'     => 'fallback',
        ];
    }

    // private function storeDetails ($data) {
    //     try {
    //         $userDetails = $this->bookingService->saveData($data);
    //     } catch (\Throwable $th) {
    //         throw $th;
    //     }
    // }
}