@props(['hotelsData', 'request'])

@php
    $hotels = $hotelsData['hotels']['hotel'] ?? [];
@endphp

<style>
    .hotel-results-container {
        padding: 20px 0;
    }

    .hotel-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        margin-bottom: 25px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #edf0f5;
        display: flex;
        flex-direction: row;
    }

    @media (max-width: 768px) {
        .hotel-card {
            flex-direction: column;
        }
    }

    .hotel-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .hotel-image-wrapper {
        width: 300px;
        height: 220px;
        position: relative;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .hotel-image-wrapper {
            width: 100%;
            height: 200px;
        }
    }

    .hotel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hotel-rating-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(0, 120, 138, 0.9);
        color: #fff;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }

    .hotel-content {
        padding: 24px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .hotel-header {
        margin-bottom: 12px;
    }

    .hotel-name {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 6px;
        line-height: 1.2;
    }

    .hotel-location {
        color: #6c757d;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .hotel-location i {
        color: #00788a;
    }

    .hotel-stars {
        color: #ffc107;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .hotel-footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-top: 1px solid #f0f2f5;
        padding-top: 15px;
    }

    .hotel-price-box {
        text-align: left;
    }

    .hotel-price-label {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 2px;
    }

    .hotel-price-value {
        font-size: 24px;
        font-weight: 800;
        color: #00788a;
    }

    .hotel-currency {
        font-size: 14px;
        font-weight: 600;
        margin-left: 2px;
    }

    .hotel-booking-btn {
        background-color: #00788a;
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .hotel-booking-btn:hover {
        background-color: #005f6d;
        color: #fff;
        transform: scale(1.02);
    }

    .no-hotel-results {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        border: 2px dashed #e0e6ed;
    }

    .no-hotel-results i {
        font-size: 48px;
        color: #cbd5e0;
        margin-bottom: 20px;
    }
</style>

<div class="hotel-results-container">
    @if(count($hotels) > 0)
        @foreach($hotels as $hotel)
            @php
                $info = $hotel['hotelInfo'] ?? [];
                $name = $info['name'] ?? 'Hotel Name Not Available';
                $image = (!empty($info['image'])) ? $info['image'] : 'https://placehold.co/600x400?text=Hotel+Image+Coming+Soon';
                $rating = (int)($info['starRating'] ?? 0);
                $address = $info['add1'] ?? '';
                $city = $info['city'] ?? '';
                $price = $hotel['minPrice'] ?? 'N/A';
                $currency = $hotel['supplierCurrency'] ?? 'AED';
                $lat = $info['lat'] ?? '';
                $lng = $info['lon'] ?? '';
                
                // Get session ID and format rooms from the results or search request
                $sessionId = $hotelsData['generalInfo']['sessionId'] ?? '';
                
                // Helper to format rooms exactly as the API expects
                $formattedRooms = [];
                $rawRooms = $request->input('rooms.Room', []);
                foreach($rawRooms as $idx => $room) {
                    $formattedRooms[] = [
                        'RoomIdentifier' => (int)($room['RoomIdentifier'] ?? ($idx + 1)),
                        'Adult' => (int)($room['Adult'] ?? 1),
                        'Children' => isset($room['Children']) ? [
                            'Count' => (int)($room['Children']['Count'] ?? 0),
                            'ChildAge' => $room['Children']['ChildAge'] ?? []
                        ] : null
                    ];
                }
            @endphp
            <div class="hotel-card">
                <div class="hotel-image-wrapper">
                    <img src="{{ $image }}" alt="{{ $name }}" class="hotel-image" onerror="this.src='https://placehold.co/600x400?text=Hotel+Image'">
                    @if($rating > 0)
                        <div class="hotel-rating-badge">{{ $rating }}.0 <i class="fa-solid fa-star"></i></div>
                    @endif
                </div>
                <div class="hotel-content">
                    <div class="hotel-header">
                        <h3 class="hotel-name">{{ $name }}</h3>
                        <div class="hotel-stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fa-{{ $i < $rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank" class="hotel-location">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $address }}{{ $address && $city ? ', ' : '' }}{{ $city }}
                        </a>
                    </div>
                    
                    <div class="hotel-footer">
                        <div class="hotel-price-box">
                            <span class="hotel-price-label">Starting from</span>
                            <div class="hotel-price-value">
                                <span class="hotel-currency">{{ $currency }}</span> {{ is_numeric($price) ? number_format($price, 2) : $price }}
                            </div>
                        </div>
                        @php
                            $bookingUrl = route('hotels.booking', array_merge($request->query(), [
                                'hotel_id' => $hotel['code'],
                                'session_id' => $sessionId,
                                'hotel_name' => $name,
                                'hotel_address' => $address,
                                'hotel_city' => $city,
                                'hotel_image' => !empty($info['image']) ? $info['image'] : '',
                                'hotel_rating' => $rating,
                            ]));
                        @endphp
                        <button data-url="{{ $bookingUrl }}" data-hotel-id="{{ $hotel['code'] }}" data-session-id="{{ $sessionId }}" data-hotel-name="{{ $name }}" data-hotel-address="{{ $address }}" data-hotel-city="{{ $city }}" data-hotel-image="{{ !empty($info['image']) ? $info['image'] : '' }}" data-hotel-rating="{{ $rating }}" class="hotel-booking-btn">
                            View Details
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="no-hotel-results">
            <i class="fa-solid fa-hotel"></i>
            <h3>No Hotels Found</h3>
            <p class="text-muted">We couldn't find any hotels matching your criteria. Try adjusting your search filters.</p>
        </div>
    @endif
</div>

<script>
    $(document).on('click', '.hotel-booking-btn', function(e) {
        e.preventDefault();
        let hotelId = $(this).data('hotel-id');
        let sessionId = $(this).data('session-id');
        let hotelName = $(this).data('hotel-name');
        let hotelAddress = $(this).data('hotel-address');
        let hotelCity = $(this).data('hotel-city');
        let hotelImage = $(this).data('hotel-image');
        let hotelRating = $(this).data('hotel-rating');
        let url = $(this).data('url');
        localStorage.clear();
        window.location.href = url;
    });
</script>