<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function airports(Request $request)
    {
        $search = $request->input('term');

        $query = Airport::query();

        if (!$search) {
            return response()->json(['results' => []]);
        }

        $airports = Airport::query()
            ->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                ->orWhere('name', 'LIKE', '%' . $search . '%');
            })
            ->orderByRaw("CASE 
                            WHEN code LIKE ? THEN 0 
                            ELSE 1 
                        END", ["%$search%"])
            ->orderBy('name')
            ->limit(1)
            ->get();

        $results = $airports->map(function ($airport) {
            return [
                'id' => $airport->code,
                'text' => $airport->name . ' (' . $airport->code . ')',
            ];
        });

        return response()->json(['results' => $results]);
    }
    public function profile()
    {
        $client = auth('client')->user();
        $client->load(['bookings.flights', 'passengers']);

        return view('home.pages.profile', compact('client'));
    }

    public function updateClient(Client $client, Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
            'accept_notification' => 'nullable|boolean',
            'profile_path' => 'nullable|image|max:2048', // 2MB Max
        ]);

        if ($request->hasFile('profile_path')) {
            $path = $request->file('profile_path')->store('profiles', 'public');
            $validated['profile_path'] = $path;
        }

        if (!empty($validated['password'])) {
            $validated['original_password'] = null;
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $client->update($validated);

        return redirect()->back()->with('message', 'Client updated successfully.')->with('status', 'success');
    }
    public function searchBooking(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string|max:255',
            'email'    => 'required|email',
        ]);

        // --- 1. Search Flight Booking ---
        $booking = Booking::where('airline_id', $validated['order_id'])
            ->whereHas('client', function ($q) use ($validated) {
                $q->where('email', $validated['email']);
            })
            ->first();

        if (!$booking) {
            $booking = Booking::where('order_id', $validated['order_id'])
                ->whereHas('client', function ($q) use ($validated) {
                    $q->where('email', $validated['email']);
                })
                ->first();
        }

        if ($booking) {
            $cacheKey = 'verified_booking_' . session()->getId();
            Cache::put($cacheKey, $booking->id, now()->addMinutes(5));
            return redirect()->route('view.booking.details');
        }

        // --- 2. Fallback: Search Hotel Booking by reference or id + email ---
        $hotelBooking = HotelBooking::where('reference', $validated['order_id'])
            ->whereHas('client', function ($q) use ($validated) {
                $q->where('email', $validated['email']);
            })
            ->first();

        // Also try matching bare numeric ID (e.g. user types "42" instead of "TASSPRO-42")
        if (!$hotelBooking && is_numeric($validated['order_id'])) {
            $hotelBooking = HotelBooking::where('id', $validated['order_id'])
                ->whereHas('client', function ($q) use ($validated) {
                    $q->where('email', $validated['email']);
                })
                ->first();
        }

        if ($hotelBooking) {
            $cacheKey = 'verified_hotel_booking_' . session()->getId();
            Cache::put($cacheKey, $hotelBooking->id, now()->addMinutes(5));
            return redirect()->route('view.hotel.booking.details');
        }

        return redirect()->back()->withInput()->with(['status' => 'error', 'message' => 'Booking not found. Please check your Order ID and email.']);
    }
    public function viewBookingDetails(Request $request)
    {
        // 1. Check if user is logged in as client and passing order_id
        if (auth('client')->check() && $request->has('order_id')) {
            $booking = Booking::where('order_id', $request->order_id)
                ->where('client_id', auth('client')->id())
                ->with('client', 'flights', 'bookingItems')
                ->first();

            if ($booking) {
                 return view('home.pages.view-booking-details', compact('booking'));
            }
        }

        // 2. Fallback to existing session/cache based logic for guest/search flow
        $cacheKey = 'verified_booking_' . session()->getId();
        $bookingId = Cache::get($cacheKey);
        if (!$bookingId) return redirect()->route('search.booking')->with(['status' => 'error', 'message' => 'Please verify your booking first.']);

        $booking = Booking::with('client', 'flights', 'bookingItems')->find($bookingId);
        if (!$booking) return redirect()->route('search.booking')->with(['status' => 'error', 'message' => 'Booking not found.']);

        return view('home.pages.view-booking-details', compact('booking'));
    }

    public function viewHotelBookingDetails(Request $request)
    {
        // 1. Logged-in client viewing their own hotel booking by reference
        if (auth('client')->check() && $request->has('reference')) {
            $hotelBooking = HotelBooking::where('reference', $request->reference)
                ->where('client_id', auth('client')->id())
                ->with('client', 'rooms.passengers')
                ->first();

            if ($hotelBooking) {
                return view('home.pages.view-hotel-booking-details', compact('hotelBooking'));
            }
        }

        // 2. Guest/search flow via verified cache
        $cacheKey = 'verified_hotel_booking_' . session()->getId();
        $hotelBookingId = Cache::get($cacheKey);
        if (!$hotelBookingId) {
            return redirect()->route('search.booking')->with(['status' => 'error', 'message' => 'Please verify your booking first.']);
        }

        $hotelBooking = HotelBooking::with('client', 'rooms.passengers')->find($hotelBookingId);
        if (!$hotelBooking) {
            return redirect()->route('search.booking')->with(['status' => 'error', 'message' => 'Hotel booking not found.']);
        }

        return view('home.pages.view-hotel-booking-details', compact('hotelBooking'));
    }

    public function updatePassenger(\App\Models\Passenger $passenger, Request $request)
    {
        // Ensure the authenticated client owns this passenger
        if ($passenger->client_id !== auth('client')->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:10',
            'given_name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'dob' => 'required|date',
            'nationality' => 'required|string|max:2',
            'passport_no' => 'nullable|string|max:50',
            'passport_exp' => 'nullable|date',
        ]);

        $passenger->update($validated);

        return redirect()->back()->with('message', 'Passenger updated successfully.')->with('status', 'success');
    }
}