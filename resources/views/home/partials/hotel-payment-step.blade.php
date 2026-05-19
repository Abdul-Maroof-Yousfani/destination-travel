
<style>
   
         .bg-primary {
        background-color: #00788a !important;
    }

   .badge {
    color: #fff;
    margin-right: 7px;
}
    .btn-primary {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:hover {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:active {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:focus {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled:hover {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled:active {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .btn-primary:disabled:focus {
        color: #fff;
        background-color: #00788a;
        border-color: #00788a;
    }

    .text-primary {
        color: #00788a !important;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    color: #fff;
    background-color: #00788a;
}
    .col-md-4 {
        margin-bottom: 20px;
    }
    .col-md-6 {
        margin-bottom: 20px;
    }
    .form-control
 {
    display: block;
    width: 100%;
    height: calc(1.5em + .75rem + 2px);
    padding: .375rem .75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
label.form-label.small.fw-bold {
    margin-bottom: 6px;
    margin-top: 15px;
}   

.form-group label {
    font-size: 14px;
    margin-bottom: 10px;
}
.text-muted.small.fw-bold.text-uppercase {
    margin-bottom: 4px;
    font-weight: 600;
}

.re-booking {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

</style>

<div class="booking-step-content" data-step="3">
    <div class="row">
        <div class="col-lg-8">
            <div class="payment-card bg-white p-4 rounded-4 shadow-sm mb-4">
                <div class="re-booking ">
                    <h4 class="fw-bold mb-0">Review Your Booking</h4>
                    <span class="booking-badge px-3 py-1 rounded-pill"
                        style="background: #e0f2fe; color: #0369a1; font-weight: 700;">Ref:
                        {{ $booking->reference }}</span>
                </div>

                <div class="summary-section mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-hotel me-2"></i> Hotel Information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase">Hotel Name</div>
                            <div class="fw-bold">{{ $booking->hotel_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase">City</div>
                            <div class="fw-bold">{{ $booking->city }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase">Check-In</div>
                            <div class="fw-bold">{{ $booking->check_in->format('d M, Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase">Check-Out</div>
                            <div class="fw-bold">{{ $booking->check_out->format('d M, Y') }}</div>
                        </div>
                    </div>
                </div>

                <div class="summary-section mb-4 pb-4 border-bottom">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-users me-2"></i> Guests & Rooms</h6>
                    @foreach ($booking->rooms as $roomIndex => $room)
                        <div class="mb-4 p-3 rounded-3 bg-light">
                            <div class="fw-bold small text-muted mb-2">ROOM {{ $roomIndex + 1 }}: {{ $room->room_name }}
                            </div>
                            <div class="row">
                                @foreach ($room->passengers as $pax)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center" style="gap: 3px;">
                                            <div class="guest-icon d-flex align-items-center justify-content-center bg-primary text-white rounded-circle"
                                                style="width: 24px; height: 24px; font-size: 10px;">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small">{{ $pax->title }} {{ $pax->given_name }}
                                                    {{ $pax->surname }}</div>
                                                <div class="text-muted" style="font-size: 10px;">
                                                    {{ ucfirst($pax->type) }} @if ($pax->is_lead_pax)
                                                        (Lead)
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="summary-section mb-0">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-envelope me-2"></i> Contact Details</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase">Full Name</div>
                            <div class="fw-bold">{{ $booking->client->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small fw-bold text-uppercase">Email</div>
                            <div class="fw-bold">{{ $booking->client->email }}</div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="payment-section mt-4 mb-4 pt-4 border-top">
                    <div class="pays mb-4">
                        <h4 class="fw-bold mb-3">Select a Payment Method</h4>
                        <ul class="list-unstyled d-flex gap-3 flex-wrap">
                            <li class="d-flex align-items-center gap-2 p-2 border rounded bg-light" style="gap:5px;">
                                <div class="payicon text-primary"><i class="fa-regular fa-credit-card"></i></div>
                                <p class="mb-0 small fw-bold">Secure Payments</p>
                            </li>
                            <li class="d-flex align-items-center gap-2 p-2 border rounded bg-light" style="gap:5px;">
                                <div class="payicon text-warning"><i class="fa-solid fa-bolt"></i></div>
                                <p class="mb-0 small fw-bold">Quick Refunds</p>
                            </li>
                            <li class="d-flex align-items-center gap-2 p-2 border rounded bg-light" style="gap:5px;">
                                <div class="payicon text-success"><i class="fa-regular fa-thumbs-up"></i></div>
                                <p class="mb-0 small fw-bold">Trusted by 1M+ customers</p>
                            </li>
                        </ul>
                    </div>

                    <div class="pay-taps">
                        <div class="row g-4">
                            <div class="col-md-4 br-right">
                                <div class="payments-taps-lists">
                                    <ul class="nav flex-column nav-pills gap-2" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active border d-flex align-items-center justify-content-between p-3"
                                                data-bs-toggle="pill" href="#tab-9" role="tab">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="radio" name="payment_method_ui" checked>
                                                    <div class="text-start">
                                                        <div class="fw-bold small" style="font-size: 12px;">HBL Direct Transfer</div>
                                                        <div class="text-success" style="font-size: 8px;">Save PKR
                                                            4,223</div>
                                                    </div>
                                                </div>
                                                <img src="/assets/images/hbl.png" alt="HBL" style="height: 20px;">
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link border d-flex align-items-center justify-content-between p-3"
                                                data-bs-toggle="pill" href="#tab-10" role="tab">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="radio" name="payment_method_ui">
                                                    <div class="text-start">
                                                        <div class="fw-bold small">JazzCash</div>
                                                    </div>
                                                </div>
                                                <img src="/assets/images/jazz.png" alt="JazzCash" style="height: 20px;">
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="tab-content">
                                    <!-- HBL Tab -->
                                    <div id="tab-9" class="tab-pane fade show active" role="tabpanel">
                                        <div class="alert alert-info d-flex align-items-center gap-3 mb-3" style="gap:5px;">
                                            <i class="fa-solid fa-coins fs-4"></i>
                                            <div>
                                                <h6 class="mb-0 fw-bold">Sasta Wallet</h6>
                                                <p class="mb-0 small">Login to access wallet.</p>
                                            </div>
                                            <a href="#" class="btn btn-sm btn-dark ms-auto">Login</a>
                                        </div>

                                        <div class="custom-method mb-3">
                                            <div class="d-flex align-items-center gap-2 mb-2 text-warning" style="gap:5px;">
                                                <i class="fa-solid fa-circle small"></i>
                                                <p class="mb-0 small">Click continue to receive your customer ID</p>
                                            </div>
                                        </div>

                                        <div class="method-field">
                                            <div class="mb-3">
                                                <label class="small fw-bold mb-1">Select Transfer Method</label>
                                                <select class="form-select form-control form-select-sm" name="transfer_method">
                                                    <option>Select Method</option>
                                                    <option value="online">Online Banking</option>
                                                    <option value="atm">ATM Transfer</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="small fw-bold mb-1">Select Bank</label>
                                                <select class="form-select form-control form-select-sm" name="bank_type">
                                                    <option value="card">Card Payment</option>
                                                    <option value="cash">Pay cash at franchise</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="voucher bg-light p-3 rounded small">
                                            <h6 class="fw-bold">Please <span class="text-primary">login</span> to
                                                avail discounts.</h6>
                                            <p class="mb-0 text-muted">By booking, I acknowledge terms & privacy
                                                policy.</p>
                                        </div>
                                    </div>

                                    <!-- JazzCash Tab -->
                                    <div id="tab-10" class="tab-pane fade" role="tabpanel">
                                        <div class="text-center p-4">
                                            <img src="/assets/images/jazz.png" alt="JazzCash" class="mb-3"
                                                style="height: 50px;">
                                            <p class="text-muted">Enter your JazzCash mobile number to proceed.</p>
                                            <input type="text" class="form-control mb-3"
                                                placeholder="03XX-XXXXXXX">
                                            <div class="alert alert-warning small">
                                                Please ensure your mobile wallet is active and has sufficient balance.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="payment-card sticky-top bg-white p-4 rounded-4 shadow-sm" style="top: 20px;">
                <h5 class="fw-bold mb-4">Price Details</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Net Amount</span>
                    <span class="fw-bold">{{ convertCurrency($booking->total_net, $booking->currency) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Taxes & Fees</span>
                    <span class="fw-bold">{{ convertCurrency($booking->total_tax, $booking->currency) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-4 align-items-center">
                    <span class="h6 fw-bold mb-0 text-dark">Total Price</span>
                    <span class="h5 fw-bold mb-0 text-primary">{{ convertCurrency($booking->total_net, $booking->currency) }}</span>
                </div>

                <form id="hotel-confirm-ajax-form" data-booking-id="{{ $booking->id }}">
                    @csrf
                    <!-- Include hidden inputs for payment choices if they are outside, or just move the selects here -->
                    <input type="hidden" name="bank_type" id="hidden_bank_type" value="card">
                    <input type="hidden" name="transfer_method" id="hidden_transfer_method" value="online">

                    <button type="submit"
                        class="btn btn-primary w-100 py-3 rounded-3 fw-bold fs-5 confirm-booking-ajax-btn">
                        <i class="fa-solid fa-check-circle me-2"></i> Confirm Booking
                    </button>
                    <script>
                        $(document).ready(function() {
                            $('select[name="bank_type"]').on('change', function() {
                                $('#hidden_bank_type').val($(this).val());
                            });
                            $('select[name="transfer_method"]').on('change', function() {
                                $('#hidden_transfer_method').val($(this).val());
                            });
                            // Initialize values
                            $('#hidden_bank_type').val($('select[name="bank_type"]').val());
                            $('#hidden_transfer_method').val($('select[name="transfer_method"]').val());
                        });
                    </script>
                    <!-- <button type="button" class="btn btn-outline-secondary w-100 mt-3 rounded-pill prev-step-btn">Modify Details</button> -->
                </form>

                <p class="text-center text-muted small mt-4 mb-0">
                    <i class="fa-solid fa-shield-halved me-1"></i> Secure Booking Guaranteed
                </p>
            </div>
        </div>
    </div>
</div>
