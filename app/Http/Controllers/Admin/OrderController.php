<?php

namespace App\Http\Controllers\Admin;

use App\Models\Log;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\HotelBooking;
use App\Models\CancelResponse;
use App\Services\TassProService;
use App\Services\HotelBookingService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    protected $tassProService;
    protected $hotelBookingService;

    public function __construct(TassProService $tassProService, HotelBookingService $hotelBookingService)
    {
        $this->tassProService = $tassProService;
        $this->hotelBookingService = $hotelBookingService;
    }

    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    // ------------------------------------------------ BOOKING ------------------------------------------------
    public function list()
    {
        return view('admin.orders.list');
    }
    public function fetch(Request $request)
    {
        $user = auth()->user();
        $product = $request->product;

        $bookings = collect();

        // Fetch Flights if requested or if no product filter
        if (!$product || $product === 'flight') {
            $query = Booking::with(['client', 'agent']);
            if (!$user->can('manage all bookings')) {
                $query->where('agent_id', $user->id);
            }
            if ($request->order_id) $query->where('id', 'like', '%' . $request->order_id . '%');
            if ($request->order_ref) $query->where('order_id', 'like', '%' . $request->order_ref . '%');
            if ($request->pnr) $query->where('flight_booking_id', 'like', '%' . $request->pnr . '%');
            if ($request->status) $query->where('status', $request->status);
            if ($request->agent && $user->can('manage all bookings')) {
                $query->whereHas('agent', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->agent . '%');
                });
            }

            $flightItems = $query->latest()->get()->map(function ($b) {
                return [
                    'id' => $b->id,
                    'order_id' => $b->order_id,
                    'product' => 'Flight',
                    'product_type' => 'flight',
                    'flight_booking_id' => $b->flight_booking_id,
                    'status' => $b->status,
                    'is_oneway' => $b->is_oneway,
                    'type' => $b->type ?? ($b->is_oneway ? 'oneway' : 'return'),
                    'airline' => $b->airline ?? null,
                    'agent_name' => optional($b->agent)->name,
                    'client_name' => optional($b->client)->name,
                    'client_phone' => optional($b->client)->phone,
                    'client_email' => optional($b->client)->email,
                    'total_tax_price' => $b->total_tax_price,
                    'summary' => $b->getFlightSummary(),
                    'created_at' => $b->created_at->format('Y-m-d H:i'),
                    'raw_date' => $b->created_at
                ];
            });
            $bookings = $bookings->concat($flightItems);
        }

        // Fetch Hotels if requested or if no product filter
        if (!$product || $product === 'hotel') {
            $query = \App\Models\HotelBooking::with(['client', 'agent']);
            if (!$user->can('manage all bookings')) {
                $query->where('agent_id', $user->id);
            }
            if ($request->order_id) $query->where('id', 'like', '%' . $request->order_id . '%');
            if ($request->order_ref) $query->where('reference', 'like', '%' . $request->order_ref . '%');
            if ($request->pnr) $query->where('pnr', 'like', '%' . $request->pnr . '%');
            if ($request->status) $query->where('status', $request->status);
            if ($request->agent && $user->can('manage all bookings')) {
                $query->whereHas('agent', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->agent . '%');
                });
            }

            $hotelItems = $query->latest()->get()->map(function ($b) {
                return [
                    'id' => $b->id,
                    'order_id' => $b->reference,
                    'product' => 'Hotel',
                    'product_type' => 'hotel',
                    'flight_booking_id' => $b->pnr ?? $b->confirmation_no,
                    'status' => $b->status,
                    'is_oneway' => false,
                    'type' => 'HOTEL',
                    'airline' => 'TassPro',
                    'agent_name' => optional($b->agent)->name,
                    'client_name' => optional($b->client)->name,
                    'client_phone' => optional($b->client)->phone,
                    'client_email' => optional($b->client)->email,
                    'total_tax_price' => $b->currency . ' ' . number_format($b->total_gross, 2),
                    'summary' => $b->getHotelSummary(),
                    'created_at' => $b->created_at->format('Y-m-d H:i'),
                    'raw_date' => $b->created_at
                ];
            });
            $bookings = $bookings->concat($hotelItems);
        }

        // Sort combined list by date
        $sorted = $bookings->sortByDesc('raw_date');

        // Manual Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $pagedData = $sorted->forPage($page, $perPage)->values();

        return response()->json([
            'data' => $pagedData,
            'current_page' => (int)$page,
            'last_page' => ceil($bookings->count() / $perPage),
            'total' => $bookings->count()
        ]);
    }
    public function details(Booking $booking)
    {
        $user = auth()->user();

        if (!$user->can('manage all bookings') && $booking->agent_id !== $user->id) {
            abort(403, 'You are not authorized to view this booking.');
        }

        $booking->load(['payments', 'flights.segments', 'tickets', 'client', 'bookingItems.penalties', 'cancelResponse', 'errorLogs', 'bookingRequest']);
        $agents = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))->get();
        if (!$booking) abort(404, 'Booking not found.');

        // dd($agents, $booking);
        return view('admin.orders.manage', compact('agents', 'booking'));
    }
    public function update(Request $request, Booking $booking)
    {
        // dd($request->all());
        $validated = $request->validate([
            'status' => 'nullable|string',
            'order_id' => 'required|string',
        ]);

        $booking->update($validated);

        return back()->with(['message' => 'Booking updated successfully.', 'status' => 'success']);
    }
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.orders.index')->with(['message' => 'Booking deleted successfully.', 'status' => 'success']);
    }
    // ------------------------------------------------ LOGS ------------------------------------------------
    public function logStore(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'booking_id' => 'required',
            'booking_type' => 'nullable|string|in:flight,hotel',
            'notes' => 'required|string|max:10000',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('logs', 'public');
        }

        $bookingType = $request->booking_type ?? 'flight';

        $logData = [
            'user_id' => $request->agent_id,
            'notes' => $request->notes,
            'image' => $imagePath,
        ];

        if ($bookingType === 'hotel') {
            HotelBooking::where('id', $request->booking_id)->update(['agent_id' => $request->agent_id]);
            $logData['hotel_booking_id'] = $request->booking_id;
        } else {
            Booking::where('id', $request->booking_id)->update(['agent_id' => $request->agent_id]);
            $logData['booking_id'] = $request->booking_id;
        }

        Log::create($logData);

        return response()->json(['message' => 'Note added successfully.']);
    }
    public function logHistory($bookingId, Request $request)
    {
        $bookingType = $request->booking_type ?? 'flight';
        $query = Log::with('user');

        if ($bookingType === 'hotel') {
            $query->where('hotel_booking_id', $bookingId);
        } else {
            $query->where('booking_id', $bookingId);
        }

        $logs = $query->latest()->get();

        return response()->json([
            'logs' => $logs
        ]);
    }
    // ------------------------------------------------ PAYMENT ------------------------------------------------
    public function paymentStore(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'hotel_booking_id' => 'nullable|exists:hotel_bookings,id',
            'client_id' => 'required|exists:clients,id',
            'airline' => 'required|string',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'base_price' => 'required|numeric',
            'base_price_code' => 'nullable|string|max:5',
            'tax' => 'nullable|numeric',
            'merchant_fee' => 'nullable|numeric',
            'service_fee' => 'nullable|numeric',
            'status' => 'required|string|in:pending,success,failed',
            'refund_status' => 'nullable|string',
        ]);

        Payment::create($validated);

        return back()->with(['message' => 'Payment added successfully.', 'status' => 'success']);
    }
    public function paymentUpdate(Request $request, Payment $payment)
    {
        // dd($request->all());
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'base_price' => 'required|numeric',
            'base_price_code' => 'nullable|string|max:5',
            'tax' => 'nullable|numeric',
            'merchant_fee' => 'nullable|numeric',
            'service_fee' => 'nullable|numeric',
            'status' => 'required|string|in:pending,success,failed',
            'refund_status' => 'nullable|string',
        ]);

        $payment->update($validated);

        return back()->with(['message' => 'Payment updated successfully.', 'status' => 'success']);
    }
    public function paymentDestroy(Payment $payment)
    {
        // (Optional) authorize or ensure current user can delete it
        // $this->authorize('delete', $payment);

        $payment->delete();

        return back()->with(['message' => 'Payment deleted successfully.', 'status' => 'success']);
    }
    // ------------------------------------------------ TICKET ------------------------------------------------
    public function ticketStore(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'passenger_reference' => 'required|string',
            'type' => 'nullable|string',
            'ticket_number' => 'required|string',
        ]);
        $booking = Booking::find($validated['booking_id']);
        if ($booking->status !== 'issued') {
            return back()->with(['message' => 'Tickets can only be added to issued bookings.', 'status' => 'error']);
        }
        $booking->tickets()->create([
            'airline' => $booking->airline,
            'passenger_reference' => $validated['passenger_reference'],
            'type' => $validated['type'],
            'ticket_no' => $validated['ticket_number'],
            'issue_date' => now(),
            'ticket_details' => 'admin issued ticket',
            'client_id' => $booking->client_id,
            'booking_id' => $booking->id,
        ]);
        return back()->with(['message' => 'Ticket added successfully.', 'status' => 'success']);
    }

    // ------------------------------------------------ HOTEL ------------------------------------------------
    public function hotelDetails(HotelBooking $booking)
    {
        $user = auth()->user();

        if (!$user->can('manage all bookings') && $booking->agent_id !== $user->id) {
            abort(403, 'You are not authorized to view this booking.');
        }

        $booking->load(['rooms.passengers', 'client', 'agent', 'payments', 'errorLogs', 'bookingRequest']);
        $agents = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))->get();

        return view('admin.orders.hotel-manage', compact('agents', 'booking'));
    }

    public function hotelUpdate(Request $request, HotelBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'pnr' => 'nullable|string',
            'reference' => 'nullable|string',
            'confirmation_no' => 'nullable|string',
        ]);

        $booking->update($validated);

        return back()->with(['message' => 'Hotel booking updated successfully.', 'status' => 'success']);
    }

    public function hotelDestroy(HotelBooking $booking)
    {
        $booking->delete();

        return redirect()->route('admin.orders.index')->with(['message' => 'Hotel booking deleted successfully.', 'status' => 'success']);
    }

    public function confirmHotel(Request $request, HotelBooking $booking)
    {
        $result = $this->hotelBookingService->confirmBooking($booking);

        if (!$result['success']) {
            return back()->with(['message' => 'API confirmation failed: ' . $result['message'], 'status' => 'error']);
        }

        return back()->with(['message' => 'Hotel booking confirmed via API successfully.', 'status' => 'success']);
    }

    public function preBookHotel(Request $request, HotelBooking $booking)
    {
        $result = $this->hotelBookingService->checkPriceChange($booking);
        // dd($result);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'comparison' => $result['comparison'],
            'data' => $result['data']
        ]);
    }

    public function cancelHotel(Request $request, HotelBooking $booking)
    {
        // For cancellation, TassPro requires specific room identifiers if canceling partially.
        // For full cancellation, we send all room identifiers.
        $roomIdentifiers = $booking->rooms->pluck('room_identifier')->toArray();

        $payload = [
            'SessionId' => $booking->session_id,
            'Currency' => $booking->currency,
            'ADSConfirmationNumber' => $booking->pnr ?? $booking->confirmation_no,
            'CancelRooms' => [
                'CancelRoom' => array_map(function ($id) {
                    return ['RoomIdentifier' => (int)$id];
                }, $roomIdentifiers)
            ]
        ];

        $result = $this->tassProService->cancelHotel($payload);

        if (isset($result['error'])) {
            return back()->with(['message' => 'Cancellation failed: ' . $result['error'], 'status' => 'error']);
        }

        $booking->update(['status' => 'cancelled']);
        CancelResponse::create([
            'xml_body' => json_encode($result),
            'hotel_booking_id' => $booking->id,
        ]);

        return back()->with(['message' => 'Hotel booking cancelled successfully.', 'status' => 'success']);
    }
}
