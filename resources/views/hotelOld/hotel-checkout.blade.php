@extends('home/layouts/master')

@section('title', 'Hotel Checkout')

@section('style')
<style>
    .checkout-container {
        padding: 60px 0;
        background: #f4f7fa;
    }
    .checkout-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        padding: 35px;
        margin-bottom: 30px;
    }
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-title i {
        color: #00788a;
    }
    .hotel-summary-mini {
        display: flex;
        gap: 20px;
        padding-bottom: 25px;
        border-bottom: 1px solid #edf2f7;
        margin-bottom: 25px;
    }
    .hotel-img-mini {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
    }
    .hotel-info-mini h3 {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 5px;
    }
    .price-breakup-table {
        width: 100%;
        border-collapse: collapse;
    }
    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .price-row.total {
        border-bottom: none;
        padding-top: 20px;
        margin-top: 10px;
    }
    .price-label {
        font-weight: 500;
        color: #64748b;
    }
    .price-value {
        font-weight: 700;
        color: #1e293b;
    }
    .grand-total-label {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
    }
    .grand-total-value {
        font-size: 24px;
        font-weight: 800;
        color: #00788a;
    }
    .daily-rate-item {
        background: #f8fafc;
        padding: 10px 15px;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        font-size: 13px;
    }
    .passenger-form .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }
    .passenger-form .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
    }
    .confirm-booking-btn {
        background: #00788a;
        color: #fff;
        border: none;
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 18px;
        width: 100%;
        margin-top: 20px;
        transition: all 0.3s ease;
    }
    .confirm-booking-btn:hover {
        background: #005f6d;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 120, 138, 0.25);
    }
    .policy-card {
        background: #fffcf0;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 13px;
    }
    .policy-title {
        font-weight: 700;
        color: #856404;
        margin-bottom: 5px;
    }
    .remark-box {
        background: #f1f5f9;
        padding: 15px;
        border-radius: 10px;
        font-size: 13px;
        color: #475569;
        margin-top: 20px;
    }
    .remark-title {
        font-weight: 700;
        margin-bottom: 5px;
    }
</style>
@endsection

@section('content')
@php 
    $rooms = $breakup['hotel']['rooms']['room'] ?? [];
    $currency = $breakup['hotel']['supplierCurrency'] ?? 'AED';
@endphp
<div class="checkout-container">
    <div class="container">
        <div class="row">
            <!-- Left Side: Passenger Info & Payment -->
            <div class="col-lg-8">
                <div class="checkout-card">
                    <h2 class="section-title"><i class="fa-solid fa-envelope"></i> Contact Details</h2>
                    <form action="{{ route('hotels.saveBooking') }}" method="POST" class="passenger-form">
                        @csrf
                        {{-- Hidden Booking Meta --}}
                        <input type="hidden" name="hotel_id" value="{{ $hotel['code'] }}">
                        <input type="hidden" name="hotel_name" value="{{ $hotel['name'] }}">
                        <input type="hidden" name="hotel_city" value="{{ $hotel['city'] }}">
                        <input type="hidden" name="session_id" value="{{ $breakup['sessionId'] ?? $request['session_id'] }}">
                        <input type="hidden" name="destination_code" value="{{ $request['destination_code'] ?? '160-0' }}">
                        <input type="hidden" name="group_code" value="{{ $request['group_code'] }}">
                        <input type="hidden" name="nationality" value="{{ $request['nationality'] ?? 'AE' }}">
                        <input type="hidden" name="total_net" value="{{ $breakup['monetary']['totalNet'] ?? 0 }}">
                        <input type="hidden" name="total_gross" value="{{ $breakup['monetary']['totalGross'] ?? 0 }}">
                        <input type="hidden" name="total_tax" value="{{ $breakup['monetary']['totalTax'] ?? 0 }}">
                        <input type="hidden" name="currency" value="{{ $breakup['hotel']['supplierCurrency'] ?? 'AED' }}">
                        <input type="hidden" name="check_in" value="{{ $request['check_in'] ?? '' }}">
                        <input type="hidden" name="check_out" value="{{ $request['check_out'] ?? '' }}">
                        <input type="hidden" name="raw_prebook_response" value="{{ json_encode($breakup) }}">

                        <div class="row g-3 mb-5">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required placeholder="Lead Contact First Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required placeholder="Lead Contact Last Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required placeholder="your@email.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" required placeholder="+971 ...">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h2 class="section-title mt-4"><i class="fa-solid fa-users"></i> Guest Details</h2>
                        
                        @foreach($rooms as $roomIndex => $item)
                            <div class="room-guest-section mb-5 p-3" style="background: #f8fafc; border-radius: 15px;">
                                <h5 class="fw-bold mb-4" style="color: #00788a;">
                                    <i class="fa-solid fa-bed me-2"></i> Room {{ $roomIndex + 1 }}: {{ $item['roomName'] }}
                                </h5>
                                
                                {{-- Pass room data for saving --}}
                                <input type="hidden" name="rooms_data[{{ $roomIndex }}]" value="{{ json_encode($item) }}">

                                {{-- Adults --}}
                                @for($i = 0; $i < ($item['adult'] ?? 1); $i++)
                                    <div class="guest-row mb-4">
                                        <h6 class="small fw-bold text-muted mb-3">Adult {{ $i + 1 }} @if($roomIndex == 0 && $i == 0) (Lead Guest) @endif</h6>
                                        <div class="row g-3">
                                            <div class="col-md-2">
                                                <select name="guests[{{ $roomIndex }}][{{ $i }}][title]" class="form-select form-control">
                                                    <option>Mr</option>
                                                    <option>Mrs</option>
                                                    <option>Ms</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="guests[{{ $roomIndex }}][{{ $i }}][first_name]" class="form-control" placeholder="First Name" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="guests[{{ $roomIndex }}][{{ $i }}][last_name]" class="form-control" placeholder="Last Name" required>
                                            </div>
                                            <input type="hidden" name="guests[{{ $roomIndex }}][{{ $i }}][type]" value="adult">
                                        </div>
                                    </div>
                                @endfor

                                {{-- Children --}}
                                @if(isset($item['children']) && $item['children'] > 0)
                                    @for($j = 0; $j < $item['children']; $j++)
                                        @php $childIdx = ($item['adult'] ?? 1) + $j; @endphp
                                        <div class="guest-row mb-4">
                                            <h6 class="small fw-bold text-muted mb-3">Child {{ $j + 1 }}</h6>
                                            <div class="row g-3">
                                                <div class="col-md-2">
                                                    <select name="guests[{{ $roomIndex }}][{{ $childIdx }}][title]" class="form-select form-control">
                                                        <option>Mstr</option>
                                                        <option>Miss</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="guests[{{ $roomIndex }}][{{ $childIdx }}][first_name]" class="form-control" placeholder="First Name" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="guests[{{ $roomIndex }}][{{ $childIdx }}][last_name]" class="form-control" placeholder="Last Name" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="guests[{{ $roomIndex }}][{{ $childIdx }}][age]" class="form-control" placeholder="Age" required min="0" max="17">
                                                </div>
                                                <input type="hidden" name="guests[{{ $roomIndex }}][{{ $childIdx }}][type]" value="child">
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>
                        @endforeach

                        <div class="mb-4">
                            <label class="form-label">Special Requests (Optional)</label>
                            <textarea name="remarks" class="form-control" rows="3" placeholder="e.g. Late check-in, honeymoon, high floor..."></textarea>
                        </div>

                        <button type="submit" class="confirm-booking-btn">Proceed to Payment Information</button>
                    </form>
                </div>
            </div>

            <!-- Right Side: Booking Summary & Price Breakup -->
            <div class="col-lg-4">
                <div class="checkout-card p-4">
                    <h2 class="section-title mb-4"><i class="fa-solid fa-receipt"></i> Booking Summary</h2>
                    
                    <div class="hotel-summary-mini">
                        <img src="{{ $hotel['image'] ?: 'https://placehold.co/120x120?text=Hotel' }}" class="hotel-img-mini" alt="Hotel">
                        <div class="hotel-info-mini">
                            <h3>{{ $hotel['name'] }}</h3>
                            <div class="text-muted small">
                                <p class="mb-1"><i class="fa-solid fa-location-dot me-1"></i> {{ $hotel['city'] }}</p>
                                <div class="text-warning">
                                    @for($i = 0; $i < (int)$hotel['rating']; $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>


                    @foreach($rooms as $item)
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" style="color: #00788a;">{{ $item['roomName'] }}</h5>
                            
                            <div class="price-breakup-details">
                                <h6 class="small fw-bold text-muted mb-2">Daily Rate Breakdown</h6>
                                @if(isset($item['price']['priceBreakdownRules']['perNightInfo']))
                                    @foreach($item['price']['priceBreakdownRules']['perNightInfo'] as $date)
                                        <div class="daily-rate-item">
                                            <span>{{ \Carbon\Carbon::parse($date['startDate'])->format('d M') }}</span>
                                            <span class="fw-bold">{{ $currency }} {{ number_format($date['amount'], 2) }}</span>
                                        </div>
                                    @endforeach
                                @endif
                                
                                <div class="price-row mt-3">
                                    <span class="price-label">Net Amount</span>
                                    <span class="price-value">{{ $currency }} {{ number_format($item['price']['gross'] ?? $item['price']['supplierGross'], 2) }}</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Taxes & Fees</span>
                                    <span class="price-value">{{ $currency }} {{ number_format($item['price']['tax'] ?? $item['price']['supplierTax'], 2) }}</span>
                                </div>
                                <div class="price-row total">
                                    <span class="grand-total-label">Subtotal</span>
                                    <span class="grand-total-value">{{ $currency }} {{ number_format($item['price']['net'] ?? $item['price']['supplierNet'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Cancellation Policies --}}
                        @if(isset($item['policies']['policy']))
                            <div class="mt-4">
                                <h6 class="fw-bold small text-muted mb-3">Policies & Cancellation</h6>
                                @foreach($item['policies']['policy'] as $policy)
                                    <div class="policy-card">
                                        <div class="policy-title">
                                            @if($policy['type'] == 'CAN') Cancellation Policy @elseif($policy['type'] == 'MOD') Modification Policy @else {{ $policy['type'] }} Policy @endif
                                        </div>
                                        <p class="mb-1">{{ $policy['textCondition'] }}</p>
                                        @if(isset($policy['condition']) && is_array($policy['condition']))
                                            @foreach($policy['condition'] as $cond)
                                                <div class="text-danger small fw-bold">
                                                    From {{ \Carbon\Carbon::parse($cond['fromDate'])->format('d M') }}: {{ $currency }} {{ number_format($cond['text'], 2) }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Remarks --}}
                        @if(isset($item['remarks']['remark']))
                            <div class="remark-box">
                                @foreach($item['remarks']['remark'] as $remark)
                                    <div class="mb-2">
                                        <div class="remark-title text-uppercase">{{ $remark['type'] }}</div>
                                        <div>{!! nl2br(e($remark['text'])) !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                    
                    <div class="alert alert-info py-2 px-3 small mt-3" style="font-size: 11px; border-radius: 10px;">
                        <i class="fa-solid fa-circle-info me-1"></i> Special rates applied based on your search criteria. Prices are subject to availability.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
