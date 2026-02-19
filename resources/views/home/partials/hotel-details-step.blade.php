@php
    $mainImg = !empty($hotel['hotelInfo']['image'])
        ? $hotel['hotelInfo']['image']
        : 'https://placehold.co/1200x600?text=Hotel+Image';
@endphp

<style>
    .btn-outline-primary {
        --bs-btn-color: #127f9f;
        --bs-btn-border-color: #127f9f;
        --bs-btn-hover-color: #fff;
        --bs-btn-hover-bg: #127f9f;
        --bs-btn-hover-border-color: #127f9f;
        --bs-btn-focus-shadow-rgb: 13, 110, 253;
        --bs-btn-active-color: #fff;
        --bs-btn-active-bg: #127f9f;
        --bs-btn-active-border-color: #127f9f;
        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
        --bs-btn-disabled-color: #127f9f;
        --bs-btn-disabled-bg: transparent;
        --bs-btn-disabled-border-color: #127f9f;
        --bs-gradient: none;
    }

    .btn-outline-primary {
        color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:hover {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:active {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:focus {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:disabled {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:disabled:hover {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:disabled:active {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .btn-outline-primary:disabled:focus {
        color: #fff;
        background-color: #127f9f;
        border-color: #127f9f;
    }

    .hotel-name-large {
        margin-bottom: 6px;
    }

    .hotel-stars-large {
        margin-bottom: 10px;
    }

    .col-6 {

        margin-bottom: 16px;
    }

    .bg-primary {
        background-color: #00788a !important;
    }

    .badge {

        color: #fff;
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

    h4.heading-hotl {
        color: #000 !important;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    h6.parag-hotl {
        margin-bottom: 5px !important;
    }

    button.comb-hotl {
        font-size: 14px;
    }
</style>
<div class="booking-step-content" data-step="1">
    <!-- Hotel Header -->
    <div class="hotel-header-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="hotel-name-large">{{ $hotel['hotelInfo']['name'] }}</h1>
                <div class="hotel-meta-info">
                    <div class="hotel-stars-large">
                        @for ($i = 0; $i < 5; $i++)
                            <i
                                class="fa-{{ $i < (int) $hotel['hotelInfo']['starRating'] ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                    </div>
                    <span><i class="fa-solid fa-location-dot"></i> {{ $hotel['hotelInfo']['add1'] }},
                        {{ $hotel['hotelInfo']['city'] }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="https://www.google.com/maps?q={{ $hotel['hotelInfo']['lat'] }},{{ $hotel['hotelInfo']['lon'] }}"
                    target="_blank" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fa-solid fa-map-location-dot me-2"></i> View on Map
                </a>
            </div>
        </div>

        <div class="hotel-gallery-single mt-4">
            <img src="{{ $mainImg }}" alt="{{ $hotel['hotelInfo']['name'] }}"
                onerror="this.src='https://placehold.co/1200x600?text=Hotel+Image'">
        </div>

        <div class="row mt-4">
            <div class="col-lg-8">
                <h5 class="fw-bold mb-3">About the Hotel</h5>
                <p class="text-muted" style="line-height: 1.7;">
                    {{ $hotel['hotelInfo']['description'] ?: "Located in the heart of {$hotel['hotelInfo']['city']}, {$hotel['hotelInfo']['name']} offers a premium experience with comfortable accommodations and world-class service." }}
                </p>
            </div>
            <div class="col-lg-4">
                <h5 class="fw-bold mb-3">Hotel Amenities</h5>
                <div class="row g-2">
                    <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-wifi me-2"></i> Free
                            WiFi</span></div>
                    <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-p me-2"></i>
                            Parking</span></div>
                    <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-clock me-2"></i> 24h
                            Front Desk</span></div>
                    <div class="col-6"><span class="amenity-badge w-100 p-2"><i class="fa-solid fa-snowflake me-2"></i>
                            AC</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Selection -->
    <h4 class="room-options-title mb-4"><i class="fa-solid fa-bed"></i> Available Room Options</h4>

    @php
        $rooms = $hotel['rooms']['room'] ?? [];
        $groupedRooms = collect($rooms)->groupBy(function ($item) {
            return $item['roomCombinationId'] ?? $item['rateKey'];
        });
    @endphp

    @forelse($groupedRooms as $combinationId => $roomGroup)
        @php
            // Sort by roomIdentifier to ensure "Room 1", "Room 2" match search order
            $roomGroup = $roomGroup->sortBy('roomIdentifier')->values();

            $firstRoom = $roomGroup[0];
            $totalPrice = $roomGroup->sum(function ($r) {
                return isset($r['price']) && isset($r['price']['supplierNet']) ? $r['price']['supplierNet'] : 0;
            });
            $currency =
                isset($firstRoom['price']) && isset($firstRoom['price']['supplierCurrency'])
                    ? $firstRoom['price']['supplierCurrency']
                    : 'AED';

            // Collect rate keys in correct identifier order
            $rateKeys = $roomGroup->pluck('rateKey')->all();

            $roomNames = $roomGroup->pluck('roomName')->unique()->implode(' + ');
            if (count($roomGroup) > 1) {
                $roomNames = count($roomGroup) . ' Rooms: ' . $roomNames;
            }
            $totalAdults = $roomGroup->sum('adult');
            $totalChildren = $roomGroup->sum(function ($r) {
                return isset($r['children']) && isset($r['children']['count']) ? (int) $r['children']['count'] : 0;
            });
        @endphp

        <div class="room-card mb-4 border shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="row g-0">
                <div class="col-md-3 bg-light d-flex align-items-center justify-content-center border-end"
                    style="min-height: 200px;">
                    <div class="text-center p-4">
                        <i class="fa-solid fa-hotel fa-4x text-muted mb-3"></i>
                        <div class="badge bg-primary rounded-pill">{{ count($roomGroup) }}
                            {{ count($roomGroup) > 1 ? 'Rooms' : 'Room' }}</div>
                    </div>
                </div>
                <div class="col-md-6 p-4">
                    <h4 class="heading-hotl fw-bold text-dark">{{ $roomNames }}</h4>

                    <div class="room-combination-details">
                        @foreach ($roomGroup as $index => $room)
                            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="mb-2">
                                    <h6 class="parag-hotl fw-bold mb-0">Room {{ $index + 1 }}:
                                        {{ $room['roomName'] }}</h6>
                                    <span class="small text-muted"><i class="fa-solid fa-user me-1"></i>
                                        {{ $room['adult'] }} Adult(s) @if (isset($room['children']['count']) && $room['children']['count'] > 0)
                                            , {{ $room['children']['count'] }} Child(ren)
                                        @endif
                                    </span>
                                </div>
                                <div class="text-muted small">
                                    <div class="mb-1"><i class="fa-solid fa-utensils me-2"></i>
                                        {{ $room['meal'] ?: 'Room Only' }}</div>
                                    @if (isset($room['sMeal']))
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            @foreach (explode(',', $room['sMeal']) as $amenity)
                                                <span
                                                    class="badge bg-light text-secondary border fw-normal">{{ trim($amenity) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="mt-2 text-info fw-bold"><i class="fa-solid fa-shield-check me-1"></i>
                                        {{ $room['rateType'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3 bg-light border-start d-flex align-items-center">
                    <div class="room-pricing p-4 text-center w-100">
                        <span class="text-muted small d-block mb-1">Total Combination Price</span>
                        <h2 class="fw-bold text-primary mb-1">
                            <span class="fs-6">{{ $currency }}</span> {{ number_format($totalPrice, 2) }}
                        </h2>
                        <div class="text-muted small mb-4">Final price (incl. taxes)</div>

                        <button class="comb-hotl btn btn-primary w-100 py-3 rounded-3 fw-bold checkout-ajax-btn"
                            data-rate-key="{{ implode(',', $rateKeys) }}"
                            data-group-code="{{ $firstRoom['groupCode'] }}"
                            data-room-name="{{ count($roomGroup) > 1 ? count($roomGroup) . ' Rooms Bundle' : $firstRoom['roomName'] }}"
                            data-room-price="{{ $totalPrice }}">
                            Select Combination
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center py-5 rounded-4 border-0 shadow-sm">
            <i class="fa-solid fa-circle-question fa-3x mb-3 text-primary"></i>
            <h5>No Rooms Available</h5>
            <p class="text-muted mb-0">We couldn't find any available rooms for your selection. Try adjusting your dates
                or search parameters.</p>
        </div>
    @endforelse
</div>
