@extends('home/layouts/master')

@section('title', 'Booking Confirmed!')

@section('style')
<style>
    .confirmation-container {
        padding: 80px 0;
        background: #f0fdf4;
        min-height: 80vh;
        display: flex;
        align-items: center;
    }
    .confirmation-card {
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.05);
        padding: 50px;
        text-align: center;
    }
    .success-icon {
        width: 100px;
        height: 100px;
        background: #22c55e;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 50px;
        margin: 0 auto 30px;
        box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);
        animation: scaleIn 0.5s ease-out;
    }
    @keyframes scaleIn {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }
    .conf-id {
        background: #f0fdf4;
        color: #166534;
        padding: 10px 25px;
        border-radius: 12px;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        font-size: 24px;
        display: inline-block;
        margin: 20px 0;
        border: 2px dashed #bbf7d0;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 40px;
        text-align: left;
    }
    .info-item {
        background: #f8fafc;
        padding: 15px;
        border-radius: 15px;
    }
    .info-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    .info-value {
        font-weight: 700;
        color: #1e293b;
    }
    .home-btn {
        background: #00788a;
        color: #fff;
        border: none;
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        margin-top: 40px;
        transition: all 0.3s;
    }
    .home-btn:hover {
        background: #005f6d;
        color: #fff;
        transform: translateY(-2px);
    }
</style>
@endsection

@section('content')
<div class="confirmation-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirmation-card" id="printable-confirmation">
                    <div class="success-icon">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h1 class="fw-bold mb-2">Booking Confirmed!</h1>
                    <p class="text-muted">Thank you for choosing us. Your hotel stay is officially reserved.</p>
                    
                    <div class="text-muted small mt-4">Supplier Confirmation ID</div>
                    <div class="conf-id">{{ $booking->pnr }}</div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Booking Reference</div>
                            <div class="info-value">{{ $booking->reference }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Hotel</div>
                            <div class="info-value">{{ $booking->hotel_name }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Check-in</div>
                            <div class="info-value">{{ $booking->check_in->format('d M, Y') }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Rooms</div>
                            <div class="info-value">{{ $booking->rooms->count() }} Room(s)</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4 print-hide">
                        <a href="{{ url('/') }}" class="home-btn mt-0">
                            <i class="fa-solid fa-house me-2"></i> Homepage
                        </a>
                        <button onclick="window.print()" class="home-btn mt-0" style="background: #64748b;">
                            <i class="fa-solid fa-print me-2"></i> Print 
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css" media="print">
    @page { size: auto; margin: 20mm; }
    body * { visibility: hidden; }
    #printable-confirmation, #printable-confirmation * { visibility: visible; }
    #printable-confirmation { 
        position: absolute; 
        left: 0; 
        top: 0; 
        width: 100%; 
        box-shadow: none !important; 
        padding: 0 !important;
    }
    .print-hide { display: none !important; }
    .success-icon { border: 2px solid #22c55e !important; color: #22c55e !important; background: none !important; }
</style>
@endsection
