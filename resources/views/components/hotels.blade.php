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
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid #f0f2f5;
        display: flex;
        flex-direction: row;
        position: relative;
    }

    @media (max-width: 768px) {
        .hotel-card {
            flex-direction: column;
        }
    }

    .hotel-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
        border-color: rgba(0, 120, 138, 0.2);
    }

    .hotel-image-wrapper {
        width: 300px;
        height: 220px;
        position: relative;
        flex-shrink: 0;
        overflow: hidden;
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
        transition: transform 0.6s ease;
    }

    .hotel-card:hover .hotel-image {
        transform: scale(1.1);
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
        font-family: 'Poppins', sans-serif;
        transition: color 0.3s ease;
    }

    .hotel-card:hover .hotel-name {
        color: #00788a;
    }

    .hotel-location {
        color: #6c757d;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .hotel-location:hover {
        color: #00788a;
    }

    .hotel-location i {
        color: #00788a;
    }

    .hotel-stars {
        color: #ffc107;
        margin-bottom: 15px;
        font-size: 14px;
        display: flex;
        gap: 2px;
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
        box-shadow: 0 4px 15px rgba(0, 120, 138, 0.2);
    }

    .hotel-booking-btn:hover {
        background: linear-gradient(135deg, #008fa3 0%, #00788a 100%);
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0, 120, 138, 0.3);
    }

    .no-hotel-results {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 16px;
        border: 2px dashed #e0e6ed;
    }

    .no-hotel-results i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 25px;
    }

    /* Paginator Styling */
    .hotels-pagination {
        margin-top: 40px;
        display: flex;
        justify-content: center;
    }
    
    .hotels-pagination .pagination {
        gap: 8px;
    }

    .hotels-pagination .page-item .page-link {
        border-radius: 12px;
        padding: 10px 18px;
        font-weight: 600;
        color: #475569;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .hotels-pagination .page-item.active .page-link {
        background-color: #00788a;
        border-color: #00788a;
        color: #fff;
        box-shadow: 0 4px 12px rgba(0, 120, 138, 0.25);
    }

    .hotels-pagination .page-item:not(.active):hover .page-link {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #00788a;
    }

    .hotel-type-tag {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.5);
        color: #fff;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        backdrop-filter: blur(4px);
    }

    /* --- Hotel Card Visibility & Animation ---
     * All cards start hidden (display:none) so PHP-rendered HTML
     * never flashes before JS runs. JS adds .hotel-visible to reveal them.
     */
    .hotel-item {
        display: none;
    }

    .hotel-item.hotel-visible {
        display: flex;
        animation: hotelFadeSlideIn 0.35s ease both;
    }

    @keyframes hotelFadeSlideIn {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="hotel-results-container">
    @if(count($hotels) > 0)
        @foreach($hotels as $hotel)
            @php
                $info = $hotel['hotelInfo'] ?? [];
                $name = $info['name'] ?? 'Hotel Name Not Available';
                $image = (!empty($info['image'])) ? $info['image'] : 'https://placehold.co/600x400?text=Hotel+Image+Coming+Soon';
                $rating = (float)($info['starRating'] ?? 0);
                $address = $info['add1'] ?? '';
                $city = $info['city'] ?? '';
                $price = $hotel['minPrice'] ?? 'N/A';
                $currency = $hotel['supplierCurrency'] ?? 'AED';
                $lat = $info['lat'] ?? '';
                $lng = $info['lon'] ?? '';
                
                // Get session ID from the results
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
            <div class="hotel-card hotel-item wow fadeInUp" 
                 data-name="{{ strtolower($name) }}" 
                 data-rating="{{ floor($rating) }}"
                 data-wow-delay="{{ $loop->index * 0.05 }}s">
                <div class="hotel-image-wrapper">
                    <img src="{{ $image }}" alt="{{ $name }}" class="hotel-image" onerror="this.src='https://placehold.co/600x400?text=Hotel+Image'">
                    @if($rating > 0)
                        <div class="hotel-rating-badge">{{ number_format($rating, 1) }} <i class="fa-solid fa-star"></i></div>
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
                            <span>{{ $address ?? $city }}</span>
                        </a>
                    </div>
                    
                    <div class="hotel-footer">
                        <div class="hotel-price-box">
                            <span class="hotel-price-label">Price per night</span>
                            <div class="hotel-price-value">
                                {{ is_numeric($price) ? convertCurrency($price, $currency) : $price }}
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
                            View Details <i class="fa-solid fa-arrow-right-long ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Client-side Pagination --}}
        <div class="hotels-pagination" id="hotels-js-pagination">
            <!-- Pagination will be generated here by JS -->
        </div>
        
    @else
        <div class="no-hotel-results">
            <i class="fa-solid fa-hotel"></i>
            <h3>No Hotels Found</h3>
            <p class="text-muted">We couldn't find any hotels matching your criteria. Try adjusting your search filters.</p>
        </div>
    @endif
</div>

<script>
    $(document).ready(function() {
        const itemsPerPage = 10;
        let $allHotelItems = $('.hotel-item');
        let $filteredItems = $allHotelItems;
        let totalItems = $filteredItems.length;
        let totalPages = Math.ceil(totalItems / itemsPerPage);
        let currentPage = 1;

        function applyFilters() {
            const searchTerm = $('#hotel-name-filter').val().toLowerCase();
            const selectedRatings = $('.rating-checkbox:checked').map(function() {
                return parseInt($(this).val());
            }).get();

            $filteredItems = $allHotelItems.filter(function() {
                const name = $(this).data('name');
                const rating = parseInt($(this).data('rating'));
                
                const matchesName = name.includes(searchTerm);
                const matchesRating = selectedRatings.length === 0 || selectedRatings.includes(rating);
                
                return matchesName && matchesRating;
            });

            totalItems = $filteredItems.length;
            totalPages = Math.ceil(totalItems / itemsPerPage);
            
            // Update UI count
            $('#hotel-count-badge').text(totalItems + ' Hotels Found');
            
            showPage(1);
        }

        function showPage(page) {
            currentPage = page;
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            const $toShow = $filteredItems.slice(start, end);

            // Step 1: Hide ALL items — jQuery .hide() sets inline style="display:none"
            $allHotelItems.hide().removeClass('hotel-visible');

            // Step 2: For the cards we WANT to show, we must clear the inline display
            // style that .hide() just set — otherwise .hotel-visible { display:flex }
            // gets blocked by the inline style and cards remain invisible.
            $toShow.each(function(i) {
                const el = this;
                el.style.animationDelay = (i * 50) + 'ms';
                el.style.display = '';          // clear inline "display:none"
                el.classList.add('hotel-visible'); // CSS now applies display:flex + animation
            });

            renderPagination();
        }

        function renderPagination() {
            if (totalPages <= 1) {
                $('#hotels-js-pagination').empty();
                return;
            }

            let paginationHtml = '<ul class="pagination">';
            
            // Previous Button
            paginationHtml += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="fa-solid fa-chevron-left"></i></a>
                </li>
            `;

            // Page Numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            // Next Button
            paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="fa-solid fa-chevron-right"></i></a>
                </li>
            `;

            paginationHtml += '</ul>';
            $('#hotels-js-pagination').html(paginationHtml);
        }

        // Event Listeners for filters
        $('#hotel-name-filter').on('keyup', function() {
            applyFilters();
        });

        $('.rating-checkbox').on('change', function() {
            applyFilters();
        });

        $(document).on('click', '#hotels-js-pagination .page-link', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                showPage(page);
                window.scrollTo({ top: $('.hotel-results-container').offset().top - 100, behavior: 'smooth' });
            }
        });

        // Initialize
        if (totalItems > 0) {
            showPage(1);
        }

        // Booking Button Handler
        $(document).on('click', '.hotel-booking-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');
            if (typeof window.showLoader === 'function') {
                window.showLoader('Loading Hotel Details');
            }
            localStorage.clear();
            window.location.href = url;
        });
    });
</script>
