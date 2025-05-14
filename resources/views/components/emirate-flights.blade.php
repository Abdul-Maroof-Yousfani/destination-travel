{{-- @dd($flights) --}}
@php
    $labels = ['Departure', 'Return'];
@endphp
@if (!empty($flights) && !isset($flights['error']))
    <li>
        <div class="row mt-5">
            @foreach ($flights as $index => $item)
                @if (!empty($item['flights']) && is_array($item['flights']))
                    <div class="col-sm-{{ $isRoundTrip ? '6' : '12' }} col-12">
                        <div class="d-flex mb-3">
                            <p class="font-weight-bold mr-2">
                                {{ $labels[$index] ?? 'Flight Segment' }}
                            </p>
                            <i><p>{{ $item['departureCode'] ?? '' }} → {{ $item['arrivalCode'] ?? '' }}</p></i>
                        </div>
                        {{-- @dd($item) --}}
                        @foreach ($item['flights'] as $key => $flight)
                            <div class="flight-item {{ $key > 0 ? 'd-none extra-flight-emi' : '' }}">
                                <div class="prices2 d-flex align-items-center mb-2">
                                    <input type="radio"
                                        id="singleFlightEmi{{ $index }}_{{ $key }}"
                                        value="{{ $flight['price']['amount'] ?? 0 }}"
                                        name="{{ $index == 0 ? 'emiDepFlight' : 'emiRtnFlight' }}"
                                        {{ $key == 0 ? 'checked' : '' }}
                                        onchange="updateTotalPrice()"
                                        data-segment="{{ json_encode($flight) }}"
                                        data-response-id="{{ json_encode($item['responseId']) }}"
                                    >
                                    <label class="flex1" for="singleFlightEmi{{ $index }}_{{ $key }}">
                                        <div class="emri text-center">
                                            <img class="w-75 p-2" src="assets/images/emirates.png" alt="emirates_logo">
                                        </div>
                                        <div class="der-time">
                                            <ul>
                                                <li><h2 class="timeIn12Hr">{{ $flight['Departure']['Time']['value'] }}</h2></li>
                                                <li>
                                                    <div class="stays"><p>{{ $flight['duration'] }}</p></div>
                                                </li>
                                                <li class="d-flex">
                                                    <h2 class="timeIn12Hr">{{ $flight['Arrival']['Time']['value'] }}</h2>
                                                    {{-- @if ($flight['Arrival']['ChangeOfDay'] != 0)
                                                        <sup class="w-100 text-primary">+ {{$flight['Arrival']['ChangeOfDay']}}</sup>
                                                    @endif --}}
                                                </li>
                                            </ul>
                                            <div class="citys">
                                                <div class="cit">
                                                    <ul>
                                                        <li><p>{{$flight['Departure']['AirportName']['value']}}</p></li>
                                                        <li><p>-</p></li>
                                                        <li><p>{{ $flight['flightDetails']['isConnected'] ? '1 Stop' : 'Nonstop' }}</p></li>
                                                        <li><p>-</p></li>
                                                        <li><p>{{$flight['Arrival']['AirportName']['value']}}</p></li>
                                                    </ul>
                                                    <div class="weig weig2">
                                                        <ul>
                                                            <li><p><i class="fa-solid fa fa-money-bill-1-wave"></i> {{ $flight['price']['code'] }} {{ number_format($flight['price']['amount'], 2) }}</p></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <hr>
                            </div>
                        @endforeach
                    </div>
                    @endif
                @endforeach
            <div class="text-center mb-3 col-12">
                <span class="text-info font-weight-bolder pointer toggle-flights-btn" data-target=".extra-flight-emi"></span>
            </div>
        </div>
        <div class="prices2 mt-3">
            <div class="select-flight">
                <button class="btn btn-b bundleModalBtnEmi">
                    PKR <span id="totalEmiPrice">0</span> - Select flight
                </button>
            </div>
        </div>
    </li>
@else
    <p class="text-center">Emirate flights not available</p>
@endif
<script>
    $(document).ready(function () {
        // const emiExtraFlightCount = $('.extra-flight-emi').length;
        // const originalTextEmi = `+ ${emiExtraFlightCount} more flight option${(emiExtraFlightCount > 1 ? 's' : '')}`;
        // emiExtraFlightCount > 0 ? $('.toggleFlightsBtnEmi').text(originalTextEmi) : $('.toggleFlightsBtnEmi').text('');
        // $('.toggleFlightsBtnEmi').on('click', function () {
        //     if ($('.extra-flight-emi').is(':visible')) {
        //         $('.extra-flight-emi').addClass('d-none');
        //         $('.toggleFlightsBtnEmi').text(originalTextEmi);
        //     } else {
        //         $('.extra-flight-emi').removeClass('d-none');
        //         $('.toggleFlightsBtnEmi').text('Show less');
        //     }
        // });
        let updateTimeoutEmi;
        let depSegmentEmi, rtnSegmentEmi, responseId;
        function emiUpdateTotalPrice() {
            clearTimeout(updateTimeoutEmi);
            
            updateTimeoutEmi = setTimeout(() => {
                let selectedDepFlight = $('input[name="emiDepFlight"]:checked');
                let selectedRtnFlight = $('input[name="emiRtnFlight"]:checked');
                responseId = (selectedDepFlight.data('response-id') || '').replace(/^"|"$/g, '');

                let departurePrice = parseFloat(selectedDepFlight.val()) || 0;
                let returnPrice = parseFloat(selectedRtnFlight.val()) || 0;

                let totalPrice = departurePrice + returnPrice;
                $('#totalEmiPrice').text(formatCurrency(totalPrice));
                
                depSegmentEmi = selectedDepFlight.data('segment');
                rtnSegmentEmi = selectedRtnFlight.data('segment');
            }, 100);
        }
        $(document).on('change', 'input[name="emiDepFlight"], input[name="emiRtnFlight"]', emiUpdateTotalPrice);
        emiUpdateTotalPrice();

        $('.bundleModalBtnEmi').click(function() {
            // let segments = [];
            // if (rtnSegmentEmi) {
            //     segments = [depSegmentEmi, rtnSegmentEmi].map(getSegmentEmi);
            // } else {
            //     segments = [getSegmentEmi(depSegmentEmi)];
            // }
            // console.log(depSegmentEmi, rtnSegmentEmi);
            $('.modalFlights').html(flightHtml(depSegmentEmi, rtnSegmentEmi, 'emirate'))
            $('#bundleModal').modal("show");
            $(".directModalBundles").html(renderEmiBundles(depSegmentEmi.bundles, false));
            if (rtnSegmentEmi) {
                $(".returnModalBundles").html(renderEmiBundles(rtnSegmentEmi.bundles, true));
            }
        });
        // const getSegmentEmi = data => {
        //     if (!data) return null;
        //     return {
        //         departure: convertTo12Hour(data['Departure']['Time']),
        //         arrival: convertTo12Hour(data['Arrival']['Time']),
        //         origin: data['Arrival']['AirportCode'],
        //         destination: data['Departure']['AirportCode'],
        //         flightNumber: `${data['flightDetails']['marketingCarrier']['AirlineID']}-${data['flightDetails']['marketingCarrier']['FlightNumber']}`
        //     };
        // }
        const renderEmiBundles = (data, isReturn) => {
            const normalizedData = Array.isArray(data) ? data : (data ? [data] : []);
            if (normalizedData.length === 0) {
                return `<div class="alert alert-danger" role="alert">No flights available</div>`;
            }
            return normalizedData.map(row => {
                // let baggage = getBaggageDetails(row['baggageAllowance']);
                // const baggageText = baggage?.Value && baggage?.UOM
                //     ? `${baggage.Value} ${baggage.UOM}`
                //     : 'Not Included';
                let shortTexts = row['priceClass']['Descriptions']['Description'].filter(item => 
                    item.hasOwnProperty('Text') && Object.keys(item).length === 1
                );
                // console.log(row)
                return`
                    <li data-id="${row['offerID']['OfferID'] ?? 'N/A'}">
                        <div class="flex-plus flex-plusul2">
                            <h4>${row['priceClass']['Name']['value'] ?? 'N/A'}</h4>
                            <div class="flex-plus2 ">
                                <ul>
                                    <li>
                                        <div class="plus-fle">
                                            <div class="">
                                                ${shortTexts.map(item => `<p>${item.Text.value}</p>`).join('')}
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="conti">
                                <a class="btn btn-b bookBtn"
                                    data-airline="emirate"
                                    data-is-return="${isReturn}"
                                    data-bundle-id="${encodeURIComponent(JSON.stringify(row['offerID']))}"
                                    data-response-id="${responseId}"
                                    data-offer-ids="${encodeURIComponent(JSON.stringify(getOfferIds(row['offerItem'])))}"
                                role="button">
                                    ${row['totalPrice']['code'] ?? 'PKR'}
                                    ${formatCurrency(Math.round(row['totalPrice']['amount'] || '0'))}
                                </a>
                            </div>
                        </div>
                    </li>`;
            }).join('');
        }
        const getOfferIds = data =>
            (Array.isArray(data) ? data : data ? [data] : []).map(item => ({
                id: item?.id || null,
                PassengerRef: item?.services?.[0]?.passengerRefs || null
            }));
        // const getBaggageDetails = data => {
        //     if (!Array.isArray(data)) return null;
        //     const baggageItem = data.find(item =>
        //         item['PassengerRefs'] === 'T1' &&
        //         item['baggage_detail']?.['BaggageCategory'] === 'Checked'
        //     );
        //     return baggageItem?.['baggage_detail']?.['WeightAllowance']?.['MaximumWeight'] || null;
        // }
    });
</script>
