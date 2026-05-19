<?php

namespace App\Http\Controllers;

use App\Models\CountryHotel;
use App\Models\Client;
use App\Models\Passenger;
use App\Models\HotelBooking;
use App\Models\ErrorLog;
use App\Models\BookingRequestBody;
use App\Models\HotelBookingRoom;
use App\Services\TassProService;
use App\Services\HotelBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class HotelController extends Controller
{
    protected $tassProService;
    protected $hotelBookingService;

    public function __construct(TassProService $tassProService, HotelBookingService $hotelBookingService)
    {
        $this->tassProService = $tassProService;
        $this->hotelBookingService = $hotelBookingService;
    }

    public function searchSuggestions(Request $request)
    {
        $term = $request->term;
        $hotels = CountryHotel::where('city', 'LIKE', "%$term%")
            ->orWhere('destinationcode', 'LIKE', "%$term%")
            ->where('status', true)
            ->limit(20)
            ->get();

        $results = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->destinationcode,
                'name' => $hotel->city,
                // 'text' => "{$hotel->city} ({$hotel->destinationcode})",
                'text' => "{$hotel->city}",
                'country' => $hotel->country,
                'nationality' => $hotel->nationality
            ];
        });

        return response()->json(['results' => $results]);
    }
    public function search(Request $request)
    {
        $validated = $request->validate([
            'destination_code' => 'required|string',
            'destination_name' => 'nullable|string',
            'country_code' => 'required|string',
            'nationality' => 'nullable|string',
            'check_in' => 'required|string',
            'check_out' => 'required|string',
            'rooms' => 'required|array',
        ]);

        $this->storeRecentSearch($validated);

        $results = $this->tassProService->searchHotels($request->all());

        if (!$results) {
            return back()->with('error', 'Unable to fetch hotels at this time.');
        }

        $hotelsArray = $results['hotels']['hotel'] ?? [];
        if (!is_array($hotelsArray)) {
            $hotelsArray = [$hotelsArray];
        }

        $hotelsCollection = collect($hotelsArray);
        $results['hotels']['hotel'] = $hotelsCollection->values()->toArray();

        session(['IdsExpireTimeEmi' => null]);
        return view('home.flights', [
            'request'     => $request,
            'data'        => $results,
        ]);
    }

    public function bookingFlow(Request $request)
    {
        return view('home.hotel-booking-flow');
    }

    public function show(Request $request)
    {
        // dd($request->all());
        $hotelCode = $request->hotel_id;
        $sessionId = $request->session_id;
        $rooms     = $request->rooms;

        if (!$hotelCode || !$sessionId) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Missing hotel or session information.'], 400);
            }
            return redirect()->route('home')->with('error', 'Missing hotel or session information. Please search again.');
        }

        $results = $this->tassProService->getRoomDetails($sessionId, $hotelCode, $rooms);

        if (!$results || empty($results['hotel']['rooms']['room'])) {
            return back()->with('error', 'No rooms available for this hotel at the moment.');
        }

        if ($request->ajax()) {
            return view('home.partials.hotel-details-step', [
                'hotel'   => $results['hotel'],
                'general' => $results['generalInfo'],
                'monetary' => $results['monetary'],
                'requestRooms' => $rooms
            ]);
        }

        return redirect()->route('hotels.booking', $request->query());
    }

    public function checkout(Request $request)
    {
        // dd($request->all());
        $hotelCode = $request->hotel_id;
        $sessionId = $request->session_id;
        $groupCode = $request->group_code;
        $rateKey   = $request->rate_key;

        // If rate_key is a comma-separated string (from grouped room selection), explode it
        if (is_string($rateKey) && str_contains($rateKey, ',')) {
            $rateKey = explode(',', $rateKey);
        }

        if (!$hotelCode || !$sessionId || !$groupCode || !$rateKey) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Booking session expired. Please search again.'], 400);
            }
            return redirect()->route('home')->with('error', 'Booking session expired. Please search and select your room again.');
        }

        // dd($request->all());
        $results = $this->tassProService->preBook($sessionId, $hotelCode, $groupCode, $rateKey);

        // Debug logging for PreBook response structure
        // \Log::info("PreBook Response for Hotel {$hotelCode}: " . json_encode($results));

        if (!$results || isset($results['error'])) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unable to fetch checkout details at this time.'], 500);
            }
            return back()->with('error', 'Unable to fetch checkout details at this time.');
        }
        if ($results['hotel']['rooms']['room'] == null) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No rooms available for this hotel at the moment.'], 500);
            }
            return back()->with('error', 'No rooms available for this hotel at the moment.');
        }

        if (isset($results['isBookable']) && $results['isBookable'] === false) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This room is no longer bookable. Please select another room.'], 400);
            }
            return back()->with('error', 'This room is no longer bookable. Please select another room.');
        }

        $client = auth()->guard('client')->user();
        $savedPassengers = [];
        if ($client) {
            $client->load('passengers');
            $savedPassengers = $client->passengers;
        }

        $data = [
            'breakup' => $results,
            'hotel'   => [
                'name'    => $request->hotel_name,
                'address' => $request->hotel_address,
                'city'    => $request->hotel_city,
                'image'   => $request->hotel_image,
                'rating'  => $request->hotel_rating,
                'code'    => $hotelCode
            ],
            'request' => $request->all(),
            'client'  => $client,
            'savedPassengers' => $savedPassengers
        ];

        if ($request->ajax()) {
            return view('home.partials.hotel-checkout-step', $data);
        }

        return redirect()->route('hotels.booking', $request->query());
    }


    public function saveBooking(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'guests' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            // 1. Handle Client (Lead Contact)
            $client = Client::where('email', $request->email)->first();
            if (!$client) {
                $client = Client::create([
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make(Str::random(10)),
                    'is_active' => true
                ]);
            }

            // 2. Create Hotel Booking
            $booking = HotelBooking::create([
                'source' => 'TassPro',
                'hotel_name' => $request->hotel_name,
                'hotel_code' => $request->hotel_id,
                'city' => $request->hotel_city,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'currency' => $request->currency ?? 'AED',
                'total_net' => $request->total_net,
                'total_gross' => $request->total_gross,
                'total_tax' => $request->total_tax,
                'session_id' => $request->session_id,
                'destination_code' => $request->destination_code,
                'group_code' => $request->group_code,
                'nationality' => $request->nationality,
                'status' => 'pending',
                'client_id' => $client->id,
                'agent_id' => null,
                'remarks' => $request->remarks,
                'raw_response' => json_decode($request->raw_prebook_response, true)
            ]);
            $booking->update([
                'reference' => 'TASSPRO-' . $booking->id,
            ]);

            // Save Booking Request Body (Initial PreBook Response)
            BookingRequestBody::create([
                'hotel_booking_id' => $booking->id,
                'airline' => 'TassPro',
                'xml_body' => json_encode(['prebook_response' => $booking->raw_response]),
                'client_id' => $client->id,
            ]);

            // 3. Save Rooms and Guests (Passengers)
            foreach ($request->guests as $roomIndex => $guests) {
                $roomData = json_decode($request->rooms_data[$roomIndex], true);

                $room = HotelBookingRoom::create([
                    'hotel_booking_id' => $booking->id,
                    'room_identifier' => $roomData['roomIdentifier'],
                    'room_name' => $roomData['roomName'],
                    'meal_plan' => $roomData['meal'] ?? 'N/A',
                    'rate_keys' => [$roomData['rateKey']],
                    'net_price' => $roomData['price']['supplierNet'] ?? 0,
                    'gross_price' => $roomData['price']['supplierGross'] ?? 0,
                    'tax_price' => $roomData['price']['supplierTax'] ?? 0,
                    'rate_type' => $roomData['rateType'] ?? 'N/A',
                    'cancel_policies' => $roomData['policies'] ?? null,
                ]);

                foreach ($guests as $guestIndex => $guest) {
                    Passenger::create([
                        'title' => $guest['title'],
                        'given_name' => $guest['first_name'],
                        'surname' => $guest['last_name'],
                        'type' => $guest['type'],
                        'dob' => $guest['dob'] ?? ($guest['type'] == 'child' ? now()->subYears(8)->format('Y-m-d') : now()->subYears(30)->format('Y-m-d')), // Default if not provided
                        'nationality' => $request->nationality ?? 'AE',
                        'client_id' => $client->id,
                        'hotel_booking_room_id' => $room->id,
                        'is_lead_pax' => ($roomIndex == 0 && $guestIndex == 0) // First guest in first room
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'booking_id' => $booking->id,
                    'message' => 'Booking saved successfully.'
                ]);
            }

            return redirect()->route('hotels.payment', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Hotel saveBooking Error: " . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Booking failed: ' . $e->getMessage()], 400);
            }
            return back()->with('error', 'Booking failed: ' . $e->getMessage());
        }
    }

    public function payment(Request $request, $id)
    {
        $booking = HotelBooking::with(['rooms.passengers', 'client'])->findOrFail($id);

        if ($request->ajax()) {
            return view('home.partials.hotel-payment-step', compact('booking'));
        }

        return redirect()->route('hotels.booking', ['booking_id' => $id]);
    }

    public function confirmBooking(Request $request, $id)
    {
        $booking = HotelBooking::findOrFail($id);

        // Handle Temporary/Cash Booking
        if ($request->bank_type === 'cash') {
            session(['last_confirmed_hotel_booking' => $booking->id]);
            session(["hotel_booking_{$booking->id}_is_cash" => true]);

            if ($request->ajax()) {
                return view('home.partials.hotel-confirmation-step', ['booking' => $booking, 'is_cash' => true])->render();
            }

            // return view('home.hotel-confirmation', ['booking' => $booking, 'is_cash' => true]);
        }

        if (!$booking->payment) return response()->json(['status' => false, 'message' => 'No payment found for this booking.'], 400);
        $result = $this->hotelBookingService->confirmBooking($booking);

        if (!$result['success']) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $result['message']], 400);
            }
            return back()->with('error', $result['message']);
        }

        // Store booking ID in session for secure confirmation viewing
        session(['last_confirmed_hotel_booking' => $booking->id]);

        if ($request->ajax()) {
            return view('home.partials.hotel-confirmation-step', [
                'booking' => $booking,
                'result' => $result['data'] ?? []
            ])->render();
        }

        // return view('home.hotel-confirmation', [
        //     'booking' => $booking,
        //     'result' => $result['data'] ?? []
        // ]);
    }
    public function confirmationPartial(Request $request, $id)
    {
        $booking = HotelBooking::with(['rooms.passengers'])->findOrFail($id);

        $isSessionAuthorized = (session('last_confirmed_hotel_booking') == $id);
        $isUserAuthorized = (auth()->check() && $booking->agent_id == auth()->id()) ||
            (auth('client')->check() && $booking->client_id == auth('client')->id());

        if (!$isSessionAuthorized && !$isUserAuthorized) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or booking not found.'], 403);
        }

        $is_cash = session("hotel_booking_{$id}_is_cash", false);

        return view('home.partials.hotel-confirmation-step', compact('booking', 'is_cash'));
    }

    private function storeRecentSearch(array $searchData)
    {
        try {
            $recentSearches = session()->get('recent_hotel_searches', []);
            
            // Create a unique key for this search configuration
            // We ignore dates for uniqueness if we want to just track "Places", 
            // but usually, "Recent Searches" implies exact configurations.
            // Let's use destination + dates + room count.
            $searchKey = $searchData['destination_code'] . '|' . $searchData['check_in'] . '|' . $searchData['check_out'];
            
            // Remove existing if it matches the key (to move it to top)
            $recentSearches = array_filter($recentSearches, function($search) use ($searchKey) {
                $key = $search['destination_code'] . '|' . $search['check_in'] . '|' . $search['check_out'];
                return $key !== $searchKey;
            });

            // Add to the beginning
            array_unshift($recentSearches, $searchData);

            // Limit to 10
            $recentSearches = array_slice($recentSearches, 0, 10);

            session()->put('recent_hotel_searches', $recentSearches);
        } catch (\Exception $e) {
            Log::error("Error storing recent hotel search: " . $e->getMessage());
        }
    }
}
