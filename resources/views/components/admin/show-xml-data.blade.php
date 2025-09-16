<div class="modal-body">
    @php
        $bookingRequest = $booking->bookingRequest ?? null;
        $xmlBody = $bookingRequest && isset($bookingRequest->xml_body) ? json_decode($bookingRequest->xml_body, true) : null;
        $airline = strtolower($booking->airline);
    @endphp
    @if ($bookingRequest)
        @if ($airline === 'emirates')
            <div class="accordion" id="bookingAccordion">
                <!-- General Booking Information -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="generalInfoHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generalInfo" aria-expanded="true" aria-controls="generalInfo">
                            General Booking Information Emirates
                        </button>
                    </h2>
                    <div id="generalInfo" class="accordion-collapse collapse show" aria-labelledby="generalInfoHeading" data-bs-parent="#bookingAccordion">
                        <div class="accordion-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>ID:</strong> {{ $bookingRequest->id ?? 'N/A' }}</li>
                                <li class="list-group-item"><strong>Airline:</strong> {{ $bookingRequest->airline ?? 'N/A' }}</li>
                                <li class="list-group-item"><strong>Ticket Limit:</strong> {{ isset($bookingRequest->ticket_limit) ? \Carbon\Carbon::parse($bookingRequest->ticket_limit)->format('d M Y, H:i') : 'N/A' }}</li>
                                <li class="list-group-item"><strong>Payment Limit:</strong> {{ isset($bookingRequest->payment_limit) ? \Carbon\Carbon::parse($bookingRequest->payment_limit)->format('d M Y, H:i') : 'N/A' }}</li>
                                <li class="list-group-item"><strong>Status:</strong> {{ isset($bookingRequest->status) ? ucfirst($bookingRequest->status) : 'N/A' }}</li>
                                <li class="list-group-item"><strong>Client ID:</strong> {{ $bookingRequest->client_id ?? 'N/A' }}</li>
                                <li class="list-group-item"><strong>Booking ID:</strong> {{ $bookingRequest->booking_id ?? 'N/A' }}</li>
                                <li class="list-group-item"><strong>Created At:</strong> {{ isset($bookingRequest->created_at) ? \Carbon\Carbon::parse($bookingRequest->created_at)->format('d M Y, H:i') : 'N/A' }}</li>
                                <li class="list-group-item"><strong>Updated At:</strong> {{ isset($bookingRequest->updated_at) ? \Carbon\Carbon::parse($bookingRequest->updated_at)->format('d M Y, H:i') : 'N/A' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Passenger Information -->
                @if ($xmlBody && isset($xmlBody['passengers']) && !empty($xmlBody['passengers']))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="passengerInfoHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#passengerInfo" aria-expanded="false" aria-controls="passengerInfo">
                                Passenger Information
                            </button>
                        </h2>
                        <div id="passengerInfo" class="accordion-collapse collapse" aria-labelledby="passengerInfoHeading" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                @foreach ($xmlBody['passengers'] as $passenger)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            Passenger {{ $passenger['id'] ?? 'Unknown' }} ({{ $passenger['type'] ?? 'N/A' }})
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Name:</strong> {{ isset($passenger['title']) ? $passenger['title'] : '' }} {{ $passenger['givenName'] ?? '' }} {{ $passenger['surname'] ?? '' }}</li>
                                                <li class="list-group-item"><strong>Birthdate:</strong> {{ isset($passenger['birthdate']) ? \Carbon\Carbon::parse($passenger['birthdate'])->format('d M Y') : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Gender:</strong> {{ $passenger['gender'] ?? 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Contact Ref:</strong> {{ $passenger['contactRef'] ?? 'N/A' }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">No passenger information available.</div>
                @endif

                <!-- Flight Segments -->
                @if ($xmlBody && isset($xmlBody['segments']) && !empty($xmlBody['segments']))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="segmentsHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#segments" aria-expanded="false" aria-controls="segments">
                                Flight Segments
                            </button>
                        </h2>
                        <div id="segments" class="accordion-collapse collapse" aria-labelledby="segmentsHeading" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                @foreach ($xmlBody['segments'] as $index => $segment)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            Segment: {{ $segment['departureCode'] ?? 'N/A' }} to {{ $segment['arrivalCode'] ?? 'N/A' }}
                                        </div>
                                        <div class="card-body">
                                            <h6>Flight Details</h6>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Departure:</strong> {{ $segment['flights']['Departure']['AirportName']['value'] ?? 'N/A' }} ({{ $segment['flights']['Departure']['AirportCode']['value'] ?? 'N/A' }}) on {{ isset($segment['flights']['Departure']['Date']['value']) ? \Carbon\Carbon::parse($segment['flights']['Departure']['Date']['value'])->format('d M Y') : 'N/A' }} at {{ $segment['flights']['Departure']['Time']['value'] ?? 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Arrival:</strong> {{ $segment['flights']['Arrival']['AirportName']['value'] ?? 'N/A' }} ({{ $segment['flights']['Arrival']['AirportCode']['value'] ?? 'N/A' }}) on {{ isset($segment['flights']['Arrival']['Date']['value']) ? \Carbon\Carbon::parse($segment['flights']['Arrival']['Date']['value'])->format('d M Y') : 'N/A' }} at {{ $segment['flights']['Arrival']['Time']['value'] ?? 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Duration:</strong> {{ $segment['duration'] ?? 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Price:</strong> {{ isset($segment['price']) ? $segment['price']['code'] . ' ' . number_format($segment['price']['amount'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Aircraft:</strong> {{ $segment['flights']['equipment']['Name']['value'] ?? 'N/A' }} ({{ $segment['flights']['equipment']['AircraftCode']['value'] ?? 'N/A' }})</li>
                                                <li class="list-group-item"><strong>Carrier:</strong> {{ $segment['flights']['marketingCarrier']['Name']['value'] ?? 'N/A' }} (Flight {{ $segment['flights']['marketingCarrier']['FlightNumber']['value'] ?? 'N/A' }})</li>
                                            </ul>
                                            @if (isset($segment['flights']['secondFlight']))
                                                <h6 class="mt-3">Connecting Flight</h6>
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><strong>Departure:</strong> {{ $segment['flights']['secondFlight']['departure']['AirportName']['value'] ?? 'N/A' }} ({{ $segment['flights']['secondFlight']['departure']['AirportCode']['value'] ?? 'N/A' }}) on {{ isset($segment['flights']['secondFlight']['departure']['Date']['value']) ? \Carbon\Carbon::parse($segment['flights']['secondFlight']['departure']['Date']['value'])->format('d M Y') : 'N/A' }} at {{ $segment['flights']['secondFlight']['departure']['Time']['value'] ?? 'N/A' }}</li>
                                                    <li class="list-group-item"><strong>Arrival:</strong> {{ $segment['flights']['secondFlight']['arrival']['AirportName']['value'] ?? 'N/A' }} ({{ $segment['flights']['secondFlight']['arrival']['AirportCode']['value'] ?? 'N/A' }}) on {{ isset($segment['flights']['secondFlight']['arrival']['Date']['value']) ? \Carbon\Carbon::parse($segment['flights']['secondFlight']['arrival']['Date']['value'])->format('d M Y') : 'N/A' }} at {{ $segment['flights']['secondFlight']['arrival']['Time']['value'] ?? 'N/A' }}</li>
                                                    <li class="list-group-item"><strong>Aircraft:</strong> {{ $segment['flights']['secondFlight']['equipment']['Name']['value'] ?? 'N/A' }} ({{ $segment['flights']['secondFlight']['equipment']['AircraftCode']['value'] ?? 'N/A' }})</li>
                                                    <li class="list-group-item"><strong>Carrier:</strong> {{ $segment['flights']['secondFlight']['marketingCarrier']['Name']['value'] ?? 'N/A' }} (Flight {{ $segment['flights']['secondFlight']['marketingCarrier']['FlightNumber']['value'] ?? 'N/A' }})</li>
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">No flight segments available.</div>
                @endif

                <!-- Pricing Information -->
                @if ($xmlBody && isset($xmlBody['ticketInfos']) && !empty($xmlBody['ticketInfos']))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="pricingInfoHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingInfo" aria-expanded="false" aria-controls="pricingInfo">
                                Pricing Information
                            </button>
                        </h2>
                        <div id="pricingInfo" class="accordion-collapse collapse" aria-labelledby="pricingInfoHeading" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                @foreach ($xmlBody['ticketInfos'] as $ticket)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            Ticket for Passenger {{ $ticket['passengerReference'] ?? 'N/A' }}
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Ticket Number:</strong> {{ $ticket['ticketDocument']['ticketDocNbr'] ?? 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Base Fare:</strong> {{ isset($ticket['price']['details']['amount']) ? $ticket['price']['details']['amount']['code'] . ' ' . number_format($ticket['price']['details']['amount']['value'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Total Price:</strong> {{ isset($ticket['price']['total']) ? $ticket['price']['total']['code'] . ' ' . number_format($ticket['price']['total']['value'], 2) : 'N/A' }}</li>
                                            </ul>
                                            @if (isset($ticket['price']['details']['taxes']['breakdown']) && !empty($ticket['price']['details']['taxes']['breakdown']))
                                                <h6 class="mt-3">Tax Breakdown</h6>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($ticket['price']['details']['taxes']['breakdown'] as $tax)
                                                        <li class="list-group-item">{{ $tax['description'] ?? 'Unknown Tax' }}: {{ isset($tax['amount']) ? $tax['amount']['code'] . ' ' . number_format($tax['amount']['value'], 2) : 'N/A' }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>No tax breakdown available.</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">No pricing information available.</div>
                @endif

                <!-- Penalties -->
                @if ($xmlBody && isset($xmlBody['bundle']['offerItem']) && !empty($xmlBody['bundle']['offerItem']))
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="penaltiesHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#penalties" aria-expanded="false" aria-controls="penalties">
                                Penalties
                            </button>
                        </h2>
                        <div id="penalties" class="accordion-collapse collapse" aria-labelledby="penaltiesHeading" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                @foreach ($xmlBody['bundle']['offerItem'] as $offer)
                                    @if (isset($offer['fareDetail']['penalties']) && !empty($offer['fareDetail']['penalties']))
                                        @foreach ($offer['fareDetail']['penalties'] as $penalty)
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    {{ $penalty['arrival'] ?? 'N/A' }} to {{ $penalty['destination'] ?? 'N/A' }} ({{ $penalty['cabinType'] ?? 'N/A' }})
                                                </div>
                                                <div class="card-body">
                                                    <h6>Cancellation Fees</h6>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Prior to Departure:</strong> {{ isset($penalty['fareRules']['cancelFee']['Prior to Departure']['price']) ? $penalty['fareRules']['cancelFee']['Prior to Departure']['price']['code'] . ' ' . number_format($penalty['fareRules']['cancelFee']['Prior to Departure']['price']['amount'], 2) : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>No Show:</strong> {{ isset($penalty['fareRules']['cancelFee']['No Show']['price']) ? $penalty['fareRules']['cancelFee']['No Show']['price']['code'] . ' ' . number_format($penalty['fareRules']['cancelFee']['No Show']['price']['amount'], 2) : 'N/A' }}</li>
                                                    </ul>
                                                    <h6>Change Fees</h6>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Prior to Departure:</strong> {{ isset($penalty['fareRules']['changeFee']['Prior to Departure']['price']) ? $penalty['fareRules']['changeFee']['Prior to Departure']['price']['code'] . ' ' . number_format($penalty['fareRules']['changeFee']['Prior to Departure']['price']['amount'], 2) : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>After Departure:</strong> {{ isset($penalty['fareRules']['changeFee']['After Departure']['price']) ? $penalty['fareRules']['changeFee']['After Departure']['price']['code'] . ' ' . number_format($penalty['fareRules']['changeFee']['After Departure']['price']['amount'], 2) : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>No Show:</strong> {{ isset($penalty['fareRules']['changeFee']['No Show']['price']) ? $penalty['fareRules']['changeFee']['No Show']['price']['code'] . ' ' . number_format($penalty['fareRules']['changeFee']['No Show']['price']['amount'], 2) : 'N/A' }}</li>
                                                    </ul>
                                                    <h6>Refund Status</h6>
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Status:</strong> {{ $penalty['fareRules']['refundFee']['Status'] ?? 'N/A' }}</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p>No penalties available for this offer.</p>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">No penalties information available.</div>
                @endif
            </div>
        @elseif ($airline === 'flyjinnah')
        @php
            $airReservation = !empty($xmlBody['response']['Body']['OTA_AirBookRS']['AirReservation']) ? $xmlBody['response']['Body']['OTA_AirBookRS']['AirReservation'] : (!empty($xmlBody['Body']['OTA_AirBookRS']['AirReservation']) ? $xmlBody['Body']['OTA_AirBookRS']['AirReservation'] : null);
        @endphp
            @if ($bookingRequest && (is_array($xmlBody) || is_array($airReservation)))
                <div class="accordion" id="bookingAccordion">
                    <!-- General Booking Information -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="generalInfoHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generalInfo" aria-expanded="true" aria-controls="generalInfo">
                                General Booking Information
                            </button>
                        </h2>
                        <div id="generalInfo" class="accordion-collapse collapse show" aria-labelledby="generalInfoHeading" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>ID:</strong> {{ $bookingRequest->id ?? 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Airline:</strong> {{ $bookingRequest->airline ?? 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Ticket Limit:</strong> {{ !empty($bookingRequest->ticket_limit) ? \Carbon\Carbon::parse($bookingRequest->ticket_limit)->format('d M Y, H:i') : (!empty($airReservation['Ticketing']['@attributes']['TicketTimeLimit']) ? \Carbon\Carbon::parse($airReservation['Ticketing']['@attributes']['TicketTimeLimit'])->format('d M Y, H:i') : 'N/A') }}</li>
                                    <li class="list-group-item"><strong>Payment Limit:</strong> {{ !empty($bookingRequest->payment_limit) ? \Carbon\Carbon::parse($bookingRequest->payment_limit)->format('d M Y, H:i') : 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Status:</strong> {{ !empty($bookingRequest->status) ? ucfirst($bookingRequest->status) : (!empty($airReservation['Ticketing']['TicketAdvisory']) ? $airReservation['Ticketing']['TicketAdvisory'] : 'N/A') }}</li>
                                    <li class="list-group-item"><strong>Client ID:</strong> {{ $bookingRequest->client_id ?? 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Booking ID:</strong> {{ $bookingRequest->booking_id ?? 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Transaction ID:</strong> {{ !empty($xmlBody['transactionId']) ? $xmlBody['transactionId'] : (!empty($xmlBody['Body']['OTA_AirBookRS']['@attributes']['TransactionIdentifier']) ? $xmlBody['Body']['OTA_AirBookRS']['@attributes']['TransactionIdentifier'] : 'N/A') }}</li>
                                    <li class="list-group-item"><strong>Total Amount:</strong> {{ !empty($xmlBody['code']) && !empty($xmlBody['amount']) ? $xmlBody['code'] . ' ' . number_format($xmlBody['amount'], 2) : (!empty($airReservation['PriceInfo']['ItinTotalFare']['TotalFare']['@attributes']['CurrencyCode']) && !empty($airReservation['PriceInfo']['ItinTotalFare']['TotalFare']['@attributes']['Amount']) ? $airReservation['PriceInfo']['ItinTotalFare']['TotalFare']['@attributes']['CurrencyCode'] . ' ' . number_format($airReservation['PriceInfo']['ItinTotalFare']['TotalFare']['@attributes']['Amount'], 2) : 'N/A') }}</li>
                                    <li class="list-group-item"><strong>Message:</strong> {{ !empty($xmlBody['message']) ? $xmlBody['message'] : (!empty($airReservation['Ticketing']['TicketAdvisory']) ? $airReservation['Ticketing']['TicketAdvisory'] : 'N/A') }}</li>
                                    <li class="list-group-item"><strong>Booking Reference:</strong> {{ !empty($airReservation['BookingReferenceID']['@attributes']['ID']) ? $airReservation['BookingReferenceID']['@attributes']['ID'] : 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Created At:</strong> {{ !empty($bookingRequest->created_at) ? \Carbon\Carbon::parse($bookingRequest->created_at)->format('d M Y, H:i') : 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Updated At:</strong> {{ !empty($bookingRequest->updated_at) ? \Carbon\Carbon::parse($bookingRequest->updated_at)->format('d M Y, H:i') : 'N/A' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Information -->
                    @if (!empty($xmlBody['passengers']) && is_array($xmlBody['passengers']))
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="passengerInfoHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#passengerInfo" aria-expanded="false" aria-controls="passengerInfo">
                                    Passenger Information
                                </button>
                            </h2>
                            <div id="passengerInfo" class="accordion-collapse collapse" aria-labelledby="passengerInfoHeading" data-bs-parent="#bookingAccordion">
                                <div class="accordion-body">
                                    @foreach ($xmlBody['passengers'] as $index => $passenger)
                                        @if (is_array($passenger))
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    Passenger {{ !empty($passenger['ref_no']) ? $passenger['ref_no'] : 'Unknown' }} ({{ !empty($passenger['passenger_type']) ? $passenger['passenger_type'] : 'N/A' }})
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Name:</strong> {{ !empty($passenger['name']) ? $passenger['name'] : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Nationality:</strong> {{ !empty($passenger['nationality']) ? $passenger['nationality'] : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Phone Number:</strong> {{ !empty($passenger['phone_number']) ? $passenger['phone_number'] : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Reference Number:</strong> {{ !empty($passenger['ref_no']) ? $passenger['ref_no'] : 'N/A' }}</li>
                                                    </ul>
                                                    <!-- Seats -->
                                                    @if (!empty($passenger['seats']) && is_array($passenger['seats']))
                                                        <h6 class="mt-3">Seats</h6>
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($passenger['seats'] as $seat)
                                                                @if (is_array($seat))
                                                                    <li class="list-group-item">
                                                                        <strong>Seat:</strong> {{ !empty($seat['seat_number']) ? $seat['seat_number'] : 'N/A' }} 
                                                                        (Flight {{ !empty($seat['flight_number']) ? $seat['flight_number'] : 'N/A' }}, 
                                                                        {{ !empty($seat['departure_date']) ? \Carbon\Carbon::parse($seat['departure_date'])->format('d M Y, H:i') : 'N/A' }})
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No seat information available.</p>
                                                    @endif
                                                    <!-- Baggage -->
                                                    @if (!empty($passenger['baggage']) && is_array($passenger['baggage']))
                                                        <h6 class="mt-3">Baggage</h6>
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($passenger['baggage'] as $baggage)
                                                                @if (is_array($baggage))
                                                                    <li class="list-group-item">
                                                                        <strong>Baggage:</strong> {{ !empty($baggage['baggage_code']) ? $baggage['baggage_code'] : 'N/A' }} 
                                                                        (Flight {{ !empty($baggage['flight_number']) ? $baggage['flight_number'] : 'N/A' }}, 
                                                                        {{ !empty($baggage['departure_date']) ? \Carbon\Carbon::parse($baggage['departure_date'])->format('d M Y, H:i') : 'N/A' }})
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No baggage information available.</p>
                                                    @endif
                                                    <!-- Meals -->
                                                    @if (!empty($passenger['meals']) && is_array($passenger['meals']))
                                                        <h6 class="mt-3">Meals</h6>
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($passenger['meals'] as $meal)
                                                                @if (is_array($meal))
                                                                    <li class="list-group-item">
                                                                        <strong>Meal:</strong> {{ !empty($meal['meal_code']) ? $meal['meal_code'] : 'N/A' }} 
                                                                        (Quantity: {{ !empty($meal['meal_quantity']) ? $meal['meal_quantity'] : 'N/A' }}, 
                                                                        Flight {{ !empty($meal['flight_number']) ? $meal['flight_number'] : 'N/A' }}, 
                                                                        {{ !empty($meal['departure_date']) ? \Carbon\Carbon::parse($meal['departure_date'])->format('d M Y, H:i') : 'N/A' }})
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No meal information available.</p>
                                                    @endif
                                                    <!-- Tickets -->
                                                    @if (!empty($passenger['tickets']) && is_array($passenger['tickets']))
                                                        <h6 class="mt-3">Tickets</h6>
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($passenger['tickets'] as $ticket)
                                                                @if (is_array($ticket))
                                                                    <li class="list-group-item">
                                                                        <strong>Ticket:</strong> {{ !empty($ticket['e_ticket_no']) ? $ticket['e_ticket_no'] : 'N/A' }} 
                                                                        (Coupon: {{ !empty($ticket['coupon_no']) ? $ticket['coupon_no'] : 'N/A' }}, 
                                                                        Segment: {{ !empty($ticket['flight_segment']) ? $ticket['flight_segment'] : 'N/A' }}, 
                                                                        Status: {{ !empty($ticket['status']) ? $ticket['status'] : 'N/A' }})
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No ticket information available.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @elseif (!empty($airReservation['TravelerInfo']['AirTraveler']))
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="passengerInfoHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#passengerInfo" aria-expanded="false" aria-controls="passengerInfo">
                                    Passenger Information
                                </button>
                            </h2>
                            <div id="passengerInfo" class="accordion-collapse collapse" aria-labelledby="passengerInfoHeading" data-bs-parent="#bookingAccordion">
                                <div class="accordion-body">
                                    @php
                                        // Normalize AirTraveler to always be an array
                                        $travelers = is_array($airReservation['TravelerInfo']['AirTraveler']) && isset($airReservation['TravelerInfo']['AirTraveler'][0]) ? $airReservation['TravelerInfo']['AirTraveler'] : [$airReservation['TravelerInfo']['AirTraveler']];
                                    @endphp
                                    @foreach ($travelers as $index => $traveler)
                                        @if (is_array($traveler))
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    Passenger {{ !empty($traveler['TravelerRefNumber']['@attributes']['RPH']) ? $traveler['TravelerRefNumber']['@attributes']['RPH'] : 'Unknown' }} ({{ !empty($traveler['@attributes']['PassengerTypeCode']) ? $traveler['@attributes']['PassengerTypeCode'] : 'N/A' }})
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Name:</strong> {{ !empty($traveler['PersonName']['GivenName']) && !empty($traveler['PersonName']['Surname']) ? $traveler['PersonName']['GivenName'] . ' ' . $traveler['PersonName']['Surname'] : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Nationality:</strong> {{ !empty($traveler['Document']['@attributes']['DocHolderNationality']) ? $traveler['Document']['@attributes']['DocHolderNationality'] : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Phone Number:</strong> {{ !empty($traveler['Telephone']['@attributes']['PhoneNumber']) ? $traveler['Telephone']['@attributes']['PhoneNumber'] : 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Reference Number:</strong> {{ !empty($traveler['TravelerRefNumber']['@attributes']['RPH']) ? $traveler['TravelerRefNumber']['@attributes']['RPH'] : 'N/A' }}</li>
                                                    </ul>
                                                    <!-- Tickets -->
                                                    @if (!empty($traveler['ETicketInfo']) && is_array($traveler['ETicketInfo']) && !empty($traveler['ETicketInfo']['ETicketInformation']) && is_array($traveler['ETicketInfo']['ETicketInformation']))
                                                        <h6 class="mt-3">Tickets</h6>
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($traveler['ETicketInfo']['ETicketInformation'] as $ticket)
                                                                @if (is_array($ticket))
                                                                    <li class="list-group-item">
                                                                        <strong>Ticket:</strong> {{ !empty($ticket['@attributes']['eTicketNo']) ? $ticket['@attributes']['eTicketNo'] : 'N/A' }}
                                                                        (Coupon: {{ !empty($ticket['@attributes']['couponNo']) ? $ticket['@attributes']['couponNo'] : 'N/A' }},
                                                                        Segment: {{ !empty($ticket['@attributes']['flightSegmentCode']) ? $ticket['@attributes']['flightSegmentCode'] : 'N/A' }},
                                                                        Status: {{ !empty($ticket['@attributes']['status']) ? $ticket['@attributes']['status'] : 'N/A' }})
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>No ticket information available.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">No passenger information available.</div>
                    @endif

                    <!-- Flight Segments -->
                    @if (!empty($airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']) && is_array($airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']))
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="segmentsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#segments" aria-expanded="false" aria-controls="segments">
                                    Flight Segments
                                </button>
                            </h2>
                            <div id="segments" class="accordion-collapse collapse" aria-labelledby="segmentsHeading" data-bs-parent="#bookingAccordion">
                                <div class="accordion-body">
                                    @php
                                        $options = $airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'];
                                        $options = is_array($options) && isset($options[0]) ? $options : [$options];
                                    @endphp
                                    @foreach ($options as $index => $option)
                                        @if (!empty($option['FlightSegment']) && is_array($option['FlightSegment']))
                                            @foreach ($option['FlightSegment'] as $segment)
                                                @if (is_array($segment))
                                                    <div class="card mb-3">
                                                        <div class="card-header">
                                                            Segment: {{ !empty($segment['DepartureAirport']['@attributes']['LocationCode']) ? $segment['DepartureAirport']['@attributes']['LocationCode'] : 'N/A' }} to {{ !empty($segment['ArrivalAirport']['@attributes']['LocationCode']) ? $segment['ArrivalAirport']['@attributes']['LocationCode'] : 'N/A' }}
                                                        </div>
                                                        <div class="card-body">
                                                            <h6>Flight Details</h6>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item"><strong>Departure:</strong> {{ !empty($segment['Comment']) ? str_replace('airport_short_names:', '', $segment['Comment']) : 'N/A' }} ({{ !empty($segment['DepartureAirport']['@attributes']['LocationCode']) ? $segment['DepartureAirport']['@attributes']['LocationCode'] : 'N/A' }}) on {{ !empty($segment['@attributes']['DepartureDateTime']) ? \Carbon\Carbon::parse($segment['@attributes']['DepartureDateTime'])->format('d M Y') : 'N/A' }} at {{ !empty($segment['@attributes']['DepartureDateTime']) ? \Carbon\Carbon::parse($segment['@attributes']['DepartureDateTime'])->format('H:i') : 'N/A' }}</li>
                                                                <li class="list-group-item"><strong>Arrival:</strong> {{ !empty($segment['Comment']) ? str_replace('airport_short_names:', '', $segment['Comment']) : 'N/A' }} ({{ !empty($segment['ArrivalAirport']['@attributes']['LocationCode']) ? $segment['ArrivalAirport']['@attributes']['LocationCode'] : 'N/A' }}) on {{ !empty($segment['@attributes']['ArrivalDateTime']) ? \Carbon\Carbon::parse($segment['@attributes']['ArrivalDateTime'])->format('d M Y') : 'N/A' }} at {{ !empty($segment['@attributes']['ArrivalDateTime']) ? \Carbon\Carbon::parse($segment['@attributes']['ArrivalDateTime'])->format('H:i') : 'N/A' }}</li>
                                                                <li class="list-group-item"><strong>Terminal:</strong> Departure - {{ !empty($segment['DepartureAirport']['@attributes']['Terminal']) ? $segment['DepartureAirport']['@attributes']['Terminal'] : 'N/A' }}, Arrival - {{ !empty($segment['ArrivalAirport']['@attributes']['Terminal']) ? $segment['ArrivalAirport']['@attributes']['Terminal'] : 'N/A' }}</li>
                                                                <li class="list-group-item"><strong>Duration:</strong> {{ !empty($segment['@attributes']['DepartureDateTime']) && !empty($segment['@attributes']['ArrivalDateTime']) ? \Carbon\Carbon::parse($segment['@attributes']['DepartureDateTime'])->diffInMinutes(\Carbon\Carbon::parse($segment['@attributes']['ArrivalDateTime'])) . ' minutes' : 'N/A' }}</li>
                                                                <li class="list-group-item"><strong>Flight Number:</strong> {{ !empty($segment['@attributes']['FlightNumber']) ? $segment['@attributes']['FlightNumber'] : 'N/A' }}</li>
                                                                <li class="list-group-item"><strong>Cabin Class:</strong> {{ !empty($segment['@attributes']['ResCabinClass']) ? $segment['@attributes']['ResCabinClass'] : 'N/A' }}</li>
                                                                <li class="list-group-item"><strong>Status:</strong> {{ !empty($segment['@attributes']['Status']) ? $segment['@attributes']['Status'] : 'N/A' }}</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">No flight segments available.</div>
                    @endif

                    <!-- Pricing Information -->
                    @if (!empty($airReservation['PriceInfo']['ItinTotalFare']) && is_array($airReservation['PriceInfo']['ItinTotalFare']))
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="pricingInfoHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingInfo" aria-expanded="false" aria-controls="pricingInfo">
                                    Pricing Information
                                </button>
                            </h2>
                            <div id="pricingInfo" class="accordion-collapse collapse" aria-labelledby="pricingInfoHeading" data-bs-parent="#bookingAccordion">
                                <div class="accordion-body">
                                    @php
                                        $priceInfo = $airReservation['PriceInfo']['ItinTotalFare'];
                                    @endphp
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            Pricing Details
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Base Fare:</strong> {{ !empty($priceInfo['BaseFare']['@attributes']['CurrencyCode']) && !empty($priceInfo['BaseFare']['@attributes']['Amount']) ? $priceInfo['BaseFare']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['BaseFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Equivalent Base Fare:</strong> {{ !empty($priceInfo['EquiBaseFare']['@attributes']['CurrencyCode']) && !empty($priceInfo['EquiBaseFare']['@attributes']['Amount']) ? $priceInfo['EquiBaseFare']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['EquiBaseFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Taxes:</strong> {{ !empty($priceInfo['Taxes']['Tax']['@attributes']['CurrencyCode']) && !empty($priceInfo['Taxes']['Tax']['@attributes']['Amount']) ? $priceInfo['Taxes']['Tax']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['Taxes']['Tax']['@attributes']['Amount'], 2) . ' (' . $priceInfo['Taxes']['Tax']['@attributes']['TaxCode'] . ')' : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Fees:</strong> {{ !empty($priceInfo['Fees']['Fee']['@attributes']['CurrencyCode']) && !empty($priceInfo['Fees']['Fee']['@attributes']['Amount']) ? $priceInfo['Fees']['Fee']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['Fees']['Fee']['@attributes']['Amount'], 2) . ' (' . $priceInfo['Fees']['Fee']['@attributes']['FeeCode'] . ')' : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Total Fare:</strong> {{ !empty($priceInfo['TotalFare']['@attributes']['CurrencyCode']) && !empty($priceInfo['TotalFare']['@attributes']['Amount']) ? $priceInfo['TotalFare']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['TotalFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Equivalent Total Fare:</strong> {{ !empty($priceInfo['TotalEquivFare']['@attributes']['CurrencyCode']) && !empty($priceInfo['TotalEquivFare']['@attributes']['Amount']) ? $priceInfo['TotalEquivFare']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['TotalEquivFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Total Fare with CC Fee:</strong> {{ !empty($priceInfo['TotalFareWithCCFee']['@attributes']['CurrencyCode']) && !empty($priceInfo['TotalFareWithCCFee']['@attributes']['Amount']) ? $priceInfo['TotalFareWithCCFee']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['TotalFareWithCCFee']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                <li class="list-group-item"><strong>Equivalent Total Fare with CC Fee:</strong> {{ !empty($priceInfo['TotalEquivFareWithCCFee']['@attributes']['CurrencyCode']) && !empty($priceInfo['TotalEquivFareWithCCFee']['@attributes']['Amount']) ? $priceInfo['TotalEquivFareWithCCFee']['@attributes']['CurrencyCode'] . ' ' . number_format($priceInfo['TotalEquivFareWithCCFee']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                            </ul>
                                            <!-- Detailed Tax Breakdown -->
                                            @if (!empty($airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown']) && is_array($airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown']))
                                                @php
                                                    $ptcFare = is_array($airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown']) && isset($airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'][0]) ? $airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'] : [$airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown']];
                                                @endphp
                                                <h6 class="mt-3">Fare Breakdown</h6>
                                                @foreach ($ptcFare as $fare)
                                                    @if (is_array($fare) && !empty($fare['PassengerTypeQuantity']['@attributes']))
                                                        <div class="card mb-3">
                                                            <div class="card-header">
                                                                Passenger Type: {{ !empty($fare['PassengerTypeQuantity']['@attributes']['Code']) ? $fare['PassengerTypeQuantity']['@attributes']['Code'] : 'N/A' }} (Quantity: {{ !empty($fare['PassengerTypeQuantity']['@attributes']['Quantity']) ? $fare['PassengerTypeQuantity']['@attributes']['Quantity'] : 'N/A' }})
                                                            </div>
                                                            <div class="card-body">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item"><strong>Fare Basis Code:</strong> {{ !empty($fare['FareBasisCodes']['FareBasisCode']) ? $fare['FareBasisCodes']['FareBasisCode'] : 'N/A' }}</li>
                                                                    <li class="list-group-item"><strong>Base Fare:</strong> {{ !empty($fare['PassengerFare']['BaseFare']['@attributes']['CurrencyCode']) && !empty($fare['PassengerFare']['BaseFare']['@attributes']['Amount']) ? $fare['PassengerFare']['BaseFare']['@attributes']['CurrencyCode'] . ' ' . number_format($fare['PassengerFare']['BaseFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                                    <li class="list-group-item"><strong>Equivalent Base Fare:</strong> {{ !empty($fare['PassengerFare']['EquiBaseFare']['@attributes']['CurrencyCode']) && !empty($fare['PassengerFare']['EquiBaseFare']['@attributes']['Amount']) ? $fare['PassengerFare']['EquiBaseFare']['@attributes']['CurrencyCode'] . ' ' . number_format($fare['PassengerFare']['EquiBaseFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                                    <li class="list-group-item"><strong>Total Fare:</strong> {{ !empty($fare['PassengerFare']['TotalFare']['@attributes']['CurrencyCode']) && !empty($fare['PassengerFare']['TotalFare']['@attributes']['Amount']) ? $fare['PassengerFare']['TotalFare']['@attributes']['CurrencyCode'] . ' ' . number_format($fare['PassengerFare']['TotalFare']['@attributes']['Amount'], 2) : 'N/A' }}</li>
                                                                </ul>
                                                                @if (!empty($fare['PassengerFare']['Taxes']['Tax']) && is_array($fare['PassengerFare']['Taxes']['Tax']))
                                                                    <h6 class="mt-3">Taxes</h6>
                                                                    <ul class="list-group list-group-flush">
                                                                        @foreach ($fare['PassengerFare']['Taxes']['Tax'] as $tax)
                                                                            @if (is_array($tax))
                                                                                <li class="list-group-item">
                                                                                    <strong>{{ !empty($tax['@attributes']['TaxName']) ? $tax['@attributes']['TaxName'] : 'Tax' }}:</strong> 
                                                                                    {{ !empty($tax['@attributes']['CurrencyCode']) && !empty($tax['@attributes']['Amount']) ? $tax['@attributes']['CurrencyCode'] . ' ' . number_format($tax['@attributes']['Amount'], 2) : 'N/A' }} 
                                                                                    ({{ !empty($tax['@attributes']['TaxCode']) ? $tax['@attributes']['TaxCode'] : 'N/A' }})
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                                @if (!empty($fare['PassengerFare']['Fees']['Fee']) && is_array($fare['PassengerFare']['Fees']['Fee']))
                                                                    <h6 class="mt-3">Fees</h6>
                                                                    <ul class="list-group list-group-flush">
                                                                        @foreach ($fare['PassengerFare']['Fees']['Fee'] as $fee)
                                                                            <li class="list-group-item"><strong>Fee:</strong> {{ $fee }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                            <!-- Payment Details -->
                                            @if (!empty($airReservation['Fulfillment']['PaymentDetails']['PaymentDetail']) && is_array($airReservation['Fulfillment']['PaymentDetails']['PaymentDetail']))
                                                <h6 class="mt-3">Payment Details</h6>
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($airReservation['Fulfillment']['PaymentDetails']['PaymentDetail'] as $payment)
                                                        @if (is_array($payment))
                                                            <li class="list-group-item">
                                                                <strong>Payment:</strong> {{ !empty($payment['PaymentAmount']['@attributes']['CurrencyCode']) && !empty($payment['PaymentAmount']['@attributes']['Amount']) ? $payment['PaymentAmount']['@attributes']['CurrencyCode'] . ' ' . number_format($payment['PaymentAmount']['@attributes']['Amount'], 2) : 'N/A' }} 
                                                                ({{ !empty($payment['PaymentAmountInPayCur']['@attributes']['CurrencyCode']) && !empty($payment['PaymentAmountInPayCur']['@attributes']['Amount']) ? $payment['PaymentAmountInPayCur']['@attributes']['CurrencyCode'] . ' ' . number_format($payment['PaymentAmountInPayCur']['@attributes']['Amount'], 2) : 'N/A' }})
                                                                @if (!empty($payment['DirectBill']['CompanyName']))
                                                                    <br><strong>Company:</strong> {{ $payment['DirectBill']['CompanyName'] }}
                                                                @endif
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>No payment details available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">No pricing information available.</div>
                    @endif

                    <!-- Penalties -->
                    @if (!empty($airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']) && is_array($airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption']))
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="penaltiesHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#penalties" aria-expanded="false" aria-controls="penalties">
                                    Penalties
                                </button>
                            </h2>
                            <div id="penalties" class="accordion-collapse collapse" aria-labelledby="penaltiesHeading" data-bs-parent="#bookingAccordion">
                                <div class="accordion-body">
                                    @php
                                        $hasPenalties = false;
                                        $options = $airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'];
                                        $options = is_array($options) && isset($options[0]) ? $options : [$options];
                                    @endphp
                                    @foreach ($options as $index => $option)
                                        @if (!empty($option['FlightSegment']) && is_array($option['FlightSegment']))
                                            @foreach ($option['FlightSegment'] as $segment)
                                                @if (is_array($segment) && !empty($segment['AvailableFlexiOperations']['FlexiOperations']) && is_array($segment['AvailableFlexiOperations']['FlexiOperations']))
                                                    @php $hasPenalties = true; @endphp
                                                    <div class="card mb-3">
                                                        <div class="card-header">
                                                            Segment: {{ !empty($segment['DepartureAirport']['@attributes']['LocationCode']) ? $segment['DepartureAirport']['@attributes']['LocationCode'] : 'N/A' }} to {{ !empty($segment['ArrivalAirport']['@attributes']['LocationCode']) ? $segment['ArrivalAirport']['@attributes']['LocationCode'] : 'N/A' }}
                                                        </div>
                                                        <div class="card-body">
                                                            <h6>Flexi Operations</h6>
                                                            <ul class="list-group list-group-flush">
                                                                @foreach ($segment['AvailableFlexiOperations']['FlexiOperations'] as $operation)
                                                                    @if (is_array($operation) && !empty($operation['@attributes']))
                                                                        <li class="list-group-item">
                                                                            <strong>{{ !empty($operation['@attributes']['AllowedOperationName']) ? $operation['@attributes']['AllowedOperationName'] : 'N/A' }}:</strong> 
                                                                            Allowed {{ !empty($operation['@attributes']['NumberOfAllowedOperations']) ? $operation['@attributes']['NumberOfAllowedOperations'] : 'N/A' }} time(s), 
                                                                            Cutoff: {{ !empty($operation['@attributes']['FlexiOperationCutoverTimeInMinutes']) ? $operation['@attributes']['FlexiOperationCutoverTimeInMinutes'] . ' minutes' : 'N/A' }}
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                    @if (!$hasPenalties)
                                        <p>No penalty information available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">No penalties information available.</div>
                    @endif
                </div>
            @endif
        @endif
    @else
        <div class="alert alert-danger">No booking request data available.</div>
    @endif
</div>