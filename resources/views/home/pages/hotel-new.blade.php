<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edestinations – Discover Luxury Stays Around the World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ url('assets/css/hotel-page.css') }}">
    
    <!-- jQuery, Select2, Flatpickr, and SweetAlert2 CDNs -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>


<body>

    <!-- NAV -->
    <nav>
        <div class="container">
            <a class="logo" href="#">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                    </svg>
                </div>
                <span class="logo-text">e<em>destinations</em></span>
            </a>
            <ul class="nav-links">
                <li><a href="#">Flights</a></li>
                <li><a href="#" class="active">Hotel</a></li>
                <li><a href="#">Rental</a></li>
                <li><a href="#">Visa</a></li>
                <li><a href="#">Cruises</a></li>
                <li><a href="#">My Bookings</a></li>
            </ul>
            <div class="nav-right">
                <div class="nav-phone"><i class="fa-solid fa-phone"></i>+1 (425) 576-4567</div>
                <button class="btn-login">Login</button>
                <button class="btn-signup">Sign Up</button>
                <button class="hamburger" onclick="document.getElementById('mm').classList.toggle('open')"><i
                        class="fa-solid fa-bars"></i></button>
            </div>
        </div>
    </nav>
    <div class="mob-menu" id="mm">
        <a href="#">Flights</a><a href="#" class="active">Hotel</a><a href="#">Rental</a>
        <a href="#">Visa</a><a href="#">Cruises</a><a href="#">My Bookings</a>
        <a href="#">Login</a><a href="#" style="color:#0d7c6b;font-weight:700;">Sign Up</a>
    </div>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-img"></div>
        <div class="container">
            <div class="hero-inner">

                <!-- LEFT: heading + search -->
                <div class="hero-left">
                    <h1>Discover Luxury Stays<br />Around the World</h1>
                    <p>Experience premium hospitality with exclusive deals on hotels,<br />flights, and travel packages
                    </p>
                    <div class="sbox">
                        <div class="sbox-row1">
                            <div class="sf">
                                <label>Destination</label>
                                <div class="si select2-icon-wrapper" style="position: relative;">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <select id="hotel_destination" class="form-control" data-placeholder="Going To (City or Hotel)" style="width: 100%;"></select>
                                </div>
                                <input type="hidden" id="hotel_country_code">
                                <input type="hidden" id="hotel_nationality">
                            </div>
                        </div>
                        <div class="guests-row" style="margin-bottom:18px;">
                            <div class="sf">
                                <label>Check-in</label>
                                <div class="si calendar-container">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input id="checkIn" name="checkIn" type="text" placeholder="Check In" autocomplete="off" />
                                </div>
                            </div>
                            <div class="sf">
                                <label>Check-out</label>
                                <div class="si calendar-container">
                                    <i class="fa-regular fa-calendar"></i>
                                    <input id="checkOut" name="checkOut" type="text" placeholder="Check Out" autocomplete="off" />
                                </div>
                            </div>
                            <div class="sf">
                                <label>Guests &amp; Rooms</label>
                                <div class="si dropdowns" style="border: none; padding: 0;">
                                    <div class="dropdown w-100" style="position: relative;">
                                        <div class="si dropdown-toggle" id="hotelOccupancyToggle" style="cursor: pointer; width: 100%; display: flex;">
                                            <i class="fa-solid fa-user-group"></i>
                                            <span class="hotelOccupancyDetails" style="font-size: 13px; font-weight: 500; color: #333;">1 Room, 1 Adult</span>
                                        </div>
                                        <div class="dropdown-menu" id="hotelOccupancyMenu" style="min-width: 280px; position: absolute; top: 100%; left: 0; background: #fff; border: 1.5px solid #e8e8e8; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 16px; z-index: 1050; display: none; margin-top: 6px;">
                                            <div id="hotelRoomsContainer">
                                                <!-- Room 1 -->
                                                <div class="room-block mb-3" data-room="1">
                                                    <h6 class="fw-bold" style="font-size: 13px; font-weight: 700; color: #111; margin-bottom: 10px;">Room 1</h6>
                                                    <div class="dropdown-item quantity d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                        <span style="font-size: 13px; font-weight: 500; color: #555;">Adults</span>
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <button type="button" class="hotelDecrement" data-type="adult" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">-</button>
                                                            <span class="count adult-count" style="font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; color: #0f172a;">1</span>
                                                            <button type="button" class="hotelIncrement" data-type="adult" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-item quantity d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                                        <span style="font-size: 13px; font-weight: 500; color: #555;">Children</span>
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <button type="button" class="hotelDecrement" data-type="child" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">-</button>
                                                            <span class="count child-count" style="font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; color: #0f172a;">0</span>
                                                            <button type="button" class="hotelIncrement" data-type="child" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">+</button>
                                                        </div>
                                                    </div>
                                                    <div class="child-ages-container mt-2"></div>
                                                    <p class="room-error-message text-danger mt-1" style="font-size: 11px; display: none; color: #ef4444;"></p>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" id="addHotelRoomBtn" style="color: #0d7c6b; border: 1.5px solid #0d7c6b; background: transparent; padding: 8px; border-radius: 8px; cursor: pointer; font-size: 12.5px; font-weight: 600; width: 100%; display: block; text-align: center; transition: all 0.2s;">+ Add Room</button>
                                            <p id="hotel-error-message" class="error-limit" style="color: #ef4444; font-size: 11px; margin-top: 5px;"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn-search" id="searchHotelsBtn"><i class="fa-solid fa-magnifying-glass"></i> Search Hotels</button>
                    </div>
                </div>

                <!-- RIGHT: Latest searches panel -->
                @php
                    $recentSearches = session('recent_hotel_searches', []);
                    // $recentSearches = [];                        
                @endphp
                @if(count($recentSearches) > 0)
                <div class="ls-panel">
                    <div class="ls-title"><i class="fa-solid fa-clock-rotate-left"></i>Latest searches</div>
                    <div class="ls-list" id="lsList">
                            @foreach(array_slice($recentSearches, 0, 10) as $search)
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
                                <div class="ls-item" onclick="reRunHotelSearch({{ json_encode($search) }})">
                                    <p style="font-size: 10.5px; color: rgba(255,255,255,0.85); line-height: 1.55; font-weight: 400; margin: 0; font-family: 'Poppins', sans-serif;">
                                        Going to <strong style="color: #fff; font-weight: 600;">{{ $search['destination_name'] ?? 'Unknown Destination' }}</strong> from <strong style="color: #fff; font-weight: 600;">{{ $checkIn->format('d M Y') }}</strong> to <strong style="color: #fff; font-weight: 600;">{{ $checkOut->format('d M Y') }}</strong> ({{ $nights }} {{ Str::plural('Night', $nights) }}) with <strong style="color: #fff; font-weight: 600;">{{ $totalAdults + $totalChildren }}</strong> {{ Str::plural('passenger', $totalAdults + $totalChildren) }}
                                    </p>
                                </div>
                            @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </section>

    <!-- STATS -->
    <div class="stats">
        <div class="container">
            <div class="stat">
                <div class="stat-ic"><i class="fa-solid fa-users"></i></div>
                <div class="stat-n">50K+</div>
                <div class="stat-l">Happy Travelers</div>
            </div>
            <div class="stat">
                <div class="stat-ic"><i class="fa-solid fa-hotel"></i></div>
                <div class="stat-n">5,000+</div>
                <div class="stat-l">Partner Hotels</div>
            </div>
            <div class="stat">
                <div class="stat-ic"><i class="fa-solid fa-award"></i></div>
                <div class="stat-n">100%</div>
                <div class="stat-l">Best Price Guarantee</div>
            </div>
            <div class="stat">
                <div class="stat-ic"><i class="fa-solid fa-headset"></i></div>
                <div class="stat-n">24/7</div>
                <div class="stat-l">Customer Support</div>
            </div>
        </div>
    </div>

    <!-- DESTINATIONS -->
    <div class="dest-wrap">
        <div class="container">
            <h2 class="sec-title">Explore Popular Destinations</h2>
            <p class="sec-sub">Discover the world's most sought-after luxury destinations</p>
            <div class="dest-border">
                <div class="dest-grid" id="dGrid"></div>
            </div>
        </div>
    </div>

    <!-- WHY CHOOSE -->
    <section class="why">
        <div class="container">
            <h2 class="sec-title">Why Choose Us</h2>
            <p class="sec-sub">Experience the difference with our premium travel services</p>
            <div class="why-grid">
                <div class="wc">
                    <div class="wc-ic"><i class="fa-solid fa-bolt"></i></div>
                    <h4>Easy Booking</h4>
                    <p>Simple and fast booking with secure confirmation at every step of your journey.</p>
                </div>
                <div class="wc">
                    <div class="wc-ic"><i class="fa-solid fa-shield-halved"></i></div>
                    <h4>Secure Payments</h4>
                    <p>All payments secured with end-to-end encryption and multiple secure protections.</p>
                </div>
                <div class="wc">
                    <div class="wc-ic"><i class="fa-solid fa-globe"></i></div>
                    <h4>Global Hotels</h4>
                    <p>Access to 5,000+ premium partner hotels across 200+ destinations worldwide.</p>
                </div>
                <div class="wc">
                    <div class="wc-ic"><i class="fa-solid fa-circle-check"></i></div>
                    <h4>Instant Confirmation</h4>
                    <p>Real-time booking confirmation in multiple languages across the globe.</p>
                </div>
                <div class="wc">
                    <div class="wc-ic"><i class="fa-solid fa-headset"></i></div>
                    <h4>24/7 Support</h4>
                    <p>Dedicated round-the-clock support in multiple languages anytime you need.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testi">
        <div class="container">
            <h2 class="sec-title">What Our Travelers Say</h2>
            <p class="sec-sub">Real experiences from our satisfied customers</p>
            <div class="testi-grid" id="tGrid"></div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="ft-grid">
                <div>
                    <div class="ft-brand">
                        <div class="ft-brand-ic"><i class="fa-solid fa-globe"></i></div>
                        e<em>destinations</em>
                    </div>
                    <p class="ft-desc">Your trusted partner for luxury travel experiences worldwide. Discover, book,
                        and explore the world in premium style with exclusive deals.</p>
                    <div class="ft-con"><i class="fa-solid fa-location-dot"></i>527 Tower Street, New York, NY 10201
                    </div>
                    <div class="ft-con"><i class="fa-solid fa-phone"></i>+1 (425) 576-4567</div>
                    <div class="ft-con"><i class="fa-solid fa-envelope"></i>info@edestinations.com</div>
                </div>
                <div class="fc">
                    <h5>Company</h5>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">News</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Partnerships</a></li>
                    </ul>
                </div>
                <div class="fc">
                    <h5>Support</h5>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Refund Policy</a></li>
                    </ul>
                </div>
                <div class="fc">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="#">Hotels</a></li>
                        <li><a href="#">Flights</a></li>
                        <li><a href="#">Packages</a></li>
                        <li><a href="#">Visa Services</a></li>
                        <li><a href="#">Cruises</a></li>
                    </ul>
                </div>
                <div class="fc">
                    <h5>Destinations</h5>
                    <ul>
                        <li><a href="#">Dubai</a></li>
                        <li><a href="#">Bangkok</a></li>
                        <li><a href="#">Istanbul</a></li>
                        <li><a href="#">Makkah</a></li>
                        <li><a href="#">Switzerland</a></li>
                    </ul>
                </div>
            </div>
            <div class="nl-box">
                <h4>Subscribe to Our Newsletter</h4>
                <p>Get exclusive deals and travel inspiration delivered straight to your inbox</p>
                <div class="nl-form"><input type="email"
                        placeholder="Enter your email now..." /><button>Subscribe</button></div>
            </div>
            <div class="ft-bottom">
                <div class="socials">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <p class="copy">© 2024 Edestinations. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Re-run hotel search dynamically using session search data
        window.reRunHotelSearch = function(searchData) {
            if (!searchData) return;

            // 1. Set Destination
            if (searchData.destination_code) {
                const text = searchData.destination_name || searchData.destination_code;
                const option = new Option(text, searchData.destination_code, true, true);
                $('#hotel_destination').empty().append(option).trigger('change');
            }

            // 2. Set Dates (converting ISO back to Flatpickr format)
            if (searchData.check_in) {
                const checkInDate = searchData.check_in.split('T')[0];
                const d = new Date(checkInDate);
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                const formatted = d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear();
                $('#checkIn').val(formatted);
                
                // Update flatpickr internal date
                const inPicker = document.getElementById('checkIn')._flatpickr;
                if (inPicker) inPicker.setDate(d, false);
            }
            if (searchData.check_out) {
                const checkOutDate = searchData.check_out.split('T')[0];
                const d = new Date(checkOutDate);
                const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                const formatted = d.getDate() + " " + months[d.getMonth()] + " " + d.getFullYear();
                $('#checkOut').val(formatted);
                
                // Update flatpickr internal date
                const outPicker = document.getElementById('checkOut')._flatpickr;
                if (outPicker) outPicker.setDate(d, false);
            }

            // 3. Set Hidden Fields
            $('#hotel_country_code').val(searchData.country_code);
            $('#hotel_nationality').val(searchData.nationality || searchData.country_code);

            // 4. Submit form immediately using form builder
            const form = $('<form action="{{ route("hotels.search") }}" method="GET"></form>');
            form.append(`<input type="hidden" name="destination_code" value="${searchData.destination_code}">`);
            form.append(`<input type="hidden" name="destination_name" value="${searchData.destination_name}">`);
            form.append(`<input type="hidden" name="country_code" value="${searchData.country_code}">`);
            form.append(`<input type="hidden" name="nationality" value="${searchData.nationality || searchData.country_code}">`);
            form.append(`<input type="hidden" name="check_in" value="${searchData.check_in}">`);
            form.append(`<input type="hidden" name="check_out" value="${searchData.check_out}">`);
            
            // Handle rooms
            if (searchData.rooms && searchData.rooms.Room) {
                searchData.rooms.Room.forEach((room, idx) => {
                    form.append(`<input type="hidden" name="rooms[Room][${idx}][RoomIdentifier]" value="${room.RoomIdentifier || (idx + 1)}">`);
                    form.append(`<input type="hidden" name="rooms[Room][${idx}][Adult]" value="${room.Adult}">`);
                    if (room.Children && room.Children.Count > 0) {
                        form.append(`<input type="hidden" name="rooms[Room][${idx}][Children][Count]" value="${room.Children.Count}">`);
                        if (room.Children.ChildAge) {
                            Object.values(room.Children.ChildAge).forEach((ageObj, cIdx) => {
                                form.append(`<input type="hidden" name="rooms[Room][${idx}][Children][ChildAge][${cIdx}][Identifier]" value="${cIdx + 1}">`);
                                form.append(`<input type="hidden" name="rooms[Room][${idx}][Children][ChildAge][${cIdx}][Text]" value="${ageObj.Text}">`);
                            });
                        }
                    }
                });
            }

            if (typeof window.showLoader === 'function') {
                window.showLoader('Re-loading Your Search');
            }
            $('body').append(form);
            form.submit();
        };

        // Destination cards
        var dests = [{
                name: 'Dubai',
                country: 'UAE',
                url: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=600&q=80'
            },
            {
                name: 'Istanbul',
                country: 'Turkey',
                url: 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?w=600&q=80'
            },
            {
                name: 'Bangkok',
                country: 'Thailand',
                url: 'https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=600&q=80'
            },
            {
                name: 'Kuala Lumpur',
                country: 'Malaysia',
                url: 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=600&q=80'
            },
            {
                name: 'Karachi',
                country: 'Pakistan',
                url: 'https://images.unsplash.com/photo-1567861911437-538298e4232c?w=600&q=80'
            },
            {
                name: 'Makkah',
                country: 'Saudi Arabia',
                url: 'https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=600&q=80'
            }
        ];
        var fallbacks = ['https://picsum.photos/seed/city1/600/420', 'https://picsum.photos/seed/city2/600/420',
            'https://picsum.photos/seed/city3/600/420', 'https://picsum.photos/seed/city4/600/420',
            'https://picsum.photos/seed/city5/600/420', 'https://picsum.photos/seed/city6/600/420'
        ];
        var dg = document.getElementById('dGrid');
        dests.forEach(function(d, i) {
            var c = document.createElement('div');
            c.className = 'dc';
            c.innerHTML = '<img src="' + d.url + '" alt="' + d.name + '" loading="lazy" onerror="this.src=\'' +
                fallbacks[i] +
                '\'"/><div class="dc-overlay"></div><div class="dc-info"><div class="dc-tag"><i class="fa-solid fa-location-dot" style="font-size:8px;"></i> ' +
                d.country + '</div><div class="dc-name">' + d.name + '</div></div>';
            c.onclick = function() {
                if (typeof window.selectDestinationByName === 'function') {
                    window.selectDestinationByName(d.name);
                    $('html, body').animate({
                        scrollTop: $('.hero').offset().top - 80
                    }, 500);
                    setTimeout(() => {
                        var inPicker = document.getElementById('checkIn')._flatpickr;
                        if (inPicker) inPicker.open();
                    }, 600);
                }
            };
            dg.appendChild(c);
        });

        // Testimonials
        var testis = [{
                name: 'Sarah Johnson',
                role: 'Frequent Traveler',
                color: '0d7c6b',
                text: '"The most amazing travel booking experience I\'ve ever had! The hotels were exactly as described and the customer service was exceptional. Will definitely use again!"'
            },
            {
                name: 'Ahmed Al-Rashid',
                role: 'Business Traveler',
                color: '1565c0',
                text: '"Absolutely outstanding service! The booking process is so easy and the customer support responds very quickly. I strongly recommend this for luxury travel!"'
            },
            {
                name: 'Emma Thompson',
                role: 'Adventure Explorer',
                color: '7b1fa2',
                text: '"Totally loved this platform! Everything was seamless from booking to check-out. The prices were unbeatable and the team helped at every step of our trip."'
            }
        ];
        var tg = document.getElementById('tGrid');
        testis.forEach(function(t) {
            var c = document.createElement('div');
            c.className = 'tc';
            c.innerHTML = '<div class="tc-head"><img class="tc-av" src="https://ui-avatars.com/api/?name=' +
                encodeURIComponent(t.name) + '&background=' + t.color + '&color=fff&size=88&bold=true" alt="' + t
                .name + '"/><div><div class="tc-name">' + t.name + '</div><div class="tc-role">' + t.role +
                '</div></div><i class="fa-solid fa-quote-right tc-qi"></i></div><div class="tc-stars">★★★★★</div><p class="tc-text">' +
                t.text + '</p>';
            tg.appendChild(c);
        });

        /* ===================================================
           DYNAMIC HOTEL SEARCH CORE JQUERY LOGIC
           =================================================== */
        $(document).ready(function() {
            // 1. Select2 destination suggestion with AJAX
            $('#hotel_destination').select2({
                theme: 'default',
                placeholder: $('#hotel_destination').data('placeholder'),
                ajax: {
                    url: '{{ route("hotels.suggestions") }}',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ term: params.term }),
                    processResults: data => ({ results: data.results }),
                    cache: true
                }
            }).on('select2:select', function (e) {
                const data = e.params.data;
                $('#hotel_country_code').val(data.country);
                $('#hotel_nationality').val(data.nationality || data.country);
            });

            // 2. Flatpickr date pickers check-in & check-out
            const checkOutPicker = flatpickr("#checkOut", {
                dateFormat: "d M Y",
                minDate: "today"
            });

            const checkInPicker = flatpickr("#checkIn", {
                dateFormat: "d M Y",
                minDate: "today",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const nextDay = new Date(selectedDates[0]);
                        nextDay.setDate(nextDay.getDate() + 1);
                        checkOutPicker.set('minDate', nextDay);
                        checkOutPicker.open(); // Smooth calendar prompt flow
                    }
                }
            });

            // 3. Occupancy details dropdown menu logic
              // $('#hotelOccupancyToggle').on('click', function(e) {
              // e.preventDefault();
              //e.stopPropagation();
              //$('#hotelOccupancyMenu').toggleClass('show');
              //});
            document.getElementById('hotelOccupancyToggle').addEventListener('click', function() {
                const menu = document.getElementById('hotelOccupancyMenu');
                const rect = this.getBoundingClientRect();
                
                menu.style.top = (rect.bottom + window.scrollY) + 'px';
                menu.style.left = rect.left + 'px';
                menu.style.width = '280px';
                menu.classList.toggle('show');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('#hotelOccupancyMenu').removeClass('show');
                }
            });

            // Multi-Room logic block
            let hotelRooms = [{ roomNum: 1, adults: 2, children: [] }]; // default 2 adults to match existing layout

            function updateHotelOccupancySummary() {
                let totalRooms = hotelRooms.length;
                let totalAdults = hotelRooms.reduce((sum, room) => sum + room.adults, 0);
                let totalChildren = hotelRooms.reduce((sum, room) => sum + room.children.length, 0);
                let summary = `${totalRooms} Room${totalRooms > 1 ? 's' : ''}, ${totalAdults} Guest${(totalAdults + totalChildren) > 1 ? 's' : ''}`;
                if (totalChildren > 0) {
                    summary = `${totalRooms} Room${totalRooms > 1 ? 's' : ''}, ${totalAdults + totalChildren} Guest${(totalAdults + totalChildren) > 1 ? 's' : ''}`;
                }
                $('.hotelOccupancyDetails').text(summary);
            }

            // Sync initial state
            updateHotelOccupancySummary();

            function syncRoomOccupancyUI(roomBlock, roomIndex) {
                const room = hotelRooms[roomIndex];
                if (!room) return;
                const adultCount = room.adults;
                const childCount = room.children.length;
                const errorMsg = roomBlock.find('.room-error-message');

                // Toggle dynamic styling classes
                roomBlock.find('.hotelIncrement[data-type="adult"]').toggleClass('hotel-btn-disabled', adultCount >= 4);
                roomBlock.find('.hotelDecrement[data-type="adult"]').toggleClass('hotel-btn-disabled', adultCount <= 1);
                roomBlock.find('.hotelIncrement[data-type="child"]').toggleClass('hotel-btn-disabled', childCount >= 3);
                roomBlock.find('.hotelDecrement[data-type="child"]').toggleClass('hotel-btn-disabled', childCount <= 0);

                if (adultCount >= 4) {
                    errorMsg.text('Maximum 4 adults per room allowed.').show();
                } else if (childCount >= 3) {
                    errorMsg.text('Maximum 3 children per room allowed.').show();
                } else {
                    errorMsg.hide().text('');
                }
            }

            $(document).on('click', '.hotelIncrement, .hotelDecrement', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const roomBlock = $(this).closest('.room-block');
                const roomIndex = parseInt(roomBlock.attr('data-room')) - 1;
                const type = $(this).data('type');
                const isIncrement = $(this).hasClass('hotelIncrement');

                if (type === 'adult') {
                    if (isIncrement && hotelRooms[roomIndex].adults < 4) hotelRooms[roomIndex].adults++;
                    if (!isIncrement && hotelRooms[roomIndex].adults > 1) hotelRooms[roomIndex].adults--;
                    roomBlock.find('.adult-count').text(hotelRooms[roomIndex].adults);
                } else if (type === 'child') {
                    if (isIncrement && hotelRooms[roomIndex].children.length < 3) {
                        hotelRooms[roomIndex].children.push(8); // Default child age
                        renderChildAges(roomBlock, roomIndex);
                    } else if (!isIncrement && hotelRooms[roomIndex].children.length > 0) {
                        hotelRooms[roomIndex].children.pop();
                        renderChildAges(roomBlock, roomIndex);
                    }
                    roomBlock.find('.child-count').text(hotelRooms[roomIndex].children.length);
                }
                syncRoomOccupancyUI(roomBlock, roomIndex);
                updateHotelOccupancySummary();
            });

            function renderChildAges(roomBlock, roomIndex) {
                const container = roomBlock.find('.child-ages-container');
                container.empty();
                hotelRooms[roomIndex].children.forEach((age, childIdx) => {
                    const ageHtml = `
                        <div class="mt-2" style="margin-bottom: 8px;">
                            <label style="font-size: 11px; font-weight: 600; color: #64748b;">Child ${childIdx + 1} Age</label>
                            <select class="child-age-select" data-room="${roomIndex}" data-child="${childIdx}">
                                ${Array.from({length: 10}, (_, i) => {
                                    const val = i + 2;
                                    return `<option value="${val}" ${val === age ? 'selected' : ''}>${val} years</option>`;
                                }).join('')}
                            </select>
                        </div>
                    `;
                    container.append(ageHtml);
                });
            }

            $(document).on('change', '.child-age-select', function(e) {
                e.stopPropagation();
                const roomIdx = $(this).data('room');
                const childIdx = $(this).data('child');
                hotelRooms[roomIdx].children[childIdx] = parseInt($(this).val());
            });

            $('#addHotelRoomBtn').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (hotelRooms.length < 4) {
                    const roomNum = hotelRooms.length + 1;
                    hotelRooms.push({ roomNum, adults: 1, children: [] });
                    const roomHtml = `
                        <div class="room-block mb-3 border-top pt-3" data-room="${roomNum}" style="border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: 12px;">
                            <div class="d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <h6 class="fw-bold" style="font-size: 13px; font-weight: 700; color: #111; margin: 0;">Room ${roomNum}</h6>
                                <button type="button" class="remove-room-btn">Remove</button>
                            </div>
                            <div class="dropdown-item quantity d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 13px; font-weight: 500; color: #555;">Adults</span>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <button type="button" class="hotelDecrement" data-type="adult" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">-</button>
                                    <span class="count adult-count" style="font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; color: #0f172a;">1</span>
                                    <button type="button" class="hotelIncrement" data-type="adult" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">+</button>
                                </div>
                            </div>
                            <div class="dropdown-item quantity d-flex justify-content-between align-items-center" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 13px; font-weight: 500; color: #555;">Children</span>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <button type="button" class="hotelDecrement" data-type="child" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">-</button>
                                    <span class="count child-count" style="font-size: 13px; font-weight: 600; min-width: 20px; text-align: center; color: #0f172a;">0</span>
                                    <button type="button" class="hotelIncrement" data-type="child" style="width: 28px; height: 28px; border: 1px solid #e2e8f0; background: #f8fafc; cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; color: #475569; transition: all 0.2s;">+</button>
                                </div>
                            </div>
                            <div class="child-ages-container mt-2"></div>
                            <p class="room-error-message text-danger mt-1" style="font-size: 11px; display: none; color: #ef4444;"></p>
                        </div>
                    `;
                    $('#hotelRoomsContainer').append(roomHtml);
                    syncRoomOccupancyUI($(`.room-block[data-room="${roomNum}"]`), hotelRooms.length - 1);
                    updateHotelOccupancySummary();
                }
                if (hotelRooms.length >= 4) {
                    $('#addHotelRoomBtn').hide();
                }
            });

            $(document).on('click', '.remove-room-btn', function(e) {
                e.stopPropagation();
                const roomBlock = $(this).closest('.room-block');
                const roomNum = roomBlock.data('room');
                hotelRooms = hotelRooms.filter(r => r.roomNum !== roomNum);
                roomBlock.remove();
                
                // Renumber rooms
                $('#hotelRoomsContainer .room-block').each(function(idx) {
                    const newNum = idx + 1;
                    $(this).attr('data-room', newNum);
                    $(this).find('h6').text(`Room ${newNum}`);
                    hotelRooms[idx].roomNum = newNum;
                });
                
                updateHotelOccupancySummary();
                if (hotelRooms.length < 4) {
                    $('#addHotelRoomBtn').show();
                }
            });

            // Date formatting helper
            function formatDateToISO(dateStr) {
                if (!dateStr) return null;
                const date = new Date(dateStr);
                if (isNaN(date)) return null;
                const year = date.getFullYear();
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const day = date.getDate().toString().padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Alerts helper
            const _alert = (msg, type = 'success') => {
                const icons = {
                    success: '#28a745',
                    error: '#f27474',
                    warning: '#ffc107',
                    info: '#17a2b8'
                };
                Swal.fire({
                    position: 'top-end',
                    icon: type,
                    title: `<span style="font-size: 15px;">${msg}</span>`,
                    showConfirmButton: false,
                    timer: 2500,
                    toast: true,
                    background: '#fff',
                    customClass: { title: 'custom-title', popup: 'custom-toast' },
                    iconColor: icons[type] || icons.success
                });
            }

            // Search submission logic
            $('#searchHotelsBtn').click(function(e) {
                e.preventDefault();
                const dest = $('#hotel_destination').val();
                const destName = $('#hotel_destination').find("option:selected").text() || $('#hotel_destination').val();
                const checkInVal = $('#checkIn').val();
                const checkOutVal = $('#checkOut').val();

                if (!dest) {
                    _alert('Please select a destination.', "warning");
                    return;
                }
                if (!checkInVal) {
                    _alert('Please select a check-in date.', "warning");
                    return;
                }
                if (!checkOutVal) {
                    _alert('Please select a check-out date.', "warning");
                    return;
                }

                const checkIn = formatDateToISO(checkInVal);
                const checkOut = formatDateToISO(checkOutVal);

                const form = $('<form action="{{ route("hotels.search") }}" method="GET"></form>');
                form.append(`<input type="hidden" name="destination_code" value="${dest}">`);
                form.append(`<input type="hidden" name="destination_name" value="${destName}">`);
                form.append(`<input type="hidden" name="country_code" value="${$('#hotel_country_code').val()}">`);
                form.append(`<input type="hidden" name="nationality" value="${$('#hotel_nationality').val() || $('#hotel_country_code').val()}">`);
                form.append(`<input type="hidden" name="check_in" value="${checkIn}T00:00:00">`);
                form.append(`<input type="hidden" name="check_out" value="${checkOut}T00:00:00">`);
                
                hotelRooms.forEach((room, idx) => {
                    form.append(`<input type="hidden" name="rooms[Room][${idx}][RoomIdentifier]" value="${idx + 1}">`);
                    form.append(`<input type="hidden" name="rooms[Room][${idx}][Adult]" value="${room.adults}">`);
                    if (room.children.length > 0) {
                        form.append(`<input type="hidden" name="rooms[Room][${idx}][Children][Count]" value="${room.children.length}">`);
                        room.children.forEach((age, cIdx) => {
                            form.append(`<input type="hidden" name="rooms[Room][${idx}][Children][ChildAge][${cIdx}][Identifier]" value="${cIdx + 1}">`);
                            form.append(`<input type="hidden" name="rooms[Room][${idx}][Children][ChildAge][${cIdx}][Text]" value="${age}">`);
                        });
                    }
                });

                if (typeof window.showLoader === 'function') {
                    window.showLoader('Searching for Hotels');
                }
                $('body').append(form);
                form.submit();
            });

            // Programmatic search suggestion trigger
            window.selectDestinationByName = function(cityName) {
                $.ajax({
                    url: '{{ route("hotels.suggestions") }}',
                    data: { term: cityName },
                    dataType: 'json',
                    success: function(data) {
                        if (data.results && data.results.length > 0) {
                            const match = data.results[0];
                            const option = new Option(match.text, match.id, true, true);
                            $('#hotel_destination').empty().append(option).trigger('change');
                            $('#hotel_country_code').val(match.country);
                            $('#hotel_nationality').val(match.nationality || match.country);
                        } else {
                            const option = new Option(cityName, cityName, true, true);
                            $('#hotel_destination').empty().append(option).trigger('change');
                        }
                    },
                    error: function() {
                        const option = new Option(cityName, cityName, true, true);
                        $('#hotel_destination').empty().append(option).trigger('change');
                    }
                });
            };

            // Sync initial state on Room 1
            syncRoomOccupancyUI($('.room-block[data-room="1"]'), 0);
        });
    </script>
</body>

</html>
