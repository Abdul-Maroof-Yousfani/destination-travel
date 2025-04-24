@extends('home/layouts/master')

@section('title', 'Flights')
@section('style')
    <style>
        .select-flight {
            text-align: center;
        }
        .der-time ul li h2 {
            font-size: 20px;
        }
    </style>
@endsection
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <section class="mainBanner wow fadeInLeft">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <x-searchflight />
                </div>
            </div>
        </div>
    </section>
    {{-- @dd($data) --}}
    <section class="search wow fadeInRight">
        <div class="container">
            <div class="row">
                {{-- Side Bar Content --}}
                <div class="col-md-12 col-lg-2 br-right">
                    <div class="sho">
                        <div class="shops">
                            <h5>Stops</h5>

                            <div class="shop-check">
                                <div class="form-check fomcheck">
                                    <input class="form-check-input" type="checkbox" id="direct">
                                    <label class="form-check-label" for="direct">
                                        <strong>Direct</strong>
                                        <br><small>None</small>
                                    </label>
                                </div>
    
                                <div class="form-check fomcheck">
                                    <input class="form-check-input" type="checkbox" id="one-stop">
                                    <label class="form-check-label" for="one-stop">
                                        <strong>1 stop</strong>
                                        <br><small>From Rs 232,659</small>
                                    </label>
                                </div>
    
                                <div class="form-check fomcheck">
                                    <input class="form-check-input" type="checkbox" id="two-stops">
                                    <label class="form-check-label" for="two-stops">
                                        <strong>2 stops</strong>
                                        <br><small>From Rs 214,097</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="daparture">
                            <h5>Departure times</h5>
                            <div class="slider-container">
                                <h6>Outbound</h6>
                                <span id="outbound-time">00:00 - 23:59</span>
                            </div>
                            <div class="slider-container">
                                <input type="range" id="outbound-start" min="0" max="1439" step="1" value="0">
                                <input type="range" id="outbound-end" min="0" max="1439" step="1" value="1439">
                            </div>

                            <div class="slider-container">
                                <h6 class="mt-3">Return</h6>
                                <span id="return-time">00:00 - 23:59</span>
                            </div>
                            <div class="slider-container">
                                <input type="range" id="return-start" min="0" max="1439" step="1" value="0">
                                <input type="range" id="return-end" min="0" max="1439" step="1" value="1439">
                            </div>
                        </div>
                        <hr>
                        <div class="Journey">
                            <h5>Journey duration</h5>
                            <div class="slider-container">
                                <span id="duration-display">12.0 hours</span>
                            </div>
                            <input type="range" id="duration-slider" min="0" max="48" step="0.5" value="12">
                        </div>
                        <hr>
                        <div class="airlines">
                            <h5>Airlines</h5>
                            <div class="select_clear">
                                <a name="" id="selectAllBtn" class="btn btn-a" href="#" role="button">Select All</a>
                                <a name="" id="clearAllBtn" class="btn btn-a" href="#" role="button">Clear All</a>
                            </div>
                            <div class="multi-box btn-group">
                                <ul>
                                    <li>
                                        <div class="select">
                                            <input type="checkbox" id="item_1">
                                            <label class="btn btn-warning button_select" for="item_1">Star Alliance</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="select">
                                            <input type="checkbox" id="item_2">
                                            <label class="btn btn-warning button_select" for="item_2">Value Alliance</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="select">
                                            <input type="checkbox" id="item_3">
                                            <label class="btn btn-warning button_select" for="item_3">Star Alliance</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="select">
                                            <input type="checkbox" id="item_4">
                                            <label class="btn btn-warning button_select" for="item_4">Value Alliance</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>


                            <div class="shop-check">
                                <div class="form-check fomcheck">
                                    <input class="form-check-input" type="checkbox" id="direct">
                                    <label class="form-check-label" for="direct">
                                        <strong>Batik Air Malaysia</strong>
                                        <br><small>From Rs 232,659</small>
                                    </label>
                                </div>

                                <div class="form-check fomcheck">
                                    <input class="form-check-input" type="checkbox" id="one-stop">
                                    <label class="form-check-label" for="one-stop">
                                        <strong>Emirates</strong>
                                        <br><small>From Rs 232,659</small>
                                    </label>
                                </div>

                                <div class="form-check fomcheck">
                                    <input class="form-check-input" type="checkbox" id="two-stops">
                                    <label class="form-check-label" for="two-stops">
                                        <strong>Etihad Airways</strong>
                                        <br><small>From Rs 250,425</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="flightemissions">
                            <h5>Flight emissions</h5>
                            <div class="form-check fomcheck">
                                <input class="form-check-input" type="checkbox" id="two-stops">
                                <label class="form-check-label" for="two-stops">
                                    <small>Only show flights with lower CO₂ emissions</small>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-10">
                    {{-- <div class="departure-bo">
                        <div class="daparture-main">
                            <div class="derp-calender">
                                <h3>Select your departing flight to Dubai</h3>
                                <p>Total one way price for all travelers</p>
                                <p>We have found 7 results for you so far...</p>
                            </div>
                            <hr>
                        </div>
                        <div class="hep2">
                            <ul>
                                <li>
                                    <a href="tel:92 01234567 0">
                                        <div class="main-flex2">
                                            <div class="icon-head-help">
                                                <i class="fa-solid fa-headphones"></i>
                                            </div>
                                            <div class="call-content">
                                                <span>24/7 Customer Support </span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li><a href="#"><i class="fa-solid fa-phone"></i> Call: +92 012345678 9</a></li>
                                <li><a href="#"><i class="fa-brands fa-whatsapp"></i> Call: +92 012345678 9</a></li>
                                <li><a href="#"><i class="fa-regular fa-envelope"></i> callcenter@travel.com</a>
                                </li>
                            </ul>
                        </div>
                    </div> --}}
                    <div class="plane">
                        <ul>
                            {{-- @dd($data) --}}
                            @php
                                $hasAvailableFlight = false;

                                if (!empty($data)) {
                                    foreach ($data as $flightData) {
                                        foreach ($flightData['flights'] as $flight) {
                                            if ($flight['availabilityStatus'] === 'AVAILABLE') {
                                                $hasAvailableFlight = true;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            @endphp
                            @if (!empty($hasAvailableFlight))
                                <li>
                                     <div class="row">
                                        @foreach ($data as $index => $flightData)
                                            <div class="col-sm-{{ $isRoundTrip ? '6' : '12' }} col-12">
                                                <div class="d-flex mb-3">
                                                    <p class="font-weight-bold mr-2">
                                                        {{ $index == 0 ? 'Departure' : 'Return' }}</p>
                                                    <i>
                                                        <p>{{ $flightData['route'] ?? '' }}</p>
                                                        <small>{{ $flightData['date'] ?? '' }}</small>
                                                    </i>
                                                </div>
                                                @foreach ($flightData['flights'] as $key => $flight)
                                                    @if ($flight['availabilityStatus'] === 'AVAILABLE')
                                                        <div class="flight-item {{ $key > 0 ? 'd-none extra-flight' : '' }}">
                                                            <div class="prices2 d-flex align-items-center mb-2">
                                                                <input type="radio"
                                                                    name="{{ $index == 0 ? 'depFlight' : 'rtnFlight' }}"
                                                                    id="singleFlight{{ $index }}_{{ $key }}"
                                                                    value="{{ $flight['price'] ?? 0 }}"
                                                                    {{ $key == 0 ? 'checked' : '' }}
                                                                    data-segment="{{ json_encode($flight['flightSegments']) }}"
                                                                    data-selected-flight="{{ json_encode($flight) }}"
                                                                    onchange="updateTotalPrice()">

                                                                <label class="flex1"
                                                                    for="singleFlight{{ $index }}_{{ $key }}">
                                                                    <div class="emri text-center">
                                                                        <img class="w-75 p-2"
                                                                            src="assets/images/Fly_Jinnah_logo.png"
                                                                            alt="">
                                                                    </div>
                                                                    <div class="der-time">
                                                                        <ul>
                                                                            <li>
                                                                                <h2>{{ $flight['departureTime'] }}</h2>
                                                                            </li>
                                                                            <li>
                                                                                <div class="stays">
                                                                                    <p>{{ $flight['timeDifference'] }}</p>
                                                                                </div>
                                                                            </li>
                                                                            <li class="d-flex">
                                                                                <h2>{{ $flight['arrivalTime'] }}</h2>
                                                                                @if ($flight['departureDayIncrease'] != 0)
                                                                                    <sup class="w-100 text-primary">+ {{$flight['departureDayIncrease']}}</sup>
                                                                                @endif
                                                                            </li>
                                                                        </ul>
                                                                        <div class="citys">
                                                                            <div class="cit">
                                                                                <ul>
                                                                                    <li>
                                                                                        <p>{{ $flight['originCode'] }}</p>
                                                                                    </li>
                                                                                    <li>
                                                                                        <p>-</p>
                                                                                    </li>
                                                                                    <li>
                                                                                        <p>{{ $flight['isConnected'] ? '1 Stop' : 'Nonstop' }}</p>
                                                                                    </li>
                                                                                    <li>-</li>
                                                                                    <li>
                                                                                        <p>{{ $flight['destinationCode'] }}</p>
                                                                                    </li>
                                                                                </ul>
                                                                                <div class="weig weig2">
                                                                                    <ul>
                                                                                        <li><p><i class="fa-solid fa fa-money-bill-1-wave"></i> PKR {{ $flight['price'] ?? '0' }}</p></li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endforeach
                                        @php
                                            $totalExtraFlights = 0;
                                            if (!empty($data)) {
                                                foreach ($data as $flightData) {
                                                    $availableFlights = array_filter($flightData['flights'], function ($flight) {
                                                        return $flight['availabilityStatus'] === 'AVAILABLE';
                                                    });
                                                    if (count($availableFlights) > 1) {
                                                        $totalExtraFlights += count($availableFlights) - 1;
                                                    }
                                                }
                                            }
                                        @endphp
                                        @if ($totalExtraFlights > 0)
                                            <div class="text-center mb-3 col-12">
                                                <span id="toggleFlightsBtn" class="text-info font-weight-bolder pointer">
                                                    + {{ $totalExtraFlights }} more flight option{{ $totalExtraFlights > 1 ? 's' : '' }}
                                                </span>
                                            </div>
                                        @endif
                                     </div>
                                    <div class="prices2 mt-3">
                                        <div class="select-flight">
                                            <button class="btn btn-b bundleModalBtn">
                                                PKR <span id="totalPrice">0</span> - Select flight
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <p class="text-center">Flyjinnah flights not available</p>
                            @endif
                            <div class="modal fade right" id="bundleModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header w-100">
                                            <h5 class="modal-title">Flight Details</h5>
                                            <button type="button" class="btn btn-b" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- popup code -->
                                            <!-- <div class="col-md-12 col-lg-12"> -->
                                                <div class="tick modalFlights mb-4"></div>
                                                <!-- tabs -->
                                                <div class="col-12 mb-4">
                                                    <div class="dirx">
                                                        <button class="btn btn-b directBooking">Direct Booking</button>
                                                    </div>
                                                </div>
                                                @if ($isRoundTrip)
                                                    <div class="departure-bo">
                                                        <div class="daparture-main">
                                                            <div class="pack-main mt-0">
                                                                <div class="tab-links packg">
                                                                    <ul class="tab-product">
                                                                        <li data-targetit="box-16" class="current">
                                                                            <a class="pointer" data-toggle="tab">Departure</a>
                                                                        </li>
                                                                        <li data-targetit="box-17" >
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
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </div>
                </div>
                <!-- old code design -->
                <!-- <div class="col-md-12 col-lg-10">
                        <div class="departure-bo">
                            <div class="daparture-main">
                                <div class="derp-calender">
                                    <h3>Select your departing flight to Dubai</h3>
                                    <p>Total one way price for all travelers</p>
                                    <p>We have found 7 results for you so far...</p>
                                </div>
                                <hr>
                                <div class="btn-group col-xs-12">
                                    <label class="option">
                                        <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Sat, 11 Jan <br> -</span>
                                    </label>
                                    <label class="option">
                                    <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Sun, 12 Jan <br> -</span>
                                    </label>
                                    <label class="option">
                                    <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Mon, 13 Jan <br> -</span>
                                    </label>
                                    <label class="option">
                                    <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Tue, 14 Jan <br> -</span>
                                    </label>
                                    <label class="option">
                                        <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Wed, 15 Jan <br> -</span>
                                    </label>
                                    <label class="option">
                                    <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Thu, 16 Jan <br> -</span>
                                    </label>
                                    <label class="option">
                                    <input type="radio" name="optradio"><span class="btn btn-warning btn-option">Fri, 17 Jan <br> -</span>
                                    </label>
                                </div>

                                <div class="pack-main">
                                    <div class="packg">
                                        <ul class="tab-product  wow fadeInRight">
                                            <li data-targetit="box-5" class="current">
                                            <a href="#tab-5" data-toggle="tab">Suggested</a>
                                            </li>
                                            <li data-targetit="box-6" >
                                            <a href="#tab-6" data-toggle="tab">Cheapest</a>
                                            </li>
                                            <li data-targetit="box-7" >
                                            <a href="#tab-7" data-toggle="tab">Fastest</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="pacg2">
                                        <div class="nstop">
                                            <a href="#"><p>Nonstop</p></a>
                                        </div>
                                        <a href="#">More Filters <i class="fa-solid fa-angle-up"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="hep2">
                                <ul>
                                    <li>
                                        <a href="tel:92 01234567 0">
                                            <div class="main-flex2">
                                            <div class="icon-head-help">
                                                <i class="fa-solid fa-headphones"></i>
                                            </div>
                                            <div class="call-content">
                                                <span>24/7 Customer Support </span>
                                            </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li><a href="#"><i class="fa-solid fa-phone"></i> Call: +92 012345678 9</a></li>
                                    <li><a href="#"><i class="fa-brands fa-whatsapp"></i> Call: +92 012345678 9</a></li>
                                    <li><a href="#"><i class="fa-regular fa-envelope"></i> callcenter@travel.com</a></li>
                                </ul>
                            </div>
                        </div>


                        <div class="sugges">
                            <div class="row">
                            <div class="col-md-12 col-lg-10">
                                <div class="box-5 showfirst tab-content">
                                    <div class="main-border">
                                        <div class="sugge-tab">
                                        <div class="flex1">
                                            <div class="emri">
                                                <img src="assets/images/emirates.png" alt="">
                                            </div>
                                            <div class="der-time">
                                                <ul>
                                                    <li>
                                                    <h2>10:40 PM</h2>
                                                    </li>
                                                    <li>
                                                    <div class="stays">
                                                        <p>
                                                            2h 25m
                                                        </p>
                                                    </div>
                                                    </li>
                                                    <li>
                                                    <div class="tims">
                                                        <h2>12:05 AM</h2>
                                                        <span>*1D</span>
                                                    </div>
                                                    </li>
                                                </ul>
                                                <div class="citys">
                                                    <div class="cit">
                                                    <ul>
                                                        <li>
                                                            <p>Karachi (KHI)</p>
                                                        </li>
                                                        <li>
                                                            <p>-</p>
                                                        </li>
                                                        <li>
                                                            <p>Nonstop</p>
                                                        </li>
                                                        <li>
                                                            -
                                                        </li>
                                                        <li>
                                                            <p>Dubai (DXB)</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                    <div class="weig">
                                                    <ul>
                                                        <li>
                                                                <p><i class="fa-solid fa-suitcase-rolling"></i> Total: 35kg</p>
                                                        </li>
                                                        <li>
                                                            <p><i class="fa-solid fa-plate-wheat"></i> Meal</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prices">
                                            <div class="sav">
                                                <p> <i class="fa-solid fa-info"></i> Save 31,115/- PKR</p>
                                            </div>
                                            <div class="pakr">
                                                <a name="" id="" class="btn btn-b" href="#" role="button">PKR 222,252</a>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="selec-option">
                                        <p>Select a fare option</p>
                                        </div>
                                        <div class="flex-plus">
                                        <h4>Flex Plus</h4>
                                        <div class="flex-plus2">
                                            <ul>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-suitcase"></i> Check-in Baggage</h4>
                                                    <div class="plus-widh">
                                                        <p>Total: 35Kg</p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-plane-slash"></i> Cancellation</h4>
                                                    <div class="plus-widh">
                                                        <p><span>Penalties Apply</span></p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-pencil"></i> Modification</h4>
                                                    <div class="plus-widh">
                                                        <p><span>Penalties Apply</span></p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-chair"></i> Seat</h4>
                                                    <div class="plus-widh">
                                                        <p>Not Included</p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-plate-wheat"></i> Meal</h4>
                                                    <div class="plus-widh">
                                                        <p>Not Included</p>
                                                    </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <p>Total round-trip price</p>
                                        <div class="conti">
                                            <a name="" id="" class="btn btn-b" href="/" role="button">Continue</a>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="sugge-tab sugge-tab-time2 ">
                                        <div class="flex1">
                                        <div class="emri">
                                            <img src="assets/images/airblue.png" alt="">
                                        </div>
                                        <div class="der-time">
                                            <ul>
                                                <li>
                                                    <h2>06:10 PM</h2>
                                                </li>
                                                <li>
                                                    <div class="stays">
                                                    <p>
                                                        2h 30m
                                                    </p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <h2>07:40 PM</h2>
                                                </li>
                                            </ul>
                                            <div class="citys">
                                                <div class="cit">
                                                    <ul>
                                                    <li>
                                                        <p>Karachi (KHI)</p>
                                                    </li>
                                                    <li>
                                                        <p>-</p>
                                                    </li>
                                                    <li>
                                                        <p>Nonstop</p>
                                                    </li>
                                                    <li>
                                                        -
                                                    </li>
                                                    <li>
                                                        <p>Dubai (DXB)</p>
                                                    </li>
                                                    </ul>
                                                    <div class="weig weig2">
                                                    <ul>
                                                        <li>
                                                            <p><i class="fa-solid fa-plate-wheat"></i> Meal</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="prices2">
                                        <a name="" id="" class="btn btn-b" href="#" role="button">PKR 91,100</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-6 tab-content">
                                    <div class="main-border">
                                        <div class="sugge-tab">
                                        <div class="flex1">
                                            <div class="emri">
                                                <img src="assets/images/emirates.png" alt="">
                                            </div>
                                            <div class="der-time">
                                                <ul>
                                                    <li>
                                                    <h2>10:40 PM</h2>
                                                    </li>
                                                    <li>
                                                    <div class="stays">
                                                        <p>
                                                            2h 25m
                                                        </p>
                                                    </div>
                                                    </li>
                                                    <li>
                                                    <div class="tims">
                                                        <h2>12:05 AM</h2>
                                                        <span>*1D</span>
                                                    </div>
                                                    </li>
                                                </ul>
                                                <div class="citys">
                                                    <div class="cit">
                                                    <ul>
                                                        <li>
                                                            <p>Karachi (KHI)</p>
                                                        </li>
                                                        <li>
                                                            <p>-</p>
                                                        </li>
                                                        <li>
                                                            <p>Nonstop</p>
                                                        </li>
                                                        <li>
                                                            -
                                                        </li>
                                                        <li>
                                                            <p>Dubai (DXB)</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                    <div class="weig">
                                                    <ul>
                                                        <li>
                                                                <p><i class="fa-solid fa-suitcase-rolling"></i> Total: 35kg</p>
                                                        </li>
                                                        <li>
                                                            <p><i class="fa-solid fa-plate-wheat"></i> Meal</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prices">
                                            <div class="sav">
                                                <p> <i class="fa-solid fa-info"></i> Save 31,115/- PKR</p>
                                            </div>
                                            <div class="pakr">
                                                <a name="" id="" class="btn btn-b" href="#" role="button">PKR 222,252</a>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="selec-option">
                                        <p>Select a fare option</p>
                                        </div>
                                        <div class="flex-plus">
                                        <h4>Flex Plus</h4>
                                        <div class="flex-plus2">
                                            <ul>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-suitcase"></i> Check-in Baggage</h4>
                                                    <div class="plus-widh">
                                                        <p>Total: 35Kg</p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-plane-slash"></i> Cancellation</h4>
                                                    <div class="plus-widh">
                                                        <p><span>Penalties Apply</span></p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-pencil"></i> Modification</h4>
                                                    <div class="plus-widh">
                                                        <p><span>Penalties Apply</span></p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-chair"></i> Seat</h4>
                                                    <div class="plus-widh">
                                                        <p>Not Included</p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-plate-wheat"></i> Meal</h4>
                                                    <div class="plus-widh">
                                                        <p>Not Included</p>
                                                    </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <p>Total round-trip price</p>
                                        <div class="conti">
                                            <a name="" id="" class="btn btn-b" href="/" role="button">Continue</a>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="sugge-tab sugge-tab-time2 ">
                                        <div class="flex1">
                                        <div class="emri">
                                            <img src="assets/images/airblue.png" alt="">
                                        </div>
                                        <div class="der-time">
                                            <ul>
                                                <li>
                                                    <h2>06:10 PM</h2>
                                                </li>
                                                <li>
                                                    <div class="stays">
                                                    <p>
                                                        2h 30m
                                                    </p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <h2>07:40 PM</h2>
                                                </li>
                                            </ul>
                                            <div class="citys">
                                                <div class="cit">
                                                    <ul>
                                                    <li>
                                                        <p>Karachi (KHI)</p>
                                                    </li>
                                                    <li>
                                                        <p>-</p>
                                                    </li>
                                                    <li>
                                                        <p>Nonstop</p>
                                                    </li>
                                                    <li>
                                                        -
                                                    </li>
                                                    <li>
                                                        <p>Dubai (DXB)</p>
                                                    </li>
                                                    </ul>
                                                    <div class="weig weig2">
                                                    <ul>
                                                        <li>
                                                            <p><i class="fa-solid fa-plate-wheat"></i> Meal</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="prices2">
                                        <a name="" id="" class="btn btn-b" href="#" role="button">PKR 91,100</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-7 tab-content">
                                    <div class="main-border">
                                        <div class="sugge-tab">
                                        <div class="flex1">
                                            <div class="emri">
                                                <img src="assets/images/emirates.png" alt="">
                                            </div>
                                            <div class="der-time">
                                                <ul>
                                                    <li>
                                                    <h2>10:40 PM</h2>
                                                    </li>
                                                    <li>
                                                    <div class="stays">
                                                        <p>
                                                            2h 25m
                                                        </p>
                                                    </div>
                                                    </li>
                                                    <li>
                                                    <div class="tims">
                                                        <h2>12:05 AM</h2>
                                                        <span>*1D</span>
                                                    </div>
                                                    </li>
                                                </ul>
                                                <div class="citys">
                                                    <div class="cit">
                                                    <ul>
                                                        <li>
                                                            <p>Karachi (KHI)</p>
                                                        </li>
                                                        <li>
                                                            <p>-</p>
                                                        </li>
                                                        <li>
                                                            <p>Nonstop</p>
                                                        </li>
                                                        <li>
                                                            -
                                                        </li>
                                                        <li>
                                                            <p>Dubai (DXB)</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                    <div class="weig">
                                                    <ul>
                                                        <li>
                                                                <p><i class="fa-solid fa-suitcase-rolling"></i> Total: 35kg</p>
                                                        </li>
                                                        <li>
                                                            <p><i class="fa-solid fa-plate-wheat"></i> Meal</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="prices">
                                            <div class="sav">
                                                <p> <i class="fa-solid fa-info"></i> Save 31,115/- PKR</p>
                                            </div>
                                            <div class="pakr">
                                                <a name="" id="" class="btn btn-b" href="#" role="button">PKR 222,252</a>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="selec-option">
                                        <p>Select a fare option</p>
                                        </div>
                                        <div class="flex-plus">
                                        <h4>Flex Plus</h4>
                                        <div class="flex-plus2">
                                            <ul>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-suitcase"></i> Check-in Baggage</h4>
                                                    <div class="plus-widh">
                                                        <p>Total: 35Kg</p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-plane-slash"></i> Cancellation</h4>
                                                    <div class="plus-widh">
                                                        <p><span>Penalties Apply</span></p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-pencil"></i> Modification</h4>
                                                    <div class="plus-widh">
                                                        <p><span>Penalties Apply</span></p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-chair"></i> Seat</h4>
                                                    <div class="plus-widh">
                                                        <p>Not Included</p>
                                                    </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="plus-fle">
                                                    <h4><i class="fa-solid fa-plate-wheat"></i> Meal</h4>
                                                    <div class="plus-widh">
                                                        <p>Not Included</p>
                                                    </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <p>Total round-trip price</p>
                                        <div class="conti">
                                            <a name="" id="" class="btn btn-b" href="/" role="button">Continue</a>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="sugge-tab sugge-tab-time2 ">
                                        <div class="flex1">
                                        <div class="emri">
                                            <img src="assets/images/airblue.png" alt="">
                                        </div>
                                        <div class="der-time">
                                            <ul>
                                                <li>
                                                    <h2>06:10 PM</h2>
                                                </li>
                                                <li>
                                                    <div class="stays">
                                                    <p>
                                                        2h 30m
                                                    </p>
                                                    </div>
                                                </li>
                                                <li>
                                                    <h2>07:40 PM</h2>
                                                </li>
                                            </ul>
                                            <div class="citys">
                                                <div class="cit">
                                                    <ul>
                                                    <li>
                                                        <p>Karachi (KHI)</p>
                                                    </li>
                                                    <li>
                                                        <p>-</p>
                                                    </li>
                                                    <li>
                                                        <p>Nonstop</p>
                                                    </li>
                                                    <li>
                                                        -
                                                    </li>
                                                    <li>
                                                        <p>Dubai (DXB)</p>
                                                    </li>
                                                    </ul>
                                                    <div class="weig weig2">
                                                    <ul>
                                                        <li>
                                                            <p><i class="fa-solid fa-plate-wheat"></i> Meal</p>
                                                        </li>
                                                    </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        <div class="prices2">
                                        <a name="" id="" class="btn btn-b" href="#" role="button">PKR 91,100</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>

                    </div> -->
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        document.getElementById("selectAllBtn").addEventListener("click", function() {
            let checkboxes = document.querySelectorAll('.btn-group .select input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });

        document.getElementById("clearAllBtn").addEventListener("click", function() {
            let checkboxes = document.querySelectorAll('.btn-group .select input[type="checkbox"]');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });

        function formatTime(minutes) {
            let hours = Math.floor(minutes / 60);
            let mins = minutes % 60;
            return (hours < 10 ? "0" : "") + hours + ":" + (mins < 10 ? "0" : "") + mins;
        }
        // Function to update the range and display
        function updateSliderDisplay(slider1, slider2, display) {
            let time1 = formatTime(parseInt(slider1.value));
            let time2 = formatTime(parseInt(slider2.value));

            if (parseInt(slider1.value) > parseInt(slider2.value)) {
                [slider1.value, slider2.value] = [slider2.value, slider1.value];
            }
            display.textContent = time1 + " - " + time2;
        }
        // Outbound Sliders
        let outboundStart = document.getElementById("outbound-start");
        let outboundEnd = document.getElementById("outbound-end");
        let outboundDisplay = document.getElementById("outbound-time");
        // Return Sliders
        let returnStart = document.getElementById("return-start");
        let returnEnd = document.getElementById("return-end");
        let returnDisplay = document.getElementById("return-time");
        // Update outbound sliders display on input
        outboundStart.addEventListener("input", () => updateSliderDisplay(outboundStart, outboundEnd, outboundDisplay));
        outboundEnd.addEventListener("input", () => updateSliderDisplay(outboundStart, outboundEnd, outboundDisplay));
        // Update return sliders display on input
        returnStart.addEventListener("input", () => updateSliderDisplay(returnStart, returnEnd, returnDisplay));
        returnEnd.addEventListener("input", () => updateSliderDisplay(returnStart, returnEnd, returnDisplay));
        // Initial display update
        updateSliderDisplay(outboundStart, outboundEnd, outboundDisplay);
        updateSliderDisplay(returnStart, returnEnd, returnDisplay);
        //  Duration
        function updateDurationDisplay(slider, display) {
            let value = parseFloat(slider.value);
            display.textContent = value.toFixed(1) + " hours";
        }
        let durationSlider = document.getElementById("duration-slider");
        let durationDisplay = document.getElementById("duration-display");
        durationSlider.addEventListener("input", () => updateDurationDisplay(durationSlider, durationDisplay));
        updateDurationDisplay(durationSlider, durationDisplay);
    </script>
    <script>
        localStorage.clear();
        let isReturn = @json($isRoundTrip) ? true : false;
        let paxCount = @json($paxCount);

        let firstFlight, firstConnectedFlight, returnFlight, returnConnectedFlight;
        let segments, flightSegments;
        let firstBundleId, secondBundleId;
        let updateTimeout, depSegments, rtnSegments, depSelectedFlight, rtnSelectedFlight;
        let flightTotalFare;

        
        $(document).ready(function () {
            const $btn = $('#toggleFlightsBtn');
            const extraFlightCount = $('.extra-flight').length;
            const originalText = extraFlightCount + ' more flight option' + (extraFlightCount > 1 ? 's' : '');

            $btn.on('click', function () {
                const $extraFlights = $('.extra-flight');
                const isVisible = $extraFlights.is(':visible');

                if (isVisible) {
                    $extraFlights.addClass('d-none');
                    $btn.text(originalText);
                } else {
                    $extraFlights.removeClass('d-none');
                    $btn.text('Show less');
                }
            });
        });

        function updateTotalPrice() {
            clearTimeout(updateTimeout);

            updateTimeout = setTimeout(() => {
                let selectedDepFlight = $('input[name="depFlight"]:checked');
                let selectedRtnFlight = $('input[name="rtnFlight"]:checked');

                let departurePrice = parseFloat(selectedDepFlight.val()) || 0;
                let returnPrice = parseFloat(selectedRtnFlight.val()) || 0;

                let totalPrice = departurePrice + returnPrice;
                $('#totalPrice').text(totalPrice.toLocaleString());

                depSegments = selectedDepFlight.data('segment');
                rtnSegments = selectedRtnFlight.data('segment');
                depSelectedFlight = selectedDepFlight.data('selected-flight') || null;
                rtnSelectedFlight = selectedRtnFlight.data('selected-flight') || null;
            }, 100);
        }
        $(document).ready(function() {
            updateTotalPrice();

            $('input[name="depFlight"], input[name="rtnFlight"]').change(function() {
                updateTotalPrice();
            });
        });
        $('.bundleModalBtn').click(function() {
            bookBothBundle(depSegments, rtnSegments);
            $('.modalFlights').html(flightHtml(depSelectedFlight, rtnSelectedFlight))
        });
        /** 
         * Function to book the both bundle 
         */
        const bookBothBundle = (departureData, returnData) => {
            firstFlight = getFlightData(departureData[0]);
            firstConnectedFlight = getFlightData(departureData[1] || null);
            if(!returnData) {
                return getFlightBundle();
            };
            returnFlight = getFlightData(returnData[0]);
            returnConnectedFlight = getFlightData(returnData[1] || null);

            if (!firstFlight || !returnFlight) {
                _alert('Please select both departure and return flights.');
                return;
            }

            getFlightBundle();
        };
        /**
         * Function to fetch flight bundles from the server
         */
        const getFlightBundle = () => {
            $.ajax({
                type: "POST",
                url: "{{ route('get_bundles') }}",
                data: {
                    firstFlight,
                    firstConnectedFlight,
                    returnFlight,
                    returnConnectedFlight,
                    paxCount,
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: () => _loader('show'),
                success: (res) => {
                    if (res.error) {
                        _alert(res.details?.ShortText || res.error, "error");
                        return;
                    }
                    $('#bundleModal').modal("show");
                    if (!res.bundles || res.bundles.length === 0 || (!res.bundles.bundledService && !res.bundles[0]?.bundledService)) {
                        $(".directModalBundles").html(`<div class="alert alert-danger" role="alert">No bundles available</div>`);
                        return;
                    }
                    segments = getSegment(res.originDestinationOptions.FlightSegment) || res.originDestinationOptions.map(item => getSegment(item.FlightSegment));
                    flightTotalFare = res['prices']['ItinTotalFare'] ?? null;
                    let bundledService = res.bundles[0]?.bundledService || res.bundles.bundledService;
                    $(".directModalBundles").html(renderBundles(bundledService, false));
                    if (res.bundles.length > 1) {
                        $(".returnModalBundles").html(renderBundles(res.bundles[1].bundledService, true));
                    }
                },
                error: (xhr, status, error) => console.error('Error:', error),
                complete: () => _loader('hide')
            });
        };

        const flightHtml = (dep, rtn) => {
            if (!dep) return '';
            let rtnflightHtml = rtn ?
                `<li>
                    <div class="sugge-tab sugge-tab-time2 ">
                        <div class="flex1">
                            <div class="emri">
                                <img class="w-75 p-2" src="assets/images/Fly_Jinnah_logo.png" alt="">
                            </div>   
                            <div class="der-time">
                                <ul>
                                    <li><h2>${rtn['departureTime']}</h2></li>
                                    <li><div class="stays"><p>${rtn['timeDifference']}</p></div></li>
                                    <li><h2>${rtn['arrivalTime']}</h2></li>
                                </ul>
                                <div class="citys">
                                    <div class="cit">
                                        <ul>
                                            <li><p>${rtn['originCode']}</p></li>
                                            <li><p>-</p></li>
                                            <li><p>${rtn['isConnected'] ? '1 Stop' : 'Nonstop'}</p></li>
                                            <li>-</li>
                                            <li><p>${rtn['destinationCode']}</p></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="prick">

                            <a class="bg-info btn text-light" href="#" role="button">PKR ${rtn['price']}</a>
                        </div>
                    </div>
                </li>` : '';
            return `
                <ul>
                    <li>
                        <div class="sugge-tab sugge-tab-time2 ">
                            <div class="flex1">
                                <div class="emri">
                                    <img class="w-75 p-2" src="assets/images/Fly_Jinnah_logo.png" alt="">
                                </div>   
                                <div class="der-time ">
                                    <ul>
                                        <li><h2>${dep['departureTime']}</h2></li>
                                        <li><div class="stays"><p>${dep['timeDifference']}</p></div></li>
                                        <li><h2>${dep['arrivalTime']}</h2></li>
                                    </ul>
                                    <div class="citys">
                                        <div class="cit">
                                            <ul>
                                                <li><p>${dep['originCode']}</p></li>
                                                <li><p>-</p></li>
                                                <li><p>${dep['isConnected'] ? '1 Stop' : 'Nonstop'}</p></li>
                                                <li>-</li>
                                                <li><p>${dep['destinationCode']}</p></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="pritik">
                                <a class="bg-info btn text-light" href="#" role="button">PKR ${dep['price']}</a>
                            </div>
                        </div>
                    </li>
                    ${rtnflightHtml}
                </ul>`;
        }

        /**
         * Function to render flight bundles
         */
        const renderBundles = (data, isReturn) => {
            const normalizedData = Array.isArray(data) ? data : (data ? [data] : []);

            if (normalizedData.length === 0) {
                return `<div class="alert alert-danger" role="alert">No flights available</div>`;
            }
            return normalizedData.map(row => {
                let description = parseDescription(row['description']);
                return`
                    <li data-id="${row['bunldedServiceId'] ?? 'N/A'}">
                        <div class="flex-plus flex-plusul2">
                            <h4>${row['bundledServiceName'] ?? 'N/A'}</h4>
                            <div class="flex-plus2 ">
                                <ul>
                                    <li>
                                        <div class="plus-fle">
                                            <h4><i class="fa-solid fa-suitcase"></i>
                                                Check-in Baggage
                                            </h4>
                                            <div class="plus-widh">
                                                <p>${description['Baggage'] ?? 'Not Included'}</p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="plus-fle">
                                            <h4><i class="fa-solid fa-plane-slash"></i>Cancellation</h4>
                                            <div class="plus-widh">
                                                <p><span>${description['Cancellation'] ? 'PenaltiesApply' : 'Not Available'}</span></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="plus-fle">
                                            <h4><i class="fa-solid fa-pencil"></i>Modification</h4>
                                            <div class="plus-widh"><p><span>${description['Modification'] ? 'PenaltiesApply' : 'Not Available'}</span></p></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="plus-fle">
                                            <h4><i class="fa-solid fa-plate-wheat"></i>Meal</h4>
                                            <div class="plus-widh"><p>${description['Any Meal'] ? 'Included' : 'Not Included'}</p></div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="conti">
                                <a class="btn btn-b bookBtn" data-is-return="${isReturn}" data-bundle-id="${row['bunldedServiceId']}" role="button">+ PKR ${Math.round(row['perPaxBundledFee'] || '0')}</a>
                            </div>
                        </div>
                    </li>`;
            }).join('');
        };

        /** 
         * Handle booking button click 
         */
        $(document).on('click', '.bookBtn', function() {
            $(this).addClass('active');
            let bundleId = $(this).data('bundle-id');
            let isReturnBundle = $(this).data('is-return');

            if (!isReturnBundle) {
                firstBundleId = bundleId;
                if (isReturn) {
                    $('.tab-product li[data-targetit="box-17"] a').trigger('click');
                    // _alert('First bundle selected. Now select a return bundle.');
                }
            } else {
                if (!firstBundleId) {
                    _alert('You must select the first bundle before selecting the return bundle.', 'warning');
                    return;
                }
                secondBundleId = bundleId;
            }
            if (firstBundleId && (!isReturn || secondBundleId)) {
                sendBookingRequest(false);
            }
        });
        $(document).on('click', '.directBooking', function() {
            sendBookingRequest(true);
        });
        /**
         * Function to send booking request AJAX
         */
        const sendBookingRequest = isDirectBooking => {
            // if (!firstBundleId) {
            //     _alert('You must select at least one bundle.');
            //     return;
            // }
            $.ajax({
                type: "POST",
                url: "{{ route('booking_details') }}",
                data: {
                    firstBundleId: firstBundleId ?? null,
                    secondBundleId: secondBundleId ?? null,
                    depSelectedFlight: depSelectedFlight ?? null, 
                    rtnSelectedFlight: rtnSelectedFlight ?? null,
                    isDirectBooking, flightTotalFare, segments, paxCount,
                    _token: "{{ csrf_token() }}"
                },
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
                    console.error('Error:', error);
                },
                complete: () => _loader('hide')
            });
        };

        /**
         * Function to extract flight data
         */
        const getFlightData = (data) => {
            if (!data) return null;
            return {
                departure: data['departureDateTimeLocal'],
                arrival: data['arrivalDateTimeLocal'],
                origin: data['origin'],
                destination: data['destination'],
                flightNumber: data['flightNumber']
            };
        };

        /**
         * Function to parse flight description
         */
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

        /**
         * Function to extract flight segment data
         */
        const getSegment = (data) => {
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
    </script>
@endsection
