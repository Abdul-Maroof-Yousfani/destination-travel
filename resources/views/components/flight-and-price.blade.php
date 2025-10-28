{{-- @dd($flightData, $totalFare, $tax) --}}
@if (isset($flightData) && !empty($flightData))
    @php
        // $airline = $flightData['airline'] ?? null;
        // $logo = $flightData['logo'] ?? '';
        // $isEmirate = $airline === 'emirate';
        // $isFlyJinnah = $airline === 'flyjinnah';

        // $hasReturn = false;
        
        // if ($isEmirate) {
        //     $segments = $flightData['flightDetails']['segments'] ?? [];

        //     $seg0 = $segments[0]['flights'] ?? [];
        //     $depDate = !empty($seg0['Departure']['Date']['value']) 
        //         ? \Carbon\Carbon::parse($seg0['Departure']['Date']['value'])->format('d M Y')
        //         : '';

        //     $depTime = $seg0['Departure']['Time']['value'] ?? '';
        //     $depTimeDiff = $seg0['duration'] ?? '';
        //     $arrTime = $seg0['Arrival']['Time']['value'] ?? '';
        //     $originCode = $segments[0]['departureCode'] ?? $seg0['Departure']['AirportCode']['value'] ?? '';
        //     $destinationCode = $segments[0]['arrivalCode'] ?? $seg0['Arrival']['AirportCode']['value'] ?? '';
        //     $isConnected = $seg0['flightDetails']['isConnected'] ?? false;

        //     $hasReturn = !empty($segments[1]['departureCode'] ?? null);
        //     if ($hasReturn) {
        //         $seg1 = $segments[1]['flights'] ?? [];
        //         $returnDepTime = $seg1['Departure']['Time']['value'] ?? '';
        //         $returnTimeDiff = $seg1['duration'] ?? '';
        //         $returnArrTime = $seg1['Arrival']['Time']['value'] ?? '';
        //         $returnOriginCode = $segments[1]['departureCode'] ?? $seg1['Departure']['AirportCode']['value'] ?? '';
        //         $returnDestinationCode = $segments[1]['arrivalCode'] ?? $seg1['Arrival']['AirportCode']['value'] ?? '';
        //         $returnIsConnected = $seg1['flightDetails']['isConnected'] ?? false;
        //     }
        // }

        // if ($isFlyJinnah) {
        //     $df = $flightData['departureFlight'] ?? [];
        //     $rf = $flightData['returnFlight'] ?? [];

        //     $depDate = $df['departureDate'] ?? '--';
        //     $depTime = $df['departureTime'] ?? '--';
        //     $depTimeDiff = $df['timeDifference'] ?? '--';
        //     $arrTime = $df['arrivalTime'] ?? '--';
        //     $originCode = $df['originCode'] ?? '--';
        //     $destinationCode = $df['destinationCode'] ?? '--';
        //     $isConnected = $df['isConnected'] ?? false;

        //     $hasReturn = !empty($rf);
        //     if ($hasReturn) {
        //         $returnDepTime = $rf['departureTime'] ?? '--';
        //         $returnTimeDiff = $rf['timeDifference'] ?? '--';
        //         $returnArrTime = $rf['arrivalTime'] ?? '--';
        //         $returnOriginCode = $rf['originCode'] ?? '--';
        //         $returnDestinationCode = $rf['destinationCode'] ?? '--';
        //         $returnIsConnected = $rf['isConnected'] ?? false;
        //     }
        // }
    @endphp
    {{-- <div class="bokkings-bar">
        <div class="book-head">
            <div class="youbook">
                <h2>Your Bookings</h2>
            </div>
            <div class="depar-head">
                <ul>
                    <li><p>Departing</p></li>
                    <li><p><i class="fa-regular fa-calendar"></i> {{ $depDate }}</p></li>
                </ul>
            </div>
        </div>

        <div class="book-flex">
            <div class="emr w-25">
                <img src="/assets/images/{{ $logo }}" alt="Flight logo">
            </div>
        </div>

        <div class="d-flex flex-column">
            @if (!empty($destinationCode))
                <div class="der-time der-time3 mb-2">
                    <ul>
                        <li><h2 class="{{ $isEmirate ? 'timeIn12Hr' : '' }}">{{ $depTime }}</h2></li>
                        <li><div class="stays"><p>{{ $depTimeDiff }}</p></div></li>
                        <li><div class="tims"><h2 class="{{ $isEmirate ? 'timeIn12Hr' : '' }}">{{ $arrTime }}</h2></div></li>
                    </ul>
                    <div class="citys citys2">
                        <div class="cit">
                            <ul>
                                <li><p>{{ $originCode }}</p></li>
                                <li><p>{{ $isConnected ? '1 Stop' : 'Nonstop' }}</p></li>
                                <li><p>{{ $destinationCode }}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if ($hasReturn)
                <div class="der-time der-time3 mb-2">
                    <ul>
                        <li><h2 class="{{ $isEmirate ? 'timeIn12Hr' : '' }}">{{ $returnDepTime }}</h2></li>
                        <li><div class="stays"><p>{{ $returnTimeDiff }}</p></div></li>
                        <li><div class="tims"><h2 class="{{ $isEmirate ? 'timeIn12Hr' : '' }}">{{ $returnArrTime }}</h2></div></li>
                    </ul>
                    <div class="citys citys2">
                        <div class="cit">
                            <ul>
                                <li><p>{{ $returnOriginCode }}</p></li>
                                <li><p>{{ $returnIsConnected ? '1 Stop' : 'Nonstop' }}</p></li>
                                <li><p>{{ $returnDestinationCode }}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div> --}}
    @php
        $airline = $flightData['airline'] ?? null;
        $isFlyJinnah = $airline === 'flyjinnah';
        $isEmirate = $airline === 'emirate';
        // dd($isFlyJinnah, $isEmirate);

        $logo = $flightData['airline'] ?? 'default';

        $departure = $flightData['departure'] ?? null;
        $originCode = $departure['departure']['code'] ?? '';
        $destinationCode = $departure['arrival']['code'] ?? '';
        $depTime = $departure['departure']['time'] ?? '';
        $arrTime = $departure['arrival']['time'] ?? '';
        $depTimeDiff = $departure['duration'] ?? '';
        $isConnected = $departure['isConnected'] === 'true' ?? false;
        $stopCount = count($departure['segments']) - 1;
        
        $hasReturn = isset($flightData['return']);
        // dd($departure, $originCode, $destinationCode, $depTime, $arrTime, $depTimeDiff, $isConnected, $stopCount, $hasReturn);
        if ($hasReturn) {
            $return = $flightData['return'];
            $returnOriginCode = $return['departure']['code'] ?? '';
            $returnDestinationCode = $return['arrival']['code'] ?? '';
            $returnDepTime = $return['departure']['time'] ?? '';
            $returnArrTime = $return['arrival']['time'] ?? '';
            $returnTimeDiff = $return['duration'] ?? '';
            $returnIsConnected = $return['isConnected'] === 'true';
            $returnStopCount = count($return['segments']) - 1;
        }

        $depDate = $departure['departure']['date'] ?? '';
        // dd($return, $returnOriginCode, $returnDestinationCode, $returnDepTime, $returnArrTime, $returnTimeDiff, $returnIsConnected, $returnStopCount, $depDate);
    @endphp
    <div class="bokkings-bar">
        <div class="book-head">
            <div class="youbook">
                <h2>Your Bookings</h2>
            </div>
            <div class="depar-head">
                <ul>
                    <li><p>Departing</p></li>
                    <li><p><i class="fa-regular fa-calendar"></i> {{ $depDate }}</p></li>
                </ul>
            </div>
        </div>

        <div class="book-flex">
            <div class="emr w-25">
                <img src="{{ asset('assets/images/logos/modal/' . $logo . '.png') }}" alt="{{ strtoupper($logo) }}">
            </div>
        </div>

        <div class="d-flex flex-column">
            {{-- Departure Flight --}}
            @if (!empty($destinationCode))
                <div class="der-time der-time3 mb-2">
                    <ul>
                        <li><h2>{{ $depTime }}</h2></li>
                        <li><div class="stays"><p>{{ $depTimeDiff }}</p></div></li>
                        <li><div class="tims"><h2>{{ $arrTime }}</h2></div></li>
                    </ul>
                    <div class="citys citys2">
                        <div class="cit">
                            <ul>
                                <li><p>{{ $originCode }}</p></li>
                                @if ($isConnected)
                                    <li><p>{{ $stopCount }} {{ $stopCount > 1 ? 'Stops' : 'Stop' }}</p></li>
                                @else
                                    Nonstop
                                @endif
                                <li><p>{{ $destinationCode }}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Return Flight (if any) --}}
            @if ($hasReturn)
                <div class="der-time der-time3 mb-2">
                    <ul>
                        <li><h2>{{ $returnDepTime }}</h2></li>
                        <li><div class="stays"><p>{{ $returnTimeDiff }}</p></div></li>
                        <li><div class="tims"><h2>{{ $returnArrTime }}</h2></div></li>
                    </ul>
                    <div class="citys citys2">
                        <div class="cit">
                            <ul>
                                <li><p>{{ $returnOriginCode }}</p></li>
                                @if ($returnIsConnected)
                                    <li><p>{{ $returnStopCount }} {{ $returnStopCount > 1 ? 'Stops' : 'Stop' }}</p></li>
                                @else
                                    Nonstop
                                @endif
                                <li><p>{{ $returnDestinationCode }}</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if ($isEmirate || $isFlyJinnah)
        <div class="bokkings-bar bokkings-bar2 {{ $priceclass }}">
            <div class="book-head">
                <div class="youbook">
                    <h2><span>Price Summary</span></h2>
                </div>
            </div>

            <div class="book-flex">
                <div class="emr w-25">
                    <img src="{{ asset('assets/images/logos/modal/' . $logo . '.png') }}" alt="{{ strtoupper($logo) }}">
                </div>
            </div>

            <div class="der-time der-time3">
                @if ($isEmirate)
                    @if (!empty($flightData['flightDetails']['bundle']['offerItem']))
                        @foreach ($flightData['flightDetails']['bundle']['offerItem'] as $offer)
                            <div class="emr-adul justify-content-between">
                                <p>{{ $offer['fareDetail']['passengers'] ?? '' }}</p>
                                <p>{{ $offer['totalPrice']['code'] ?? 'PKR' }} {{ number_format($offer['totalPrice']['amount'] ?? 0, 2) }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="emr-adul justify-content-between">
                            <p>Flight Price</p>
                            <p>{{ $flightData['flightDetails']['bundle']['totalPrice']['code'] ?? 'PKR' }} {{ number_format($flightData['flightDetails']['bundle']['totalPrice']['amount'] ?? 0, 2) }}</p>
                        </div>
                    @endif
                @elseif ($isFlyJinnah)
                    <div class="emr-adul justify-content-between">
                        <p>{{ isset($flightData['isDirectBooking']) && !$flightData['isDirectBooking'] ? 'Flight with bundle' : 'Flight Price' }}</p>
                        <p>{{ $totalFare['TotalFare']['@attributes']['CurrencyCode'] ?? 'PKR' }} {{ $totalFare['TotalFare']['@attributes']['Amount'] ?? '' }}</p>
                    </div>
                @endif

                <div class="emr-adul justify-content-between">
                    <p>Tax</p>
                    <p>PKR {{ $tax }}</p>
                </div>

                <div class="pri-pak">
                    <h2>Total price you pay</h2>
                    <p>
                        @if ($isEmirate)
                            {{ $flightData['flightDetails']['bundle']['totalPrice']['code'] ?? 'PKR' }}
                            {{ number_format(($flightData['flightDetails']['bundle']['totalPrice']['amount'] ?? 0) + ($tax ?? 0), 2) }}
                        @elseif ($isFlyJinnah)
                            {{ $totalFare['TotalFare']['@attributes']['CurrencyCode'] ?? 'PKR' }}
                            {{ ($totalFare['TotalFare']['@attributes']['Amount'] ?? 0) + ($tax ?? 0) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if ($isEmirate)
        <div class="bokkings-bar bokkings-bar2 penaltiesContainer">
            <div class="book-head">
                <div class="youbook">
                    <h2><span>Penalties</span></h2>
                </div>
                <div class="youbook">
                <p class="text-info font-weight-bolder toggle-panelties-details pointer">Show more</p>
                </div>
            </div>
            <div class="der-time der-time3 panelties-details" style="display: none;">
                @foreach ($flightData['flightDetails']['bundle']['offerItem'] ?? [] as $offer)
                    <div class="emr-adul justify-content-between">
                        <h2>{{ $offer['fareDetail']['passengers'] ?? '' }}</h2>
                    </div>
                    @foreach ($offer['fareDetail']['penalties'] ?? [] as $penalty)
                        <div class="emr-adul justify-content-between">
                            <p>{{ $penalty['arrival'] ?? '' }}</p>
                            <p>{{ $penalty['destination'] ?? '' }}</p>
                        </div>
                        @if (!empty($penalty['fareRules']['cancelFee']))
                            <div class="mt-2"><strong class="font-weight-bolder">Cancel Fee</strong></div>
                            @foreach ($penalty['fareRules']['cancelFee'] as $label => $fee)
                                <div class="emr-adul justify-content-between">
                                    <p>{{ $label }}</p>
                                    @if (isset($fee['price']))
                                        <p>
                                            {{ $fee['price']['amount'] ?? '-' }}
                                            {{ $fee['price']['code'] ?? '' }}
                                            <small class="small">({{ $fee['amountApplication'] ?? '' }})</small>
                                        </p>
                                    @else
                                        <p>{{ $fee }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                        @if (!empty($penalty['fareRules']['changeFee']))
                            <div class="mt-2"><strong class="font-weight-bolder">Change Fee</strong></div>
                            @foreach ($penalty['fareRules']['changeFee'] as $label => $fee)
                                <div class="emr-adul justify-content-between">
                                    <p>{{ $label }}</p>
                                    @if (isset($fee['price']))
                                        <p>
                                            {{ $fee['price']['amount'] ?? '-' }}
                                            {{ $fee['price']['code'] ?? '' }}
                                            <small class="small">({{ $fee['amountApplication'] ?? '' }})</small>
                                        </p>
                                    @else
                                        <p>{{ $fee }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                        @if (!empty($penalty['fareRules']['refundFee']))
                            <div class="mt-2"><strong class="font-weight-bolder">Refund Fee</strong></div>
                            @foreach ($penalty['fareRules']['refundFee'] as $label => $fee)
                                <div class="emr-adul justify-content-between">
                                    <p>{{ $label }}</p>
                                    @if (isset($fee['price']))
                                        <p>
                                            {{ $fee['price']['amount'] ?? '-' }}
                                            {{ $fee['price']['code'] ?? '' }}
                                            <small class="small">({{ $fee['amountApplication'] ?? '' }})</small>
                                        </p>
                                    @else
                                        <p>{{ $fee }}</p>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                        <br><hr>
                    @endforeach
                @endforeach
            </div>
        </div>
    @endif
@endif
