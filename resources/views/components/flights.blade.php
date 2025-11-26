<!-- Departure Flights -->
{{-- @dd($flightData) --}}
{{-- HOTEL BOOKING PAX COUNT RULES
    6 rooms per request
    9 adults per room
    4 children per room (age 1–17)
--}}
@php
    use Carbon\Carbon;
    use App\Helpers\HelperFunctions;
    // Temp
    use Illuminate\Support\Facades\Cache;

    // flight_data_rtn
    // flight_data_ow
    // $flightData = Cache::remember('flight_data_rtn', 6600, function () use ($flightData) {
    //     return $flightData; // first response will be cached
    // });
    // Temp
@endphp
<style>
    .flight-card{border:1px solid #e0e0e0;border-radius:12px;padding:20px;margin:20px auto;background:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.08);}
    .airline-logo{width:60px;height:auto;}
    .price-btn,.price-btn-rtn{background-color:#004080;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:bold;}
    .details-section{display:none;border-top:1px dashed #ccc;margin-top:15px;padding-top:15px;font-size:0.9rem;}
    .connected{font-size:1em;color:#127f9f;cursor:pointer;text-decoration:underline;font-weight:600;}
    .durationBadge{border:1px solid #127f9f;background:#2fbbe530;padding:2px 4px;border-radius:5px;font-size:0.8em !important;}
    .pia-bundle-item{border:1px solid #ddd;border-radius:12px;padding:16px;margin-bottom:16px;background:#fafafa;}
    .bundle-header{margin-bottom:12px;}
    .bundle-header h4{margin:0 0 4px;}
    .baggage-summary{font-size:0.9rem;color:#555;}
    .option-card{border:1px solid #eee;border-radius:8px;padding:10px;margin-bottom:10px;background:#fff;}
    .option-header{display:flex;justify-content:space-between;align-items:center;}
    .option-header h5{margin:0;font-size:1rem;}
    .option-price{font-weight:600;color:#008000;}
    .fare-list{padding-left:20px;margin:5px 0;}
    .service-list{margin-top:5px;}
    .service-badge{background:#e9ecef;padding:3px 8px;border-radius:4px;margin-right:4px;font-size:0.8rem;}
    .bundle-footer{margin-top:12px;text-align:right;}
    .df-items.plane{display:flex;align-items:end;justify-content:space-between;}
    .df-items.plane h1{font-size:30px;}
    .df-items.plane p{font-size:18px;font-weight:400;}
    .timesHeading{display:flex;justify-content:space-around;}
    .flight-duration{margin:11px 0;}
    .price-btn,.price-btn-rtn{padding:8px 14px;font-size:13px;width:100%;}
    .text-muted.small.roundtrip{text-align:center;font-weight:600;color:#000;}
    .fare-scroll {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    padding-bottom: 1rem;
    scroll-snap-type: x mandatory;
    }

    .fare-scroll::-webkit-scrollbar {
    height: 8px;
    }

    .fare-scroll::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
    }

    .fare-scroll > .card {
    flex: 0 0 auto;
    scroll-snap-align: start;
    }
    .bundle-section{
        display: none;
    }
    .bundle-loader {
        padding: 20px;
        text-align: center;
    }
    .spinner {
        width: 32px;
        height: 32px;
        border: 4px solid #e0e0e0;
        border-top-color: #007bff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: auto;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

</style>
@if (!empty($flightData))
    @php
        $isReturn = $flightData['return_count'] > 0;
        $bundles = $flightData['bundles'];
    @endphp
    {{-- @dd($flightData) --}}
    @forelse ($flightData['flights'] as $key => $segments)
        @php
            $departure = $key === 0 ? $flightData['departure'] : $flightData['arrival'];
            $arrival = $key === 0 ? $flightData['arrival'] : $flightData['departure'];
        @endphp
        <div class="departure_names"  id="{{ $key === 0 ? 'departure-section' : 'return-section' }}" style="display:{{ $key === 0 ? 'block' : 'none' }};">
            <div class="df-items plane">
                <h1>{{ $key === 0 ? 'Departure' : 'Return' }} Flights</h1>
                <p class="small font-italic">
                    {{ $departure['airport'] }} ({{ $departure['code'] }}) →
                    {{ $arrival['airport'] }} ({{ $arrival['code'] }})
                </p>
            </div>
            {{-- Flights :) --}}
            @forelse ($segments as $flight)
                {{-- @dd($flightData, $flight) --}}
                @php
                    $logo = strtolower($flight['carrier']);
                    $flightDep = $flight['departure'];
                    $flightArr = $flight['arrival'];
                    $firstSegment = $flight['segments'][0] ?? $flight['segments'];
                    $stopCount = count($flight['segments']) - 1;
                @endphp
                <div class="flight-card">
                    <!-- Airline Info -->
                    <div class="row align-items-center text-center text-md-start">
                        <div class="col-12 col-md-2 mb-3 mb-md-0 d-flex flex-column align-items-center">
                            <img src="{{ asset('assets/images/logos/' . $logo . '.png') }}" alt="{{ strtoupper($logo) }}"
                                class="airline-logo mb-1">
                            <div><strong>{{ $flight['carrier'] ?? '' }}</strong></div>
                            <div class="text-muted small flight-nums">
                                <p>{{ $firstSegment['carrier'] }} ({{ $firstSegment['flight_number'] }})</p>
                            </div>
                        </div>

                        <div class="col-12 col-md-8">
                            <!-- Time Info -->
                            <div class="timesHeading">
                                <div><h2> <strong>{{ $flightDep['time'] ?? '' }}</strong></h2></div>
                                <div class="flight-duration">{{ $flight['duration'] ?? '' }}</div>
                                <div><h2><strong>{{ $flightArr['time'] ?? '' }}</strong></h2></div>
                            </div>
                            <div class="my-3 flight-names-dec text-center">
                                <p>

                                    {{ $flightDep['airport'] }} ({{ $flightDep['code'] }}) -
                                    @if ($flight['isConnected'])
                                    <span class="connected">{{ $stopCount }} {{ $stopCount > 1 ? 'Stops' : 'Stop' }}</span>
                                    @else
                                    Nonstop
                                    @endif
                                    - {{ $flightArr['airport'] }} ({{ $flightArr['code'] }})
                                </p>
                            </div>
                            {{-- <div class="text-muted small kgs-total">🧳 Total: 20kg &nbsp;&nbsp; 🍴 Meal</div> --}}
                        </div>

                        <!-- Price Info -->
                        <div class="col-12 col-md-2 text-md-end">
                            <button class="{{ $key === 0 ? 'price-btn' : 'price-btn-rtn' }} mb-2"
                                data-flight="{{ json_encode($flight) }}">
                                {{ $flight['code'] ?? 'PKR' }} {{ $flight['price'] ?? 0 }}
                            </button>
                            <div class="text-muted small roundtrip">{{ $flightData['return_count'] === 0 ? 'One Way' : 'Round Trip' }}</div>
                        </div>
                    </div>
                    <!-- Connected Flight Details -->
                    <div class="details-section">
                        <div class=" mb-3">
                            <span class="durationBadge text-dark">{{ Carbon::parse($flight['arrival']['datetime'])->format('l d, F') }}</span>
                        </div>
                        @forelse ($flight['segments'] as $index => $segment)
                            <div class="card mb-3 shadow-sm">
                                <div class="card-body d-flex flex-wrap align-items-center">
                                    <!-- Airline -->
                                    <div class="col-6 col-md-2 text-center mb-3 mb-md-0">
                                        <img src="{{ asset('assets/images/logos/' . $logo . '.png') }}"
                                            alt="{{ strtoupper($logo) }}" class="airline-logo"
                                            style="max-height:40px; max-width:40px;">
                                    </div>

                                    <!-- Departure -->
                                    <div class="col-6 col-md-2 mb-3 mb-md-0">
                                        <p class="fw-bold mb-1">
                                            {{ Carbon::parse($segment['departure']['datetime'])->format('h:i A') }}
                                        </p>
                                        <small class="text-muted">
                                            {{ $segment['departure']['airport'] }}
                                            ({{ $segment['departure']['code'] }})
                                        </small>
                                    </div>

                                    <!-- Duration -->
                                    <div class="col-6 col-md-2 mb-3 mb-md-0 text-center">
                                        <div class="flight-duration">
                                            {{ str_replace(['PT', 'H', 'M'], ['', 'h ', 'm'], $segment['duration']) }}
                                        </div>
                                    </div>

                                    <!-- Arrival -->
                                    <div class="col-6 col-md-2 mb-3 mb-md-0">
                                        <p class="fw-bold mb-1">
                                            {{ Carbon::parse($segment['arrival']['datetime'])->format('h:i A') }}
                                        </p>
                                        <small class="text-muted">
                                            {{ $segment['arrival']['airport'] }} ({{ $segment['arrival']['code'] }})
                                        </small>
                                    </div>

                                    <!-- Flight No -->
                                    <div class="col-6 col-md-2 mb-3 mb-md-0 text-center">
                                        <p class="fw-bold mb-1">Flight No</p>
                                        <span
                                            class="badge bg-light text-dark">{{ $segment['carrier'] }}-{{ $segment['flight_number'] }}</span>
                                    </div>

                                    <!-- Cabin Class -->
                                    <div class="col-6 col-md-2 text-md-end">
                                        <p class="fw-bold mb-1">Cabin Class</p>
                                        <span class="badge bg-secondary text-light">{{ $flight['cabinClass'] }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Show layover only if this is not the last segment --}}
                            @if (isset($flight['segments'][$index + 1]))
                                @php
                                    $arrival = Carbon::parse($segment['arrival']['datetime']);
                                    $nextDeparture = Carbon::parse(
                                        $flight['segments'][$index + 1]['departure']['datetime'],
                                    );
                                    $layover = $arrival->diff($nextDeparture);
                                    // dd($segment, $flight, $segment['arrival']['datetime'], $flight['segments'][$index+1]['departure']['datetime'], $layover);
                                @endphp

                                @if ($layover->h > 0 || $layover->i > 0)
                                    <div class="text-center mb-3">
                                        <span class="badge bg-warning text-dark">
                                            {{ $layover->h ? $layover->h . 'h ' : '' }}{{ $layover->i ? $layover->i . 'm' : '' }}
                                            layover in {{ $segment['arrival']['airport'] }}
                                        </span>
                                    </div>
                                @endif
                            @endif
                        @empty
                            <p class="text-center text-muted">No flights available.</p>
                        @endforelse
                    </div>
                    <!-- Bundle Details -->
                    <div class="bundle-section my-4">
                        <h5 class="mb-3">Select a fare option</h5>
                        <!-- Horizontal Scroll Wrapper -->
                        <div class="fare-scroll bundle-loop">
                            <div class="bundle-loader w-100">
                                <div class="spinner"></div>
                                <p class="small text-muted mt-2">Loading bundles...</p>
                            </div>
                            <!-- Nil Baggage -->
                            {{-- <div class="card shadow-sm mx-2" style="min-width: 350px;">
                                <div class="card-header bg-light fw-bold">
                                    <span class="">Basic</span>
                                </div>
                                <div class="card-body">
                                    <span class="fw-bold">Included</span>
                                    <ul class="list-unstyled small">
                                        <li>💼 Check-in: 10 Kg</li>
                                        <span class="fw-bold">Chargeable</span>
                                        <li>🧳 Checked Baggage Baggage Rate</li>
                                        <li>💺 Seat</strong></li>
                                        <li>🍴 Meal</strong></li>
                                        <li>✏️ Modification: Penalties Apply</li>
                                        <li>❌ Cancellation: Penalties Apply</li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center bg-white">
                                    <button class="btn btn-primary w-100 fw-bold">PKR 21,590</button>
                                </div>
                            </div> --}}

                            <!-- Standard -->
                            {{-- <div class="card shadow-sm mx-2" style="min-width: 250px;">
                                <div class="card-body">
                                    <h5 class="card-title">Standard</h5>
                                    <ul class="list-unstyled small">
                                    <li>🧳 Carry-on: <strong>7kg 1 Piece</strong></li>
                                    <li>💼 Check-in: <strong>20kg (1 PC)</strong></li>
                                    <li>💺 Seat: <strong>Not Included</strong></li>
                                    <li>🍴 Meal: <strong>Included</strong></li>
                                    <li>✏️ Modification: <a href="#">Penalties Apply</a></li>
                                    <li>❌ Cancellation: <a href="#">Penalties Apply</a></li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center bg-white">
                                    <div class="small text-muted mb-1">Sasta Refund: PKR 899</div>
                                    <button class="btn btn-outline-primary btn-sm w-100 mb-2">Add to this flight</button>
                                    <button class="btn btn-primary w-100 fw-bold">PKR 23,090</button>
                                </div>
                            </div>

                            <!-- Value -->
                            <div class="card shadow-sm mx-2" style="min-width: 250px;">
                                <div class="card-body">
                                    <h5 class="card-title">Value</h5>
                                    <ul class="list-unstyled small">
                                        <li>🧳 Carry-on: <strong>7kg 1 Piece</strong></li>
                                        <li>💼 Check-in: <strong>30kg (1 PC)</strong></li>
                                        <li>💺 Seat: <strong>Not Included</strong></li>
                                        <li>🍴 Meal: <strong>Included</strong></li>
                                        <li>✏️ Modification: <a href="#">Penalties Apply</a></li>
                                        <li>❌ Cancellation: <a href="#">Penalties Apply</a></li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center bg-white">
                                    <div class="small text-muted mb-1">Sasta Refund: PKR 899</div>
                                    <button class="btn btn-outline-primary btn-sm w-100 mb-2">Add to this flight</button>
                                    <button class="btn btn-primary w-100 fw-bold">PKR 24,590</button>
                                </div>
                            </div>

                            <!-- Premium -->
                            <div class="card shadow-sm mx-2" style="min-width: 250px;">
                                <div class="card-body">
                                    <h5 class="card-title">Premium</h5>
                                    <ul class="list-unstyled small">
                                    <li>🧳 Carry-on: <strong>7kg 1 Piece</strong></li>
                                    <li>💼 Check-in: <strong>40kg (1 PC)</strong></li>
                                    <li>💺 Seat: <strong>Included</strong></li>
                                    <li>🍴 Meal: <strong>Included</strong></li>
                                    <li>✏️ Modification: <a href="#">Penalties Apply</a></li>
                                    <li>❌ Cancellation: <a href="#">Penalties Apply</a></li>
                                    </ul>
                                </div>
                                <div class="card-footer text-center bg-white">
                                    <button class="btn btn-primary w-100 fw-bold">PKR 26,090</button>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">No flights available.</p>
            @endforelse
        </div>
    @empty
        <p class="text-center text-muted">No flights available.</p>
    @endforelse
    <div class="modal fade right" id="bundleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header w-100">
                    <h5 class="modal-title">Flight Details</h5>
                    <button type="button" class="btn btn-b" data-bs-dismiss="modal"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="tick modalFlights mb-4"></div>
                    <!-- tabs -->
                    <div class="directBookingBtn"></div>
                    @if ($isReturn)
                        <div class="departure-bo bundleBtns">
                            <div class="daparture-main">
                                <div class="pack-main mt-0">
                                    <div class="tab-links packg">
                                        <ul class="tab-product">
                                            <li data-targetit="box-16" class="current">
                                                <a class="pointer" data-toggle="tab">Departure</a>
                                            </li>
                                            <li data-targetit="box-17">
                                                <a class="pointer" data-toggle="tab">Return</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="box-16 showfirst tab-content">
                        <div class="row">
                            <div class="col-12">
                                <div class="main-border">
                                    <div class="flcul">
                                        <ul class="directModalBundles"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-17 tab-content">
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="main-border">
                                    <div class="flcul">
                                        <ul class="returnModalBundles"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
<script>
    $(document).ready(function() {
        let paxCount = @json($paxCount);
        let isReturn = @json($isReturn);
        let bundles = @json($bundles);
        // console.log(bundles);
        let extras = @json($flightData['extras']);
        let departureFlight, returnFlight, returnFlightRaw, selectedCarrier;
        let responseId, firstBundleId, offerIdsDep, secondBundleId, offerIdsRtn;
        let firstFlight, firstConnectedFlight, returnConnectedFlight;
        let segments, flightTotalFare, rtnSelectedFlight, airline, depSelectedFlight;
        // let firstSegments, secondSegments;
        $(".connected").click(function() {
            $(this).closest(".flight-card").find(".details-section").slideToggle();
        });

        $(".price-btn").click(function() {
            departureFlight = $(this).data('flight');
            selectedCarrier = departureFlight.carrier;
            if (isReturn && selectedCarrier === 'pia') {
                handlePiaReturnPriceAdjustment(departureFlight, selectedCarrier);
            }
            if (selectedCarrier === 'flyJinnah') {
                depSelectedFlight = departureFlight.flightRaw;
                firstFlight = getFlightData(depSelectedFlight.flightSegments[0]);
                firstConnectedFlight = getFlightData(depSelectedFlight.flightSegments[1] || null);
                getFlightBundle(firstFlight, firstConnectedFlight, this, false, null, null);
            }
            if (selectedCarrier === 'emirates') {
                renderEmirateBundles(departureFlight.bundles || [], this, false);
            }
            if (selectedCarrier === 'pia') {
                if (!isReturn) return showModal();
            }
            if (!isReturn) return;

            let matchingFlights = 0;

            $("#return-section .flight-card").hide();

            $("#return-section .flight-card").each(function() {
                let rtnFlight = $(this).find("button").data("flight");
                if (rtnFlight.carrier === selectedCarrier) {
                    $(this).show();
                    matchingFlights++;
                }
            });

            if (matchingFlights === 0) {
                _alert(("No return flights available for " + selectedCarrier), 'warning');
                return;
            }

            if (selectedCarrier === 'pia') {
                $("#departure-section").slideUp(500, function() {
                    $("#return-section").fadeIn(500);
                });
            }
        });

        $(".price-btn-rtn").click(function() {
            returnFlight = $(this).data('flight');
            returnFlightRaw = returnFlight;
            if (selectedCarrier === 'emirates') {
                renderEmirateBundles(returnFlight.bundles || [], this, true);
            } else if (selectedCarrier === 'flyJinnah') {
                rtnSelectedFlight = returnFlightRaw.flightRaw;
                let newReturnFlight = getFlightData(rtnSelectedFlight.flightSegments[0]);
                returnConnectedFlight = getFlightData(rtnSelectedFlight.flightSegments[1] || null);
                getFlightBundle(firstFlight, firstConnectedFlight, this, true, newReturnFlight, returnConnectedFlight);
            } else {
                showModal();
            }
        });

        // Useless for now
        const showModal = () => {
            console.log('showModal');
            const depInfo = formatFlight(departureFlight);
            const rtnInfo = returnFlight ? formatFlight(returnFlight) : null;
            $('.modalFlights').html(flightHtml(depInfo, rtnInfo));

            // $('#bundleModal').modal("show");

            // console.log(selectedCarrier, departureFlight, returnFlight)
            if (selectedCarrier === 'emirates') {
                $('#bundleModal').modal("show");
                $(".directModalBundles").html(renderEmiBundles(departureFlight.bundles, false));
                if (returnFlight) {
                    $(".returnModalBundles").html(renderEmiBundles(returnFlight.bundles, true));
                }
            }
            // else if (selectedCarrier === 'flyJinnah') {
            //     depSelectedFlight = departureFlight.flightRaw;
            //     if (returnFlight) {
            //         rtnSelectedFlight = returnFlight.flightRaw;
            //         bookBothBundle(depSelectedFlight.flightSegments, rtnSelectedFlight.flightSegments);
            //     } else {
            //         bookBothBundle(depSelectedFlight.flightSegments, null);
            //     }
            // }
            else if (selectedCarrier === 'pia') {
                console.log('pia');
                const matchedBundle = findMatchingBundles(departureFlight, returnFlight, bundles);

                if (matchedBundle) {
                    airline = selectedCarrier;
                    // firstBundleId = extractSelectedBundleData(matchedBundle);
                    // sendBookingRequest(false);
                    // console.log(firstBundleId)
                    $(".directModalBundles").html(renderPiaBundles(matchedBundle));
                    $('#bundleModal').modal("show");
                    $(".bundleBtns").addClass('d-none');
                    // console.log('✅ Bundle found:', matchedBundle);
                } else {
                    _alert('No bundle found for this selection', 'warning');
                }
            }

        };
        const renderEmirateBundles = (data, el, isReturn) => {
            responseId = extras.emirates.responseId ?? '';
            const $flightCard = $(el).closest(".flight-card");
            const $bundleSection = $flightCard.find(".bundle-section");
            const $bundleLoop = $flightCard.find(".bundle-loop");
            $bundleSection.slideToggle();

            // $bundleLoop.html(`
            //     <div class="bundle-loader w-100">
            //         <div class="spinner"></div>
            //         <p class="small text-muted mt-2">Loading bundles...</p>
            //     </div>
            // `);

            if (!data || data.length === 0) {
                setTimeout(() => {
                    $bundleLoop.html(`<div class="w-100 bg-body-secondary text-dark-emphasis rounded-2 text-center py-2">No bundles available for Emirates</div>`);
                }, 400);
                return;
            }

            const normalizedData = Array.isArray(data) ? data : [data];
            setTimeout(() => {
                const cardsHtml = normalizedData.map(row => {
                    const shortTexts = (row.priceClass?.Descriptions?.Description || [])
                        .filter(item => item?.Text && Object.keys(item).length === 1)
                        .map(item => `<li>${item.Text.value}</li>`)
                        .join('');

                    const name = row.priceClass?.Name?.value ?? 'N/A';
                    const code = row.totalPrice?.code ?? 'PKR';
                    const amount = formatCurrency(Math.round(row.totalPrice?.amount || 0));
                    const offerId = row.offerID?.OfferID ?? '';

                    return `
                        <div class="card h-100 shadow-sm mx-2" style="min-width: 350px;">
                            <div class="card-header bg-light fw-bold">
                                ${name}
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled small">${shortTexts}</ul>
                            </div>
                            <div class="card-footer text-center bg-white">
                                <button
                                    class="btn btn-primary w-100 fw-bold bookBtn"
                                    data-airline="emirate"
                                    data-is-return="${isReturn}"
                                    data-bundle-id="${encodeURIComponent(JSON.stringify(row['offerID']))}"
                                    data-response-id="${responseId}"
                                    data-offer-ids="${encodeURIComponent(JSON.stringify(getOfferIds(row['offerItem'])))}"
                                    >
                                    ${code} ${amount}
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                $bundleLoop.html(`
                    <div class="fare-scroll d-flex overflow-auto pb-3">${cardsHtml}</div>
                `);
            }, 500);
        }
        const formatFlight = (flight) => {
            if (!flight) return {};

            const carrier = flight.carrier || '';
            let stopCount = flight.segments ? flight.segments.length - 1 : 0;

            return {
                logo: carrier ? carrier.toLowerCase() : 'default',
                carrier: carrier,
                depTime: flight.departure?.time || '',
                arrTime: flight.arrival?.time || '',
                origCode: flight.departure?.code || '',
                destCode: flight.arrival?.code || '',
                timeDiff: flight.duration || '',
                stops: stopCount > 0 ? `${stopCount} ${stopCount > 1 ? 'Stops' : 'Stop'}` : 'Nonstop',
                price: flight.price || 0,
                priceCode: flight.code || ''
            };
        };
        const flightHtml = (depInfo, rtnInfo) => {
            const renderFlight = (info) => `
            <li>
                <div class="sugge-tab sugge-tab-time2">
                    <div class="flex1">
                        <div class="emri">
                            <img class="${rtnInfo ? 'w-75' : 'w-50'} p-2" src="/assets/images/logos/modal/${info.logo}.png" alt="${info.carrier}">
                        </div>
                        <div class="der-time">
                            <ul>
                                <li><h2>${info.depTime}</h2></li>
                                <li><div class="stays"><p>${info.timeDiff}</p></div></li>
                                <li><h2>${info.arrTime}</h2></li>
                            </ul>
                            <div class="citys">
                                <div class="cit">
                                    <ul>
                                        <li><p>${info.origCode}</p></li>
                                        <li><p>-</p></li>
                                        <li><p>${info.stops}</p></li>
                                        <li>-</li>
                                        <li><p>${info.destCode}</p></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pritik">
                        <a class="bg-info btn text-light" role="button">
                            ${info.priceCode} ${info.price}
                        </a>
                    </div>
                </div>
            </li>`;

            return `<ul>${renderFlight(depInfo)}${rtnInfo ? renderFlight(rtnInfo) : ''}</ul>`;
        };

        // const renderEmiBundles = (data, isReturn) => {
        //     responseId = extras.emirates.responseId ?? '';
        //     const normalizedData = Array.isArray(data) ? data : (data ? [data] : []);
        //     if (normalizedData.length === 0) {
        //         return `<div class="alert alert-danger" role="alert">No flights available</div>`;
        //     }
        //     return normalizedData.map(row => {
        //         let shortTexts = row['priceClass']['Descriptions']['Description'].filter(item =>
        //             item.hasOwnProperty('Text') && Object.keys(item).length === 1
        //         );
        //         // console.log(row)
        //         return `
        //         <li data-id="${row['offerID']['OfferID'] ?? 'N/A'}">
        //             <div class="flex-plus flex-plusul2">
        //                 <h4>${row['priceClass']['Name']['value'] ?? 'N/A'}</h4>
        //                 <div class="flex-plus2 ">
        //                     <ul>
        //                         <li>
        //                             <div class="plus-fle">
        //                                 <div class="">
        //                                     ${shortTexts.map(item => `<p>${item.Text.value}</p>`).join('')}
        //                                 </div>
        //                             </div>
        //                         </li>
        //                     </ul>
        //                 </div>
        //                 <div class="conti">
        //                     <a class="btn btn-b bookBtn"
        //                         data-airline="emirate"
        //                         data-is-return="${isReturn}"
        //                         data-bundle-id="${encodeURIComponent(JSON.stringify(row['offerID']))}"
        //                         data-response-id="${responseId}"
        //                         data-offer-ids="${encodeURIComponent(JSON.stringify(getOfferIds(row['offerItem'])))}"
        //                     role="button">
        //                         ${row['totalPrice']['code'] ?? 'PKR'}
        //                         ${formatCurrency(Math.round(row['totalPrice']['amount'] || '0'))}
        //                     </a>
        //                 </div>
        //             </div>
        //         </li>`;
        //     }).join('');
        // };

        const getOfferIds = data =>
            (Array.isArray(data) ? data : data ? [data] : []).map(item => ({
                id: item?.id || null,
                PassengerRef: item?.services?.[0]?.passengerRefs || null
            }));
        // AJAX to get bundles
        const getFlightBundle = (firstFlight, firstConnectedFlight, element, direction, returnFlight, returnConnectedFlight) => {
            // console.log(firstFlight, firstConnectedFlight, direction, returnFlight, returnConnectedFlight);
            const $flightCard = $(element).closest(".flight-card");
            const $bundleSection = $flightCard.find(".bundle-section");
            const $bundleLoop = $flightCard.find(".bundle-loop");
            const $loader = $flightCard.find(".bundle-loader");

            if ($flightCard.data("loading-bundles")) return;
            $flightCard.data("loading-bundles", true);

            if ($loader.length === 0) {
                $bundleSection.slideToggle();
                $flightCard.data("loading-bundles", false);
                return;
            }
            $.ajax({
                type: "POST",
                url: "{{ route('get_bundles') }}",
                data: {
                    firstFlight,
                    firstConnectedFlight,
                    returnFlight,
                    returnConnectedFlight,

                    // firstFlight:flight, connectedFlight,
                    paxCount, isReturn,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: () => $bundleSection.slideToggle(),
                success: (res) => {
                    // segments = getSegment(res.originDestinationOptions.FlightSegment) || res
                    //     .originDestinationOptions.map(item => getSegment(item.FlightSegment));
                    // flightTotalFare = res['prices']['ItinTotalFare'] ?? null;

                    if (res.error) {
                        $bundleLoop.html(`<div class="w-100 bg-body-secondary text-dark-emphasis rounded-2 text-center py-2">No bundles available</div>`);
                        return;
                    }
                    renderBundles(res || [], element, direction, firstFlight, firstConnectedFlight, returnFlight, returnConnectedFlight);
                },
                error: (xhr, status, error) => {
                    console.error('Error:', error)
                    $bundleLoop.html(`<div class="w-100 bg-body-secondary text-dark-emphasis rounded-2 text-center py-2">No bundles available</div>`);
                },
                complete: () => {
                    $flightCard.data("loading-bundles", false);
                }
            });
        };
        const renderBundles = (data, el, isReturn, firstFlight, firstConnectedFlight, returnFlight, returnConnectedFlight) => {
            let useBundleId = data.bundles[0] ? (data.bundles[0].bundledService.some(b => b.bunldedServiceId == firstBundleId) ? firstBundleId : null) : null;
            firstBundleId = (firstBundleId === 'basic') ? 'basic' : (isReturn ? useBundleId : useBundleId);
            const bundles = isReturn ? (data.bundles[1].bundledService || []) : (data.bundles.bundledService || []);
            const bundlesArray = Array.isArray(bundles) ? bundles : (bundles ? [bundles] : []);
            const $flightCard = $(el).closest(".flight-card");
            const $bundleSection = $flightCard.find(".bundle-section");
            const $bundleLoop = $flightCard.find(".bundle-loop");
            const segments = getSegment(data.originDestinationOptions.FlightSegment) || data
                    .originDestinationOptions.map(item => getSegment(item.FlightSegment));
            const flightTotalFare = data['prices']['ItinTotalFare'] ?? null;

            const flightData = isReturn
                ? { firstFlight, firstConnectedFlight, returnFlight, returnConnectedFlight }
                : { firstFlight, firstConnectedFlight };
            if (!bundlesArray || bundlesArray.length === 0) {
                $bundleLoop.html(`<div class="w-100 bg-body-secondary text-dark-emphasis rounded-2 text-center py-2">No bundles available</div>`);
                return;
            }
            setTimeout(() => {
                const staticCard = `
                    <div class="card shadow-sm mx-2" style="min-width: 350px;">
                        <div class="card-header bg-light fw-bold">
                            Basic
                        </div>
                        <div class="card-body">
                            <span class="fw-bold">Included</span>
                            <ul class="list-unstyled small">
                                <li>Check-in: 10 Kg</li>
                                <li>Checked Baggage (Baggage Rate)</li>
                                <li>Seat</li>
                                <li>Meal</li>
                                <li>Modification (Penalties Apply)</li>
                                <li>Cancellation (Penalties Apply)</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center bg-white">
                            <button class="btn btn-primary w-100 fw-bold bookBtn"
                                data-airline="flyjinnah"
                                data-flight='${JSON.stringify(flightData).replace(/'/g, "&apos;")}'
                                data-segments='${JSON.stringify(segments).replace(/"/g, "&quot;")}'
                                data-flight-total-fare="${JSON.stringify(flightTotalFare).replace(/"/g, "&quot;")}"
                                data-is-return="${isReturn}"
                                data-bundle-id="basic">
                                + PKR 0.00
                            </button>
                        </div>
                    </div>
                `;
                const dynamicCards = bundles
                    .filter(row => row.description && String(row.description).trim() !== "") 
                    .map(row => {
                        const name = row.bundledServiceName ?? "N/A";
                        const price = formatCurrency(Math.round(row.perPaxBundledFee || 0));
                        const descArr = Array.isArray(row.description)
                            ? [] : (row.description || "").split("\n");
                        const descHTML = descArr.map(d => `<li>${d}</li>`).join("");
                        return `
                            <div class="card shadow-sm mx-2" style="min-width: 350px;">
                                <div class="card-header bg-light fw-bold">
                                    ${name}
                                </div>
                                <div class="card-body">
                                    <span class="fw-bold">Included</span>
                                    <ul class="list-unstyled small">${descHTML}</ul>
                                </div>
                                <div class="card-footer text-center bg-white">
                                    <button class="btn btn-primary w-100 fw-bold bookBtn"
                                        data-airline="flyjinnah"
                                        data-flight='${JSON.stringify(flightData).replace(/'/g, "&apos;")}'
                                        data-segments='${JSON.stringify(segments).replace(/"/g, "&quot;")}'
                                        data-flight-total-fare="${JSON.stringify(flightTotalFare).replace(/"/g, "&quot;")}}"
                                        data-is-return="${isReturn}"
                                        data-bundle-id="${row['bunldedServiceId']}">
                                        + PKR ${price}
                                    </button>
                                </div>
                            </div>
                        `;
                    })
                    .join("");
                const finalOutput = dynamicCards.trim() === "" 
                    ? `<div class="alert alert-warning">No valid bundles available</div>`
                    : `
                        <div class="fare-scroll d-flex overflow-auto pb-3">
                            ${staticCard}
                            ${dynamicCards}
                        </div>
                    `;
                $bundleLoop.html(finalOutput);
            }, 300);
        };

        $(document).on('click', '.bookBtn', function () {
            airline = $(this).data('airline');
            if (['flyjinnah', 'emirate'].includes(airline)) {
                let isDirect = false;
                let bundleId = $(this).data('bundle-id') ?? null;
                let isReturnBundle = $(this).data('is-return');
                let offerIds = $(this).data('offer-ids') ?? null;
                let segmentsRaw = $(this).data('segments') ?? null;
                flightTotalFare = $(this).data('flight-total-fare') ?? null;
                responseId = $(this).data('response-id') ?? null;
                const flightData = $(this).data('flight') ?? null;
                if (!isReturnBundle) {
                    if (airline === 'flyjinnah') {
                        // console.log(flightData)
                        firstFlight = flightData.firstFlight;
                        firstConnectedFlight = flightData.firstConnectedFlight;
                        // console.log('firstFlight', firstFlight, firstConnectedFlight)
                    }
                    //FJ
                    segments = segmentsRaw;
                    // firstFlightTotalFare = flightTotalFare;
                    //FJ
                    firstBundleId = bundleId;
                    offerIdsDep = offerIds;
                    if (isReturn) {
                        $("#departure-section").slideUp(500, function() {
                            $("#return-section").fadeIn(500);
                        });
                    }
                } else {
                    // if (!firstBundleId) {
                    //     _alert('You must select the first bundle before selecting the return bundle.', 'warning');
                    //     return;
                    // }
                    //FJ
                    segments = segmentsRaw;
                    // secondFlightTotalFare = flightTotalFare;
                    //FJ
                    secondBundleId = bundleId;
                    offerIdsRtn = offerIds;
                    if (airline === 'flyjinnah') {
                        secondFlight = flightData.returnFlight;
                        secondConnectedFlight = flightData.returnConnectedFlight;
                        isDirect = false;
                    }
                }
                // console.log(firstBundleId)
                if (firstBundleId && (!isReturn || secondBundleId)) {
                    if (airline === 'flyjinnah') {
                        isDirect = firstBundleId === 'basic' && (!isReturn || secondBundleId === 'basic');
                    }
                    sendBookingRequest(isDirect);
                }
            }
            else if(['pia'].includes(airline)){
                const offerID = JSON.parse(decodeURIComponent($(this).data('bundle-id')));
                const selectedBundle = bundles.find(b => b.offerID === offerID);
                const selectedData = extractSelectedBundleData(selectedBundle);
                firstBundleId = selectedData;
                sendBookingRequest(false);
            } else {
                _alert('Missing Carrier', 'warning')
            }
        });
        // const getFlightBundle = () => {
        //     $.ajax({
        //         type: "POST",
        //         url: "{{ route('get_bundles') }}",
        //         data: {
        //             firstFlight,
        //             firstConnectedFlight,
        //             returnFlight,
        //             returnConnectedFlight,
        //             paxCount,
        //             _token: "{{ csrf_token() }}"
        //         },
        //         beforeSend: () => _loader('show'),
        //         success: (res) => {
        //             if (res.error) {
        //                 console.log(res.error);
        //                 _alert(res.details?.ShortText || res.error, "error");
        //                 return;
        //             }
        //             $('#bundleModal').modal("show");
        //             if (!res.bundles || res.bundles.length === 0 || (!res.bundles
        //                     .bundledService && !res.bundles[0]?.bundledService)) {
        //                 $(".directModalBundles").html(
        //                     `<div class="alert alert-danger" role="alert">No bundles available</div>`
        //                 );
        //                 return;
        //             }
        //             segments = getSegment(res.originDestinationOptions.FlightSegment) || res
        //                 .originDestinationOptions.map(item => getSegment(item.FlightSegment));
        //             flightTotalFare = res['prices']['ItinTotalFare'] ?? null;
        //             let bundledService = res.bundles[0]?.bundledService || res.bundles
        //                 .bundledService;
        //             $(".directModalBundles").html(renderBundles(bundledService, false));
        //             $(".directBookingBtn").html(
        //                 '<button class="btn btn-b directBooking mb-4">Direct Booking</button>'
        //             );
        //             if (res.bundles.length > 1) {
        //                 $(".returnModalBundles").html(renderBundles(res.bundles[1]
        //                     .bundledService, true));
        //             }
        //         },
        //         error: (xhr, status, error) => console.error('Error:', error),
        //         complete: () => _loader('hide')
        //     });
        // };
        // $(document).on('click', '.basicBooking', function() {
        //     airline = 'flyjinnah';
        //     sendBookingRequest(true);
        // });
        const getFlightData = data => {
            if (!data) return null;
            return {
                departure: data['departureDateTimeLocal'],
                arrival: data['arrivalDateTimeLocal'],
                origin: data['origin'],
                destination: data['destination'],
                flightNumber: data['flightNumber']
            };
        };
        // const renderBundles = (data, isReturn) => {
        //     const normalizedData = Array.isArray(data) ? data : (data ? [data] : []);

        //     if (normalizedData.length === 0) {
        //         return `<div class="alert alert-danger" role="alert">No bundles available</div>`;
        //     }
        //     return normalizedData.map(row => {
        //         let description = parseDescription(row['description']);
        //         return `
        //         <li data-id="${row['bunldedServiceId'] ?? 'N/A'}">
        //             <div class="flex-plus flex-plusul2">
        //                 <h4>${row['bundledServiceName'] ?? 'N/A'}</h4>
        //                 <div class="flex-plus2 ">
        //                     <ul>
        //                         <li>
        //                             <div class="plus-fle">
        //                                 <h4><i class="fa-solid fa-suitcase"></i>
        //                                     Check-in Baggage
        //                                 </h4>
        //                                 <div class="plus-widh">
        //                                     <p>${description['Baggage'] ?? 'Not Included'}</p>
        //                                 </div>
        //                             </div>
        //                         </li>
        //                         <li>
        //                             <div class="plus-fle">
        //                                 <h4><i class="fa-solid fa-plane-slash"></i>Cancellation</h4>
        //                                 <div class="plus-widh">
        //                                     <p><span>${description['Cancellation'] ? 'PenaltiesApply' : 'Not Available'}</span></p>
        //                                 </div>
        //                             </div>
        //                         </li>
        //                         <li>
        //                             <div class="plus-fle">
        //                                 <h4><i class="fa-solid fa-pencil"></i>Modification</h4>
        //                                 <div class="plus-widh"><p><span>${description['Modification'] ? 'PenaltiesApply' : 'Not Available'}</span></p></div>
        //                             </div>
        //                         </li>
        //                         <li>
        //                             <div class="plus-fle">
        //                                 <h4><i class="fa-solid fa-plate-wheat"></i>Meal</h4>
        //                                 <div class="plus-widh"><p>${description['Any Meal'] ? 'Included' : 'Not Included'}</p></div>
        //                             </div>
        //                         </li>
        //                     </ul>
        //                 </div>
        //                 <div class="conti">
        //                     <a class="btn btn-b bookBtn" data-airline="flyjinnah" data-is-return="${isReturn}" data-bundle-id="${row['bunldedServiceId']}" role="button">+ PKR ${formatCurrency(Math.round(row['perPaxBundledFee'] || '0'))}</a>
        //                 </div>
        //             </div>
        //         </li>`;
        //     }).join('');
        // };
        const getSegment = data => {
            if (!data) return null;
            return {
                departure: data['@attributes']['DepartureDateTime'],
                arrival: data['@attributes']['ArrivalDateTime'],
                origin: data['ArrivalAirport']['@attributes']['LocationCode'],
                destination: data['DepartureAirport']['@attributes']['LocationCode'],
                flightNumber: data['@attributes']['FlightNumber'],
                returnFlag: data['@attributes']['returnFlag'],
                rph: data['@attributes']['RPH'],
                arrTerminal: data['ArrivalAirport']['@attributes']['Terminal'],
                depTerminal: data['DepartureAirport']['@attributes']['Terminal']
            };
        };
        const parseDescription = (description) => {
            let descriptionArray = {};
            if (typeof description === "string" && description.trim().length > 0) {
                let lines = description.trim().split("\n");
                lines.forEach(line => {
                    let parts = line.split(":", 2);
                    if (parts.length === 2) {
                        descriptionArray[$.trim(parts[0])] = $.trim(parts[1]);
                    }
                });
            }
            return descriptionArray;
        };
        // AJAX
        const sendBookingRequest = isDirectBooking => {
            let data = {};
            if (airline === "flyjinnah") {
                data = {
                    firstBundleId: firstBundleId ?? null,
                    secondBundleId: secondBundleId ?? null,
                    depSelectedFlight: depSelectedFlight ?? null,
                    rtnSelectedFlight: rtnSelectedFlight ?? null,
                    departureFlight: departureFlight ?? null,
                    returnFlight: returnFlightRaw ?? null,
                    isDirectBooking, flightTotalFare, segments,
                    paxCount, airline, _token: "{{ csrf_token() }}"
                }
            } else if (airline === "emirate") {
                data = {
                    firstBundleId: firstBundleId ? JSON.parse(decodeURIComponent(firstBundleId)) : null,
                    secondBundleId: secondBundleId ? JSON.parse(decodeURIComponent(secondBundleId)) : null,
                    depOfferIds: offerIdsDep ? JSON.parse(decodeURIComponent(offerIdsDep)) : null,
                    rtnOfferIds: offerIdsRtn ? JSON.parse(decodeURIComponent(offerIdsRtn)) : null,
                    departureFlight: departureFlight ?? null,
                    returnFlight: returnFlight ?? null,
                    responseId, airline, paxCount,
                    _token: "{{ csrf_token() }}"
                }
            } else if (airline === "pia") {
                data = {
                    bundle: firstBundleId,
                    departureFlight: departureFlight ?? null,
                    returnFlight: returnFlight ?? null,
                    airline, paxCount,
                    _token: "{{ csrf_token() }}"
                }
            }
            // console.log('ajax data =>', data);
            // return;
            $.ajax({
                type: "POST",
                url: "{{ route('booking_details') }}",
                data,
                beforeSend: () => _loader('show'),
                success: function(response) {
                    if (response.redirect) {
                        localStorage.setItem('flights', window.location.search);
                        window.location.href = '/flights/booking';
                    } else if (response.error) {
                        _alert(response.error, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    _alert(xhr.responseJSON.message, 'error')
                    console.error('Error Details:', xhr.responseJSON.details);
                    console.error('Error:', error);
                },
                complete: () => _loader('hide')
            });
        };
        // PIA MULTIPLE
        function findMatchingBundles(depFlight, retFlight, bundles) {
            let selectedKeys = [];

            if (retFlight) {
                selectedKeys.push(depFlight.bundles);
                selectedKeys.push(retFlight.bundles);
            } else {
                selectedKeys.push(depFlight.bundles);
            }

            // Return ALL matching bundles
            const matched = bundles.filter(bundle => {
                const bundleKeys = bundle.flightKey;

                if (bundleKeys.length !== selectedKeys.length) return false;

                // Check if all keys match, regardless of order
                return bundleKeys.every(key => selectedKeys.includes(key));
            });

            return matched.length ? matched : null;
        }
        // PIA SINGLE
        // function findMatchingBundles(depFlight, retFlight, bundles) {
        //     let selectedKeys = [];
        //     if (retFlight) {
        //         selectedKeys.push(depFlight.bundles);
        //         selectedKeys.push(retFlight.bundles);
        //     } else {
        //         selectedKeys.push(depFlight.bundles);
        //     }
        //     const matched = bundles.find(bundle => {
        //         const bundleKeys = bundle.flightKey;
        //         if (bundleKeys.length !== selectedKeys.length) return false;
        //         return bundleKeys.every(key => selectedKeys.includes(key));
        //     });
        //     return matched || null;
        // }
        function extractSelectedBundleData(bundle) {
            if (!bundle) return null;
 
            // const paxRefIDs = bundle.offerItem
            //     ?.flatMap(item => item.fareDetail?.map(fd => fd.PaxRefID) || [])
            //     .flatMap(id => Array.isArray(id) ? id : [id])
            //     .filter(Boolean) || [];
 
            const paxRefIDs = bundle.offerItem
                ?.flatMap(item => {
                    const fareDetails = Array.isArray(item.fareDetail)
                        ? item.fareDetail
                        : item.fareDetail
                            ? [item.fareDetail]
                            : [];
 
                    return fareDetails.map(fd => fd.PaxRefID);
                })
                .flatMap(id => Array.isArray(id) ? id : [id])
                .filter(Boolean) || [];
            const uniquePaxRefs = [...new Set(paxRefIDs)];
            const offerItemID = bundle.offerItem?.[0]?.id || '';
 
            return {
                PaxRefID: uniquePaxRefs,
                offerID: bundle.offerID || '',
                offerItemID: offerItemID,
                ownerCode: bundle.ownerCode || '',
                totalAmount: bundle.totalPrice?.total_amount || '0',
                currency: bundle.totalPrice?.currency || 'PKR'
            };
        }

        // const renderPiaBundles = (bundles) => {
        //     const normalizedData = Array.isArray(bundles) ? bundles : (bundles ? [bundles] : []);
        //     if (normalizedData.length === 0) {
        //         return `<div class="alert alert-danger" role="alert">No PIA bundles available</div>`;
        //     }

        //     return normalizedData.map(bundle => {
        //         const offerID = bundle.offerID || '';
        //         const totalPrice = bundle.totalPrice || {};
        //         const offerItems = bundle.offerItem || [];

        //         const displayPrice = totalPrice.total_amount || offerItems[0]?.price?.base_amount ||
        //             '0';
        //         const displayCurrency = totalPrice.currency || 'PKR';

        //         // Unique baggage summary
        //         const baggageSummary = (bundle.baggageAllowance || [])
        //             .map(b => `${b.weight}${b.unit}`)
        //             .filter((v, i, self) => self.indexOf(v) === i)
        //             .join(' + ');

        //         // Format offer item options cleanly
        //         const offerItemsHtml = offerItems.map((item, idx) => {
        //             // Fare details (show meaningful labels if exist)
        //             const fareDetailHtml = (item.fareDetail || []).map(fd => {
        //                 const name = fd.brandName || fd.fareBasisCode ||
        //                     'Standard Fare';
        //                 const seg = fd.PaxSegmentRefID ?
        //                     `<small>(${fd.PaxSegmentRefID})</small>` : '';
        //                 return `<li>${name} ${seg}</li>`;
        //             }).join('');

        //             // Services list
        //             const servicesHtml = (item.service || []).map(srv => {
        //                 const label = srv.name || srv.desc || srv.serviceType ||
        //                     'Service';
        //                 return `<span class="badge service-badge">${label}</span>`;
        //             }).join(' ');

        //             // Baggage per option
        //             const itemBaggage = (item.baggage || []).map(b =>
        //                 `${b.weight}${b.unit}`).join(' + ');

        //             const price = item.price || {};
        //             const base = price.base_amount || '0';
        //             const tax = price.taxSummary?.totalTaxAmount || price.taxes || '0';
        //             const total = price.total_amount || parseFloat(base) + parseFloat(tax);

        //             return `
        //                 <div class="option-card">
        //                     <div class="option-header">
        //                         <h5>Option ${idx + 1}</h5>
        //                         <div class="option-price">${displayCurrency} ${total}</div>
        //                     </div>
        //                     <div class="option-body">
        //                         ${fareDetailHtml ? `<ul class="fare-list">${fareDetailHtml}</ul>` : ''}
        //                         ${servicesHtml ? `<div class="service-list">${servicesHtml}</div>` : ''}
        //                         ${itemBaggage ? `<p><strong>Baggage:</strong> ${itemBaggage}</p>` : ''}
        //                         <p class="price-breakdown">
        //                             <small>Base: ${displayCurrency} ${base} | Tax: ${displayCurrency} ${tax}</small>
        //                         </p>
        //                     </div>
        //                 </div>
        //             `;
        //         }).join('');

        //         return `
        //             <li class="pia-bundle-item" data-id="${offerID}">
        //                 <div class="bundle-header">
        //                     <h4>PIA - ${bundle.parameters?.cabin_type || 'ECONOMY'}</h4>
        //                     ${baggageSummary ? `<p class="baggage-summary"><strong>Baggage:</strong> ${baggageSummary}</p>` : ''}
        //                 </div>
        //                 <div class="bundle-options">
        //                     ${offerItemsHtml}
        //                 </div>
        //                 <div class="bundle-footer">
        //                     <a class="btn btn-b bookBtn"
        //                         data-airline="pia"
        //                         data-bundle-id="${encodeURIComponent(JSON.stringify(offerID))}"
        //                         data-offer-ids="${encodeURIComponent(JSON.stringify(offerItems.map(item => item.id)))}"
        //                         role="button">
        //                         Book for ${displayCurrency} ${parseFloat(displayPrice.replace(/,/g, ''))}
        //                     </a>
        //                 </div>
        //             </li>
        //         `;
        //     }).join('');
        // };
        const renderPiaBundles = bundles => {
            const normalizedData = Array.isArray(bundles) ? bundles : (bundles ? [bundles] : []);
            console.log(normalizedData);
            if (normalizedData.length === 0) {
                return `<div class="alert alert-danger" role="alert">No PIA bundles available</div>`;
            }

            const getPassengerType = (paxRef) => {
                console.log(paxRef)
                if (!paxRef || typeof paxRef !== 'string') return 'Passenger';
                if (paxRef.includes('ADT')) return 'Adult';
                if (paxRef.includes('CHD')) return 'Child';
                if (paxRef.includes('INF')) return 'Infant';
                return 'Passenger';
            };

            // const getPassengerCounts = (offerItems) => {
            //     const counts = { Adult: 0, Child: 0, Infant: 0 };
            //     offerItems.forEach(item => {
            //         const paxRefs = item.fareDetail?.[0]?.PaxRefID || [];
            //         (Array.isArray(paxRefs) ? paxRefs : [paxRefs]).forEach(ref => {
            //             counts[getPassengerType(ref)]++;
            //         });
            //     });
            //     return counts;
            // };
            const getPassengerCounts = (offerItems) => {
                const paxRefs = new Set();

                offerItems.forEach(item => {
                    let fareDetails = [];
                    if (Array.isArray(item.fareDetail)) {
                        fareDetails = item.fareDetail;
                    } else if (item.fareDetail && typeof item.fareDetail === 'object') {
                        fareDetails = [item.fareDetail];
                    }

                    fareDetails.forEach(detail => {
                        const paxRef = detail?.PaxRefID;
                        if (Array.isArray(paxRef)) {
                            paxRef.forEach(ref => paxRefs.add(ref));
                        } else if (typeof paxRef === 'string') {
                            paxRefs.add(paxRef);
                        }
                    });
                });
                const counts = { Adult: 0, Child: 0, Infant: 0 };
                paxRefs.forEach(ref => {
                    if (ref.includes('ADT')) counts.Adult++;
                    else if (ref.includes('CHD')) counts.Child++;
                    else if (ref.includes('INF')) counts.Infant++;
                });

                return counts;
            };


            // const getBaggageByType = (offerItems) => {
            //     const baggageByType = {};
            //     offerItems.forEach(item => {
            //         const paxRefs = item.fareDetail?.[0]?.PaxRefID || [];
            //         const type = getPassengerType(Array.isArray(paxRefs) ? paxRefs[0] : paxRefs);
            //         const baggage = (item.baggage || [])
            //             .map(b => `${b.weight}${b.unit}`)
            //             .filter((v, i, self) => self.indexOf(v) === i)
            //             .join(' + ');
            //         if (baggage && !baggageByType[type]) baggageByType[type] = baggage;
            //     });
            //     return baggageByType;
            // };
            const getBaggageByType = (offerItems) => {
                const baggageByType = {};
                offerItems.forEach(item => {
                    let fareDetails = [];
                    if (Array.isArray(item.fareDetail)) fareDetails = item.fareDetail;
                    else if (item.fareDetail && typeof item.fareDetail === 'object') fareDetails = [item.fareDetail];

                    let paxRef = fareDetails?.[0]?.PaxRefID;
                    const type = getPassengerType(Array.isArray(paxRef) ? paxRef[0] : paxRef);

                    const baggage = (item.baggage || [])
                        .map(b => `${b.weight || 0}${b.unit || 'KG'}`)
                        .filter((v, i, self) => self.indexOf(v) === i)
                        .join(' + ');

                    baggageByType[type] = baggage || '0KG';
                });
                return baggageByType;
            };

            // return normalizedData.map((bundle, bundleIdx) => {
            //     const offerID = bundle.offerID || '';
            //     const offerItems = bundle.offerItem || [];
            //     const passengerCounts = getPassengerCounts(offerItems);
            //     const baggageByType = getBaggageByType(offerItems);

            //     const totalPrice = bundle.totalPrice || {};
            //     const displayPrice = totalPrice.total_amount || '0';
            //     const displayCurrency = totalPrice.currency || 'PKR';
            //     const baggageSummary = Object.values(baggageByType).filter(Boolean).join(' | ');

            //     const passengerSectionsHtml = Object.entries(passengerCounts)
            //         .filter(([_, count]) => count > 0)
            //         .map(([type, count]) => {
            //             const matchingItem = offerItems.find(item => {
            //                 const paxRefs = item.fareDetail?.[0]?.PaxRefID || [];
            //                 return (Array.isArray(paxRefs) ? paxRefs : [paxRefs])
            //                     .some(ref => getPassengerType(ref) === type);
            //             });
            //             if (!matchingItem) return '';

            //             const price = matchingItem.price || {};
            //             const base = parseFloat(price.base_amount || 0) * count;
            //             const tax = parseFloat(price.taxSummary?.TotalTaxAmount || 0) * count;

            //             const fareDetailHtml = (matchingItem.fareDetail || [])
            //                 .filter(fd => fd.FareComponent?.PaxSegmentRefID)
            //                 .slice(0, 2)
            //                 .map(fd => {
            //                     const name = fd.FareComponent?.FareBasisCode || 'Standard';
            //                     const seg = fd.FareComponent?.PaxSegmentRefID
            //                         ? `<small class="text-muted">(${fd.FareComponent.PaxSegmentRefID})</small>` : '';
            //                     return `<li class="mb-1"><span class="fw-bold">${name}</span> ${seg}</li>`;
            //                 })
            //                 .join('');

            //             const servicesHtml = [...new Set((matchingItem.service || []).map(s => s.name || s.code || 'BAG'))]
            //                 .slice(0, 3)
            //                 .map(srv => `<span class="badge bg-secondary me-1">${srv}</span>`)
            //                 .join('');

            //             return `
            //                 <div class="card mb-3 passenger-section" style="border-left: 4px solid ${type === 'Adult' ? '#007bff' : type === 'Child' ? '#28a745' : '#ffc107'};">
            //                     <div class="card-body">
            //                         <div class="d-flex justify-content-between align-items-start mb-2">
            //                             <h6 class="card-title mb-0 fw-bold">${type} <span class="badge bg-light text-dark">${count} ${count > 1 ? 'passengers' : 'passenger'}</span></h6>
            //                         </div>
            //                         <div class="row">
            //                             <div class="col-md-6">
            //                                 ${fareDetailHtml ? `<ul class="list-unstyled small mb-2">${fareDetailHtml}</ul>` : ''}
            //                                 ${servicesHtml ? `<div class="service-list small mb-2">${servicesHtml}</div>` : ''}
            //                             </div>
            //                             <div class="col-md-6">
            //                                 ${baggageByType[type] ? `<p class="mb-2"><i class="fas fa-suitcase me-1"></i><strong>Baggage:</strong> ${baggageByType[type]}</p>` : ''}
            //                                 <p class="price-breakdown small text-muted mb-0">
            //                                     Base: ${displayCurrency} ${base.toLocaleString()} | Tax: ${displayCurrency} ${tax.toLocaleString()}
            //                                 </p>
            //                             </div>
            //                         </div>
            //                     </div>
            //                 </div>
            //             `;
            //         }).join('');

            //     return `
            //         <div class="card bundle-card shadow-sm mb-4 ${bundleIdx > 0 ? 'mt-4' : ''}" style="border-radius: 12px; overflow: hidden;">
            //             <div class="card-header bg-info text-white p-3">
            //                 <div class="d-flex justify-content-between align-items-center">
            //                     <h4 class="mb-0 fw-bold">✈️ PIA Bundle ${bundleIdx + 1} - ${bundle.parameters?.cabin_type || 'ECONOMY'}</h4>
            //                     ${baggageSummary ? `<span class="badge bg-light text-dark fs-6">${baggageSummary}</span>` : ''}
            //                 </div>
            //                 ${baggageSummary ? `<p class="mb-0 small opacity-75 mt-1"><i class="fas fa-suitcase me-1"></i> Overall Baggage: ${baggageSummary}</p>` : ''}
            //             </div>
            //             <div class="card-body p-0">
            //                 <div class="p-3">
            //                     ${passengerSectionsHtml || '<div class="text-center text-muted py-4"><i class="fas fa-users fa-2x mb-2"></i><p>No passenger details available.</p></div>'}
            //                 </div>
            //             </div>
            //             <div class="bundle-footer pb-4 pr-4 mt-0">
            //                 <a class="btn btn-b bookBtn"
            //                     data-airline="pia"
            //                     data-bundle-id="${encodeURIComponent(JSON.stringify(offerID))}"
            //                     data-offer-ids="${encodeURIComponent(JSON.stringify(offerItems.map(item => item.id)))}"
            //                     role="button">
            //                     Book for ${displayCurrency} ${displayPrice}
            //                 </a>
            //             </div>
            //         </div>
            //     `;
            // }).join('');
            
            return normalizedData.map((bundle, bundleIdx) => {
                const offerID = bundle.offerID || '';
                const offerItems = bundle.offerItem || [];
                const passengerCounts = getPassengerCounts(offerItems);
                const baggageByType = getBaggageByType(offerItems);

                const totalPrice = bundle.totalPrice || {};
                const displayPrice = totalPrice.total_amount || '0';
                const displayCurrency = totalPrice.currency || 'PKR';
                const baggageSummary = Object.values(baggageByType).filter(Boolean).join(' | ') || '0KG';

                // ✅ Build passenger cards
                const passengerSectionsHtml = Object.entries(passengerCounts)
                    .filter(([_, count]) => count > 0)
                    .map(([type, count]) => {
                        const matchingItem = offerItems.find(item => {
                            let fareDetail = item.fareDetail;
                            if (!fareDetail) return false;
                            if (!Array.isArray(fareDetail)) fareDetail = [fareDetail];
                            return fareDetail.some(fd => {
                                const paxRefs = fd.PaxRefID;
                                const refs = Array.isArray(paxRefs) ? paxRefs : [paxRefs];
                                return refs.some(ref => getPassengerType(ref) === type);
                            });
                        });
                        if (!matchingItem) return '';

                        const price = matchingItem.price || {};
                        const base = parseFloat(price.base_amount || 0) * count;
                        const tax = parseFloat(matchingItem.taxSummary?.TotalTaxAmount || 0) * count;

                        const fareDetailHtml = (Array.isArray(matchingItem.fareDetail)
                            ? matchingItem.fareDetail
                            : [matchingItem.fareDetail || {}])
                            .filter(fd => fd?.FareComponent?.PaxSegmentRefID)
                            .slice(0, 2)
                            .map(fd => {
                                const name = fd.FareComponent?.FareBasisCode || 'Standard';
                                const seg = fd.FareComponent?.PaxSegmentRefID
                                    ? `<small class="text-muted">(${fd.FareComponent.PaxSegmentRefID})</small>` : '';
                                return `<li class="mb-1"><span class="fw-bold">${name}</span> ${seg}</li>`;
                            })
                            .join('');

                        const servicesHtml = [...new Set((matchingItem.service || []).map(s => s.name || s.code || 'BAG'))]
                            .slice(0, 3)
                            .map(srv => `<span class="badge bg-secondary me-1">${srv}</span>`)
                            .join('');

                        return `
                            <div class="card mb-3 passenger-section" style="border-left: 4px solid ${type === 'Adult' ? '#007bff' : type === 'Child' ? '#28a745' : '#ffc107'};">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0 fw-bold">${type} <span class="badge bg-light text-dark">${count} ${count > 1 ? 'passengers' : 'passenger'}</span></h6>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            ${fareDetailHtml ? `<ul class="list-unstyled small mb-2">${fareDetailHtml}</ul>` : ''}
                                            ${servicesHtml ? `<div class="service-list small mb-2">${servicesHtml}</div>` : ''}
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-2"><i class="fas fa-suitcase me-1"></i><strong>Baggage:</strong> ${baggageByType[type] || '0KG'}</p>
                                            <p class="price-breakdown small text-muted mb-0">
                                                Base: ${displayCurrency} ${base.toLocaleString()} | Tax: ${displayCurrency} ${tax.toLocaleString()}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                // ✅ Final bundle card
                return `
                    <div class="card bundle-card shadow-sm mb-4 ${bundleIdx > 0 ? 'mt-4' : ''}" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-info text-white p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 fw-bold">✈️ PIA Bundle ${bundleIdx + 1} - ${bundle.parameters?.cabin_type || 'ECONOMY'}</h4>
                                <span class="badge bg-light text-dark fs-6">${baggageSummary}</span>
                            </div>
                            <p class="mb-0 small opacity-75 mt-1"><i class="fas fa-suitcase me-1"></i> Overall Baggage: ${baggageSummary}</p>
                        </div>
                        <div class="card-body p-0">
                            <div class="p-3">
                                ${passengerSectionsHtml || '<div class="text-center text-muted py-4"><i class="fas fa-users fa-2x mb-2"></i><p>No passenger details available.</p></div>'}
                            </div>
                        </div>
                        <div class="bundle-footer pb-4 pr-4 mt-0">
                            <a class="btn btn-b bookBtn"
                                data-airline="pia"
                                data-bundle-id="${encodeURIComponent(JSON.stringify(offerID))}"
                                data-offer-ids="${encodeURIComponent(JSON.stringify(offerItems.map(item => item.id)))}"
                                role="button">
                                Book for ${displayCurrency} ${displayPrice}
                            </a>
                        </div>
                    </div>
                `;
            }).join('');
        };

        function handlePiaReturnPriceAdjustment(departureFlight, selectedCarrier) {
            if (!departureFlight || !selectedCarrier) return;

            const depPrice = parseFloat(departureFlight.price.replace(/,/g, '')) || 0;

            $("#return-section .flight-card").each(function () {
                const btn = $(this).find(".price-btn-rtn");
                const rtnFlight = btn.data("flight");

                if (rtnFlight && rtnFlight.carrier === selectedCarrier) {
                    const originalRtnPrice = parseFloat(rtnFlight.price.replace(/,/g, '')) || 0;
                    let adjustedPrice = originalRtnPrice - depPrice;
                    if (adjustedPrice < 0) adjustedPrice = 0;

                    // Update UI price text (you can customize formatting if needed)
                    const currency = rtnFlight.currency || 'PKR';
                    btn.text(`${currency} ${adjustedPrice.toLocaleString()}`);

                    rtnFlight.adjustedPrice = adjustedPrice;
                    btn.data("flight", rtnFlight);
                }
            });
        }




    });
</script>