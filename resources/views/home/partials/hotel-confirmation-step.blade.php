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
    font-size: 15px !important;
    padding: 10px 10px;
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


</style>


<div class="booking-step-content" data-step="4">
    <div class="confirmation-card bg-white p-3 rounded-5 shadow-lg text-center mx-auto" id="printable-confirmation" style="max-width: 700px;">
        <div class="success-icon mb-4 mx-auto d-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow-lg"
            style="width: 100px; height: 100px; font-size: 50px;">
            <i class="fa-solid fa-check"></i>
        </div>
        @if (isset($is_cash) && $is_cash)
            <h1 class="fw-bold mb-3 text-warning">Booking Request Received!</h1>
            <p class="text-muted fs-5 mb-4">Your request has been received. Please visit our franchise to complete your
                payment.</p>
            <div class="alert alert-warning border-0 rounded-4 p-3 mb-5">
                <i class="fa-solid fa-clock me-2"></i> <strong>Important:</strong> Please pay for your booking within
                <strong>24 hours</strong> to avoid cancellation.
            </div>
        @else
            <h1 class="fw-bold mb-3">Booking Confirmed!</h1>
            <p class="text-muted fs-5 mb-5">Thank you for choosing us. Your hotel stay is officially reserved.</p>

            <div class="conf-box p-4 rounded-4 mb-5" style="border: 2px dashed #bbf7d0; background: #f0fdf4;">
                <div class="text-muted small fw-bold text-uppercase mb-2">Supplier Confirmation ID</div>
                <div class="conf-id text-success fw-bold fs-3" style="font-family: monospace;">{{ $booking->pnr }}</div>
            </div>
        @endif

        <div class="info-grid row g-3 text-start mb-5">
            <div class="col-sm-6">
                <div class="info-item p-3 rounded-3 bg-light">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Order ID</div>
                    <div class="fw-bold">{{ $booking->reference }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="info-item p-3 rounded-3 bg-light">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Hotel</div>
                    <div class="fw-bold text-truncate">{{ $booking->hotel_name }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="info-item p-3 rounded-3 bg-light">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Check-in</div>
                    <div class="fw-bold">{{ $booking->check_in->format('d M, Y') }}</div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="info-item p-3 rounded-3 bg-light">
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 10px;">Rooms</div>
                    <div class="fw-bold">{{ $booking->rooms->count() }} Room(s)</div>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 print-hide">
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg  rounded-pill fw-bold">
                <i class="fa-solid fa-house me-2"></i> Return to Homepage
            </a>
            <button class="print-hot btn btn-outline-secondary rounded-pill py-2" onclick="window.print()">
                <i class="fa-solid fa-print me-2"></i> Print Confirmation
            </button>
        </div>
    </div>
</div>

<style type="text/css" media="print">
    @page {
        size: A4;
        /* margin: 20mm; */
    }

    body * {
        visibility: hidden;
    }

    #printable-confirmation,
    #printable-confirmation * {
        visibility: visible;
    }

    #printable-confirmation {
        /* position: absolute !important;
        left: 0 !important;
        top: 0 !important; */
        width: 100% !important;
        /* box-shadow: none !important; */
        /* padding: 0 !important;
        margin: 0 !important; */
    }

    .print-hide {
        display: none !important;
    }

    .success-icon {
        border: 2px solid #22c55e !important;
        color: #22c55e !important;
        background: none !important;
    }

    .stepper,
    .booking-summary-sidebar,
    .navbar,
    footer {
        display: none !important;
    }
    .booking-step-content {
    margin-top: -500px !important;
}
</style>
