@extends('admin/layouts/master')

@section('title', 'Hotel Booking Manage')
@section('style')
    <style>
        .box {
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 1rem;
            overflow-x: auto;
        }

        .section-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: 500;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <!-- Left Side -->
        <div class="col-md-3 left-side">
            {{-- Contact Details (User) --}}
            <form method="POST" action="{{ route('admin.clients.update', $booking->client->id) }}" class="card box">
                @csrf
                @method('PUT')
                <div class="section-title">Contact Details (User)</div>
                <div class="mb-2">
                    <input type="text" name="name" class="form-control mb-1" placeholder="name"
                        value="{{ $booking->client->name }}">
                    <input type="email" name="email" class="form-control mb-1" placeholder="email"
                        value="{{ $booking->client->email }}">
                    <div class="input-group mb-3">
                        <span class="input-group-text">+{{ $booking->client->phone_code }}</span>
                        <input type="number" placeholder="phone" name="phone" class="form-control m-0"
                            value="{{ $booking->client->phone }}">
                    </div>
                    <button class="btn btn-sm btn_primary" type="submit">Update</button>
                </div>
            </form>

            {{-- Agent Selection --}}
            <div class="card box">
                <label class="form-label">Select Agent</label>
                <select class="form-select mb-2" id="agentSelect" @if (!empty($booking->agent_id)) disabled @endif>
                    <option selected disabled value="">-- Select Agent --</option>
                    @forelse ($agents as $agent)
                        <option value="{{ $agent->id }}" @selected(isset($booking->agent_id) && $booking->agent_id == $agent->id)>{{ $agent->email }}</option>
                    @empty
                        <option selected>-- No Agents Found --</option>
                    @endforelse
                </select>
            </div>

            {{-- Notes --}}
            <div class="box notes-box">
                <div class="section-title d-flex justify-content-between">
                    <span>Notes</span>
                    <label for="noteImage" class="btn btn-sm btn_secondary">Add Image</label>
                    <input type="file" class="d-none" name="noteImage" id="noteImage">
                </div>
                <textarea id="note-editor" class="form-control" rows="3"></textarea>
                <div class="d-flex justify-content-between mt-2">
                    <button type="button" id="addNoteBtn" class="btn btn-sm btn_secondary">Add Notes</button>
                    <button type="button" class="btn btn-sm btn_secondary_outline" id="showLogHistoryBtn">History</button>
                </div>
            </div>

            {{-- Order Cancellation --}}
            <div class="card box">
                <div class="section-title">Hotel Cancellation</div>
                <div class="mt-2 text-center">
                    @if ($booking->status === 'cancelled')
                        <div class="alert alert-danger mb-0"><strong>Cancelled At: </strong> {{ $booking->cancelled_at ?: 'N/A' }}</div>
                    @elseif($booking->status === 'confirmed')
                        <button class="btn btn-sm btn_danger w-100 mb-2" data-bs-toggle="modal"
                            data-bs-target="#cancelHotelModal">API Cancellation</button>
                    @else
                        <div class="alert alert-warning mb-0"><strong>Not Confirmed</strong></div>
                    @endif
                </div>
            </div>

            <x-modal id="cancelHotelModal" title="Cancel Hotel Booking" size="modal-md">
                <form action="{{ route('admin.orders.hotel.cancel', $booking) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-danger">Are you sure you want to cancel this hotel booking from the supplier
                            platform?</p>
                        <div class="alert alert-warning">
                            <strong>Note:</strong> Cancellation policies may apply. Check the cancellation policies in the
                            booking breakdown below.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                    </div>
                </form>
            </x-modal>

            {{-- Admin Actions --}}
            <form method="POST" action="{{ route('admin.orders.hotel.update', $booking) }}" class="card box">
                @csrf
                @method('PUT')
                <div class="section-title">Admin Actions</div>
                <div class="mb-2">
                    <label class="form-label mb-0 small">Booking Status</label>
                    <select class="form-select form-select-sm mb-2" name="status">
                        @foreach (\App\Models\HotelBooking::getStatuses() as $status)
                            <option value="{{ $status }}" @selected($booking->status == $status)>{{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    <label class="form-label mb-0 small">Confirmation No / PNR</label>
                    <input type="text" name="confirmation_no" class="form-control form-control-sm mb-1"
                        placeholder="Confirmation No" value="{{ $booking->confirmation_no }}">
                    <input type="text" name="pnr" class="form-control form-control-sm mb-2" placeholder="PNR"
                        value="{{ $booking->pnr }}">

                    <label class="form-label mb-0 small">Source Reference</label>
                    <input type="text" name="reference" class="form-control form-control-sm mb-2" placeholder="Reference"
                        value="{{ $booking->reference }}">

                    <div class="d-flex gap-1 justify-content-between">
                        <button class="btn btn-sm btn_primary" type="submit">Update</button>
                        <button type="button" class="btn btn-sm btn-danger delete-booking-btn"
                            data-id="{{ $booking->id }}">Delete</button>
                    </div>
                </div>
            </form>

            {{-- Delete Booking Form --}}
            <form id="delete-booking-{{ $booking->id }}" action="{{ route('admin.orders.hotel.destroy', $booking) }}"
                method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>

            {{-- Guest Status --}}
            @if (!$booking->client->is_user)
                <div class="alert alert-info p-2 small mb-3">
                    <i class='bx bx-info-circle'></i> <strong>Guest User:</strong> This client is not a registered
                    member.
                </div>
            @endif
        </div>

        <!-- Right Side -->
        <div class="col-md-9 right-side">
            {{-- Top Btns --}}
            <div class="d-block d-md-flex justify-content-between mb-3">
                <a href="{{ route('admin.orders.index') }}"
                    class="btn btn_secondary_outline d-flex align-items-center mb-3"><i class='bx bx-chevron-left'></i>
                    Back to Order Management</a>
                <div class="btn-group d-block">
                    @can('booking actions')
                        @if ($booking->status === 'initial' || $booking->status === 'pending')
                            <button class="btn btn-success approveHotelBtn" data-payment-exist="{{ $booking->payments->isEmpty() ? 0 : 1 }}">Approve Booking</button>
                        @endif
                    @endcan
                    <button class="btn btn-{{ $booking->status === 'confirmed' ? 'success' : 'secondary' }}">Status:{{ strtoupper($booking->status) }}</button>
                    <button class="btn btn_{{ $booking->client->status ? 'success' : 'danger' }}_outline">{{ $booking->client->status ? 'Regular User' : 'Guest User' }}</button>
                    <button class="btn btn_secondary_outline" data-bs-toggle="modal" data-bs-target="#detailedOverviewModal">Detailed Overview</button>
                    <button class="btn btn_secondary_outline" data-bs-toggle="modal" data-bs-target="#rawResponseModal">Raw API Data</button>
                    @if ($booking->payments->isNotEmpty())
                        <button class="btn btn-outline-info">Payment: {{ $booking->payments->sum('base_price') }}{{ $booking->currency }}</button>
                    @endif
                    @if ($booking->errorLogs->isNotEmpty())
                        <button class="btn btn_danger_outline" data-bs-toggle="modal"
                            data-bs-target="#errorLogsModal">Error Logs</button>
                        <x-modal id="errorLogsModal" title="Error Logs" size="modal-lg">
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Error Type</th>
                                                <th>Message</th>
                                                {{-- <th>Details</th> --}}
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($booking->errorLogs as $index => $log)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ ucfirst($log->error_type) }}</td>
                                                    <td>
                                                        @php
                                                            $errorMessage = json_decode($log->error_message, true);
                                                        @endphp
                                                        {{ is_array($errorMessage) ? implode(', ', $errorMessage) : $log->error_message }}
                                                    </td>
                                                    {{-- <td>
                                                    @php
                                                        $details = json_decode($log->details, true);
                                                    @endphp
                                                    @if (is_array($details))
                                                        <pre class="mb-0" style="white-space: pre-wrap;">{{ json_encode($details, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        {{ $log->details }}
                                                    @endif
                                                </td> --}}
                                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y h:i A') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <x-slot name="footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </x-slot>
                        </x-modal>
                    @endif
                </div>
            </div>

            <x-modal id="detailedOverviewModal" title="Detailed Booking Overview" size="modal-xl">
                <div class="modal-body">
                    <x-admin.show-xml-data :booking="$booking" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </x-modal>

            <x-modal id="approveHotelModal" title="Approve Hotel Booking via API" size="modal-lg">
                <div id="approveModalContent">
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Checking latest prices with TassPro...</p>
                    </div>
                </div>
            </x-modal>

            <x-modal id="rawResponseModal" title="Raw API Response" size="modal-lg">
                <div class="modal-body">
                    <pre style="background: #f4f4f4; padding: 10px; border-radius: 5px; height: 500px; overflow: auto;">{{ json_encode($booking->raw_response, JSON_PRETTY_PRINT) }}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </x-modal>
            {{-- Hotel Info Card --}}
            <div class="card box bg-light-subtle">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="fw-bold mb-1">{{ $booking->hotel_name }}</h4>
                        <p class="text-muted mb-0"><i class='bx bx-map'></i> {{ $booking->city }},
                            {{ $booking->nationality }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="badge bg-primary fs-6">Ref: {{ $booking->reference }}</div><br>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted d-block uppercase">Check-In</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->check_in->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-3 border-start">
                        <small class="text-muted d-block uppercase">Check-Out</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->check_out->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-3 border-start">
                        <small class="text-muted d-block uppercase">Duration</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->check_in->diffInDays($booking->check_out) }} Nights
                        </div>
                    </div>
                    <div class="col-md-3 border-start">
                        <small class="text-muted d-block uppercase">Total Price</small>
                        <div class="h5 mb-0 fw-bold text-success">{{ $booking->currency }}
                            {{ number_format($booking->total_gross, 2) }}</div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-2">
                        <small class="text-muted d-block uppercase">Order Ref</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->id }}</div>
                    </div>
                    <div class="col-md-2 border-start">
                        <small class="text-muted d-block uppercase">PNR</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->pnr ?? 'Not Issued' }}</div>
                    </div>
                    <div class="col-md-2 border-start">
                        <small class="text-muted d-block uppercase">Booking No</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->booking_no ?? 'Not Issued' }}</div>
                    </div>
                    <div class="col-md-3 border-start">
                        <small class="text-muted d-block uppercase">Confirmation No</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->confirmation_no ?? 'Not Issued' }}</div>
                    </div>
                    <div class="col-md-3 border-start">
                        <small class="text-muted d-block uppercase">Web Reference</small>
                        <div class="h5 mb-0 fw-bold">{{ $booking->session_id ?? '--' }}</div>
                    </div>
                </div>
            </div>

            {{-- Room Details --}}
            @foreach ($booking->rooms as $room)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Room {{ $loop->iteration }}: {{ $room->room_type }}</h6>
                        <span class="badge bg-info">{{ $room->rate_type }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong>Cancellation Policy:
                                        <span class="text-muted">
                                            @if (is_array($room->cancel_policies))
                                                @php
                                                    $firstPolicy = collect($room->cancel_policies)->flatten(1)->first();
                                                    // dd($firstPolicy);
                                                @endphp
                                                @if (is_array($firstPolicy))
                                                    {{ $firstPolicy['textCondition'] ?? 'Multiple policies apply (See Rules)' }}
                                                @else
                                                    {{ $room->cancel_policies[0] ?? 'Check rules for details' }}
                                                @endif
                                            @else
                                                {{ Str::limit($room->cancel_policies, 100) }}
                                            @endif
                                        </span>
                                    </strong>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#fareRulesModal{{ $room->id }}">View Rules</button>
                                </div>
                            </div>
                        </div>

                        {{-- Fare Rules Modal --}}
                        <x-modal id="fareRulesModal{{ $room->id }}"
                            title="Fare Rules & Policies - Room {{ $loop->iteration }}" size="modal-lg">
                            <div class="modal-body">
                                <h6><strong>Meal Plan:</strong> {{ $room->meal_plan ?? 'N/A' }}</h6>
                                <h6><strong>Rate Type:</strong> {{ $room->rate_type ?? 'N/A' }}</h6>
                                <hr>
                                <h6 class="fw-bold">Cancellation Policies:</h6>
                                <div class="p-3 border rounded bg-light">
                                    @if (is_array($room->cancel_policies))
                                        @foreach ($room->cancel_policies as $policy)
                                            <div class="mb-3">
                                                @if (is_array($policy))
                                                    @foreach ($policy as $key => $val)
                                                        <div class="mb-1">
                                                            <span class="fw-bold text-primary">{{ $key }}:</span>
                                                            @if (is_array($val))
                                                                <pre class="small bg-white p-2 border mt-1">{{ json_encode($val, JSON_PRETTY_PRINT) }}</pre>
                                                            @else
                                                                {{ $val }}
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p>{{ $policy }}</p>
                                                @endif
                                                @if (!$loop->last)
                                                    <hr>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        {!! nl2br(e($room->cancel_policies)) !!}
                                    @endif
                                </div>
                            </div>
                        </x-modal>
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Nationality</th>
                                    <th>Lead Pax</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($room->passengers as $passenger)
                                    <tr>
                                        <td>{{ $passenger->title }} {{ $passenger->given_name ?? '' }}
                                            {{ $passenger->surname }}</td>
                                        <td>{{ $passenger->type }}</td>
                                        <td>{{ $passenger->nationality }}</td>
                                        <td>{{ $passenger->is_lead_pax ? 'Yes' : 'No' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            {{-- Payments --}}
            <div class="card box">
                <div class="section-title">
                    Payments
                    @can('manage payment')
                        <button class="btn btn_secondary_outline m-1 float-end" data-bs-toggle="modal"
                            data-bs-target="#addPayment" type="button">Add Payment</button>
                    @endcan
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Created at</th>
                            <th>Payment Method</th>
                            <th>Transaction ID</th>
                            <th>Amount</th>
                            <th>Merchant Fee</th>
                            <th>Service Fee</th>
                            <th>Status</th>
                            <th>Refund Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($booking->payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d M Y h:i a') }}</td>
                                <td>{{ strtoupper($payment->payment_method) }}</td>
                                <td>{{ $payment->transaction_id ?? 'N/A' }}</td>
                                <td>{{ $payment->base_price_code }} {{ number_format($payment->base_price, 2) }}</td>
                                <td>{{ $payment->merchant_fee }}%</td>
                                <td>{{ $payment->service_fee }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $payment->status === 'success' ? 'success' : 'danger' }}">{{ strtoupper($payment->status) }}</span>
                                </td>
                                <td>{{ $payment->refund_status ?? 'N/A' }}</td>
                                <td>
                                    @can('manage payment')
                                        <button class="btn btn_secondary_outline btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editPayment{{ $payment->id }}">Adjust</button>
                                    @endcan
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <x-modal id="editPayment{{ $payment->id }}" title="Edit Payment" size="modal-lg">
                                <form id="update-payment-{{ $payment->id }}" method="POST"
                                    action="{{ route('admin.orders.payment.update', $payment) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <x-admin.payment-form :payment="$payment" />
                                    </div>
                                    <div class="modal-footer w-100 d-flex justify-content-between">
                                        <button type="button" class="btn btn-danger delete-payment-btn"
                                            data-id="{{ $payment->id }}">
                                            Delete
                                        </button>
                                        <button type="submit" class="btn btn_primary">Update</button>
                                    </div>
                                </form>

                                {{-- DELETE form (separate, hidden) --}}
                                <form id="delete-payment-{{ $payment->id }}" method="POST"
                                    action="{{ route('admin.orders.payment.destroy', $payment) }}" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </x-modal>

                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No Payment Found</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

                {{-- Add Payment Modal --}}
                <x-modal id="addPayment" title="Add Payment" size="modal-lg">
                    <form method="POST" action="{{ route('admin.orders.payment.store') }}">
                        @csrf
                        <input type="hidden" name="hotel_booking_id" value="{{ $booking->id }}">
                        <input type="hidden" name="client_id" value="{{ $booking->client_id }}">
                        <input type="hidden" name="airline" value="TassPro">
                        <div class="modal-body">
                            <x-admin.payment-form :booking="$booking" />
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn_primary">Add Payment</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </x-modal>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Function to format currency
            const formatCurrency = (amount, currency) => {
                return `${currency} ${parseFloat(amount).toFixed(2)}`;
            };

            $(document).on('click', '.approveHotelBtn', function() {
                let paymentExist = $(this).data('payment-exist');
                if (paymentExist == 0) return _alert('Please add some payments before issue tickets', 'warning');
                $('#approveHotelModal').modal('show');
                const modalContent = $('#approveModalContent');
                modalContent.html(`
                    <div class="modal-body text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Checking live availability and prices...</p>
                    </div>
                `);
    
                $.ajax({
                    url: "{{ route('admin.orders.hotel.prebook', $booking) }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        const comp = res.comparison;
                        let comparisonHtml = `
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Please review the price comparison before confirming.
                                </div>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Current Saved Price</th>
                                        <td>${comp.old_price} ${comp.old_price_code}</td>
                                    </tr>
                                    <tr>
                                        <th>Live API Price</th>
                                        <td>${comp.new_price} ${comp.new_price_code}</td>
                                    </tr>
                                    <tr class="${comp.difference > 0 ? 'table-warning' : 'table-success'}">
                                        <th>Difference</th>
                                        <td>
                                            ${comp.difference} ${comp.new_price_code} 
                                            <span class="badge bg-${comp.difference > 0 ? 'danger' : (comp.difference < 0 ? 'success' : 'secondary')}">
                                                ${comp.difference_label}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                <p class="text-muted small mb-0">
                                    <strong>Note:</strong> Proceeding will finalize the booking with TassPro at the <strong>Live API Price</strong>.
                                    Ensure the customer is charged accordingly.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route('admin.orders.hotel.confirm', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Confirm Booking Now</button>
                                </form>
                            </div>
                        `;
                        modalContent.html(comparisonHtml);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to fetch price check.';
                        modalContent.html(`
                            <div class="modal-body">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    <strong>Pre-Book Check Failed:</strong><br>
                                    ${msg}
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        `);
                    }
                });
            });

            $('#showLogHistoryBtn').on('click', function() {
                const bookingId = '{{ $booking->id }}';
                const bookingType = 'hotel';

                $('#logHistoryModal').modal('show');
                $('#logHistoryContent').html('<div class="text-center py-4">Loading...</div>');

                $.ajax({
                    url: `/admin/orders/booking/${bookingId}/logs`,
                    method: 'GET',
                    data: {
                        booking_type: bookingType
                    },
                    success: function(response) {
                        let html = '';
                        if (response.logs.length === 0) {
                            html =
                                '<div class="alert alert-info">No notes found for this booking.</div>';
                        } else {
                            response.logs.forEach(log => {
                                html += `
                                <div class="border-bottom mb-3 pb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>${log.user ? log.user.name : 'Unknown'}</strong>
                                        <small class="text-muted">${new Date(log.created_at).toLocaleString()}</small>
                                    </div>
                                    <p class="mb-1">${log.notes}</p>
                                    ${log.image ? `<a href="/storage/${log.image}" target="_blank"><img src="/storage/${log.image}" class="img-thumbnail" style="max-width: 150px;"></a>` : ''}
                                </div>
                            `;
                            });
                        }
                        $('#logHistoryContent').html(html);
                    }
                });
            });

            $('#addNoteBtn').on('click', function() {
                const notes = $('#note-editor').val();
                if (!notes) return alert('Please enter some notes.');

                $.ajax({
                    url: '{{ route('admin.orders.log.add') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        agent_id: $('#agentSelect').val() || '{{ auth()->id() }}',
                        booking_id: '{{ $booking->id }}',
                        booking_type: 'hotel',
                        notes: notes,
                    },
                    success: function(res) {
                        alert('Note added successfully!');
                        $('#note-editor').val('');
                    }
                });
            });

            // Delete Booking
            $('.delete-booking-btn').on('click', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this booking?')) {
                    $(`#delete-booking-${id}`).submit();
                }
            });

            // Delete Payment
            $('.delete-payment-btn').on('click', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this payment record?')) {
                    $(`#delete-payment-${id}`).submit();
                }
            });
        });
    </script>
@endsection
