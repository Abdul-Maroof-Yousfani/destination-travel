@php
    $recentSearches = session('recent_hotel_searches', []);
@endphp

@if(count($recentSearches) > 0)
<section class="recent-searches wow fadeInUp mt-4">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0" style="font-weight: 700; color: #1e293b;">
                <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i> Recent Searches
            </h4>
            <a href="#" class="text-muted small text-decoration-none" onclick="event.preventDefault(); location.reload();">
                <i class="fa-solid fa-rotate"></i> Refresh
            </a>
        </div>
        
        <div class="recent-search-slider">
            <div class="row g-3">
                @foreach($recentSearches as $search)
                    @php
                        $checkIn = \Carbon\Carbon::parse($search['check_in']);
                        $checkOut = \Carbon\Carbon::parse($search['check_out']);
                        $nights = $checkIn->diffInDays($checkOut);
                        $roomCount = count($search['rooms']['Room'] ?? []);
                        $totalAdults = 0;
                        $totalChildren = 0;
                        foreach($search['rooms']['Room'] ?? [] as $room) {
                            $totalAdults += ($room['Adult'] ?? 0);
                            $totalChildren += ($room['Children']['Count'] ?? 0);
                        }
                    @endphp
                    <div class="col-12 col-md-4 col-lg-3">
                        <div class="recent-search-card" onclick="reRunHotelSearch({{ json_encode($search) }})">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start mb-2">
                                    <div class="search-icon-box me-3">
                                        <i class="fa-solid fa-hotel text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="destination-name mb-0 text-truncate" title="{{ $search['destination_name'] ?? 'Unknown Destination' }}">
                                            {{ $search['destination_name'] ?? 'Unknown Destination' }}
                                        </h6>
                                        <span class="country-badge">{{ $search['country_code'] ?? 'INTL' }}</span>
                                    </div>
                                </div>
                                
                                <div class="search-details mt-3">
                                    <div class="detail-item">
                                        <i class="fa-regular fa-calendar-check"></i>
                                        <span>{{ $checkIn->format('d M') }} - {{ $checkOut->format('d M Y') }}</span>
                                        <span class="nights-badge ms-1">{{ $nights }} {{ Str::plural('Night', $nights) }}</span>
                                    </div>
                                    <div class="detail-item mt-2">
                                        <i class="fa-solid fa-users"></i>
                                        <span>{{ $roomCount }} {{ Str::plural('Room', $roomCount) }}, {{ $totalAdults + $totalChildren }} Guests</span>
                                    </div>
                                </div>

                                <div class="search-action mt-3">
                                    <button class="btn btn-search-again w-100">
                                        Search Again <i class="fa-solid fa-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<style>
    .recent-search-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .recent-search-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.1);
        border-color: #3b82f6;
    }

    .search-icon-box {
        width: 40px;
        height: 40px;
        background: #eff6ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .destination-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .country-badge {
        font-size: 0.7rem;
        background: #f1f5f9;
        color: #64748b;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #475569;
    }

    .detail-item i {
        color: #94a3b8;
        width: 16px;
    }

    .nights-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 1px 6px;
        border-radius: 100px;
        font-size: 0.7rem;
        color: #64748b;
    }

    .btn-search-again {
        background: transparent;
        border: 1px solid #3b82f6;
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .recent-search-card:hover .btn-search-again {
        background: #3b82f6;
        color: #fff;
    }

    /* Animation for the section */
    .recent-searches {
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endif
