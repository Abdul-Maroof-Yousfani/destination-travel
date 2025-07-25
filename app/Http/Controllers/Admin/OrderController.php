<?php

namespace App\Http\Controllers\Admin;
use App\Models\Log;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function list()
    {
        return view('admin.orders.list');
    }
    public function fetch(Request $request)
    {
        $query = Booking::with(['client', 'agent']);

        if ($request->order_id) {
            $query->where('id', 'like', '%' . $request->order_id . '%');
        }

        if ($request->order_ref) {
            $query->where('order_id', 'like', '%' . $request->order_id . '%');
        }

        if ($request->pnr) {
            $query->where('flight_booking_id', 'like', '%' . $request->pnr . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('is_oneway', $request->type === 'oneway' ? 1 : 0);
        }

        if ($request->agent) {
            $query->whereHas('agent', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->agent . '%');
            });
        }

        $perPage = $request->input('per_page', 10);

        $bookings = $query->latest()->paginate($perPage);

        $bookings->getCollection()->transform(function ($b) {
            return [
                'id' => $b->id,
                'order_id' => $b->order_id,
                'product' => 'Flight',
                'flight_booking_id' => $b->flight_booking_id,
                'status' => $b->status,
                'is_oneway' => $b->is_oneway,
                'agent_name' => optional($b->agent)->name,
                'client_name' => optional($b->client)->name,
                'client_phone' => optional($b->client)->phone,
                'client_email' => optional($b->client)->email,
                'total_tax_price' => $b->total_tax_price,
                'summary' => $b->getFlightSummary(),
                'created_at' => $b->created_at->format('m/d/Y H:i'),
            ];
        });

        return response()->json($bookings);
    }
    public function details(Booking $booking)
    {
        $booking->load(['payments', 'flights.segments', 'tickets', 'client', 'bookingItems.penalties', 'cancelResponse', 'errorLogs', 'bookingRequest']);
        $agents = User::role('agent')->get();
        if (!$booking) abort(404, 'Booking not found.');

        // dd($agents, $booking);
        return view('admin.orders.manage', compact('agents', 'booking'));
    }
    public function logStore(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'booking_id' => 'required|exists:bookings,id',
            'notes' => 'required|string|max:10000',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('logs', 'public');
        }
        Booking::where('id', $request->booking_id)->update(['agent_id' => $request->agent_id]);

        Log::create([
            'user_id' => $request->agent_id,
            'booking_id' => $request->booking_id,
            'notes' => $request->notes,
            'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Note added successfully.']);
    }
    public function logHistory($bookingId)
    {
        $logs = Log::with('user')->where('booking_id', $bookingId)->latest()->get();

        return response()->json([
            'logs' => $logs
        ]);
    }

    public function paymentStore(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
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
}