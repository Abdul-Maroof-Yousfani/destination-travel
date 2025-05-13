@extends('home/layouts/master')

@section('title', 'Booking')
@section('style')
<style>
  .plane{padding:20px;border-radius:15px;overflow-x:auto;scrollbar-width:thin;}
  .seat-row{display:flex;flex-direction:column;align-items:center;gap:5px;margin:5px}
  .seat{cursor:pointer;border:solid 1px #b9b9b9;width:39px;height:36px;border-radius:4px;padding:8px 0px;margin-top:6px;position:relative;}
  .seat:before{content:"";display:block;width:30px;height:26px;border:solid 1px #88857c;border-left:none;border-radius:0 5px 5px 0;position:absolute;margin:-4px -1px;}
  .seat.selected{background-color:green;color:white;}
  .seat.occupied{background-color:#d9d8d6;color:#000000;pointer-events:none;}
  .aisle{height:20px;}
  .mealCard:hover,.baggageCard:hover{background-color:#127F9F;color:#ffff;box-shadow:0 5px 15px rgba(0,0,0,0.2);}
  /* .footerTimeOutContainer{z-index:9999;position:fixed;bottom:0;left:0;right:0;background-color:#127f9fe0;color:#fff;padding:15px;transition:opacity 0.5s ease-in-out;} */
  .addOnsContainer{width:100%;}
  .tabPlane{display:flex;flex-direction:column;justify-content:space-between;position:relative;min-width:min-content;margin:auto;border:2px solid #c5c5c7;min-height:280px;border-right:none;border-left:none;padding:10px;background:#fff;left:250px;top:0px;margin-bottom:115px;margin-top:115px;}
  .tabPlane:before{content:"";position:absolute;height:336px;width:548px;padding-left:115px;border-radius:60% 0% 0% 60%;border:2px solid #c5c5c7;left:-263px;top:-2px;border-right:none;}
  .tabPlane:after{content:"";position:absolute;height:336px;width:548px;padding-left:115px;border-radius:0% 60% 60% 0%;border:2px solid #c5c5c7;right:-263px;top:-2px;border-left:none;}
  .winds1{position:relative;}
  .winds1:before{content:"";position:absolute;top:-104px;right:472px;width:250px;height:94px;border-bottom-left-radius:30px;border-right:none !important;border-top:none !important;border:2px solid #c5c5c7;}
  .winds1:after{content:"";position:absolute;width:250px;height:94px;border:2px solid #c5c5c7;border-bottom-right-radius:60px;border-top:none;border-left:none;transform:skew(-35deg);left:322px;top:-104px;}
  .winds2{position:relative;}
  .winds2:after{content:"";position:absolute;width:250px;height:94px;border:2px solid #c5c5c7;border-top-right-radius:60px;border-bottom:none;border-left:none;transform:skew(35deg);left:303px;bottom:-104px;}
  .winds2:before{content:"";position:absolute;bottom:-104px;left:705px;width:250px;height:94px;border:2px solid #c5c5c7;border-top-left-radius:30px;border-right:none;border-bottom:none;}
  .windows{position:relative;}
  .windows:before{content:"";position:absolute;top:70px;left:-242px;width:100px;height:150px;background-image:url(/assets/images/windows.png);}
  .windows:after{content:"";position:absolute;top:70px;left:-66px;width:37px;height:150px;background-image:url(/assets/images/lavatory.png);}
  .windows2{position:relative;}
</style>
@endsection
@section('content')
@php
   $isEmirate = $data['airline'] === 'emirate';
   $isFlyJinnah = $data['airline'] === 'flyjinnah';
@endphp
@if (!isset($data) || empty($data))
   <script>
      Swal.fire({
         title: 'Please search for the flight again, the data has been lost.',
         icon: 'info',
         confirmButtonText: 'Go Back',
         allowOutsideClick: false,
         allowEscapeKey: false,
         preConfirm: () => {
            _loader('show');
            let goBack = localStorage.getItem('flights') || null;
            goBack ? window.location.href = `/flights${goBack}` : window.history.back();
         }
      });
   </script>
@endif
<section class="bookings wow fadeInLeft">
   <div class="container">
      <div class="row">
         <div class="col-md-12 col-lg-12">
            <div class="books">
               <form id="msform">
                  <!-- progressbar -->
                  <div class="steps-fom">
                     <ul id="progressbar">
                        <li class="active" id="account"><strong><p>Booking</p></strong></li>
                        <li id="personal"><strong><p>Payment</p></strong></li>
                        <li id="payment"><strong><p>Ticket</p></strong></li>
                        <!-- <li id="confirm"><strong>Finish</strong></li> -->
                     </ul>
                  </div>
                  {{-- @dd($data, $totalFare, $tax) --}}
                  <!-- booking form -->
                  <fieldset>
                     <div class="form-card">
                        <div class="row row2">
                           <div class="col-md-12 col-lg-8">
                              @if (\Illuminate\Support\Facades\App::environment('local'))
                                 <x-PassengersTest :flightData="$data" />
                              @elseif (\Illuminate\Support\Facades\App::environment('production'))
                                 <x-Passengers :flightData="$data" />
                              @endif
                           </div>
                           <div class="col-md-12 col-lg-4">
                              {{-- @dd($totalFare) --}}
                              <x-FlightAndPrice :flightData="$data" :totalFare="$totalFare" :tax="$tax" />
                              {{-- <div class="bokkings-bar bokkings-bar2">
                                 <div class="book-head">
                                 <div class="youbook">
                                    <h2><span>Price Summary</span></h2>
                                 </div>
                                 </div>
                                 <div class="book-flex">
                                 <div class="emr w-25">
                                    <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Flight logo">
                                 </div>
                                 </div>
                                 <div class="der-time der-time3">
                                    @if ($isEmirate)
                                       @if(isset($data['flightDetails']['bundle']['offerItem']) && count($data['flightDetails']['bundle']['offerItem']) > 0)
                                          @foreach ($data['flightDetails']['bundle']['offerItem'] as $offer)
                                             <div class="emr-adul justify-content-between">
                                                <p>{{ $offer['fareDetail']['passengers'] ?? '' }}</p>
                                                <p>{{ $offer['totalPrice']['code'] ?? 'PKR' }} {{ number_format($offer['totalPrice']['amount'] ?? 0, 2) }}</p>
                                             </div>
                                          @endforeach
                                       @else
                                          <div class="emr-adul justify-content-between">
                                             <p>Flight Price</p>
                                             <p>{{ $data['flightDetails']['bundle']['totalPrice']['code'] ?? 'PKR' }} {{ number_format($data['flightDetails']['bundle']['totalPrice']['amount'] ?? 0, 2) }}</p>
                                          </div>
                                       @endif
                                    @elseif ($isFlyJinnah)
                                       <div class="emr-adul justify-content-between">
                                          @if (isset($data['isDirectBooking']) && !$data['isDirectBooking'])
                                             <p>Flight with bundle</p>
                                          @else
                                             <p>Flight Price</p>
                                          @endif
                                          <p>{{ $totalFare['TotalFare']['@attributes']['CurrencyCode'] ?? 'PKR' }} {{ $totalFare['TotalFare']['@attributes']['Amount'] ?? '' }}</p>
                                       </div>
                                    @endif
                                    <div class="emr-adul justify-content-between">
                                       <p>Tax</p>
                                       <p>PKR {{ $tax }}</p>
                                    </div>
                                    <div class="pri-pak">
                                       <h2>Total price you pay</h2>
                                       @if ($isEmirate)
                                          <p>
                                             {{ $data['flightDetails']['bundle']['totalPrice']['code'] ?? 'PKR' }}
                                             {{ number_format(($data['flightDetails']['bundle']['totalPrice']['amount'] ?? 0) + ($tax ?? 0), 2) }}
                                          </p>
                                       @elseif ($isFlyJinnah)
                                          <p>
                                             {{ $totalFare['TotalFare']['@attributes']['CurrencyCode'] ?? 'PKR' }}
                                             {{ ($totalFare['TotalFare']['@attributes']['Amount'] ?? 0) + ($tax ?? 0) }}
                                          </p>
                                       @endif
                                    </div>
                                 </div>
                              </div>
                              @if ($isEmirate)
                                 <div class="bokkings-bar bokkings-bar2">
                                    <div class="book-head">
                                       <div class="youbook">
                                          <h2><span>Penalties</span></h2>
                                       </div>
                                    </div>
                                    <div class="book-flex">
                                       <div class="emr w-25">
                                          <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Flight logo">
                                       </div>
                                    </div>
                                    <div class="der-time der-time3">
                                       @if(isset($data['flightDetails']['bundle']['offerItem']) && count($data['flightDetails']['bundle']['offerItem']) > 0)
                                          @foreach ($data['flightDetails']['bundle']['offerItem'] as $offer)
                                             <div class="emr-adul justify-content-between">
                                                <p>{{ $offer['fareDetail']['passengers'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>{{ $offer['fareDetail']['penalties'][0]['arrival'] ?? '' }}</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['destination'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>Cancel Fee</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['fareRules']['CancelFeeInd'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>Change Fee</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['fareRules']['ChangeFeeInd'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>Refundable Fee</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['fareRules']['RefundableInd'] ?? '' }}</p>
                                             </div>
                                             @if (isset($offer['fareDetail']['penalties'][1]))
                                                <div class="emr-adul justify-content-between">
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['arrival'] ?? '' }}</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['destination'] ?? '' }}</p>
                                                </div>
                                                <div class="emr-adul justify-content-between">
                                                   <p>Cancel Fee</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['fareRules']['CancelFeeInd'] ?? '' }}</p>
                                                </div>
                                                <div class="emr-adul justify-content-between">
                                                   <p>Change Fee</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['fareRules']['ChangeFeeInd'] ?? '' }}</p>
                                                </div>
                                                <div class="emr-adul justify-content-between">
                                                   <p>Refundable Fee</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['fareRules']['RefundableInd'] ?? '' }}</p>
                                                </div>
                                             @endif
                                          @endforeach
                                       @endif
                                    </div>
                                 </div>
                              @endif --}}
                           </div>
                        @if (isset($data['isDirectBooking']) && !$data['isDirectBooking'])
                           <div class="addOnsContainer">
                              <h2 class="my-3 text-info font-weight-bolder">Add-Ons</h2>
                              <div class="card">
                                 <div class="card-header d-flex justify-content-between">
                                    <div>
                                       <div data-targetit="box-021" class="btn btn-outline-info mx-2 current" data-toggle="tab">Seat</div>
                                       <div data-targetit="box-022" class="btn btn-outline-info mx-2" data-toggle="tab">Meal</div>
                                       <div data-targetit="box-023" class="btn btn-outline-info mx-2" data-toggle="tab">Baggage</div>
                                    </div>
                                    {{-- <div class="btn btn-light" id="skipAncis">Skip to payment</div> --}}
                                 </div>   
                                 <div class="card-body">
                                    <div class="box-021 tab-content showfirst">
                                       <div class="d-flex border border-dark p-2 seatFlightSegments"></div>
                                       <div class="container mt-4">
                                          <div class="plane"></div>
                                       </div>
                                    </div>
                                    <div class="box-022 tab-content">
                                       <div class="d-flex border border-dark p-2 mealFlightSegments"></div>
                                       <div class="meals container mt-4"></div>
                                    </div>
                                    <div class="box-023 tab-content">
                                       <div class="d-flex border border-dark p-2 baggageFlightSegments"></div>
                                       <div class="baggages container mt-4"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        @endif
                        </div>
                     </div>
                     <input type="button" name="next" class="next btn btn btn-b" id="contactSubmit" value="Continue" />
                  </fieldset>
                  <!-- payment -->
                  <fieldset>
                     <div class="form-card">
                        <div class="row">
                           <div class="col-md-12 col-lg-8">
                              <div class="pays">
                                 <h2>select a payment method</h2>
                                 <ul>
                                    <li><div class="payicon"><i class="fa-regular fa-credit-card"></i> <p>Secure Payments</p> </div></li>
                                    <li><div class="payicon"><i class="fa-solid fa-bolt"></i> <p>Quick Refunds</p> </div></li>
                                    <li><div class="payicon"><i class="fa-regular fa-thumbs-up"></i> <p>Trusted by 10 lac customers</p> </div></li>
                                 </ul>
                              </div>
                              <div class="pay-taps">
                                 <div class="row">
                                    <div class="col-md-12 col-lg-4 br-right">
                                       <div class="payments-taps-lists">
                                          <ul class="tab-product  wow fadeInRight">
                                             <li data-targetit="box-10">
                                                <a href="#tab-10" data-toggle="tab">
                                                   <div class="payflexpay">
                                                      <div class="payment-option">
                                                         <label for="jazzcash">JazzCash</label>
                                                      </div>
                                                      <div class="pay-logo">
                                                         <img src="/assets/images/jazz.png" alt="">
                                                      </div>
                                                   </div>
                                                </a>
                                             </li>
                                             <li data-targetit="box-9" class="current">
                                                <a href="#tab-9" data-toggle="tab">
                                                   <div class="payflexpay">
                                                      <div class="payment-option">
                                                         <label for="hbl">HBL Direct Transfer</label>
                                                            <div class="savpkr">
                                                               <p>Save PKR 4,223</p>
                                                            </div>
                                                      </div>
                                                      <div class="pay-logo">
                                                            <img src="/assets/images/hbl.png" alt="">
                                                      </div>
                                                   </div>
                                                </a>
                                             </li>
                                          </ul>
                                       </div>
                                    </div>
                                    <div class="col-md-12 col-lg-8 ">
                                       {{-- HBL --}}
                                       <div class="box-9 showfirst tab-content">
                                          <div class="payment-radio">
                                             <div class="coinfex">
                                                <div class="cois">
                                                   <i class="fa-solid fa-coins"></i>
                                                </div>
                                                <div class="coin-sas">
                                                   <h2>Sasta Wallet</h2>
                                                   <p>Login to access wallet.</p>
                                                </div>
                                             </div>
                                             <div class="cois-butt">
                                                <a href="#" class="btn btn-c">Login</a>
                                             </div>
                                          </div>
                                          <div class="custom-method">
                                             <div class="bulco">
                                                <i class="fa-solid fa-circle"></i>
                                                <p> Click continue to receive your customer ID</p>
                                             </div>
                                          </div>
                                          <div class="method-feild">
                                             <div class="form-group">
                                                <label for="">select Transfer Method</label> 
                                             <select class="form-control" name="" id="">
                                                <option></option>
                                                <option></option>
                                                <option></option>
                                             </select>
                                             </div>
                                             <div class="form-group">
                                                <label for="">select Bank</label> 
                                             <select class="form-control" name="" id="">
                                                <option></option>
                                                <option></option>
                                                <option></option>
                                             </select>
                                             </div>
                                          </div>
                                          <div class="voucher">
                                             <h2>Please <span>login</span> to avail discounts on voucher codes.</h2>
                                             <p>By selecting to complete this booking, I acknowledge that I have read and accept the above Policy section containing Fare Rules & Restrictions, <span><a href="#">Terms of Use</a></span> and <span><a href="#">Privacy Policy</a></span>.  </p>
                                          </div>
                                       </div>
                                       <div class="box-10 tab-content">
                                          <div class="payment-radio">
                                             <div class="coinfex">
                                                <div class="cois">
                                                   <i class="fa-solid fa-coins"></i>
                                                </div>
                                                <div class="coin-sas">
                                                   <h2>Sasta Wallet</h2>
                                                   <p>Login to access wallet.</p>
                                                </div>
                                             </div>
                                             <div class="cois-butt">
                                                <a href="#" class="btn btn-c">Login</a>
                                             </div>
                                          </div>

                                          <div class="custom-method">
                                             <div class="bulco">
                                                <i class="fa-solid fa-circle"></i>
                                                <p> Click continue to receive your customer ID</p>
                                             </div>
                                             <div class="bulco">
                                                <i class="fa-solid fa-circle"></i>
                                                <p> Click continue to receive your customer ID</p>
                                             </div>
                                             <div class="bulco">
                                                <i class="fa-solid fa-circle"></i>
                                                <p> Click continue to receive your customer ID</p>
                                             </div>
                                          </div>

                                          <div class="method-feild">
                                             <div class="form-group">
                                                <label for="">select Transfer Method</label> 
                                             <select class="form-control" name="" id="">
                                                <option></option>
                                                <option></option>
                                                <option></option>
                                             </select>
                                             </div>
                                             <div class="form-group">
                                                <label for="">select Bank</label> 
                                             <select class="form-control" name="" id="">
                                                <option></option>
                                                <option></option>
                                                <option></option>
                                             </select>
                                             </div>
                                          </div>


                                          <div class="voucher">
                                             <h2>Please <span>login</span> to avail discounts on voucher codes.</h2>
                                             <p>By selecting to complete this booking, I acknowledge that I have read and accept the above Policy section containing Fare Rules & Restrictions, <span><a href="#">Terms of Use</a></span> and <span><a href="#">Privacy Policy</a></span>.  </p>
                                          </div>

                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 col-lg-4">
                              <x-FlightAndPrice :flightData="$data" :totalFare="$totalFare" :tax="$tax" priceclass="paymentPriceContainer"/>
                              {{-- <div class="bokkings-bar bokkings-bar2 paymentPriceContainer">
                                 <div class="book-head">
                                 <div class="youbook">
                                    <h2><span>Price Summary</span></h2>
                                 </div>
                                 </div>
                                 <div class="book-flex">
                                 <div class="emr w-25">
                                    <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Fly Jinnah logo">
                                 </div>
                                 </div>
                                 <div class="der-time der-time3">
                                    @if ($isEmirate)
                                       @if(isset($data['flightDetails']['bundle']['offerItem']) && count($data['flightDetails']['bundle']['offerItem']) > 0)
                                          @foreach ($data['flightDetails']['bundle']['offerItem'] as $offer)
                                             <div class="emr-adul justify-content-between">
                                                <p>{{ $offer['fareDetail']['passengers'] ?? '' }}</p>
                                                <p>{{ $offer['totalPrice']['code'] ?? 'PKR' }} {{ number_format($offer['totalPrice']['amount'] ?? 0, 2) }}</p>
                                             </div>
                                          @endforeach
                                       @else
                                          <div class="emr-adul justify-content-between">
                                             <p>Flight Price</p>
                                             <p>{{ $data['flightDetails']['bundle']['totalPrice']['code'] ?? 'PKR' }} {{ number_format($data['flightDetails']['bundle']['totalPrice']['amount'] ?? 0, 2) }}</p>
                                          </div>
                                       @endif
                                    @elseif ($isFlyJinnah)
                                       <div class="emr-adul justify-content-between">
                                          @if (isset($data['isDirectBooking']) && !$data['isDirectBooking'])
                                             <p>Flight with bundle</p>
                                          @else
                                             <p>Flight Price</p>
                                          @endif
                                          <p>{{ $totalFare['TotalFare']['@attributes']['CurrencyCode'] ?? 'PKR' }} {{ $totalFare['TotalFare']['@attributes']['Amount'] ?? '' }}</p>
                                       </div>
                                    @endif
                                    <div class="emr-adul justify-content-between">
                                       <p>Tax</p>
                                       <p>PKR {{ $tax }}</p>
                                    </div>
                                    <div class="pri-pak">
                                       <h2>Total price you pay</h2>
                                       @if ($isEmirate)
                                          <p>
                                             {{ $data['flightDetails']['bundle']['totalPrice']['code'] ?? 'PKR' }}
                                             {{ number_format(($data['flightDetails']['bundle']['totalPrice']['amount'] ?? 0) + ($tax ?? 0), 2) }}
                                          </p>
                                       @elseif ($isFlyJinnah)
                                          <p>
                                             {{ $totalFare['TotalFare']['@attributes']['CurrencyCode'] ?? 'PKR' }}
                                             {{ ($totalFare['TotalFare']['@attributes']['Amount'] ?? 0) + ($tax ?? 0) }}
                                          </p>
                                       @endif
                                    </div>
                                 </div>
                              </div>
                              @if ($isEmirate)
                                 <div class="bokkings-bar bokkings-bar2">
                                    <div class="book-head">
                                       <div class="youbook">
                                          <h2><span>Penalties</span></h2>
                                       </div>
                                    </div>
                                    <div class="book-flex">
                                       <div class="emr w-25">
                                          <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Flight logo">
                                       </div>
                                    </div>
                                    <div class="der-time der-time3">
                                       @if(isset($data['flightDetails']['bundle']['offerItem']) && count($data['flightDetails']['bundle']['offerItem']) > 0)
                                          @foreach ($data['flightDetails']['bundle']['offerItem'] as $offer)
                                             <div class="emr-adul justify-content-between">
                                                <p>{{ $offer['fareDetail']['passengers'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>{{ $offer['fareDetail']['penalties'][0]['arrival'] ?? '' }}</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['destination'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>Cancel Fee</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['fareRules']['CancelFeeInd'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>Change Fee</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['fareRules']['ChangeFeeInd'] ?? '' }}</p>
                                             </div>
                                             <div class="emr-adul justify-content-between">
                                                <p>Refundable Fee</p>
                                                <p>{{ $offer['fareDetail']['penalties'][0]['fareRules']['RefundableInd'] ?? '' }}</p>
                                             </div>
                                             @if (isset($offer['fareDetail']['penalties'][1]))
                                                <div class="emr-adul justify-content-between">
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['arrival'] ?? '' }}</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['destination'] ?? '' }}</p>
                                                </div>
                                                <div class="emr-adul justify-content-between">
                                                   <p>Cancel Fee</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['fareRules']['CancelFeeInd'] ?? '' }}</p>
                                                </div>
                                                <div class="emr-adul justify-content-between">
                                                   <p>Change Fee</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['fareRules']['ChangeFeeInd'] ?? '' }}</p>
                                                </div>
                                                <div class="emr-adul justify-content-between">
                                                   <p>Refundable Fee</p>
                                                   <p>{{ $offer['fareDetail']['penalties'][1]['fareRules']['RefundableInd'] ?? '' }}</p>
                                                </div>
                                             @endif
                                          @endforeach
                                       @endif
                                    </div>
                                 </div>
                              @endif --}}
                           </div>
                        </div>
                     </div>
                     {{-- <input type="button" name="previous" class="previous btn btn btn-b" value="Previous" /> --}}
                     <input type="button" name="next" class="next d-none" id="paymentSend"/>
                     <input type="button" class="btn btn-b" value="Continue" id="paymentSendTest"/>
                     <div class="custom-control custom-switch d-flex m-3">
                        <input type="checkbox" class="custom-control-input" checked id="paymentOnHold">
                        <label class="custom-control-label paymentOnHoldText" for="paymentOnHold">Payment is ON HOLD</label>
                      </div>
                  </fieldset>
                  <!-- payment done -->
                  <fieldset>
                     <div class="form-card">
                        <div class="row">
                           <div class="col-md-12 col-lg-8">
                              <div class="tyous">
                                 <h2>Thank You, <span class="guestName"></span>!</h2>
                                 <p>You’re one step away from traveling to 
                                    @if (isset($data) && !empty($data))
                                       @if ($isEmirate)
                                          {{$data['flightDetails']['segments'][0]['flights']['Arrival']['AirportCode']['value'] ?? ''}}
                                       @elseif ($isFlyJinnah)
                                          {{$data['departureFlight']['destinationCode'] ?? ''}}
                                       @endif
                                    @endif
                                 !</p>
                                 <p class="ticketMsg text-decoration-underline"></p>
                                 <h3><span>Order ID: <span class="orderId copyBtn font-weight-bolder"></span></span></h3>
                              </div>
                              <div class="steps">
                                 <h4>Next Steps</h4>
                                 <div class="custom-method setp-bult">
                                    <div class="bulco">
                                       <i class="fa-solid fa-circle"></i>
                                       <p> Consumer ID: Please make a payment against Consumer ID: <span class="orderId font-weight-bolder"></p>
                                    </div>
                                    <div class="bulco">
                                       <i class="fa-solid fa-circle"></i>
                                       <p> Confirmation: You will receive an email with your e-ticket confirmation and a confirmation WhatsApp message once the payment is verified.</p>
                                    </div>
                                    <div class="bulco">
                                       <i class="fa-solid fa-circle"></i>
                                       <p> Excise Duty: Please add PKR 2,000 for excise duty on ticket prices above PKR 300,000.</p>
                                    </div>
                                    <div class="bulco">
                                       <i class="fa-solid fa-circle"></i>
                                       <p> Payment Help: Contact <a href="tel:+{{ config('variables.contact.phone') }}">{{ config('variables.contact.phone') }}</a> if you need assistance. </p>
                                    </div>
                                 </div>
                              </div>
                              <div class="steps">
                                 <h4>Your Booking</h4>
                                 <div class="sugge-tab sugge-tab-tickes">
                                    <div class="flex1">
                                       <div class="emri">
                                          <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Flight logo">
                                       </div>   
                                       <div class="der-time der-time-setps">
                                          <ul>
                                             @if (isset($data) && !empty($data))
                                                @if ($isEmirate)
                                                   <li><h2 class="timeIn12Hr">{{$data['flightDetails']['segments'][0]['flights']['Departure']['Time']['value'] ?? ''}}</h2></li>
                                                   <li><div class="stays"><p>{{$data['flightDetails']['segments'][0]['flights']['duration'] ?? ''}}</p></div></li>
                                                   <li><div class="tims"><h2 class="timeIn12Hr">{{$data['flightDetails']['segments'][0]['flights']['Arrival']['Time']['value'] ?? ''}}</h2></div></li>
                                                @elseif ($isFlyJinnah)
                                                   <li><h2>{{$data['departureFlight']['departureTime'] ?? ''}}</h2></li>
                                                   <li><div class="stays"><p>{{$data['departureFlight']['timeDifference'] ?? ''}}</p></div></li>
                                                   <li><div class="tims"><h2>{{$data['departureFlight']['arrivalTime'] ?? ''}}</h2></div></li>
                                                @endif
                                             @endif
                                          </ul>
                                          <div class="citys">
                                             <div class="cit">
                                                <ul>
                                                   @if (isset($data) && !empty($data))
                                                      @if ($isEmirate)
                                                         <li><p>{{$data['flightDetails']['segments'][0]['flights']['Departure']['AirportName']['value'] ?? ''}}</p></li>
                                                         <li><p>-</p></li>
                                                         <li><p>{{$data['flightDetails']['segments'][0]['flights']['flightDetails']['isConnected'] ? '1 Stop' : 'Nonstop' }}</p></li>
                                                         <li><p>-</p></li>
                                                         <li><p>{{$data['flightDetails']['segments'][0]['flights']['Arrival']['AirportName']['value'] ?? ''}}</p></li>
                                                      @elseif ($isFlyJinnah)
                                                         <li><p>{{$data['departureFlight']['originCode'] ?? ''}}</p></li>
                                                         <li><p>-</p></li>
                                                         <li><p>{{ $data['departureFlight']['isConnected'] ? '1 Stop' : 'Nonstop' }}</p></li>
                                                         <li><p>-</p></li>
                                                         <li><p>{{$data['departureFlight']['destinationCode'] ?? ''}}</p></li>
                                                      @endif
                                                   @endif
                                                </ul>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 @if (isset($data) && !empty($data))
                                    @if ($isEmirate)
                                       @if(isset($data['flightDetails']['segments'][1]['flights']) && count($data['flightDetails']['segments'][1]['flights']) > 0)
                                          <div class="sugge-tab sugge-tab-tickes mt-2">
                                             <div class="flex1">
                                                <div class="emri">
                                                   <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Flight logo">
                                                </div>   
                                                <div class="der-time der-time-setps">
                                                   <ul>
                                                      <li><h2 class="timeIn12Hr">{{$data['flightDetails']['segments'][1]['flights']['Departure']['Time']['value'] ?? ''}}</h2></li>
                                                      <li><div class="stays"><p>{{$data['flightDetails']['segments'][1]['flights']['duration'] ?? ''}}</p></div></li>
                                                      <li><div class="tims"><h2>{{$data['flightDetails']['segments'][1]['flights']['Arrival']['Time']['value'] ?? ''}}</h2></div></li>
                                                   </ul>
                                                   <div class="citys">
                                                      <div class="cit">
                                                         <ul>
                                                            <li><p>{{$data['flightDetails']['segments'][1]['flights']['Departure']['AirportName']['value'] ?? ''}}</p></li>
                                                            <li><p>-</p></li>
                                                            <li><p>{{$data['flightDetails']['segments'][1]['flights']['flightDetails']['isConnected'] ? '1 Stop' : 'Nonstop' }}</p></li>
                                                            <li><p>-</p></li>
                                                            <li><p>{{$data['flightDetails']['segments'][1]['flights']['Arrival']['AirportName']['value'] ?? ''}}</p></li>
                                                         </ul>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       @endif
                                    @elseif ($isFlyJinnah)
                                       @if (!empty($data['returnFlight']) || !empty($data['segments'][1]))
                                          <div class="sugge-tab sugge-tab-tickes mt-2">
                                             <div class="flex1">
                                                <div class="emri">
                                                   <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Fly Jinnah logo">
                                                </div>   
                                                <div class="der-time der-time-setps">
                                                   <ul>
                                                      <li><h2>{{$data['returnFlight']['departureTime']}}</h2></li>
                                                      <li><div class="stays"><p>{{$data['returnFlight']['timeDifference']}}</p></div></li>
                                                      <li><div class="tims"><h2>{{$data['returnFlight']['arrivalTime']}}</h2></div></li>
                                                   </ul>
                                                   <div class="citys">
                                                      <div class="cit">
                                                         <ul>
                                                            <li><p>{{$data['returnFlight']['originCode']}}</p></li>
                                                            <li><p>-</p></li>
                                                            <li><p>{{ $data['returnFlight']['isConnected'] ? '1 Stop' : 'Nonstop' }}</p></li>
                                                            <li><p>-</p></li>
                                                            <li><p>{{$data['returnFlight']['destinationCode']}}</p></li>
                                                         </ul>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       @endif
                                    @endif
                                 @endif
                                 {{-- @if (!empty($data['returnFlight']) || !empty($data['segments'][1]))
                                    <div class="sugge-tab sugge-tab-tickes mt-2">
                                       <div class="flex1">
                                          <div class="emri">
                                             <img src="/assets/images/{{ $data['logo'] ?? '' }}" alt="Fly Jinnah logo">
                                          </div>   
                                          <div class="der-time der-time-setps">
                                             <ul>
                                                <li><h2>{{$data['returnFlight']['departureTime']}}</h2></li>
                                                <li><div class="stays"><p>{{$data['returnFlight']['timeDifference']}}</p></div></li>
                                                <li><div class="tims"><h2>{{$data['returnFlight']['arrivalTime']}}</h2></div></li>
                                             </ul>
                                             <div class="citys">
                                                <div class="cit">
                                                   <ul>
                                                      <li><p>{{$data['returnFlight']['originCode']}}</p></li>
                                                      <li><p>-</p></li>
                                                      <li><p>{{ $data['returnFlight']['isConnected'] ? '1 Stop' : 'Nonstop' }}</p></li>
                                                      <li><p>-</p></li>
                                                      <li><p>{{$data['returnFlight']['destinationCode']}}</p></li>
                                                   </ul>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 @endif --}}
                                 {{-- <div class="tickets-download">
                                    <a class="btn btn-b" href="#" role="button">Download E-Ticket</a>
                                 </div> --}}
                              </div>
                              <div class="steps">
                                 <h4>Traveler(s)</h4>
                                 <div class="contactDetails"></div>
                              </div>
                           </div>
                           <div class="col-md-12 col-lg-4">
                              <div class="bokkings-bar bokkings-bar5 mb-3">
                                 <div class="book-head  book-head2 ">
                                    <div class="youbook">
                                       <h2>Price Summary</h2>
                                    </div>
                                    <div class="depar-head">
                                       <ul>
                                          <li>
                                             <p>Departing</p>
                                          </li>
                                          <li>
                                             @if (isset($data) && !empty($data))
                                                @if ($isEmirate)
                                                   <p><i class="fa-regular fa-calendar"></i> {{\Carbon\Carbon::parse($data['flightDetails']['segments'][0]['flights']['Departure']['Date']['value'])->format('d M Y') ?? ''}}</p>
                                                @elseif ($isFlyJinnah)
                                                   <p><i class="fa-regular fa-calendar"></i> {{$data['departureFlight']['departureDate']}}</p>
                                                @endif
                                             @endif
                                          </li>
                                       </ul>
                                    </div>
                                 </div>
                                 <div class="paxWithPrice"></div>
                                 <div class="pri-eid">
                                    <p><span>Tax</span></p>
                                    <p><span class="taxPaid"></span></p>
                                 </div> 
                                 <div class="pri-eid">
                                    <p><span>Price You Pay</span></p>
                                    <p><span class="totalPricePaid"></span></p>
                                 </div> 
                                 {{-- <div class="pri-eid2">
                                    <h3>Payment Method</h3>
                                    <p>Bank Transfer via Mobile App - Silk Bank (SLK)</p>
                                 </div> 
                                 <div class="order-rep">
                                    <a class="btn btn-b" href="#" role="button">Order Receipt </a>
                                 </div> --}}
                              </div>
                              <div class="bokkings-bar bokkings-bar5 emiTimeLimitContainer d-none mb-3">
                                 <div class="book-head  book-head2 ">
                                    <div class="youbook">
                                       <h2>Time Limits</h2>
                                    </div>
                                 </div>
                                 <div class="timeLimitsEmi"></div>
                              </div>
                              <!-- Tax Section -->
                              <div class="bokkings-bar bokkings-bar5 emiTaxContainer d-none mb-3">
                                 <div class="book-head book-head2">
                                    <div class="youbook">
                                       <h2>Tax details</h2>
                                    </div>
                                    <div class="youbook">
                                       <p class="text-info font-weight-bolder toggle-tax-details pointer">Show more</p>
                                    </div>
                                 </div>
                                 <div class="taxDetailsEmi" style="display: none;"></div>
                              </div>
                              <!-- Service Section -->
                              <div class="bokkings-bar bokkings-bar5 emiServiceContainer d-none mb-3">
                                 <div class="book-head book-head2">
                                    <div class="youbook">
                                       <h2>Service details</h2>
                                    </div>
                                    <div class="youbook">
                                       <p class="text-info font-weight-bolder toggle-service-details pointer">Show more</p>
                                    </div>
                                 </div>
                              <div class="serviceDetailsEmi" style="display: none;"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="fligth-btn">
                        <a class="btn btn-c" href="{{route('home')}}" role="button">Back to Flight</a> 
                     </div>
                  </fieldset>
               </form>
            </div>
         </div>
      </div>      
   </div>
</section>
 <x-SessionTimeoutContainer/>
   {{-- <div class="footerTimeOutContainer">
      <div class="text-center idExpIn">
         <h3></h3>
      </div>
   </div> --}}
 {{-- <button class="btn btn-dark seses">GOTOOO</button> --}}
@endsection
@section('script')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- year and date month -->
{{-- <script>
   //  one year and date month
   var Days = [31,28,31,30,31,30,31,31,30,31,30,31];// index => month [0-11]
   $(document).ready(function(){
      var option = '<option value="day">Day</option>';
      var selectedDay="day";
      for (var i=1;i <= Days[0];i++){ //add option days
         option += '<option value="'+ i + '">' + i + '</option>';
      }
      $('#day').append(option);
      $('#day').val(selectedDay);

      var option = '<option value="month">Month</option>';
      var selectedMon ="month";
      for (var i=1;i <= 12;i++){
         option += '<option value="'+ i + '">' + i + '</option>';
      }
      $('#month').append(option);
      $('#month').val(selectedMon);

      var option = '<option value="month">Month</option>';
      var selectedMon ="month";
      for (var i=1;i <= 12;i++){
         option += '<option value="'+ i + '">' + i + '</option>';
      }
      $('#month2').append(option);
      $('#month2').val(selectedMon);
   
      var d = new Date();
      var option = '<option value="year">Year</option>';
      selectedYear ="year";
      for (var i=1930;i <= (d.getFullYear() - 21);i++){// years start i
         option += '<option value="'+ i + '">' + i + '</option>';
      }
      $('#year').append(option);
      $('#year').val(selectedYear);
   });
   function isLeapYear(year) {
      year = parseInt(year);
      if (year % 4 != 0) {
            return false;
      } else if (year % 400 == 0) {
            return true;
      } else if (year % 100 == 0) {
            return false;
      } else {
            return true;
      }
   }
   function change_year(select)
      {
      if( isLeapYear( $(select).val() ) )
      {
            Days[1] = 29;
            
      }
      else {
         Days[1] = 28;
      }
      if( $("#month").val() == 2)
            {
                  var day = $('#day');
                  var val = $(day).val();
                  $(day).empty();
                  var option = '<option value="day">Day</option>';
                  for (var i=1;i <= Days[1];i++){ //add option days
                        option += '<option value="'+ i + '">' + i + '</option>';
               }
                  $(day).append(option);
                  if( val > Days[ month ] )
                  {
                        val = 1;
                  }
                  $(day).val(val);
            }
   }

   function change_month(select) {
      var day = $('#day');
      var val = $(day).val();
      $(day).empty();
      var option = '<option value="day">Day</option>';
      var month = parseInt( $(select).val() ) - 1;
      for (var i=1;i <= Days[ month ];i++){ //add option days
         option += '<option value="'+ i + '">' + i + '</option>';
      }
      $(day).append(option);
      if( val > Days[ month ] )
      {
         val = 1;
      }
      $(day).val(val);
   }
   //  two year and date month
   var Days = [31,28,31,30,31,30,31,31,30,31,30,31];// index => month [0-11]
   $(document).ready(function(){
      function populateDropdown(dayId, monthId, yearId) {
         var option = '<option value="day">Day</option>';
         var selectedDay = "day";
         for (var i = 1; i <= Days[0]; i++) { // add option days
               option += '<option value="' + i + '">' + i + '</option>';
         }
         $('#' + dayId).append(option).val(selectedDay);

         option = '<option value="month">Month</option>';
         var selectedMon = "month";
         for (var i = 1; i <= 12; i++) {
               option += '<option value="' + i + '">' + i + '</option>';
         }
         $('#' + monthId).append(option).val(selectedMon);

         var d = new Date();
         option = '<option value="year">Year</option>';
         var selectedYear = "year";
         for (var i = 1930; i <= (d.getFullYear() - 21); i++) { // years start i
               option += '<option value="' + i + '">' + i + '</option>';
         }
         $('#' + yearId).append(option).val(selectedYear);
      }

      populateDropdown('day', 'month', 'year');
      populateDropdown('day2', 'month2', 'year2');
   });

   function isLeapYear(year) {
      year = parseInt(year);
      if (year % 4 != 0) {
         return false;
      } else if (year % 400 == 0) {
         return true;
      } else if (year % 100 == 0) {
         return false;
      } else {
         return true;
      }
   }

   function change_year(select, dayId, monthId) {
      if (isLeapYear($(select).val())) {
         Days[1] = 29;
      } else {
         Days[1] = 28;
      }
      if ($("#" + monthId).val() == 2) {
         var day = $('#' + dayId);
         var val = $(day).val();
         $(day).empty();
         var option = '<option value="day">Day</option>';
         for (var i = 1; i <= Days[1]; i++) { // add option days
               option += '<option value="' + i + '">' + i + '</option>';
         }
         $(day).append(option);
         if (val > Days[monthId]) {
               val = 1;
         }
         $(day).val(val);
      }
   }

   function change_month(select, dayId) {
      var day = $('#' + dayId);
      var val = $(day).val();
      $(day).empty();
      var option = '<option value="day">Day</option>';
      var month = parseInt($(select).val()) - 1;
      for (var i = 1; i <= Days[month]; i++) { // add option days
         option += '<option value="' + i + '">' + i + '</option>';
      }
      $(day).append(option);
      if (val > Days[month]) {
         val = 1;
      }
      $(day).val(val);
   }

</script> --}}
<!-- step-form -->

@if (isset($data) && !empty($data))
   @if ($isEmirate)
      <script>
         $(document).ready(function () {
            let data = @json($data);
            let firstBtn = true;
            let current = 1, steps = $("fieldset").length;
            function setProgressBar(step) {
               $(".progress-bar").css("width", (100 / steps * step) + "%");
            }
            function validateFields(fieldset) {
               let isValid = true;
               fieldset.find("input[required], select[required]").each(function () {
                  if (!$(this).val()) {
                     isValid = false;
                     $(this).addClass("border-danger");
                  } else {
                     $(this).removeClass("border-danger");
                  }
               });
               return isValid;
            }
            $(".next").click(function () {
               let current_fs = $(this).parent();
               let next_fs = current_fs.next();

               if (!validateFields(current_fs)) return;

               if (firstBtn) {
                  confirmationModal('Please confirm that all the provided details are correct.').then((result) => {
                     if (result.isConfirmed) {
                        firstBtn = false;
                        let index = $("fieldset").index(next_fs);
                        $("#progressbar li").eq(index).addClass("active");
                        next_fs.show();
                        current_fs.animate({ opacity: 0 }, {
                           step: (now) => {
                              current_fs.css({ 'display': 'none', 'position': 'relative' });
                              next_fs.css({ 'opacity': 1 - now });
                           },
                           duration: 500
                        });
                        setProgressBar(++current);
                     } else {
                        _alert('Confirmation cancelled.', 'warning');
                     }
                  });
               } else {
                  let index = $("fieldset").index(next_fs);
                  $("#progressbar li").eq(index).addClass("active");
                  next_fs.show();
                  current_fs.animate({ opacity: 0 }, {
                     step: (now) => {
                        current_fs.css({ 'display': 'none', 'position': 'relative' });
                        next_fs.css({ 'opacity': 1 - now });
                     },
                     duration: 500
                  });
                  setProgressBar(++current);
               }
            });
            $(".submit").click(() => false);
            setProgressBar(current);
            $(document).on("input change", "input[required], select[required]", function () {
               if ($(this).val()) {
                  $(this).removeClass("border-danger");
               }
            });

            // ------------------------------------ Booking Start ------------------------------------ //
            let passengers = [];
            let paymentdata;
            function paxCapitalize(pax) {
               const name = {
                  adt: 'Adult',
                  chd: 'Child',
                  inf: 'Infant'
               };
               return name[pax] || name[pax.toLowerCase()];
            }

            let paymentOnHold = true;
            $('#paymentOnHold').on('change', function () {
            if ($(this).is(':checked')) {
                  $('.paymentOnHoldText').text('Payment is ON HOLD');
                  paymentOnHold = true;
               } else {
                  $('.paymentOnHoldText').text('Payment is DIRECT');
                  paymentOnHold = false;
               }
            });
            let lastSubmittedData = null;
            let isSubmitting = false;

            $('#contactSubmit').click(function () {
               if (isSubmitting) return;

               passengers = [];
               let hasError = false;
               let firstErrorField = null;

               $('.paxDetails .contact2').each(function () {
                  let passenger = {
                     type: $(this).find('input[name$="_type[]"]').val(),
                     name: $(this).find('input[name$="_name[]"]').val(),
                     surname: $(this).find('input[name$="_surname[]"]').val(),
                     title: $(this).find('input[name^="title_"]:checked').val(),
                     dob: $(this).find('input[name$="_dob[]"]').val(),
                     nationality: $(this).find('select[name$="_nationality[]"]').val(),
                     passportNumber: $(this).find('input[name$="_passportnumber[]"]').val(),
                     passportExpiry: $(this).find('input[name$="_passportexp[]"]').val()
                  };

                  $(this).find("input[required], select[required]").each(function () {
                     if (!$(this).val()) {
                        $(this).addClass("border-danger");
                        hasError = true;
                        if (!firstErrorField) firstErrorField = this;
                     } else {
                        $(this).removeClass("border-danger");
                     }
                  });

                  passengers.push(passenger);
               });

               let userData = {
                  fullName: $('#userFullName').val(),
                  email: $('#userEmail').val(),
                  phoneCode: $('#userPhoneCode').val(),
                  phone: $('#userPhone').val()
               };

               if (!userData.fullName || !userData.email || !userData.phoneCode || !userData.phone) {
                  hasError = true;
                  if (!firstErrorField) {
                     if (!userData.fullName) firstErrorField = $('#userFullName');
                     else if (!userData.email) firstErrorField = $('#userEmail');
                     else if (!userData.phoneCode) firstErrorField = $('#userPhoneCode');
                     else if (!userData.phone) firstErrorField = $('#userPhone');
                  }
               }

               if (hasError) {
                  if (firstErrorField) $(firstErrorField).focus();
                  _alert('Please fill all required fields', 'warning');
                  isSubmitting = false;
                  return false;
               }

               isSubmitting = true;
               let currentData = JSON.stringify({ passengers, userData });

               if (currentData === lastSubmittedData) {
                  // _alert('No changes detected. Data already submitted.', 'info');
                  // isSubmitting = false;

                  // if (!isDirectBooking) {
                  //    getFinalPrice();
                  // } else {
                  //    isSubmitting = false;
                  // }
                  return;
               }

               lastSubmittedData = currentData;
               // if (!isDirectBooking) {
               //    getFinalPrice();
               // } else {
               //    isSubmitting = false;
               // }
            });

            // ------------------------------------ Booking End ------------------------------------ //
            // ------------------------------------ Payment Start ------------------------------------ //

            $(".toggle-tax-details").on("click", function () {
               const $toggleBtn = $(this);
               const $details = $toggleBtn.closest(".emiTaxContainer").find(".taxDetailsEmi");

               $details.slideToggle(200, function () {
                  const isVisible = $details.is(":visible");
                  $toggleBtn.text(isVisible ? "Show less" : "Show more");
               });
            });
            $(document).on("click", ".toggle-panelties-details", function () {
               const $toggleBtn = $(this);
               const $details = $toggleBtn.closest(".penaltiesContainer").find(".panelties-details");

               $details.slideToggle(200, function () {
                     const isVisible = $details.is(":visible");
                     $toggleBtn.text(isVisible ? "Show less" : "Show more");
               });
            });
            $(".toggle-service-details").on("click", function () {
               const $toggleBtn = $(this);
               const $details = $toggleBtn.closest(".emiServiceContainer").find(".serviceDetailsEmi");

               $details.slideToggle(200, function () {
                  const isVisible = $details.is(":visible");
                  $toggleBtn.text(isVisible ? "Show less" : "Show more");
               });
            });
            const paymentAjax = () => {
               let isProcessing = false;
               $.ajax({
                  type: "POST",
                  url: "{{route('payment')}}",
                  data: {
                     paymentdata: paymentdata || {},
                     _token: "{{ csrf_token() }}"
                  },
                  beforeSend: () => _loader('show'),
                  success: function (response) {
                     // console.log(response)
                     bookingAjax();
                     // $('#paymentSend').click();
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message, "error");
                  },
                  complete: function () {
                     _loader('hide');
                  }
               });
            }
            const renderTrevelerDetails = data => {
               if (!Array.isArray(data) || data.length === 0) {
                  return `<div class="alert alert-danger" role="alert">Data is missing :)</div>`;
               }
               return data.map((row, index) => {
                  // const flights = (row.eTicketInfo || []).map(fli => `
                  //       <div class="col-6">
                  //          <div class="border rounded p-3 mt-2">
                  //             <p>Route: <span>${fli.flightSegmentCode}</span></p>
                  //             <p>ETicket No: <span class="copyText">${fli.eTicketNo}</span> &nbsp; <i class="copyBtn fa fa-copy text-black-50" style="cursor:pointer;"></i></p>
                  //             <p>Coupon No: <span>${fli.couponNo}</span></p>
                  //             <p>Status: <span>${fli.usedStatus}</span></p>
                  //          </div>
                  //       </div>
                  // `).join('');

                  return `
                        <div class="custom-method setp-bult traveler-bult row">
                           <div class="col-md-6 col-12">
                              <h1 class="font-weight-bold mb-3">Passenger Details</h1>
                              <p>Traveler ${index + 1}: <span>${row.type}</span></p>
                              <p><span>Name</span>: ${row.name}</p>
                              <p><span>Surname</span>: ${row.surname}</p>
                           </div>
                        </div>`;}).join('');
               // <div class="col-md-6 col-12">
               //    <h1 class="font-weight-bold mb-3">${flights ?? 'Ticket Details'}</h1>
               // </div>
               // <div class="row">
               //    ${flights}
               // </div>
            };
            const renderPaxWithPrice = data => {
               if (data.length === 0) return ``;
               return data.map((row) => `
                  <div class="pri-eid">
                     <p>Emirates Airline - (${row.fareDetail.passengers})</p>
                     <p>Price: ${row.totalPrice.code} ${formatCurrency(row.totalPrice.amount)}</p>
                  </div>
               `).join('');
            }
            const renderTimeLimitsEmi = data => {
               if (data.length === 0) return ``;
               return data.map(row => {
                  const passengers = row.fareDetail.passengers;
                  const payTimeLimit = formatDateTime(row.timeLimits.paymentTimeLimit);
                  const ticketTimeLimit = formatDateTime(row.timeLimits.ticketingTimeLimit);
                  return `
                     <div class="pri-eid font-weight-bold">
                        <h1 class="text-info">Passenger Info: ${passengers}</h1>
                     </div>
                     <div class="pri-eid font-weight-bold">
                        <p>Payment Time Limit</p>
                        <p class="font-bold">${payTimeLimit}</p>
                     </div>
                     <div class="pri-eid font-weight-bold">
                        <p>Ticket Time Limit</p>
                        <p class="font-bold">${ticketTimeLimit}</p>
                     </div>
                  `;
               }).join('');
            }
            const renderTaxDetailsEmi = data => {
               if (data.length === 0) return ``;
               return data.map((row) => {
                  const baseAmount = formatCurrency(row.fareDetail.taxes.baseAmount.amount);
                  const baseAmountCode = row.fareDetail.taxes.baseAmount.code;
                  const taxArray = row.fareDetail.taxes.tax;
                  const totalPrice = formatCurrency(row.totalPrice.amount);
                  const totalPriceCode = row.totalPrice.code;
                  const passengers = row.fareDetail.passengers;
                  const taxDetails = taxArray.map(tax => {
                     if (tax.description && tax.description.length > 0) {
                        return `
                           <div class="pri-eid">
                              <p>${tax.description}</p>
                              <p>Price: ${tax.price.code} ${formatCurrency(tax.price.amount)}</p>
                           </div>
                        `;
                     }
                     return '';
                  }).join('');
                  return `
                     <div class="pri-eid font-weight-bold">
                        <h1 class="text-info">Passenger Info: ${passengers}</h1>
                     </div>
                     <div class="pri-eid font-weight-bold">
                        <p>Base Amount</p>
                        <p class="font-bold">Price: ${baseAmountCode} ${baseAmount}</p>
                     </div>
                     ${taxDetails}
                     <div class="pri-eid font-weight-bold">
                        <p>Final Amount ${passengers}</p>
                        <p class="font-bold">Price: ${totalPriceCode} ${totalPrice}</p>
                     </div>
                  `;
               }).join('');
            }
            const renderServiceDetailsEmi = data => {
               if (!data || data.length === 0) return ``;

               return data.map(row => {
                  const passengers = row.fareDetail.passengers;
                  const serviceArray = row.services || [];
                  const serviceDetails = serviceArray.map(service => {
                        if (service.details && service.details.Type && service.details.details?.length > 0) {
                           return `
                              <div class="pri-eid">
                                 <p class="font-weight-bold">${service.details.details}</p>
                                 <p>${service.details.Type}</p>
                              </div>`;
                        }
                        return '';
                  }).join('');
                  if (!serviceDetails.trim()) return '';

                  return `
                        <div class="pri-eid font-weight-bold">
                           <h5 class="text-info">Passenger Info: ${passengers}</h5>
                        </div>
                        ${serviceDetails}
                  `;
               }).join('');
            };

            const bookingAjax = () => {
               let user = {
                  userFullName: $('#userFullName').val(),
                  userEmail: $('#userEmail').val(),
                  userPhoneCode: $('#userPhoneCode').val(),
                  userPhone: $('#userPhone').val(),
                  acceptOffers: $('#acceptOffers').is(':checked'),
               }
               $.ajax({
                  type: "POST",
                  url: "{{route('bookFlight')}}",
                  data: {
                     user, paymentOnHold, passengers,
                     airline: data['airline'],
                     offerIds: getOfferIds(data['flightDetails']['bundle']['offerItem']) ?? null,
                     bundleId: data['flightDetails']['bundle']['offerID'] ?? null,
                     responseId: data['flightDetails']['segments'][0]['responseId'] ?? null,
                     paxCount: data['paxCount'] ?? null,
                     passengerTypes: data['passengerTypes'] ?? null,
                     _token: "{{ csrf_token() }}"
                  },
                  beforeSend: () => _loader('show'),
                  success: function (response) {
                     sessionTimer(false);
                     let tax = @json($tax);
                     let totalPrice = parseInt(response.totalPrice?.amount) + parseInt(tax);
                     _alert(response.message, response.status)
                     $('#paymentSend').click();
                     $(".guestName").text(response.userDetails.name);
                     $(".taxPaid").text(`Price: PKR ${formatCurrency(tax)}`);
                     $(".ticketMsg").text(response.ticketMsg);
                     $(".totalPricePaid").text(`Price: ${response.totalPrice?.code ?? 'PKR'} ${formatCurrency(totalPrice) ?? 0}`);
                     $(".contactDetails").html(renderTrevelerDetails(response.passengers));
                     $(".paxWithPrice").html(renderPaxWithPrice(response.data.bundle.offerItem));
                     $(".emiTimeLimitContainer").removeClass('d-none');
                     $(".timeLimitsEmi").html(renderTimeLimitsEmi(response.data.bundle.offerItem));
                     $(".emiTaxContainer").removeClass('d-none');
                     $(".emiServiceContainer").removeClass('d-none');
                     $(".taxDetailsEmi").html(renderTaxDetailsEmi(response.data.bundle.offerItem));
                     $(".serviceDetailsEmi").html(renderServiceDetailsEmi(response.data.bundle.offerItem));
                     $(".orderId").html(response.bookingRefID);
                     console.log(response.emailStatus); // Show this in alert after set live email sending
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message || 'bookingAjax Error', "error");
                  },
                  complete: function () {
                     _loader('hide');
                  }
               });
            }
            $('#paymentSendTest').click(function () {
               paymentAjax();
               paymentdata = 'test';
            });

            // ------------------------------------ Payment End ------------------------------------ //

            // -------------------------------- Combine Functions :) -------------------------------- //

            const getSegmentAttributes = flightNo => {
               let segmenArry = data['segments'][0] ? data['segments'] : [data['segments']];
               return segmenArry.find(s => s.flightNumber === flightNo);
            }
            const getOfferIds = data =>
               (Array.isArray(data) ? data : data ? [data] : []).map(item => ({
                     id: item?.id || null,
                     PassengerRef: item?.services?.[0]?.passengerRefs || null
               }));
         });
      </script>
   {{-- New changes in FJ --}}
   {{-- Comment session time func bcz i make compo for that :) --}}
   {{-- add airline in rq ob bookingAjax :) --}}
   @elseif ($isFlyJinnah)
      <script>
         $(document).ready(function () {
            let data = @json($data);
            let totalFare = @json($totalFare);
            let isDirectBooking = @json($data['isDirectBooking']) ? true : false;
            let firstBtn = true;
            let current = 1, steps = $("fieldset").length;
            function setProgressBar(step) {
               $(".progress-bar").css("width", (100 / steps * step) + "%");
            }
            function validateFields(fieldset) {
               let isValid = true;
               fieldset.find("input[required], select[required]").each(function () {
                  if (!$(this).val()) {
                     isValid = false;
                     $(this).addClass("border-danger");
                  } else {
                     $(this).removeClass("border-danger");
                  }
               });
               return isValid;
            }
            $(".next").click(function () {
               let current_fs = $(this).parent();
               let next_fs = current_fs.next();

               if (!validateFields(current_fs)) return;
               if (!isDirectBooking && !checkValidationForAncis()) return;

               if (firstBtn) {
                  confirmationModal('Please confirm that all the provided details are correct.').then((result) => {
                     if (result.isConfirmed) {
                        firstBtn = false;
                        if (!isDirectBooking) {
                           getFinalPrice();
                        }
                        let index = $("fieldset").index(next_fs);
                        $("#progressbar li").eq(index).addClass("active");
                        next_fs.show();
                        current_fs.animate({ opacity: 0 }, {
                           step: (now) => {
                              current_fs.css({ 'display': 'none', 'position': 'relative' });
                              next_fs.css({ 'opacity': 1 - now });
                           },
                           duration: 500
                        });
                        setProgressBar(++current);
                     } else {
                        _alert('Confirmation cancelled.', 'warning');
                     }
                  });
               } else {
                  let index = $("fieldset").index(next_fs);
                  $("#progressbar li").eq(index).addClass("active");
                  next_fs.show();
                  current_fs.animate({ opacity: 0 }, {
                     step: (now) => {
                        current_fs.css({ 'display': 'none', 'position': 'relative' });
                        next_fs.css({ 'opacity': 1 - now });
                     },
                     duration: 500
                  });
                  setProgressBar(++current);
               }
            });
            // $(".previous").click(function () {
            //    let current_fs = $(this).parent();
            //    let previous_fs = current_fs.prev();

            //    let index = $("fieldset").index(current_fs);
            //    $("#progressbar li").eq(index).removeClass("active");

            //    previous_fs.show();
            //    current_fs.animate({ opacity: 0 }, {
            //       step: (now) => {
            //          current_fs.css({ 'display': 'none', 'position': 'relative' });
            //          previous_fs.css({ 'opacity': 1 - now });
            //       },
            //       duration: 500
            //    });

            //    setProgressBar(--current);
            // });
            $(".submit").click(() => false);
            setProgressBar(current);
            $(document).on("input change", "input[required], select[required]", function () {
               if ($(this).val()) {
                  $(this).removeClass("border-danger");
               }
            });

            // ------------------------------------ Booking Start ------------------------------------ //
            // let skipAncis;
            // let countdown;
            // const sessionTimer = (action = true) => {
            //    if (!action) {
            //       clearInterval(countdown);
            //       $(".idExpIn").text('');
            //       return;
            //    };
            //    let expirationTime;
            //    let sessionTime = @json(session('IdsExpireTime')) || 0;
            //    let sessionTimestamp = new Date(sessionTime).getTime();
            //    let expMinutes = 10; // change this into 10 Aliiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii
            //    if (!sessionTime) {
            //       expirationTime = 0;
            //       $(".idExpIn h3").text("Invalid session time");
            //       // return;
            //    } else {
            //       expirationTime = sessionTimestamp + expMinutes * 60 * 1000;
            //    }
            //    function updateTimer() {
            //       let currentTime = new Date().getTime();
            //       let timeLeft = expirationTime - currentTime;

            //       if (timeLeft <= 0) {
            //          skipAncis = true;
            //          $(".idExpIn h3").text("Session Expired");
            //          Swal.fire({
            //             title: 'Session Expired',
            //             text: 'Your session has expired. Please go back and refresh.',
            //             icon: 'warning',
            //             confirmButtonText: 'Go Back',
            //             allowOutsideClick: false,
            //             allowEscapeKey: false,
            //             preConfirm: () => {
            //                let goBack = localStorage.getItem('flights') || null;
            //                goBack ? window.location.href = `/flights${goBack}` : window.history.back();
            //             }
            //          });

            //          clearInterval(countdown);
            //          return;
            //       }

            //       let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            //       let seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            //       let formattedMinutes = minutes < 10 ? `0${minutes} Minutes` : `${minutes} Minutes`;
            //       let formattedSeconds = seconds < 10 ? `0${seconds} Seconds` : `${seconds} Seconds`;

            //       $(".idExpIn h3").html(`Please finish your booking in : <span class="font-weight-bolder">${formattedMinutes} : ${formattedSeconds}</span>`);
            //    }

            //    if (new Date().getTime() >= expirationTime) {
            //       skipAncis = true;
            //       $(".idExpIn h3").text("Session Expired");
            //       Swal.fire({
            //          title: 'Session Expired',
            //          text: 'Your session has expired. Please go back and refresh.',
            //          icon: 'warning',
            //          confirmButtonText: 'Go Back',
            //          allowOutsideClick: false,
            //          allowEscapeKey: false,
            //          preConfirm: () => {
            //             let goBack = localStorage.getItem('flights') || null;
            //             goBack ? window.location.href = `/flights${goBack}` : window.history.back();
            //          }
            //       });
            //       return;
            //    } else {
            //       updateTimer();
            //       countdown = setInterval(updateTimer, 1000);
            //    }
            // }
            // sessionTimer(true);
            if(!isDirectBooking && !skipAncis) {
               getSeatAjax();
               getMealAjax();
               getBaggageAjax();
            }
            // IdsExpireTime end
            let passengers = [];
            let paymentdata;
            let passengerList = [];
            let passengerListCode = [];
            delete data['passengerTypes']['inf'];
            delete data['paxCount']['inf'];
            let totalPassengers = Object.values(data['paxCount']).reduce((a, b) => a + parseInt(b), 0);
            let passengerTypeMap = {
               adt: 'A',
               chd: 'C',
            };
            let runningIndex = 1;
            $.each(data['paxCount'], function (key, count) {
               // if (key === 'inf') return;
               let passengerTypeInitial = passengerTypeMap[key] || key.charAt(0).toUpperCase();
               for (let i = 0; i < parseInt(count); i++) {
                  passengerListCode.push(`${passengerTypeInitial}${runningIndex}`);
                  runningIndex++;
               }
            });
            // $.each(data['paxCount'], function (key, count) {
               //   for (let i = 1; i <= parseInt(count); i++) {
               //      passengerListCode.push(`${data['passengerTypes'][key][0]}${i}`);
               //   }
            // });
            $.each(data['paxCount'], function (key, count) {
               // if (key === 'inf') return;
               for (let i = 1; i <= parseInt(count); i++) {
                  passengerList.push(`${data['passengerTypes'][key]} - ${i}`);
               }
            });
            // console.log(passengerListCode, passengerList, data['passengerTypes'], totalPassengers)
            function paxCapitalize(pax) {
               const name = {
                  adt: 'Adult',
                  chd: 'Child',
                  inf: 'Infant'
               };
               return name[pax] || name[pax.toLowerCase()];
            }
            let selectedSeatsGlobal = {};
            let selectedMealsGlobal = {};
            let selectedBaggagesGlobal = {};
            let segmentRph = {};
            let segmentFlightNo = {};
            let segmentDepDate = {};

            // $('.seses').click(function () {
            //    sessionTimer(false);
            // });


            function getSeatAjax() {
               $.ajax({
                  type: "POST",
                  url: "{{route('get_seat')}}",
                  data: {
                     data: data['segments'], 
                     _token: "{{ csrf_token() }}"
                  },
                  beforeSend: () => _loader('show'),
                  success: function (response) {
                     if (response.status === 'success') {
                        $(".seatFlightSegments, .segmentNavTabSeat, .segmentBtnsSeat").empty();
                        $(".infantCountSeatBtn").empty();

                        let paxSeatBtns = `
                           <div class="border-bottom border-dark mt-2 p-2">
                              <div class="overflow-auto infantCountSeatBtn">${passengerList.map(pax => `<a class="btn btn-outline-info mx-2 pax-btn" data-pax="${pax}">${pax} <span class="seatNo"></span></a>`).join('')}</div>
                              <hr class="bg-dark">
                              <div class="d-flex justify-content-between">
                                 <h2 class="font-weight-bold">Total fare:</h2>
                                 <p class="totalPriceOfSeat">PKR 0</p>
                              </div>
                           </div>`;

                        let navTabs = `<ul class="nav nav-tabs segmentNavTabSeat">`;
                        let tabContent = `<div class="tab-content segmentBtnsSeat planeParent mt-3">${paxSeatBtns}`;

                        $.each(response.data, function (index, item) {
                           let rph = getSegmentAttributes(item.FlightSegmentInfo["@attributes"].FlightNumber).rph || item.FlightSegmentInfo["@attributes"].RPH;
                           segmentRph[`flight-${index}`] = rph;
                           segmentFlightNo[`flight-${index}`] = item.FlightSegmentInfo["@attributes"].FlightNumber;
                           segmentDepDate[`flight-${index}`] = item.FlightSegmentInfo["@attributes"].DepartureDateTime;
                           let departureCity = item['FlightSegmentInfo']['DepartureAirport']['City'] || item['FlightSegmentInfo']['DepartureAirport']['@attributes']['LocationCode'] || '--';
                           let arrivalCity = item['FlightSegmentInfo']['ArrivalAirport']['City'] || item['FlightSegmentInfo']['ArrivalAirport']['@attributes']['LocationCode'] || '--';

                           let tabId = `flight-${index}`;
                           selectedSeatsGlobal[tabId] = selectedSeatsGlobal[tabId] || [];

                           navTabs += `<li class="nav-item">
                              <a class="nav-link ${index === 0 ? 'active' : ''}" data-toggle="tab" href="#${tabId}">
                                 ${departureCity}-${arrivalCity}
                              </a>
                           </li>`;

                           tabContent += `<div id="${tabId}" class="tab-pane tabPlane fade ${index === 0 ? 'show active' : ''}">
                              <div class="winds1"><div class="winds2"><div class="windows"><div class="windows2"><div class="seatMapContainer d-flex"></div></div></div></div></div>
                           </div>`;
                        });

                        navTabs += `</ul>`;
                        tabContent += `</div>`;

                        $(".seatFlightSegments").append(navTabs);
                        $(".plane").append(tabContent);

                        loadSeatMap("flight-0", response.data[0].SeatMapDetails.CabinClass, totalPassengers, passengerList);

                        $('.segmentNavTabSeat a').click(function (e) {
                           e.preventDefault();
                           $(this).tab('show');

                           let tabId = $(this).attr("href").replace("#", "");
                           let index = tabId.replace("flight-", "");
                           let tabContainer = $("#" + tabId).find(".seatMapContainer");

                           if (!tabContainer.hasClass("loaded")) {
                              tabContainer.addClass("loaded");
                              loadSeatMap(tabId, response.data[index].SeatMapDetails.CabinClass, totalPassengers, passengerList);
                           }

                           updatePassengerSeats(tabId);
                        });
                     }
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message, "error");
                  },
                  complete: function () {
                     _loader('hide');
                  }
               });
            };
            function loadSeatMap(flightId, seatData, totalPassengers, passengerList) {
               let container = $("#" + flightId + " .seatMapContainer");
               container.empty();
               let selectedSeats = selectedSeatsGlobal[flightId] || [];

               if (Array.isArray(seatData)) {
                  seatData.forEach(cabin => renderSeatMap(cabin.AirRows.AirRow, container, totalPassengers, passengerList, flightId));
               } else {
                  renderSeatMap(seatData.AirRows.AirRow, container, totalPassengers, passengerList, flightId);
               }
            }
            function updatePassengerSeats(flightId) {
               $(".infantCountSeatBtn .seatNo").text(""); // Reset previous seats

               let selectedSeats = selectedSeatsGlobal[flightId] || [];
               selectedSeats.forEach((seat, index) => {
                  $(".infantCountSeatBtn .seatNo").eq(index).text(`(${seat.seatId})`);
               });

               let totalPrice = selectedSeats.reduce((total, seat) => total + parseFloat(seat.price), 0);
               $(".totalPriceOfSeat").text("PKR " + totalPrice);
            }
            function renderSeatMap(airRows, container, totalPassengers, passengerList, flightId) {
               let selectedSeats = selectedSeatsGlobal[flightId] || [];
               let rph = segmentRph[flightId];
               let flightNo = segmentFlightNo[flightId];
               let depDate = segmentDepDate[flightId];

               airRows.forEach(row => {
                  let rowNumber = row['@attributes']['RowNumber'];
                  let seats = row['AirSeats']['AirSeat'];

                  let seatRow = $("<div>").addClass("seat-row");

                  seats.forEach((seatObj, i) => {
                     if (i === 3) {
                        seatRow.append($("<div>").addClass("aisle"));
                     }

                     let attr = seatObj['@attributes'];
                     let seatId = rowNumber + attr['SeatNumber'];
                     let price = attr['SeatCharacteristics'];
                     let currency = attr['CurrencyCode'];
                     let availability = attr['SeatAvailability'];

                     let seat = $("<div>").addClass("seat").text(seatId).attr({
                        "data-seat": seatId,
                        "data-price": price,
                        "data-currency": currency,
                        "title": `${currency} ${price}`
                     });

                     if (availability !== 'VAC') {
                        seat.addClass("occupied");
                     }

                     // Restore previously selected seats
                     if (selectedSeats.some(s => s.seatId === seatId)) {
                        seat.addClass("selected");
                     }

                     seat.click(function () {
                        if ($(this).hasClass("occupied")) return;

                        let selectedForFlight = selectedSeatsGlobal[flightId] || [];

                        if ($(this).hasClass("selected")) {
                           $(this).removeClass("selected");
                           selectedSeats = selectedSeats.filter(s => s.seatId !== seatId);
                           selectedSeatsGlobal[flightId] = selectedSeats;
                        } else {
                           if (selectedForFlight.length >= totalPassengers) {
                              return;
                           }

                           let passenger = passengerListCode[selectedForFlight.length];
                           $(this).addClass("selected");
                           selectedSeats.push({ seatId, price, currency, passenger, rph, flightNo, depDate });
                           selectedSeatsGlobal[flightId] = selectedSeats;
                        }

                        updatePassengerSeats(flightId);
                     });

                     seatRow.append(seat);
                  });

                  container.append(seatRow);
               });

               selectedSeatsGlobal[flightId] = selectedSeats;
            }

            function getMealAjax() {
               $.ajax({
                  type: "POST",
                  url: "{{route('get_meal')}}",
                  data: {
                     data: data['segments'], 
                     _token: "{{ csrf_token() }}"
                  },
                  success: function (response) {
                     if (response.status === 'success') {
                        $(".mealFlightSegments, .segmentNavTabMeal, .segmentBtnsMeal").empty();
                        $(".infantCountMealBtn").empty();
                        // Create passenger buttons
                        let paxMealBtns = `
                           <div class="border-bottom border-dark mt-2 p-2">
                                 <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Select Meals for Passengers:</h5>
                                    <button type="button" class="btn btn-sm btn-primary select-same-meal">Select Same Meal for All</button>
                                 </div>
                                 <div class="overflow-auto infantCountMealBtn">
                                    ${passengerList.map((pax, index) => `
                                       <a class="btn btn-outline-info mx-2 pax-btn" data-pax="${pax}" data-pax-index="${index}">
                                             ${pax} 
                                             <span class="mealNo"></span>
                                       </a>
                                    `).join('')}
                                 </div>
                                 <hr class="bg-dark">
                                 <div class="d-flex justify-content-between">
                                    <h2 class="font-weight-bold">Total fare:</h2>
                                    <p class="totalPriceOfMeal">PKR 0</p>
                                 </div>
                           </div>`;

                        let navTabs = `<ul class="nav nav-tabs segmentNavTabMeal">`;
                        let tabContent = `<div class="tab-content segmentBtnsMeal mt-3">${paxMealBtns}`;

                        $.each(response.data, function (index, item) {
                           let rph = getSegmentAttributes(item.FlightSegmentInfo["@attributes"].FlightNumber).rph || item.FlightSegmentInfo["@attributes"].RPH;
                           segmentRph[`mealFlight-${index}`] = rph;
                           segmentFlightNo[`mealFlight-${index}`] = item.FlightSegmentInfo["@attributes"].FlightNumber;
                           segmentDepDate[`mealFlight-${index}`] = item.FlightSegmentInfo["@attributes"].DepartureDateTime;

                           let departureCity = item.FlightSegmentInfo.DepartureAirport.City || item.FlightSegmentInfo.DepartureAirport["@attributes"].LocationCode || '--';
                           let arrivalCity = item.FlightSegmentInfo.ArrivalAirport.City || item.FlightSegmentInfo.ArrivalAirport["@attributes"].LocationCode || '--';

                           let tabId = `mealFlight-${index}`;
                           selectedMealsGlobal[tabId] = selectedMealsGlobal[tabId] || {};

                           navTabs += `
                                 <li class="nav-item">
                                    <a class="nav-link ${index === 0 ? 'active' : ''}" data-toggle="tab" href="#${tabId}">
                                       ${departureCity}-${arrivalCity}
                                    </a>
                                 </li>`;

                           tabContent += `
                                 <div id="${tabId}" class="tab-pane fade ${index === 0 ? 'show active' : ''}">
                                    <div class="mealMapContainer row my-2"></div>
                                 </div>`;
                        });
                        navTabs += `</ul>`;
                        tabContent += `</div>`;

                        $(".mealFlightSegments").append(navTabs);
                        $(".meals").append(tabContent);

                        // Initialize current passenger selection
                        currentSelectedPax = 0;
                        $(".pax-btn").eq(0).addClass("active");

                        // Load Meal Map for the first segment
                        loadMealMap("mealFlight-0", response.data[0].Meal, totalPassengers, passengerList);

                        // Tab Click Handling
                        $('.segmentNavTabMeal a').click(function (e) {
                           e.preventDefault();
                           $(this).tab('show');

                           let tabId = $(this).attr("href").replace("#", "");
                           let index = tabId.split("-")[1];

                           if (!Object.keys(selectedMealsGlobal[tabId]).length) {
                              loadMealMap(tabId, response.data[index].Meal, totalPassengers, passengerList);
                           }

                           updatePassengerMeals(tabId);
                        });

                        // Passenger button click handler
                        $(".pax-btn").click(function() {
                           $(".pax-btn").removeClass("active");
                           $(this).addClass("active");
                           currentSelectedPax = $(this).data("pax-index");
                        });

                        // Select same meal for all button
                        $(".select-same-meal").click(function() {
                           let activeTab = $(".segmentNavTabMeal .nav-link.active").attr("href").replace("#", "");
                           let selectedMealCard = $(`#${activeTab} .mealCard.selected`);
                           
                           if (selectedMealCard.length) {
                                 let mealName = selectedMealCard.data("meal");
                                 let mealPrice = selectedMealCard.data("price");
                                 let mealCode = selectedMealCard.data("meal-code");
                                 let rph = segmentRph[activeTab];
                                 let flightNo = segmentFlightNo[activeTab];
                                 let depDate = segmentDepDate[activeTab];
                                 
                                 $(`#${activeTab} .mealCard`).removeClass("selected");
                                 selectedMealsGlobal[activeTab] = {};
                                 
                                 passengerListCode.forEach((pax, index) => {
                                    selectedMealsGlobal[activeTab][index] = {
                                       mealName,
                                       mealPrice,
                                       mealCode,
                                       passenger: pax,
                                       rph: rph,
                                       flightNo: flightNo,
                                       depDate: depDate
                                    };
                                 });
                                 
                                 $(`#${activeTab} .mealCard[data-meal="${mealName}"]`).addClass("selected");
                                 updatePassengerMeals(activeTab);
                           } else {
                                 _alert("Please select a meal first", "warning");
                           }
                        });
                     }
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message, "error");
                  },
                  complete: function () {
                     // _loader('hide');
                  }
               });
            };
            function loadMealMap(flightId, mealData, totalPassengers, passengerList) {
               let container = $("#" + flightId + " .mealMapContainer");
               container.empty();

               let mealsArray = Array.isArray(mealData) ? mealData : [mealData];
               let selectedMeals = selectedMealsGlobal[flightId] || {};
               let html = ``;

               mealsArray.forEach(meal => {
                  let mealImg = '/assets/images/mealdefault.jpg';
                  let isSelected = Object.values(selectedMeals).some(m => m.mealName === meal.mealName) ? 'selected' : '';

                  html += `
                        <div class="col-sm-12  col-md-4 col-lg-3  p-2 border mealCard pointer ${isSelected}" data-meal="${meal.mealName}" data-price="${meal.currencyCode} ${meal.mealCharge}" data-meal-code="${meal.mealCode}">
                           <img src="${mealImg}" alt="" class="img-thumbnail">
                           <div class="meal-head d-flex justify-content-between">
                              <p class="font-weight-bolder">Meal Name</p>
                              <p>${meal.mealName}</p>
                           </div>
                           <div class="meal-head d-flex justify-content-between">
                              <p class="font-weight-bolder">Meal Charge</p>
                              <p>${meal.currencyCode} ${meal.mealCharge}</p>
                           </div>
                        </div>`;
               });

               container.append(html);

               // Meal selection event
               $(".mealCard").click(function () {
                  let mealName = $(this).data("meal");
                  let mealPrice = $(this).data("price");
                  let mealCode = $(this).data("meal-code");
                  let flightId = $(this).closest(".tab-pane").attr("id");
                  let selectedMeals = selectedMealsGlobal[flightId] || {};
                  let currentPax = currentSelectedPax;
                  let rph = segmentRph[flightId];
                  let flightNo = segmentFlightNo[flightId];
                  let depDate = segmentDepDate[flightId];

                  if ($(this).hasClass("selected") && selectedMeals[currentPax]?.mealName === mealName) {
                        $(this).removeClass("selected");
                        delete selectedMeals[currentPax];
                  } else {
                        $(this).addClass("selected");
                        selectedMeals[currentPax] = {
                           mealName,
                           mealPrice,
                           mealCode,
                           passenger: passengerListCode[currentPax],
                           rph: rph,
                           flightNo: flightNo,
                           depDate: depDate
                        };
                  }

                  selectedMealsGlobal[flightId] = selectedMeals;
                  updatePassengerMeals(flightId);
               });
            }
            function updatePassengerMeals(flightId) {
               $(".infantCountMealBtn .mealNo").text("");

               let selectedMeals = selectedMealsGlobal[flightId] || {};
               Object.entries(selectedMeals).forEach(([index, meal]) => {
                  $(".infantCountMealBtn .mealNo").eq(index).text(`(${meal.mealName})`);
               });

               let totalPrice = Object.values(selectedMeals).reduce((total, meal) => {
                  return total + parseFloat(meal.mealPrice.replace(/[^\d.]/g, ''));
               }, 0);
               $(".totalPriceOfMeal").text("PKR " + totalPrice);
            }

            function getBaggageAjax() {
               $.post("{{route('get_baggage')}}", { data: data['segments'], _token: "{{ csrf_token() }}" }, response => {
                  if (response.status !== 'success') return _alert(response.message, "error");

                  $(".baggageFlightSegments, .segmentNavTabBaggage, .segmentBtnsBaggage, .infantCountBaggageBtn").empty();
                  let passengerButtons = `
                        <div class="border-bottom border-dark mt-2 p-2">
                           <div class="d-flex justify-content-between align-items-center mb-3">
                              <h5>Select Baggage for Passengers:</h5>
                              <button type="button" class="btn btn-sm btn-primary select-same-baggage">Select Same Baggage for All</button>
                           </div>
                           <div class="overflow-auto infantCountBaggageBtn">
                              ${passengerList.map((pax, index) => `
                                    <a class="btn btn-outline-info mx-2 pax-btn" data-pax="${pax}" data-pax-index="${index}">
                                       ${pax} 
                                       <span class="BaggageNo"></span>
                                    </a>
                              `).join('')}
                           </div>
                           <hr class="bg-dark">
                           <div class="d-flex justify-content-between">
                              <h2 class="font-weight-bold">Total fare:</h2>
                              <p class="totalPriceOfBaggage">PKR 0</p>
                           </div>
                        </div>`;

                  let navTabs = `<ul class="nav nav-tabs segmentNavTabBaggage">`;
                  let tabContent = `<div class="tab-content segmentBtnsBaggage mt-3">${passengerButtons}`;
                  response.data.forEach((item, index) => {
                     let segmentInfo = Array.isArray(item.OnDFlightSegmentInfo)
                        ? item.OnDFlightSegmentInfo
                        : [item.OnDFlightSegmentInfo];

                     if (!segmentInfo || segmentInfo.length === 0) return;

                     // Prepare combined info for all segments in the flight
                     let flightSegments = segmentInfo.map((seg, segIndex) => {
                        const attrs = seg["@attributes"];
                        return {
                           rph: getSegmentAttributes(attrs.FlightNumber).rph || attrs.RPH,
                           flightNo: attrs.FlightNumber,
                           depDate: attrs.DepartureDateTime
                        };
                     });

                     let tabId = `baggageFlight-${index}`;
                     segmentRph[tabId] = flightSegments.map(seg => seg.rph);
                     segmentFlightNo[tabId] = flightSegments.map(seg => seg.flightNo);
                     segmentDepDate[tabId] = flightSegments.map(seg => seg.depDate);

                     let firstSeg = segmentInfo[0];
                     let lastSeg = segmentInfo[segmentInfo.length - 1];

                     let departureCity = getCity(firstSeg.DepartureAirport);
                     let arrivalCity = getCity(lastSeg.ArrivalAirport);

                     selectedBaggagesGlobal[tabId] = selectedBaggagesGlobal[tabId] || {};

                     navTabs += `
                        <li class="nav-item">
                           <a class="nav-link ${index === 0 ? 'active' : ''}" data-toggle="tab" href="#${tabId}">
                              ${departureCity}-${arrivalCity}
                           </a>
                        </li>`;

                     tabContent += `
                        <div id="${tabId}" class="tab-pane fade ${index === 0 ? 'show active' : ''}">
                           <div class="baggageMapContainer row my-2"></div>
                        </div>`;

                     loadBaggageMap(tabId, item.Baggage, totalPassengers, passengerList);
                  });
                  tabContent += `</div>`;

                  $(".baggageFlightSegments").append(navTabs + `</ul>`);
                  $(".baggages").append(tabContent + `</div>`);

                  currentSelectedPaxBaggage = 0;
                  $(".segmentBtnsBaggage .pax-btn").eq(0).addClass("active");
                  loadBaggageMap("baggageFlight-0", response.data[0].Baggage, totalPassengers, passengerList);
                  $('.segmentNavTabBaggage a').click(function (e) {
                     e.preventDefault();
                     $(this).tab('show');
                     let tabId = $(this).attr("href").slice(1);
                     if (!Object.keys(selectedBaggagesGlobal[tabId]).length) {
                        loadBaggageMap(tabId, response.data[tabId.split("-")[1]].Baggage, totalPassengers, passengerList);
                     }
                     updatePassengerBaggages(tabId);
                  });

                  $(".segmentBtnsBaggage .pax-btn").click(function() {
                     $(".segmentBtnsBaggage .pax-btn").removeClass("active");
                     $(this).addClass("active");
                     currentSelectedPaxBaggage = $(this).data("pax-index");
                  });

                  $(".select-same-baggage").click(function() {
                     let activeTab = $(".segmentNavTabBaggage .nav-link.active").attr("href").replace("#", "");
                     let selectedBaggageCard = $(`#${activeTab} .baggageCard.selected`);
                     if (!selectedBaggageCard.length) {
                        _alert("Please select a baggage option first", "warning");
                        return;
                     }
                     let baggageDescription = selectedBaggageCard.data("description");
                     let baggagePrice = selectedBaggageCard.data("price");
                     let baggageCode = selectedBaggageCard.data("baggage-code");
                     let rph = segmentRph[activeTab];
                     let flightNo = segmentFlightNo[activeTab];
                     let depDate = segmentDepDate[activeTab];
                     $(`#${activeTab} .baggageCard`).removeClass("selected");
                     selectedBaggagesGlobal[activeTab] = {};
                     passengerListCode.forEach((pax, index) => {
                        selectedBaggagesGlobal[activeTab][index] = {
                           baggageDescription,
                           baggagePrice,
                           baggageCode,
                           passenger: pax,
                           rph: rph,
                           flightNo: flightNo,
                           depDate: depDate
                        };
                     });
                     
                     $(`#${activeTab} .baggageCard[data-description="${baggageDescription}"]`).addClass("selected");
                     updatePassengerBaggages(activeTab);
                  });

               }).fail(xhr => _alert(xhr.responseJSON.message, "error"))
               // .always(() => _loader('hide'));
               .always();
            };
            const loadBaggageMap = (flightId, baggageData, totalPassengers, passengerList) => {
               let container = $(`#${flightId} .baggageMapContainer`).empty();
               let baggagesArray = Array.isArray(baggageData) ? baggageData : [baggageData];
               let selectedBaggages = selectedBaggagesGlobal[flightId] || {};

               let html = baggagesArray.map(baggage => {
                  // Check if this baggage is selected for any passenger
                  let isSelected = Object.values(selectedBaggages).some(b => b.baggageDescription === baggage.baggageDescription) ? 'selected' : '';
                  
                  return `<div class="col-sm-12  col-md-4 col-lg-3 p-2 border baggageCard pointer ${isSelected}" 
                              data-description="${baggage.baggageDescription}" 
                              data-price="${baggage.currencyCode} ${baggage.baggageCharge}" 
                              data-baggage-code="${baggage.baggageCode}">
                           <img src="/assets/images/baggagedefault.jpg" alt="" class="img-thumbnail">
                           <div class="meal-head d-flex justify-content-between">
                              <p class="font-weight-bolder">${baggage.baggageDescription}</p>
                           </div>
                           <div class="meal-head d-flex justify-content-between">
                              <p class="font-weight-bolder">${baggage.currencyCode} ${baggage.baggageCharge}</p>
                           </div>
                        </div>`;
               }).join('');

               container.append(html);

               $(".baggageCard").click(function () {
                  let baggageDescription = $(this).data("description");
                  let baggagePrice = $(this).data("price");
                  let baggageCode = $(this).data("baggage-code");
                  let flightId = $(this).closest(".tab-pane").attr("id");
                  let selectedBaggages = selectedBaggagesGlobal[flightId] || {};
                  let currentPax = currentSelectedPaxBaggage;
                  let rph = segmentRph[flightId];
                  let flightNo = segmentFlightNo[flightId];
                  let depDate = segmentDepDate[flightId];

                  if ($(this).hasClass("selected") && selectedBaggages[currentPax]?.baggageDescription === baggageDescription) {
                     $(this).removeClass("selected");
                     delete selectedBaggages[currentPax];
                  } else {
                     $(this).addClass("selected");
                     selectedBaggages[currentPax] = {
                        baggageDescription,
                        baggagePrice,
                        baggageCode,
                        passenger: passengerListCode[currentPax],
                        rph: rph,
                        flightNo: flightNo,
                        depDate: depDate
                     };
                  }

                  selectedBaggagesGlobal[flightId] = selectedBaggages;
                  updatePassengerBaggages(flightId);
               });
            }
            function updatePassengerBaggages(tabId) {
               $(".infantCountBaggageBtn .BaggageNo").text("");
               let selectedForTab = selectedBaggagesGlobal[tabId] || {};
               Object.keys(selectedForTab).forEach(index => {
                  let baggage = selectedForTab[index];
                  if (baggage && baggage.baggageDescription) {
                     $(".infantCountBaggageBtn .BaggageNo").eq(index).text(`(${baggage.baggageDescription})`);
                  }
               });
               let totalPrice = Object.values(selectedForTab).reduce((sum, baggage) => {
                  if (baggage && baggage.baggagePrice) {
                     let price = parseFloat((baggage.baggagePrice || '0').replace(/[^\d.]/g, '')) || 0;
                     return sum + price;
                  }
                  return sum;
               }, 0);

               $(".totalPriceOfBaggage").text("PKR " + totalPrice.toFixed(2));
            }
            let withOutAncis = false;
            let finalPriceTag = totalFare['TotalFare']['@attributes'];
            let paymentOnHold = true;
            $('#paymentOnHold').on('change', function () {
            if ($(this).is(':checked')) {
                  $('.paymentOnHoldText').text('Payment is ON HOLD');
                  paymentOnHold = true;
               } else {
                  $('.paymentOnHoldText').text('Payment is DIRECT');
                  paymentOnHold = false;
               }
            });
            let lastSubmittedData = null;
            let isSubmitting = false;

            $('#contactSubmit').click(function () {
               if (isSubmitting) return;

               passengers = [];
               let hasError = false;
               let firstErrorField = null;

               $('.paxDetails .contact2').each(function () {
                  let passenger = {
                     type: $(this).find('input[name$="_type[]"]').val(),
                     name: $(this).find('input[name$="_name[]"]').val(),
                     surname: $(this).find('input[name$="_surname[]"]').val(),
                     title: $(this).find('input[name^="title_"]:checked').val(),
                     dob: $(this).find('input[name$="_dob[]"]').val(),
                     nationality: $(this).find('select[name$="_nationality[]"]').val(),
                     passportNumber: $(this).find('input[name$="_passportnumber[]"]').val(),
                     passportExpiry: $(this).find('input[name$="_passportexp[]"]').val()
                  };

                  $(this).find("input[required], select[required]").each(function () {
                     if (!$(this).val()) {
                        $(this).addClass("border-danger");
                        hasError = true;
                        if (!firstErrorField) firstErrorField = this;
                     } else {
                        $(this).removeClass("border-danger");
                     }
                  });

                  passengers.push(passenger);
               });

               let userData = {
                  fullName: $('#userFullName').val(),
                  email: $('#userEmail').val(),
                  phoneCode: $('#userPhoneCode').val(),
                  phone: $('#userPhone').val()
               };

               if (!userData.fullName || !userData.email || !userData.phoneCode || !userData.phone) {
                  hasError = true;
                  if (!firstErrorField) {
                     if (!userData.fullName) firstErrorField = $('#userFullName');
                     else if (!userData.email) firstErrorField = $('#userEmail');
                     else if (!userData.phoneCode) firstErrorField = $('#userPhoneCode');
                     else if (!userData.phone) firstErrorField = $('#userPhone');
                  }
               }

               if (hasError) {
                  if (firstErrorField) $(firstErrorField).focus();
                  _alert('Please fill all required fields', 'warning');
                  isSubmitting = false;
                  return false;
               }

               if (!isDirectBooking && !checkValidationForAncis()) return;
               isSubmitting = true;
               let currentData = JSON.stringify({ passengers, userData });

               if (currentData === lastSubmittedData) {
                  // _alert('No changes detected. Data already submitted.', 'info');
                  // isSubmitting = false;

                  // if (!isDirectBooking) {
                  //    getFinalPrice();
                  // } else {
                  //    isSubmitting = false;
                  // }
                  return;
               }

               lastSubmittedData = currentData;
               // if (!isDirectBooking) {
               //    getFinalPrice();
               // } else {
               //    isSubmitting = false;
               // }
            });

            function checkValidationForAncis() {
               let allValid = true;
               let missing = {
                  seats: [],
                  meals: [],
                  baggage: []
               };

               $.each(selectedSeatsGlobal, function(segmentKey, selectedSeats) {
                  if (!Array.isArray(selectedSeats)) return;
                  if (selectedSeats.length !== passengerList.length) {
                     missing.seats.push(segmentKey);
                     allValid = false;
                  }
               });

               $.each(selectedMealsGlobal, function(segmentKey, paxMeals) {
                  if (Object.keys(paxMeals).length !== passengerList.length) {
                     missing.meals.push(segmentKey);
                     allValid = false;
                  }
               });

               $.each(selectedBaggagesGlobal, function(segmentKey, paxBags) {
                  if (Object.keys(paxBags).length !== passengerList.length) {
                     missing.baggage.push(segmentKey);
                     allValid = false;
                  }
               });

               if (missing.seats.length || missing.meals.length || missing.baggage.length) {
                  let msg = 'Please select the following ancillaries for all passengers:\n';
                  if (missing.seats.length) msg += `\nSeats: ${missing.seats.join(', ')}`;
                  if (missing.meals.length) msg += `\nMeals: ${missing.meals.join(', ')}`;
                  if (missing.baggage.length) msg += `\nBaggage: ${missing.baggage.join(', ')}`;

                  Swal.fire({
                     icon: 'error',
                     title: 'Missing Selections',
                     text: msg,
                     customClass: {
                        popup: 'text-start'
                     }
                  });
                  return false;
               }
               return true;
            }
            const getFinalPrice = () => {
               // console.log(selectedSeatsGlobal, selectedMealsGlobal, selectedBaggagesGlobal)
               // return
               isSubmitting = false;
               let paymentPriceContainer = $('.paymentPriceContainer');
               paymentPriceContainer.empty();
               $.ajax({
                  type: "POST",
                  url: "{{route('get_final_price')}}",
                  data: {
                     data: data,
                     seats: selectedSeatsGlobal,
                     meals: selectedMealsGlobal,
                     baggages: selectedBaggagesGlobal,
                     _token: "{{ csrf_token() }}"
                  },
                  beforeSend: () => _loader('show'),
                  success: function (res) {
                     if (res.status === 'success') {
                        finalPriceTag = res['data']['TotalFare']['@attributes'];
                        let tax = @json($tax);
                        let basePrice = (res['data']['TotalFare']['@attributes']['Amount']) || '-';
                        paymentPriceContainer.html(`<div class="book-head">
                              <div class="youbook">
                                 <h2><span>Price Summary</span></h2>
                              </div>
                           </div>
                           <div class="book-flex">
                              <div class="emr w-25">
                                 <img src="/assets/images/Fly_Jinnah_logo.png" alt="Fly Jinnah logo">
                              </div>
                           </div>
                           <div class="der-time der-time3">
                              <div class="emr-adul justify-content-between">
                                 <p>Flight price</p>
                                 <p>${(res['data']['TotalFare']['@attributes']['CurrencyCode']) || 'Pkr'} ${basePrice}</p>
                              </div>
                              <div class="emr-adul justify-content-between">
                                 <p>Tax</p>
                                 <p>PKR ${tax}</p>
                              </div>
                              <div class="pri-pak">
                                 <h2>Total price you pay</h2>
                                 <p>${(res['data']['TotalFare']['@attributes']['CurrencyCode']) || 'Pkr'} ${parseInt(basePrice) + parseInt(tax)}</p>
                              </div>
                           </div>
                        `);
                     }
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message, "error");
                  },
                  complete: function () {
                     _loader('hide');
                  }
               })
            }

            // ------------------------------------ Booking End ------------------------------------ //
            // ------------------------------------ Payment Start ------------------------------------ //

            const paymentAjax = () => {
               let isProcessing = false;
               $.ajax({
                  type: "POST",
                  url: "{{route('payment')}}",
                  data: {
                     paymentdata: paymentdata || {},
                     _token: "{{ csrf_token() }}"
                  },
                  beforeSend: () => _loader('show'),
                  success: function (response) {
                     // console.log(response)
                     bookingAjax();
                     // $('#paymentSend').click();
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message, "error");
                  },
                  complete: function () {
                     _loader('hide');
                  }
               });
            }
            const renderTrevelerDetails = data => {
               if (!Array.isArray(data) || data.length === 0) {
                  return `<div class="alert alert-danger" role="alert">Data is missing :)</div>`;
               }
               return data.map((row, index) => {
                  const flights = (row.eTicketInfo || []).map(fli => `
                        <div class="col-6">
                           <div class="border rounded p-3 mt-2">
                              <p>Route: <span>${fli.flightSegmentCode}</span></p>
                              <p>ETicket No: <span class="copyText">${fli.eTicketNo}</span> &nbsp; <i class="copyBtn fa fa-copy text-black-50" style="cursor:pointer;"></i></p>
                              <p>Coupon No: <span>${fli.couponNo}</span></p>
                              <p>Status: <span>${fli.usedStatus}</span></p>
                           </div>
                        </div>
                  `).join('');

                  return `
                        <div class="custom-method setp-bult traveler-bult row">
                           <div class="col-md-6 col-12">
                              <h1 class="font-weight-bold mb-3">Passenger Details</h1>
                              <p>Traveler ${index + 1}: <span>${paxCapitalize(row.type)}</span></p>
                              <p><span>Name</span>: ${row.name}</p>
                              <p><span>Surname</span>: ${row.surName}</p>
                           </div>
                           <div class="col-md-6 col-12">
                              <h1 class="font-weight-bold mb-3">${flights ?? 'Ticket Details'}</h1>
                              <div class="row">
                                    ${flights}
                              </div>
                           </div>
                        </div>
                  `;
               }).join('');
            };
            const renderPaxWithPrice = data => {
               if (data.length === 0) return ``;
               return data.map((row) => `
                  <div class="pri-eid">
                     <p>FlyJinnah Airline - (${row.code} X1)</p>
                     <p>Price: ${row.price.CurrencyCode} ${row.price.Amount}</p>
                  </div>
               `).join('');
            }
            const bookingAjax = () => {
               let user = {
                  userFullName: $('#userFullName').val(),
                  userEmail: $('#userEmail').val(),
                  userPhoneCode: $('#userPhoneCode').val(),
                  userPhone: $('#userPhone').val(),
                  acceptOffers: $('#acceptOffers').is(':checked'),
               }
               $.ajax({
                  type: "POST",
                  url: "{{route('bookFlight')}}",
                  data: {
                     airline: data['airline'], user, paymentOnHold, finalPriceTag, passengers, data, _token: "{{ csrf_token() }}"
                  },
                  beforeSend: () => _loader('show'),
                  success: function (response) {
                     sessionTimer(false);
                     let tax = @json($tax);
                     let totalPrice = parseInt(response.totalPrice?.Amount) + parseInt(tax);
                     _alert(response.message, response.status)
                     $('#paymentSend').click();
                     $(".guestName").text(response.userDetails.name);
                     $(".taxPaid").text(`Price: PKR ${tax}`);
                     $(".ticketMsg").text(response.ticketMsg.TicketAdvisory);
                     $(".totalPricePaid").text(`Price: ${response.totalPrice?.CurrencyCode ?? 'PKR'} ${totalPrice ?? '-'}`);
                     $(".contactDetails").html(renderTrevelerDetails(response.data));
                     $(".paxWithPrice").html(renderPaxWithPrice(response.paxPricing));
                     $(".orderId").html(response.bookingRefID);
                     console.log(response.emailStatus); // Show this in alert after set live email sending
                  },
                  error: function (xhr) {
                     _alert(xhr.responseJSON.message || 'bookingAjax Error', "error");
                  },
                  complete: function () {
                     _loader('hide');
                  }
               });
            }
            $('#paymentSendTest').click(function () {
               paymentAjax();
               paymentdata = 'test';
            });

            // ------------------------------------ Payment End ------------------------------------ //

            // -------------------------------- Combine Functions :) -------------------------------- //

            const getCity = airport => airport?.City || airport?.["@attributes"]?.LocationCode || '--';
            const getSegmentAttributes = flightNo => {
               let segmenArry = data['segments'][0] ? data['segments'] : [data['segments']];
               return segmenArry.find(s => s.flightNumber === flightNo);
            }
            // function fetchAddOns() {
               
               //   $(".addOnsContainer").removeClass("d-none");
               //   $.when(getSeatAjax(), getMealAjax()).done(function (seatResponse, mealResponse) {
               //      let seatData = seatResponse[0].data;
               //      let mealData = mealResponse[0].data;
               //      if (!seatData && !mealData) {
               //         $(".addOnsContainer").addClass("d-none");
               //         return;
               //      }
               //      $(".loadingText").hide(); // Hide loading message
               //      if (seatData) {
               //         $(".seatFlightSegments").html(renderSeatData(seatData));
               //      } else {
               //         $(".box-021").hide();
               //      }
               //      if (mealData) {
               //         $(".mealContainer").html(renderMealData(mealData));
               //      } else {
               //         $(".box-022").hide();
               //      }
               //   }).fail(function () {
               //      $(".addOnsContainer").addClass("d-none");
               //   });
            // }
            // fetchAddOns();
         });
      </script>
   @endif
@endif
@endsection