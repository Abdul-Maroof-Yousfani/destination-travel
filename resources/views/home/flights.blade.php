@extends('home/layouts/master')
@section('title', 'Flights')
@section('style')
    <style>
        .select-flight{text-align:center;}
        .der-time ul li h2{font-size:20px;}
        .flight-card{border:1px solid #ddd;border-radius:10px;padding:20px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin:20px auto;}
        .flight-duration{background-color:#f2f2f2;padding:2px 8px;border-radius:5px;font-size:0.8rem;margin:5px 0;display:inline-block;}
        .price-btn{background-color:#127f9f;color:white;font-weight:bold;border:none;padding:8px 15px;border-radius:5px;display:inline-block;}
        .airline-logo{width:40px;height:auto;}
        .timesHeading{font-size:2em;font-weight:bolder;}
    </style>
@endsection
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <section class="mainBanner wow fadeInLeft">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <x-search-flight />
                </div>
            </div>
        </div>
    </section>
    {{-- @dd($data) --}}
    <section class="search wow fadeInRight">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-3 br-right">
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

                            <div class="shop-check shop-check2">
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
                <div class="col-md-12 col-lg-9">
                    <x-flights :flightData="$data" :paxCount="$paxCount" />
                </div>
            </div>
        </div>
    </section>
    <x-session-timeout-container/>
@endsection
@section('script')
<script>
    localStorage.clear();
</script>
@endsection
