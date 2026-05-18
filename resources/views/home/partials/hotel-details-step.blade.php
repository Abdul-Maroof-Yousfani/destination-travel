@php
    $mainImg = !empty($hotel['hotelInfo']['image'])
        ? $hotel['hotelInfo']['image']
        : 'https://placehold.co/1200x600?text=Hotel+Image';
@endphp

<style>
 .btn-outline-primary{--bs-btn-color:#127f9f;--bs-btn-border-color:#127f9f;--bs-btn-hover-color:#fff;--bs-btn-hover-bg:#127f9f;--bs-btn-hover-border-color:#127f9f;--bs-btn-focus-shadow-rgb:13,110,253;--bs-btn-active-color:#fff;--bs-btn-active-bg:#127f9f;--bs-btn-active-border-color:#127f9f;--bs-btn-active-shadow:inset 0 3px 5px rgba(0,0,0,0.125);--bs-btn-disabled-color:#127f9f;--bs-btn-disabled-bg:transparent;--bs-btn-disabled-border-color:#127f9f;--bs-gradient:none;}
.btn-outline-primary{color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:hover{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:active{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:focus{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:disabled{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:disabled:hover{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:disabled:active{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.btn-outline-primary:disabled:focus{color:#fff;background-color:#127f9f;border-color:#127f9f;}
.hotel-name-large{margin-bottom:6px;}
.hotel-stars-large{margin-bottom:10px;}
.col-6{margin-bottom:16px;}
.bg-primary{background-color:#00788a !important;}
.badge{color:#fff;}
.btn-primary{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:hover{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:active{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:focus{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:disabled{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:disabled:hover{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:disabled:active{color:#fff;background-color:#00788a;border-color:#00788a;}
.btn-primary:disabled:focus{color:#fff;background-color:#00788a;border-color:#00788a;}
.text-primary{color:#00788a !important;}
h4.heading-hotl{color:#000 !important;font-size:18px;font-weight:800;margin-bottom:10px;}
h6.parag-hotl{margin-bottom:5px !important;}
button.comb-hotl{font-size:14px;}
.room-filter-bar{background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;}
.room-filter-inner{padding:20px;}
.room-filter-bar .form-select{border-radius:10px;border:1px solid #cbd5e1;padding:10px 15px;font-size:14px;background-color:#fff;}
.room-filter-bar label{color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:block;}
#no-rooms-filter-msg{background:#fff;border:2px dashed #e2e8f0;border-radius:20px;padding:60px 20px;text-align:center;margin-top:20px;}
#no-rooms-filter-msg i{font-size:16px;color:#cbd5e1;margin-bottom:20px;}
#no-rooms-filter-msg h5{font-weight:700;color:#1e293b;}




.hotel-info-section{padding-top:10px;}
/* Section Titles */
.section-title{font-size:24px;font-weight:700;margin-bottom:20px;color:#111827;}
/* About Box */
.about-hotel-box{background:#fff;border-radius:18px;padding:28px;border:1px solid #ececec;height:100%;}
.hotel-description{color:#6b7280;line-height:1.9;font-size:16px;margin:0;}
/* Amenities Box */
.amenities-box{background:#fff;border-radius:18px;padding:28px;border:1px solid #ececec;}
/* Grid */
.amenities-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;}
/* Item */
.amenity-item{display:flex;align-items:center;gap:14px;padding:14px;border-radius:14px;background:#f9fafb;border:1px solid #f0f0f0;transition:all 0.3s ease;}
.amenity-item:hover{transform:translateY(-2px);border-color:#00788a;background:#fff;box-shadow:0 8px 18px rgba(0,0,0,0.05);}
/* Icon */
.amenity-icon{width:46px;height:46px;min-width:46px;border-radius:12px;background:rgba(37,99,235,0.1);color:#00788a;display:flex;align-items:center;justify-content:center;font-size:18px;}
.amenity-item span{font-size:15px;font-weight:600;color:#1f2937;line-height:1.4;}
/* Tablet */
@media (max-width:991px){.about-hotel-box,.amenities-box{padding:22px;}
.section-title{font-size:22px;}
}
/* Mobile */
@media (max-width:576px){.amenities-grid{grid-template-columns:1fr;}
.about-hotel-box,.amenities-box{padding:18px;border-radius:14px;}
.section-title{font-size:20px;}
.hotel-description{font-size:14px;line-height:1.8;}
.amenity-item{padding:12px;}
.amenity-icon{width:40px;height:40px;min-width:40px;font-size:16px;}
.amenity-item span{font-size:14px;}
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

        <!-- <div class="row mt-4">
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
        </div> -->
        <div class="row mt-5 align-items-start hotel-info-section">

            <!-- About Hotel -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-hotel-box">
                    <h4 class="section-title">About the Hotel</h4>

                    <p class="hotel-description">
                        {{ $hotel['hotelInfo']['description'] ?: "Located in the heart of {$hotel['hotelInfo']['city']}, {$hotel['hotelInfo']['name']} offers a premium experience with comfortable accommodations and world-class service." }}
                    </p>
                </div>
            </div>

            <!-- Amenities -->
            <div class="col-lg-6">
                <div class="amenities-box">
                    <h4 class="section-title">Hotel Amenities</h4>

                    <div class="amenities-grid">

                        <div class="amenity-item">
                            <div class="amenity-icon">
                                <i class="fa-solid fa-wifi"></i>
                            </div>
                            <span>Free WiFi</span>
                        </div>

                        <div class="amenity-item">
                            <div class="amenity-icon">
                                <i class="fa-solid fa-square-parking"></i>
                            </div>
                            <span>Parking</span>
                        </div>

                        <div class="amenity-item">
                            <div class="amenity-icon">
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <span>24h Front Desk</span>
                        </div>

                        <div class="amenity-item">
                            <div class="amenity-icon">
                                <i class="fa-solid fa-snowflake"></i>
                            </div>
                            <span>Air Conditioning</span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Room Selection -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="room-options-title m-0"><i class="fa-solid fa-bed"></i> Available Room Options</h4>
        <div id="room-filter-toggle" class="btn btn-sm btn-light border ripple">
            <i class="fa-solid fa-sliders me-1"></i> Filter Rooms
        </div>
    </div>

    <!-- Room Filter Bar -->
    <div id="room-filters-container" class="mb-4 room-filter-bar shadow-sm" style="display:none;">
        <div class="room-filter-inner">
            <div class="row g-3">
            @php
                $allBeds = [];
                $allMeals = [];
                $allRates = [];
                foreach($hotel['rooms']['room'] ?? [] as $r) {
                    if(isset($r['bedTypes']['bedType'])) $allBeds[] = $r['bedTypes']['bedType'];
                    if(isset($r['meal'])) $allMeals[] = $r['meal'];
                    if(isset($r['rateType'])) $allRates[] = $r['rateType'];
                }
                $allBeds = array_unique($allBeds);
                $allMeals = array_unique($allMeals);
                $allRates = array_unique($allRates);
            @endphp
            <div class="col-md-4">
                <label class="small fw-bold text-muted mb-1">Bed Type</label>
                <select class="form-select room-detail-filter" data-filter="bed-type">
                    <option value="all">All Bed Types</option>
                    @foreach($allBeds as $bed)
                        <option value="{{ $bed }}">{{ preg_replace('/(?<!^)([A-Z])/', ' $1', $bed) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold text-muted mb-1">Meal Plan</label>
                <select class="form-select room-detail-filter" data-filter="meal">
                    <option value="all">All Meal Plans</option>
                    @foreach($allMeals as $meal)
                        <option value="{{ $meal }}">{{ $meal }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="small fw-bold text-muted mb-1">Rate Type</label>
                <select class="form-select room-detail-filter" data-filter="rate">
                    <option value="all">All Rate Types</option>
                    @foreach($allRates as $rate)
                        <option value="{{ $rate }}">{{ $rate }}</option>
                    @endforeach
                </select>
            </div>
            </div>
        </div>
    </div>

    @php
        $rooms = $hotel['rooms']['room'] ?? [];

        // Build a lookup: roomIdentifier => requested room data
        $requestedRoomIdentifiers = collect($requestRooms['Room'] ?? [])
            ->pluck('RoomIdentifier')
            ->map(fn($id) => (int) $id)
            ->all();

        // Filter API rooms to only those matching requested RoomIdentifiers
        $filteredRooms = collect($rooms)->filter(function ($room) use ($requestedRoomIdentifiers) {
            return in_array((int) ($room['roomIdentifier'] ?? 0), $requestedRoomIdentifiers);
        });

        // Group by roomCombinationId — each group is a valid combination for all requested rooms
        $groupedRooms = $filteredRooms
            ->groupBy(function ($item) {
                return $item['roomCombinationId'];
            })
            ->filter(function ($group) use ($requestedRoomIdentifiers) {
                // Only show combinations that have exactly one room per requested RoomIdentifier
                $groupIdentifiers = $group->pluck('roomIdentifier')->map(fn($id) => (int) $id)->sort()->values()->all();
                $expected = collect($requestedRoomIdentifiers)->sort()->values()->all();
                return $groupIdentifiers === $expected;
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

        @php
            // Extract attributes for filtering
            $roomBeds = $roomGroup->pluck('bedTypes.bedType')->unique()->implode(',');
            $roomMeals = $roomGroup->pluck('meal')->unique()->implode(',');
            $roomRates = $roomGroup->pluck('rateType')->unique()->implode(',');
        @endphp

        <div class="room-card mb-4 border shadow-sm rounded-4 overflow-hidden bg-white" 
             data-bed-type="{{ $roomBeds }}" 
             data-meal="{{ $roomMeals }}" 
             data-rate="{{ $roomRates }}">
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

                                    @if (!empty($room['remarks']['remark']))
                                        @php
                                            $remarks = $room['remarks']['remark'];
                                            if (isset($remarks['type'])) {
                                                $remarks = [$remarks];
                                            }
                                            $inclusions = collect($remarks)->where('type', 'Inclusion')->pluck('text');
                                            $supplements = collect($remarks)
                                                ->where('type', 'Supplements')
                                                ->pluck('text')
                                                ->first();
                                            $hasDetails = $inclusions->isNotEmpty() || !empty($supplements);
                                            $remarkId = 'room-remarks-' . $combinationId . '-' . $index;
                                        @endphp
                                        @if ($hasDetails)
                                            <a href="javascript:void(0)"
                                                class="room-remarks-toggle small text-primary mt-2 d-inline-flex align-items-center gap-1"
                                                data-target="#{{ $remarkId }}" style="text-decoration:none;">
                                                <i class="fa-solid fa-circle-info mr-1"></i> Show details
                                            </a>
                                            <div id="{{ $remarkId }}" class="room-remarks-body mt-2"
                                                style="display:none;">
                                                {{-- Inclusions --}}
                                                @if ($inclusions->isNotEmpty())
                                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                                        @foreach ($inclusions as $inc)
                                                            <span
                                                                class="badge bg-success-subtle text-success border border-success fw-normal">
                                                                <i class="fa-solid fa-check me-1"></i> {{ $inc }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                {{-- Supplements --}}
                                                @if ($supplements)
                                                    @php
                                                        $entries = preg_split('/n?Type\s*:/i', $supplements);
                                                        $fees = collect($entries)->map(fn($e) => trim($e))->filter();
                                                    @endphp
                                                    <div class="text-muted small fw-bold mb-1">
                                                        <i class="fa-solid fa-receipt me-1"></i> Supplements / Fees:
                                                    </div>
                                                    <ul class="list-unstyled mb-0 small text-muted ps-1">
                                                        @foreach ($fees as $fee)
                                                            @php
                                                                preg_match(
                                                                    '/Description\s*:\s*([^,]+)/i',
                                                                    $fee,
                                                                    $descMatch,
                                                                );
                                                                preg_match(
                                                                    '/Price\s*:\s*([\d.]+)/i',
                                                                    $fee,
                                                                    $priceMatch,
                                                                );
                                                                preg_match('/Currency\s*:\s*(\w+)/i', $fee, $currMatch);
                                                                preg_match('/^([^,]+)/i', $fee, $typeMatch);
                                                                $feeType = isset($typeMatch[1])
                                                                    ? trim($typeMatch[1])
                                                                    : '';
                                                                $feeDesc = isset($descMatch[1])
                                                                    ? trim($descMatch[1])
                                                                    : $fee;
                                                                $feePrice = isset($priceMatch[1])
                                                                    ? trim($priceMatch[1])
                                                                    : '';
                                                                $feeCurr = isset($currMatch[1])
                                                                    ? trim($currMatch[1])
                                                                    : '';
                                                                $isAtProp = stripos($feeType, 'AtProperty') !== false;
                                                            @endphp
                                                            <li
                                                                class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                                                                <span>
                                                                    <i
                                                                        class="fa-solid fa-{{ $isAtProp ? 'building' : 'circle-dot' }} me-1 text-{{ $isAtProp ? 'warning' : 'secondary' }}"></i>
                                                                    {{ $feeDesc }}
                                                                    @if ($isAtProp)
                                                                        <span
                                                                            class="badge bg-warning-subtle text-warning border border-warning ms-1"
                                                                            style="font-size:10px;">At Property</span>
                                                                    @endif
                                                                </span>
                                                                @if ($feePrice)
                                                                    <span
                                                                        class="fw-semibold text-dark ms-2 text-nowrap">{{ $feeCurr }}
                                                                        {{ $feePrice }}</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
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

    <!-- No rooms found after filter -->
    <div id="no-rooms-filter-msg" style="display:none; margin-top: 20px;">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="fa-solid fa-filter-circle-xmark fa-4x text-muted" style="opacity: 0.3;"></i>
                </div>
                <h5 class="fw-bold text-dark">No Matching Rooms Found</h5>
                <p class="text-muted mx-auto mb-4" style="max-width: 400px;">
                    We couldn't find any rooms matching your selected filters. Try resetting the filters to see all available options.
                </p>
                <button class="btn btn-primary rounded-pill px-5 py-2 fw-bold" onclick="resetRoomFilters()">
                    <i class="fa-solid fa-rotate-left mb-0"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.room-remarks-toggle', function() {
        var $toggle = $(this);
        var $body = $($toggle.data('target'));

        $body.slideToggle(250, function() {
            var isOpen = $body.is(':visible');
            $toggle.html(
                isOpen ?
                '<i class="fa-solid fa-circle-xmark mr-1"></i> Hide details' :
                '<i class="fa-solid fa-circle-info mr-1"></i> Show details'
            );
        });
    });

    // Room Level Filtering logic
    $(document).on('click', '#room-filter-toggle', function() {
        $('#room-filters-container').stop().slideToggle(400);
    });

    $(document).on('change', '.room-detail-filter', function() {
        var bed = $('[data-filter="bed-type"]').val();
        var meal = $('[data-filter="meal"]').val();
        var rate = $('[data-filter="rate"]').val();

        $('.room-card').each(function() {
            var $card = $(this);
            var cardBeds = $card.attr('data-bed-type').split(',');
            var cardMeals = $card.attr('data-meal').split(',');
            var cardRates = $card.attr('data-rate').split(',');

            var matchBed = (bed === 'all' || cardBeds.includes(bed));
            var matchMeal = (meal === 'all' || cardMeals.includes(meal));
            var matchRate = (rate === 'all' || cardRates.includes(rate));

            if (matchBed && matchMeal && matchRate) {
                $card.show(300);
            } else {
                $card.hide(300);
            }
        });
        
        // Handle "No rooms found after filter"
        setTimeout(function() {
            if ($('.room-card:visible').length === 0) {
                $('#no-rooms-filter-msg').fadeIn(300);
            } else {
                $('#no-rooms-filter-msg').fadeOut(100);
            }
        }, 350);
    });

    window.resetRoomFilters = function() {
        $('.room-detail-filter').val('all').trigger('change');
    };
</script>
