@extends('home/layouts/master')

@section('title', 'Hotel Booking Details')

@section('style')
<style>
    .booking-container { max-width: 960px; margin: auto; }
    .back-ground { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
    .view-bookings  { padding: 50px 0 20px; }
    .view-bookings2 { padding: 10px 0 40px; }
    .view-bookings3 { padding: 10px 0 40px; }
    .view-head h3   { font-size: 30px; font-weight: 700; margin-bottom: 20px; }
    .section-head h4 { font-size: 20px; font-weight: 700; margin-bottom: 16px; }

    /* Status header bar */
    .booking-header-bar {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 12px; margin-bottom: 0;
    }
    .booking-header-bar .header-item h5 { margin-bottom: 4px; font-size: 13px; color: #888; text-transform: uppercase; letter-spacing: .5px; }
    .booking-header-bar .header-item p  { margin: 0; font-weight: 700; font-size: 15px; }

    .status-badge {
        display: inline-block; padding: 5px 16px; border-radius: 20px;
        font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
    }
    .status-confirmed  { background: #dcfce7; color: #166534; }
    .status-pending    { background: #fef9c3; color: #854d0e; }
    .status-cancelled, .status-failed { background: #fee2e2; color: #991b1b; }
    .status-initial    { background: #f1f5f9; color: #475569; }

    /* Hotel summary card */
    .hotel-summary-card {
        display: flex; gap: 20px; align-items: flex-start;
        background: #f8faff; border-radius: 10px; padding: 20px;
        border: 1px solid #e8eef8; margin-bottom: 20px;
    }
    .hotel-summary-card img {
        width: 110px; height: 90px; object-fit: cover; border-radius: 8px; flex-shrink: 0;
    }
    .hotel-summary-info h4 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
    .hotel-summary-info .text-muted { font-size: 13px; }

    .check-dates {
        display: flex; gap: 32px; flex-wrap: wrap; margin-top: 12px;
    }
    .check-date-item label { font-size: 11px; text-transform: uppercase; color: #888; letter-spacing: .5px; display: block; }
    .check-date-item strong { font-size: 16px; font-weight: 700; color: #1a1a1a; }

    /* Room cards */
    .room-booking-card {
        border: 1px solid #eef2f7; border-radius: 10px; overflow: hidden; margin-bottom: 16px;
    }
    .room-booking-header {
        background: #f1f5f9; padding: 12px 20px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .room-booking-header span { font-size: 13px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .5px; }
    .room-booking-body { padding: 16px 20px; }

    .guest-pill {
        display: inline-flex; align-items: center; gap: 8px;
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
        padding: 7px 14px; margin: 4px 4px 4px 0; font-size: 14px;
    }
    .guest-pill i { color: #00788a; font-size: 13px; }

    /* Price summary */
    .price-row { display: flex; justify-content: space-between; font-size: 14px; color: #333; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    .price-row:last-child { border-bottom: none; }
    .price-row.total { font-weight: 700; font-size: 16px; color: #00788a; padding-top: 14px; border-top: 2px solid #e2e8f0; border-bottom: none; }

    .info-chip { background: #f1f5f9; border-radius: 6px; padding: 10px 14px; font-size: 13px; color: #475569; display: inline-block; }
    .info-chip strong { color: #1a1a1a; }

    @media (max-width: 576px) {
        .hotel-summary-card { flex-direction: column; }
        .hotel-summary-card img { width: 100%; height: 160px; }
        .booking-header-bar { flex-direction: column; align-items: flex-start; }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            <section class="view-bookings">
                <div class="booking-container">
                    <div class="view-head">
                        <h3><i class="fa-solid fa-hotel me-2" style="color:#00788a;"></i> Hotel Booking</h3>
                    </div>

                    {{-- Status Header Card --}}
                    <div class="back-ground mb-4">
                        <div class="booking-header-bar">
                            <div class="header-item">
                                <h5>Order / Reference</h5>
                                <p>{{ $hotelBooking->reference ?? ('HB-' . $hotelBooking->id) }}</p>
                            </div>
                            <div class="header-item">
                                <h5>Booking Date</h5>
                                <p>{{ $hotelBooking->created_at->format('D, d M Y') }}</p>
                            </div>
                            <div class="header-item">
                                <h5>Guest Name</h5>
                                <p>{{ $hotelBooking->client->name ?? '—' }}</p>
                            </div>
                            <div class="header-item">
                                <h5>Status</h5>
                                @php $status = strtolower($hotelBooking->status ?? 'pending'); @endphp
                                <span class="status-badge status-{{ $status }}">{{ ucfirst($status) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Hotel Summary --}}
                    <div class="back-ground mb-4">
                        <div class="section-head"><h4><i class="fa-solid fa-building me-2 text-muted"></i>Hotel Details</h4></div>
                        <div class="hotel-summary-card">
                            <img src="{{ $hotelBooking->hotel_image ?? 'https://placehold.co/220x180?text=Hotel' }}"
                                 alt="{{ $hotelBooking->hotel_name }}"
                                 onerror="this.src='https://placehold.co/220x180?text=Hotel'">
                            <div class="hotel-summary-info">
                                <h4>{{ $hotelBooking->hotel_name ?? '—' }}</h4>
                                <div class="text-muted">
                                    <i class="fa-solid fa-location-dot me-1" style="color:#00788a;"></i>
                                    {{ $hotelBooking->city ?? '—' }}
                                </div>
                                <div class="check-dates mt-3">
                                    <div class="check-date-item">
                                        <label>Check-in</label>
                                        <strong>{{ $hotelBooking->check_in?->format('D, d M Y') ?? '—' }}</strong>
                                    </div>
                                    <div class="check-date-item">
                                        <label>Check-out</label>
                                        <strong>{{ $hotelBooking->check_out?->format('D, d M Y') ?? '—' }}</strong>
                                    </div>
                                    @if($hotelBooking->check_in && $hotelBooking->check_out)
                                    <div class="check-date-item">
                                        <label>Nights</label>
                                        <strong>{{ $hotelBooking->check_in->diffInDays($hotelBooking->check_out) }}</strong>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @if($hotelBooking->nationality)
                                <span class="info-chip"><i class="fa-solid fa-flag me-1"></i> Nationality: <strong>{{ $hotelBooking->nationality }}</strong></span>
                            @endif
                            @if($hotelBooking->source)
                                <span class="info-chip"><i class="fa-solid fa-satellite-dish me-1"></i> Source: <strong>{{ $hotelBooking->source }}</strong></span>
                            @endif
                            @if($hotelBooking->confirmation_no)
                                <span class="info-chip"><i class="fa-solid fa-check-circle me-1" style="color:#00788a;"></i> Confirmation No: <strong>{{ $hotelBooking->confirmation_no }}</strong></span>
                            @endif
                            @if($hotelBooking->pnr)
                                <span class="info-chip"><i class="fa-solid fa-barcode me-1"></i> PNR: <strong>{{ $hotelBooking->pnr }}</strong></span>
                            @endif
                        </div>
                    </div>

                    {{-- Rooms & Guests --}}
                    <div class="back-ground">
                        <div class="section-head"><h4><i class="fa-solid fa-bed me-2 text-muted"></i>Rooms & Guests</h4></div>
                        @forelse($hotelBooking->rooms as $i => $room)
                        <div class="room-booking-card">
                            <div class="room-booking-header">
                                <span><i class="fa-solid fa-bed me-2"></i>Room {{ $i + 1 }}: {{ $room->room_name ?? 'N/A' }}</span>
                                <span>{{ $room->meal_plan ?? '' }}</span>
                            </div>
                            <div class="room-booking-body">
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($room->rate_type)
                                        <span class="info-chip" style="font-size:12px;">Rate: <strong>{{ $room->rate_type }}</strong></span>
                                    @endif
                                    <span class="info-chip" style="font-size:12px;">
                                        Net: <strong>{{ $hotelBooking->currency }} {{ number_format($room->net_price, 2) }}</strong>
                                    </span>
                                </div>
                                @forelse($room->passengers as $guest)
                                <div class="guest-pill">
                                    <i class="fa-solid fa-user"></i>
                                    <span>
                                        {{ $guest->title }} {{ $guest->given_name }} {{ $guest->surname }}
                                        @if($guest->type === 'child') <em class="text-muted ms-1" style="font-size:11px;">(Child)</em> @endif
                                        @if($guest->is_lead_pax) <em class="text-muted ms-1" style="font-size:11px;">★ Lead</em> @endif
                                    </span>
                                </div>
                                @empty
                                <p class="text-muted small mb-0">No guest details found.</p>
                                @endforelse
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">No room details found.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <section class="view-bookings3">
                <div class="booking-container">
                    {{-- Price Summary --}}
                    <div class="back-ground mb-4">
                        <div class="section-head"><h4><i class="fa-solid fa-receipt me-2 text-muted"></i>Price Summary</h4></div>
                        <div class="price-row">
                            <span>Net Price</span>
                            <span>{{ $hotelBooking->currency }} {{ number_format($hotelBooking->total_net, 2) }}</span>
                        </div>
                        <div class="price-row">
                            <span>Tax</span>
                            <span>{{ $hotelBooking->currency }} {{ number_format($hotelBooking->total_tax, 2) }}</span>
                        </div>
                        <div class="price-row total">
                            <span>Total</span>
                            <span>{{ $hotelBooking->currency }} {{ number_format($hotelBooking->total_gross, 2) }}</span>
                        </div>
                    </div>

                    {{-- Contact Info --}}
                    <div class="back-ground">
                        <div class="section-head"><h4><i class="fa-solid fa-address-card me-2 text-muted"></i>Lead Guest</h4></div>
                        @if($hotelBooking->client)
                        <div class="d-flex flex-column gap-2">
                            <div class="guest-pill">
                                <i class="fa-solid fa-user"></i>
                                <span>{{ $hotelBooking->client->name }}</span>
                            </div>
                            <div class="guest-pill">
                                <i class="fa-solid fa-envelope"></i>
                                <span>{{ $hotelBooking->client->email }}</span>
                            </div>
                            @if($hotelBooking->client->phone)
                            <div class="guest-pill">
                                <i class="fa-solid fa-phone"></i>
                                <span>{{ $hotelBooking->client->phone }}</span>
                            </div>
                            @endif
                        </div>
                        @else
                        <p class="text-muted small">No contact info found.</p>
                        @endif

                        @if($hotelBooking->remarks)
                        <div class="mt-3">
                            <label class="text-muted small fw-bold">Remarks</label>
                            <p class="small mt-1">{{ $hotelBooking->remarks }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
