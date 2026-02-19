@extends('home/layouts/master')

@section('title', $hotel['hotelInfo']['name'] ?? 'Hotel Details')

@section('style')
<style>
    .hotel-details-container {
        padding: 40px 0;
        background: #f8faff;
    }
    .hotel-header-section {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 25px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    .hotel-title-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    .hotel-name-large {
        font-size: 32px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
    }
    .hotel-meta-info {
        display: flex;
        gap: 20px;
        color: #6c757d;
        font-size: 15px;
        margin-top: 10px;
    }
    .hotel-meta-info i {
        color: #00788a;
    }
    .hotel-stars-large {
        color: #ffc107;
        font-size: 18px;
    }
    .hotel-gallery-single {
        width: 100%;
        height: 500px;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .hotel-gallery-single img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .room-options-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 25px;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .room-card {
        background: #fff;
        border-radius: 15px;
        border: 1px solid #eef2f7;
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .room-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border-color: #00788a;
    }
    .room-grid {
        display: grid;
        grid-template-columns: 250px 1fr 220px;
    }
    @media (max-width: 768px) {
        .room-grid {
            grid-template-columns: 1fr;
        }
    }
    .room-image {
        height: 100%;
        background: #f0f4f8;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e0;
    }
    .room-image i {
        font-size: 40px;
    }
    .room-details {
        padding: 25px;
        border-right: 1px solid #f0f2f5;
        border-left: 1px solid #f0f2f5;
    }
    .room-name {
        font-size: 19px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 10px;
    }
    .room-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 15px;
    }
    .amenity-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .meal-plan {
        color: #059669;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .room-pricing {
        padding: 25px;
        background: #fafbfc;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }
    .room-price-total {
        font-size: 26px;
        font-weight: 800;
        color: #00788a;
    }
    .room-price-currency {
        font-size: 14px;
        font-weight: 600;
    }
    .room-tax-info {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 4px;
    }
    .select-room-btn {
        background: #00788a;
        color: #fff;
        border: none;
        padding: 12px 20px;
        border-radius: 10px;
        font-weight: 700;
        margin-top: 15px;
        transition: all 0.2s ease;
    }
    .select-room-btn:hover {
        background: #005f6d;
        transform: scale(1.02);
    }
    .cancellation-policy {
        font-size: 11px;
        color: #ef4444;
        margin-top: 10px;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="hotel-details-container">
    <div class="container">
        <!-- Hotel Header -->
        <div class="hotel-header-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="hotel-name-large">{{ $hotel['hotelInfo']['name'] }}</h1>
                    <div class="hotel-meta-info">
                        <div class="hotel-stars-large">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa-{{ $i < (int)$hotel['hotelInfo']['starRating'] ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <span><i class="fa-solid fa-location-dot"></i> {{ $hotel['hotelInfo']['add1'] }}, {{ $hotel['hotelInfo']['city'] }}</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="https://www.google.com/maps?q={{ $hotel['hotelInfo']['lat'] }},{{ $hotel['hotelInfo']['lon'] }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fa-solid fa-map-location-dot me-2"></i> View on Map
                    </a>
                </div>
            </div>

            <div class="hotel-gallery-single mt-4">
                @php 
                    $mainImg = !empty($hotel['hotelInfo']['image']) ? $hotel['hotelInfo']['image'] : 'https://placehold.co/1200x600?text=Hotel+Image';
                @endphp
                <img src="{{ $mainImg }}" alt="{{ $hotel['hotelInfo']['name'] }}" onerror="this.src='https://placehold.co/1200x600?text=Hotel+Image'">
            </div>

            <div class="row mt-4">
                <div class="col-lg-8">
                    <h5 class="fw-bold mb-3">About the Hotel</h5>
                    <p class="text-muted" style="line-height: 1.7;">
                        {{ $hotel['hotelInfo']['description'] ?: "Located in the heart of {$hotel['hotelInfo']['city']}, {$hotel['hotelInfo']['name']} offers a premium experience with comfortable accommodations and world-class service. Whether you're traveling for business or leisure, our hotel provides everything you need for a memorable stay." }}
                    </p>
                </div>
                <div class="col-lg-4">
                    <h5 class="fw-bold mb-3">Hotel Amenities</h5>
                    <div class="row g-2 h-75">
                        <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-wifi me-2"></i> Free WiFi</span></div>
                        <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-p me-2"></i> Parking</span></div>
                        <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-clock me-2"></i> 24h Front Desk</span></div>
                        <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-snowflake me-2"></i> AC</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Selection -->
        <h4 class="room-options-title"><i class="fa-solid fa-bed"></i> Available Room Options</h4>
        
        @if(isset($hotel['rooms']['room']) && count($hotel['rooms']['room']) > 0)
            @foreach($hotel['rooms']['room'] as $room)
                <div class="room-card">
                    <div class="room-grid">
                        <div class="room-image">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        <div class="room-details">
                            <h4 class="room-name">{{ $room['roomName'] }}</h4>
                            <div class="room-amenities">
                                @if(isset($room['sMeal']))
                                    @foreach(explode(',', $room['sMeal']) as $amenity)
                                        <span class="amenity-badge">{{ trim($amenity) }}</span>
                                    @endforeach
                                @endif
                                <span class="amenity-badge"><i class="fa-solid fa-user me-1"></i> {{ $room['adult'] }} Adult(s)</span>
                            </div>
                            <div class="meal-plan">
                                <i class="fa-solid fa-utensils"></i> {{ $room['meal'] ?: 'Room Only' }}
                            </div>
                            <p class="text-muted small mt-2 mb-0">{{ $room['mealDescription'] }}</p>
                        </div>
                        <div class="room-pricing">
                            <span class="text-muted small mb-1">Total for Stay</span>
                            <div class="room-price-total">
                                <span class="room-price-currency">{{ $room['price']['supplierCurrency'] }}</span> 
                                {{ number_format($room['price']['supplierNet'], 2) }}
                            </div>
                            <span class="room-tax-info">Incl. taxes & fees</span>
                            <div class="cancellation-policy">
                                {{ $room['rateType'] }}
                            </div>
                            <button class="select-room-btn checkout-btn" 
                                    data-rate-key="{{ $room['rateKey'] }}"
                                    data-group-code="{{ $room['groupCode'] }}">
                                Select Room
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
</div>
<script>
    $(document).on('click', '.checkout-btn', function(e) {
        e.preventDefault();
        const rateKey = $(this).data('rate-key');
        const groupCode = $(this).data('group-code');
        const hotelCode = $(this).data('hotel-code');
        
        const form = $('<form action="{{ route("hotels.checkout") }}" method="POST"></form>');
        form.append(`<input type="hidden" name="_token" value="{{ csrf_token() }}">`);
        form.append(`<input type="hidden" name="hotel_id" value="{{ $hotel['hotelInfo']['code'] }}">`);
        form.append(`<input type="hidden" name="session_id" value="{{ $general['sessionId'] }}">`);
        form.append(`<input type="hidden" name="group_code" value="${groupCode}">`);
        form.append(`<input type="hidden" name="rate_key" value="${rateKey}">`);
        
        // Add hotel info for the summary page
        form.append(`<input type="hidden" name="hotel_name" value="{{ $hotel['hotelInfo']['name'] }}">`);
        form.append(`<input type="hidden" name="hotel_address" value="{{ $hotel['hotelInfo']['add1'] }}">`);
        form.append(`<input type="hidden" name="hotel_city" value="{{ $hotel['hotelInfo']['city'] }}">`);
        form.append(`<input type="hidden" name="hotel_image" value="{{ !empty($hotel['hotelInfo']['image']) ? $hotel['hotelInfo']['image'] : '' }}">`);
        form.append(`<input type="hidden" name="hotel_rating" value="{{ $hotel['hotelInfo']['starRating'] }}">`);
        
        $('body').append(form);
        form.submit();
    });
</script>
@endsection
