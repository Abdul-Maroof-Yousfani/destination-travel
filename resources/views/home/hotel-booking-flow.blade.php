@extends('home/layouts/master')

@section('title', 'Hotel Booking')

@section('style')
    <style>
        .booking-flow-container {
            padding: 40px 0;
            background: #f8faff;
            min-height: 80vh;
        }

        /* Stepper UI */
        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 50px;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 25px;
            left: 5%;
            right: 5%;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
        }

        .stepper-progress {
            position: absolute;
            top: 25px;
            left: 5%;
            height: 4px;
            background: #00788a;
            z-index: 2;
            transition: width 0.5s ease;
            width: 0%;
        }

        .step {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20%;
        }

        .step-icon {
            width: 54px;
            height: 54px;
            background: #fff;
            border: 4px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s ease;
        }

        .step-label {
            margin-top: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .step.active .step-icon {
            border-color: #00788a;
            color: #00788a;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(0, 120, 138, 0.2);
        }

        .step.active .step-label {
            color: #00788a;
        }

        .step.completed .step-icon {
            background: #00788a;
            border-color: #00788a;
            color: #fff;
        }

        .step.completed .step-label {
            color: #00788a;
        }

        /* Skeleton Loading */
        .skeleton {
            background: #f0f2f5;
            background: linear-gradient(110deg, #ececec 8%, #f5f5f5 18%, #ececec 33%);
            background-size: 200% 100%;
            animation: 1.5s shine linear infinite;
            border-radius: 10px;
        }

        @keyframes shine {
            to {
                background-position-x: -200%;
            }
        }

        .skeleton-text {
            height: 20px;
            width: 100%;
            margin-bottom: 10px;
        }

        .skeleton-card {
            height: 200px;
            width: 100%;
            margin-bottom: 20px;
        }

        /* Custom styles from original pages */
        .hotel-name-large {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .amenity-badge {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .room-card {
            background: #fff;
            border-radius: 15px;
            border: 1px solid #eef2f7;
            overflow: hidden;
        }

        .room-grid {
            display: grid;
            grid-template-columns: 150px 1fr 180px;
        }

        @media (max-width: 768px) {
            .room-grid {
                grid-template-columns: 1fr;
            }
        }

        .room-pricing {
            padding: 20px;
            background: #fafbfc;
            text-align: center;
            border-left: 1px solid #eef2f7;
        }

        .select-room-btn {
            background: #00788a;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 700;
            width: 100%;
        }

        /* Flow Loader Overlay */
        .flow-loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .flow-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #00788a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loader-text {
            font-weight: 700;
            color: #00788a;
            letter-spacing: 1px;
        }
    </style>
@endsection

@section('content')
    <div class="booking-flow-container">
        <!-- Full Screen Loader -->
        <div class="flow-loader-overlay" id="flow-loader">
            <div class="flow-spinner"></div>
            <div class="loader-text">PROCESSING...</div>
        </div>
        <div class="container">
            <!-- Stepper -->
            <div class="stepper">
                <div class="stepper-progress" id="stepper-progress"></div>
                <div class="step" data-step="1">
                    <div class="step-icon"><i class="fa-solid fa-bed"></i></div>
                    <div class="step-label">Rooms</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-icon"><i class="fa-solid fa-user-pen"></i></div>
                    <div class="step-label">Guests</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-icon"><i class="fa-solid fa-credit-card"></i></div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-icon"><i class="fa-solid fa-check"></i></div>
                    <div class="step-label">Finish</div>
                </div>
            </div>

            <!-- Dynamic Content Area -->
            <div class="row">
                <div class="col-lg-8">
                    <div id="booking-flow-content">
                        <!-- Skeleton Loader (Initial) -->
                        <div id="flow-skeleton" class="d-none">
                            <div class="skeleton-card skeleton"></div>
                            <div class="skeleton-text skeleton" style="width: 60%;"></div>
                            <div class="skeleton-text skeleton"></div>
                            <div class="skeleton-text skeleton" style="width: 80%;"></div>
                        </div>
                        <div id="actual-content"></div>
                    </div>
                </div>

                <!-- Sticky Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="booking-summary-sidebar sticky-top" style="top: 20px;">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <img id="summary-hotel-img" src="" class="card-img-top"
                                style="height: 150px; object-fit: cover; display: none;">
                            <div class="card-body">
                                <h5 class="font-weight-bolder mb-1" id="summary-hotel-name">---</h5>
                                <div class="text-muted small mb-3" id="summary-hotel-city">---</div>

                                <hr class="my-3">

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Check-in</span>
                                    <span class="small font-weight-bolder" id="summary-check-in">---</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small">Check-out</span>
                                    <span class="small font-weight-bolder" id="summary-check-out">---</span>
                                </div>

                                <div id="summary-room-info" class="mb-3">
                                    <!-- Dynamic room selections go here -->
                                </div>

                                <div class="bg-light p-3 rounded-3 mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bolder">Total Price</span>
                                        <span class="h5 font-weight-bolder mb-0 text-primary"
                                            id="summary-total-price">---</span>
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">Including taxes & fees</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confetti (Loaded only when needed) -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // State Management Class
            function formatDate(date) {
                return moment(date).format('DD MMM YYYY');
            }
            class BookingFlow {
                constructor() {
                    this.state = JSON.parse(localStorage.getItem('hotel_booking_state')) || {
                        step: 1,
                        params: this.getSearchParams(),
                        selectedRoom: null,
                        bookingId: null,
                        guestData: null
                    };
                    this.init();
                }

                getSearchParams() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const params = {};
                    for (const [key, value] of urlParams) {
                        params[key] = value;
                    }
                    // Handle complex rooms array from URL
                    // Note: In GET form, rooms are like rooms[Room][0][Adult]
                    return params;
                }

                saveState() {
                    localStorage.setItem('hotel_booking_state', JSON.stringify(this.state));
                }

                init() {
                    this.updateSummaryFromState();

                    // Check if booking_id is in URL (for refresh persistence)
                    const urlParams = new URLSearchParams(window.location.search);
                    const urlBookingId = urlParams.get('booking_id');

                    if (urlBookingId) {
                        this.state.bookingId = urlBookingId;
                        this.state.step = 4;
                        this.saveState();
                    }

                    // If we have a bookingId, we should probably be on step 3 or 4
                    if (this.state.bookingId && this.state.step < 3) {
                        this.state.step = 3;
                    }
                    this.loadStep(this.state.step);
                }

                updateSummaryFromState() {
                    const params = this.state.params;
                    if (params.hotel_name) $('#summary-hotel-name').text(params.hotel_name);
                    if (params.hotel_city) $('#summary-hotel-city').text(params.hotel_city);
                    if (params.check_in) $('#summary-check-in').text(formatDate(params.check_in));
                    if (params.check_out) $('#summary-check-out').text(formatDate(params.check_out));
                    if (params.hotel_image) {
                        $('#summary-hotel-img').attr('src', params.hotel_image).show();
                    }

                    if (this.state.selectedRoom) {
                        $('#summary-room-info').html(`
                        <div class="p-2 border rounded-3 bg-white mb-2">
                            <div class="fw-bold small text-truncate">${this.state.selectedRoom.name}</div>
                            <div class="text-muted" style="font-size: 10px;">Selected Room</div>
                        </div>
                    `);
                        $('#summary-total-price').text(this.state.selectedRoom.price);
                    }
                }

                updateStepper(step) {
                    $('.step').removeClass('active completed');
                    $('.step').each(function() {
                        const s = $(this).data('step');
                        if (s < step) $(this).addClass('completed');
                        if (s === step) $(this).addClass('active');
                    });
                    const progress = ((step - 1) / 3) * 100;
                    $('#stepper-progress').css('width', `${progress}%`);
                }

                showLoading() {
                    $('#flow-loader').css('display', 'flex');
                    $('#actual-content').addClass('opacity-50');
                    $('#flow-skeleton').removeClass('d-none');
                }

                hideLoading() {
                    $('#flow-loader').hide();
                    $('#actual-content').removeClass('opacity-50');
                    $('#flow-skeleton').addClass('d-none');
                }

                async loadStep(step, data = {}) {
                    this.showLoading();
                    // We do NOT update stepper or save state here yet.
                    // State should only advance if the backend request is successful.

                    try {
                        let url = '';
                        let method = 'GET';
                        let sendData = {
                            ...this.state.params,
                            ...data
                        };

                        if (step === 1) {
                            url = '{{ route('hotels.show') }}';
                            sendData.hotel_id = this.state.params.hotel_id || sendData.hotel_id;
                        } else if (step === 2) {
                            url = '{{ route('hotels.checkout') }}';
                            method = 'POST';
                        } else if (step === 3) {
                            url = `{{ url('hotels/payment') }}/${this.state.bookingId}`;
                        } else if (step === 4) {
                            url = `{{ url('hotels/confirmation-partial') }}/${this.state.bookingId}`;
                        }

                        const response = await $.ajax({
                            url: url,
                            method: method,
                            data: sendData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        // Check if response is JSON (error) or HTML (success)
                        if (typeof response === 'object' && response.success === false) {
                            _alert(response.message || 'An error occurred.', 'error');
                            this.hideLoading();
                            return;
                        }

                        $('#actual-content').html(response);
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });

                        // Only update state and stepper on success
                        this.updateStepper(step);
                        this.state.step = step;

                        // Merge new data into params (e.g., room selection details)
                        if (data && Object.keys(data).length > 0) {
                            this.state.params = {
                                ...this.state.params,
                                ...data
                            };
                        }
                        this.saveState();
                    } catch (error) {
                        console.error('Step load failed:', error);

                        if (error.status === 403) {
                            _alert('Access Denied: You do not have permission to view this booking.', 'error');
                            this.state.step = 1;
                            this.state.bookingId = null;
                            this.saveState();
                            this.loadStep(1);
                            // Clean URL
                            window.history.pushState({}, '', window.location.pathname);
                        } else if (error.status === 400 || error.status === 500) {
                            let msg = error.responseJSON && error.responseJSON.message ? error.responseJSON
                                .message : null;
                            showMissingDataMsg(msg);
                        } else {
                            let msg = 'Failed to load booking step. Please try again.';
                            if (error.responseJSON && error.responseJSON.message) {
                                msg = error.responseJSON.message;
                            }
                            _alert(msg, 'error');
                        }
                    } finally {
                        this.hideLoading();
                    }
                }

                // Global Event Listeners
                bindEvents() {
                    const self = this;

                    // Step 1 -> Step 2 (Select Room)
                    $(document).on('click', '.checkout-ajax-btn', function() {
                        const roomData = {
                            name: $(this).data('room-name'),
                            price: $(this).data('room-price')
                        };
                        self.state.selectedRoom = roomData;
                        self.updateSummaryFromState();

                        const data = {
                            hotel_id: '{{ request()->hotel_id }}' || self.state.params.hotel_id,
                            session_id: '{{ request()->session_id }}' || self.state.params
                                .session_id,
                            rate_key: $(this).data('rate-key'),
                            group_code: $(this).data('group-code'),
                            hotel_name: '{{ request()->hotel_name }}' || self.state.params
                                .hotel_name,
                            hotel_address: '{{ request()->hotel_address }}' || self.state.params
                                .hotel_address,
                            hotel_city: '{{ request()->hotel_city }}' || self.state.params
                                .hotel_city,
                            hotel_image: '{{ request()->hotel_image }}' || self.state.params
                                .hotel_image,
                            hotel_rating: '{{ request()->hotel_rating }}' || self.state.params
                                .hotel_rating,
                        };
                        self.loadStep(2, data);
                    });

                    // Step 2 -> Step 3 (Save Booking)
                    $(document).on('submit', '#hotel-checkout-ajax-form', async function(e) {
                        e.preventDefault();
                        self.showLoading();
                        try {
                            const response = await $.ajax({
                                url: '{{ route('hotels.saveBooking') }}',
                                method: 'POST',
                                data: $(this).serialize(),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            if (response.success) {
                                self.state.bookingId = response.booking_id;
                                self.loadStep(3);
                            } else {
                                _alert(response.message || 'Saving failed.', 'error');
                            }
                        } catch (error) {
                            // Validation Errors
                            if (error.status === 422) {
                                let msg = 'Validation Error';
                                if (error.responseJSON && error.responseJSON.errors) {
                                    const firstKey = Object.keys(error.responseJSON.errors)[0];
                                    msg = error.responseJSON.errors[firstKey][0];
                                } else if (error.responseJSON && error.responseJSON.message) {
                                    msg = error.responseJSON.message;
                                }
                                _alert(msg, 'error');
                            }
                            // Session/Data Errors
                            else if (error.status === 400 || error.status === 500) {
                                let msg = error.responseJSON && error.responseJSON.message ? error
                                    .responseJSON.message : null;
                                showMissingDataMsg(msg);
                            }
                            // Other Errors
                            else {
                                let msg = 'An error occurred during checkout.';
                                if (error.responseJSON && error.responseJSON.message) {
                                    msg = error.responseJSON.message;
                                }
                                _alert(msg, 'error');
                            }
                        } finally {
                            self.hideLoading();
                        }
                    });

                    // Step 3 -> Step 4 (Confirm Booking)
                    $(document).on('submit', '#hotel-confirm-ajax-form', async function(e) {
                        e.preventDefault();
                        const bookingId = $(this).data('booking-id');
                        self.showLoading();
                        try {
                            const response = await $.ajax({
                                url: `{{ url('hotels/confirm-booking') }}/${bookingId}`,
                                method: 'POST',
                                data: $(this).serialize(),
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });

                            $('#actual-content').html(response);
                            self.updateStepper(4);
                            self.state.step = 4;
                            self.saveState();

                            // Update URL for refresh persistence
                            const newUrl = window.location.pathname + '?booking_id=' + bookingId;
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);

                            // Confetti!
                            confetti({
                                particleCount: 150,
                                spread: 70,
                                origin: {
                                    y: 0.6
                                }
                            });
                        } catch (error) {
                            if (error.status === 400 || error.status === 500) {
                                let msg = error.responseJSON && error.responseJSON.message ? error
                                    .responseJSON.message : null;
                                showMissingDataMsg(msg);
                            } else {
                                let msg = 'Booking confirmation failed.';
                                if (error.responseJSON && error.responseJSON.message) {
                                    msg = error.responseJSON.message;
                                }
                                _alert(msg, 'error');
                            }
                        } finally {
                            self.hideLoading();
                        }
                    });

                    // Prev Step
                    $(document).on('click', '.prev-step-btn', function() {
                        const currentStep = self.state.step;
                        if (currentStep > 1) self.loadStep(currentStep - 1);
                    });
                }
            }

            const flow = new BookingFlow();
            flow.bindEvents();
        });

        function showMissingDataMsg(serverMsg = null) {
            (async () => {
                let alMsg = serverMsg || 'Data is missing. Please search for the hotel again.';
                if (await _confirm(alMsg, false, 'warning', 'GoBack')) {
                    let goBack = localStorage.getItem('hotels') || null;
                    // goBack ? window.location.href = `/hotels${goBack}` : window.history.back();
                }
            })();
        }
    </script>
@endsection
