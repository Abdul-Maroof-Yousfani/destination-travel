<?php

namespace App\Services;

use App\Models\Flight;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Segment;
use App\Models\Ancillary;
use App\Models\Passenger;
use App\Models\BookingItem;
use Illuminate\Support\Carbon;
use App\Models\BookingRequestBody;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlightBookingService
{
    public function handleBookingEmi(array $response, int $clientId, $cabinClass): array
    {
        $segments = $response['segments'] ?? [];
        $bundle = $response['bundle'] ?? [];
        $responsePassengers = $response['passengers'] ?? [];
        $isOneWay = count($segments) === 1;
        $tax = config('variables.tax') ?? 400;
        $taxCode = config('variables.tax_code') ?? 'PKR';
        
        $paxCount = [
            'adults' => collect($responsePassengers)->where('type', 'ADT')->count(),
            'children' => collect($responsePassengers)->where('type', 'CNN')->count(),
            'infant' => collect($responsePassengers)->where('type', 'INF')->count(),
        ];
        
        $flightsCreated = [];

        DB::beginTransaction();

        try {
            $order = $bundle['offerID'] ?? [];
            $bookingReferences = $bundle['bookingReferences'] ?? [];
            $timeLimits = $bundle['timeLimits'] ?? [];
            $passengers = [];
            foreach ($responsePassengers as $passenger) {
                $apiName = strtolower(preg_replace('/\s+/', '', $passenger['givenName']));
                $dob = $passenger['birthdate'];
                $existingPassenger = Passenger::get()
                    ->filter(function ($p) use ($apiName, $dob) {
                        $dbName = strtolower(preg_replace('/\s+/', '', $p->given_name));
                        return $dbName === $apiName && $p->dob->format('Y-m-d') === $dob;
                    })
                    ->first();
                if ($existingPassenger) {
                    $existingPassenger->update([
                        'passenger_reference' => $passenger['id'],
                        'type' => $passenger['type'],
                    ]);
                    $passengers[] = $existingPassenger;
                }
            }


            // Create Booking first
            $booking = Booking::create([
                'client_id'         => $clientId,
                'passenger_details' => json_encode($passengers),
                'order_id'          => $order['OrderID'] ?? null,
                'order_owner'       => $order['Owner'] ?? null,
                'is_oneway'         => $isOneWay,
                'flight_booking_id' => $bookingReferences['bookingId'] ?? null,
                'ticket_limit'      => Carbon::parse($timeLimits['ticketingTimeLimit'] ?? null),
                'payment_limit'     => Carbon::parse($timeLimits['paymentTimeLimit'] ?? null),
                'airline_id'        => $bookingReferences['airlineID'] ?? null,
                'airline'           => $bookingReferences['airline'] ?? null,
                'transaction_id'    => $response['transactionId'] ?? '-',
                'price_code'        => $bundle['totalPrice']['code'] ?? null,
                'price'             => $bundle['totalPrice']['amount'] ?? 0,
                'tax'               => $tax,
                'tax_code'          => $taxCode,
                'status'            => Booking::STATUS_INITIAL,
            ]);
            if (!empty($bundle)) {
                foreach ($bundle['offerItem'] as $offerItem) {
                    $bookingItem = BookingItem::create([
                        'passenger_ref' => $offerItem['fareDetail']['passengerRef']['value'] ?? null,
                        'passenger_code' => $offerItem['fareDetail']['passengers'] ?? null,
                        'services' => json_encode($offerItem['services'] ?? []),
                        'taxes' => json_encode($offerItem['fareDetail']['taxes'] ?? []),
                        'price' => $offerItem['totalPrice']['amount'] ?? 0,
                        'price_code' => $offerItem['totalPrice']['code'] ?? null,
                        'booking_id' => $booking->id,
                    ]);
                    $penalties = collect($offerItem['fareDetail']['penalties'] ?? [])->map(function ($penalty) {
                        return [
                            'arrival' => $penalty['arrival'] ?? null,
                            'destination' => $penalty['destination'] ?? null,
                            'cabin_type' => $penalty['cabinType'] ?? null,
                            'cancel_fee' => $penalty['fareRules']['cancelFee'] ?? [],
                            'change_fee' => $penalty['fareRules']['changeFee'] ?? [],
                            'refund_fee' => $penalty['fareRules']['refundFee'] ?? [],
                        ];
                    })->toArray();

                    if (!empty($penalties)) $bookingItem->penalties()->createMany($penalties);
                }
            }

            foreach ($segments as $index => $segment) {
                $flightsInSegment = $segment['flights'] ?? [];

                $isConnected = isset($flightsInSegment['secondFlight']) && !empty($flightsInSegment['secondFlight']);
                // $isOneWay = count($segments) === 1;

                $departureFlight = $flightsInSegment;
                $connectingFlight = $flightsInSegment['secondFlight'] ?? null;

                // Determine overall route
                $departureCode = $departureFlight['Departure']['AirportCode']['value'];
                $arrivalCode = $isConnected
                    ? $connectingFlight['arrival']['AirportCode']['value']
                    : $departureFlight['Arrival']['AirportCode']['value'];

                $departureDate = Carbon::parse($departureFlight['Departure']['Date']['value'] . ' ' . $departureFlight['Departure']['Time']['value']);
                $arrivalDate = $isConnected
                    ? Carbon::parse($connectingFlight['arrival']['Date']['value'] . ' ' . $connectingFlight['arrival']['Time']['value'])
                    : Carbon::parse($departureFlight['Arrival']['Date']['value'] . ' ' . $departureFlight['Arrival']['Time']['value']);

                $segmentArrivalCode = $isConnected
                    ? $connectingFlight['departure']['AirportCode']['value']
                    : $departureFlight['Arrival']['AirportCode']['value'];

                $flight = Flight::create([
                    'airline'        => $departureFlight['flightDetails']['marketingCarrier']['Name']['value'] ?? null,
                    'departure_code' => $departureCode,
                    'arrival_code'   => $arrivalCode,
                    'departure_date' => $departureDate,
                    'arrival_date'   => $arrivalDate ?? null,
                    // 'is_oneway'      => $isOneWay,
                    'is_connected'   => $isConnected,
                    'pax_count'      => $paxCount,
                    'cabin_class'    => $cabinClass,
                    'price'          => $departureFlight['price']['amount'],
                    'price_code'     => $departureFlight['price']['code'],
                    'client_id'      => $clientId,
                    'booking_id'     => $booking->id ?? null,
                ]);

                // Add first segment
                Segment::create([
                    'flight_id'      => $flight->id,
                    'departure_code' => $departureFlight['Departure']['AirportCode']['value'],
                    'arrival_code'   => $segmentArrivalCode,
                    'departure_date' => $departureDate,
                    'flight_duration'=> $departureFlight['flightDetails']['details']['FlightDuration']['Value']['value'] ?? null,
                    'arrival_date'   => Carbon::parse($departureFlight['Arrival']['Date']['value'] . ' ' . $departureFlight['Arrival']['Time']['value']),
                    'flight_number'  => $departureFlight['flightDetails']['marketingCarrier']['FlightNumber']['value'],
                    'direction'      => $index === 0 ? 'outbound' : 'return',
                    // 'price'          => $departureFlight['price']['amount'],
                    // 'price_code'     => $departureFlight['price']['code'],
                ]);

                // Add second segment if connected
                if ($isConnected) {
                    Segment::create([
                        'flight_id'      => $flight->id,
                        'departure_code' => $connectingFlight['departure']['AirportCode']['value'],
                        'arrival_code'   => $connectingFlight['arrival']['AirportCode']['value'],
                        'departure_date' => Carbon::parse($connectingFlight['departure']['Date']['value'] . ' ' . $connectingFlight['departure']['Time']['value']),
                        'flight_duration'=> $connectingFlight['details']['FlightDuration']['Value']['value'] ?? null,
                        'arrival_date'   => $arrivalDate,
                        'flight_number'  => $connectingFlight['marketingCarrier']['FlightNumber']['value'],
                        'direction'      => $index === 0 ? 'outbound' : 'return',
                        // 'price'          => $departureFlight['price']['amount'], // same price
                        // 'price_code'     => $departureFlight['price']['code'],
                    ]);
                }

                $flightsCreated[] = $flight;
            }
            BookingRequestBody::create([
                'booking_id' => $booking->id,
                'airline' => $booking->airline,
                'xml_body' => json_encode($response),
                'client_id' => $clientId,
                'ticket_limit' => $booking->ticket_limit,
                'payment_limit' => $booking->payment_limit,
            ]);

            DB::commit();
            $booking->load('bookingItems.penalties', 'client');
            return [
                'message' => 'Flight booked successfully. Please complete payment before the deadline, otherwise it will be canceled.',
                // 'flights' => $flightsCreated,
                'booking' => $booking
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flight/Segment creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateBookingFieldsEmi(array $response, int $bookingId): Booking
    {
        $bundle              = $response['bundle'] ?? [];
        $order               = $bundle['offerID'] ?? [];
        $bookingReferences   = $bundle['bookingReferences'] ?? [];
        $timeLimits          = $bundle['timeLimits'] ?? [];
        $segments            = $response['segments'] ?? [];
        $isOneWay            = count($segments) === 1;
        $booking             = Booking::findOrFail($bookingId);

        $ticketTimeLimit = isset($timeLimits['ticketingTimeLimit']) ? Carbon::parse($timeLimits['ticketingTimeLimit']) : $booking->ticket_limit;
        $paymentTimeLimit = isset($timeLimits['paymentTimeLimit']) ? Carbon::parse($timeLimits['paymentTimeLimit']) : $booking->payment_limit;

        try {
            $booking->update([
                'is_oneway'         => $isOneWay,
                'order_id'          => $order['OrderID'] ?? null,
                'order_owner'       => $order['Owner'] ?? null,
                'flight_booking_id' => $bookingReferences['bookingId'] ?? null,
                'ticket_limit'      => $ticketTimeLimit,
                'payment_limit'     => $paymentTimeLimit,
                'airline_id'        => $bookingReferences['airlineID'] ?? null,
                'airline'           => $bookingReferences['airline'] ?? null,
                'transaction_id'    => $response['transactionId'] ?? $booking->transaction_id,
                'price_code'        => data_get($bundle, 'totalPrice.code', $booking->price_code),
                'price'             => data_get($bundle, 'totalPrice.amount', $booking->price),
                'status'            => $booking->status !== Booking::STATUS_ISSUED ? Booking::STATUS_CHANGED : $booking->status,
            ]);
            if ($booking->bookingRequest) {
                $booking->bookingRequest()->update([
                    'status' => 'change',
                    'ticket_limit' => $ticketTimeLimit,
                    'payment_limit' => $paymentTimeLimit,
                    'xml_body' => json_encode($response ?? []),
                ]);
            }

            return $booking->fresh();
        } catch (\Throwable $e) {
            Log::error('Booking table update failed: '.$e->getMessage(), ['booking_id' => $bookingId]);
            throw $e;
        }
    }
    public function issueTicketsEmi(array $data, int $bookingId): Booking
    {
        $booking = Booking::findOrFail($bookingId);
        DB::beginTransaction();
        try {
            $ticketTimeLimit = $data['bundle']['timeLimits']['ticketingTimeLimit'] ?? $booking->ticket_limit;
            $paymentTimeLimit = $data['bundle']['timeLimits']['paymentTimeLimit'] ?? $booking->payment_limit;
            // ----------------------------------------- Update Booking -----------------------------------------
            $booking->update([
                'status' => Booking::STATUS_ISSUED,
                'only_search' => false,
                'ticket_limit' => $ticketTimeLimit,
                'payment_limit' => $paymentTimeLimit,
            ]);
            // ----------------------------------------- Update Booking Request Body -----------------------------------------
            if ($booking->bookingRequest) {
                $booking->bookingRequest()->update([
                    'status' => 'change',
                    'ticket_limit' => $ticketTimeLimit,
                    'payment_limit' => $paymentTimeLimit,
                    'xml_body' => json_encode($data ?? []),
                ]);
            }
            // ----------------------------------------- Create Tickets -----------------------------------------
            $tickets = [];
            foreach ($data['ticketInfos'] ?? [] as $ticketInfo) {
                $tickets[] = [
                    'airline' => $ticketInfo['issuingAirlineInfo']['airline'] ?? null,
                    'passenger_reference' => $ticketInfo['passengerReference'] ?? null,
                    'place' => $ticketInfo['issuingAirlineInfo']['place'] ?? null,
                    'ticket_no' => $ticketInfo['ticketDocument']['ticketDocNbr'] ?? null,
                    'type' => $ticketInfo['ticketDocument']['type'] ?? null,
                    'issue_date' => isset($ticketInfo['ticketDocument']['dateOfIssue'], $ticketInfo['ticketDocument']['timeOfIssue'])
                        ? Carbon::parse($ticketInfo['ticketDocument']['dateOfIssue'] . ' ' . $ticketInfo['ticketDocument']['timeOfIssue'])
                        : now(),
                    'price_code' => $ticketInfo['price']['total']['code'] ?? null,
                    'price' => $ticketInfo['price']['total']['value'] ?? null,
                    'price_reference' => $ticketInfo['price']['refs'] ?? null,
                    'ticket_details' => json_encode($ticketInfo),
                    'client_id' => $booking->client_id,
                    'booking_id' => $booking->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if (!empty($tickets)) Ticket::insert($tickets);
            return $booking->load('bookingItems.penalties', 'client', 'tickets');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flight/Segment creation failed for Emirates: ' . $e->getMessage());
            throw $e;
        }
    }


    // --------------------------------------------------------------FLYJINNAH--------------------------------------------------------------
    
    public function handleBookingFJ(array $response, int $clientId): array
    {
        $otaAirBookRS = $response['Body']['OTA_AirBookRS'] ?? [];
        $airReservation = $otaAirBookRS['AirReservation'] ?? [];
        $itinerary = $airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'] ?? [];
        $airTravelers = $airReservation['TravelerInfo']['AirTraveler'] ?? [];
        $itinerary = isset($itinerary[0]) ? $itinerary : [$itinerary];
        $isOneWay = count($itinerary) === 1;
        $tax = config('variables.tax') ?? 400;
        $taxCode = config('variables.tax_code') ?? 'PKR';

        $paxCount = [
            'adults' => 0,
            'children' => 0,
            'infant' => 0,
        ];
        if (isset($airReservation['TPA_Extensions']['AAAirReservationExt']['ResSummary']['PTCCounts']['PTCCount'])) {
            $ptcCounts = $airReservation['TPA_Extensions']['AAAirReservationExt']['ResSummary']['PTCCounts']['PTCCount'];
            $ptcCounts = isset($ptcCounts[0]) ? $ptcCounts : [$ptcCounts];
            foreach ($ptcCounts as $count) {
                if (!is_array($count)) continue;
                $code = $count['PassengerTypeCode'] ?? '';
                $qty = (int) ($count['PassengerTypeQuantity'] ?? 0);
                if ($code === 'ADT') {
                    $paxCount['adults'] = $qty;
                } elseif (in_array($code, ['CHD', 'CNN'])) {
                    $paxCount['children'] = $qty;
                } elseif ($code === 'INF') {
                    $paxCount['infant'] = $qty;
                }
            }
        }
        $flightsCreated = [];

        DB::beginTransaction();

        try {
            // Handle passengers
            $passengers = [];
            $airTravelers = isset($airTravelers[0]) ? $airTravelers : [$airTravelers];
            foreach ($airTravelers as $passenger) {
                if (!is_array($passenger)) continue;
                $givenName = $passenger['PersonName']['GivenName'] ?? '';
                // $dob = null; // No DOB in FlyJinnah response
                $apiName = strtolower(preg_replace('/\s+/', '', $givenName));
                $existingPassenger = Passenger::get()
                    ->filter(function ($p) use ($apiName) {
                        $dbName = strtolower(preg_replace('/\s+/', '', $p->given_name));
                        return $dbName === $apiName;
                    })
                    ->first();
                if ($existingPassenger) {
                    $existingPassenger->update([
                        'passenger_reference' => $passenger['TravelerRefNumber']['@attributes']['RPH'] ?? null,
                        'type' => $passenger['@attributes']['PassengerTypeCode'] ?? null,
                    ]);
                    $passengers[] = $existingPassenger;
                }
            }

            // Extract booking details
            $bookingRef = $airReservation['BookingReferenceID']['@attributes'] ?? [];
            $ticketingAttrs = $airReservation['Ticketing']['@attributes'] ?? [];
            $priceInfo = $airReservation['PriceInfo']['ItinTotalFare'] ?? [];
            $totalPriceAttrs = $priceInfo['TotalFare']['@attributes'] ?? [];
            $transactionId = $otaAirBookRS['@attributes']['TransactionIdentifier'] ?? '-';
            $ticketLimit = $ticketingAttrs['TicketTimeLimit'] ?? null;
            $paymentLimit = $ticketingAttrs['TicketTimeLimit'] ?? null; // Assuming same for payment and ticketing
            // $airlineId = 'G9'; // From flight codes
            $airline = 'FlyJinnah';

            // Create Booking
            $booking = Booking::create([
                'client_id'         => $clientId,
                'passenger_details' => json_encode($passengers),
                'order_id'          => $bookingRef['ID'] ?? null,
                'order_owner'       => null,
                'is_oneway'         => $isOneWay,
                'flight_booking_id' => null,
                'ticket_limit'      => $ticketLimit ? Carbon::parse($ticketLimit) : null,
                'payment_limit'     => $paymentLimit ? Carbon::parse($paymentLimit) : null,
                'airline_id'        => null,
                'airline'           => $airline,
                'transaction_id'    => $transactionId,
                'price_code'        => $totalPriceAttrs['CurrencyCode'] ?? null,
                'price'             => $totalPriceAttrs['Amount'] ?? 0,
                'tax'               => $tax,
                'tax_code'          => $taxCode,
                'status'            => Booking::STATUS_INITIAL,
            ]);

            // Create BookingItems from PTC_FareBreakdowns
            $ptcFareBreakdowns = $airReservation['PriceInfo']['PTC_FareBreakdowns']['PTC_FareBreakdown'] ?? [];
            $ptcFareBreakdowns = isset($ptcFareBreakdowns[0]) ? $ptcFareBreakdowns : [$ptcFareBreakdowns];
            foreach ($ptcFareBreakdowns as $breakdown) {
                if (!is_array($breakdown)) continue;
                $travelerRef = $breakdown['TravelerRefNumber']['@attributes']['RPH'] ?? null;
                $passengerTypeAttrs = $breakdown['PassengerTypeQuantity']['@attributes'] ?? [];
                $passengerFare = $breakdown['PassengerFare'] ?? [];
                $passengerTotalAttrs = $passengerFare['TotalFare']['@attributes'] ?? [];
                $services = $passengerFare['Fees']['Fee'] ?? [];
                $taxes = $passengerFare['Taxes']['Tax'] ?? [];
                if (!is_array($services)) {
                    $services = [$services];
                }
                if (!is_array($taxes)) {
                    $taxes = [$taxes];
                }
                $bookingItem = BookingItem::create([
                    'passenger_ref' => $travelerRef,
                    'passenger_code' => $passengerTypeAttrs['Code'] ?? null,
                    'services' => json_encode(array_filter($services)),
                    'taxes' => json_encode(array_filter($taxes)),
                    'price' => $passengerTotalAttrs['Amount'] ?? 0,
                    'price_code' => $passengerTotalAttrs['CurrencyCode'] ?? null,
                    'booking_id' => $booking->id,
                ]);

                // No penalties
            }

            // Create Flights and Segments
            foreach ($itinerary as $index => $odo) {
                if (!is_array($odo)) continue;
                $flightSegments = $odo['FlightSegment'] ?? [];
                $flightSegments = isset($flightSegments[0]) ? $flightSegments : [$flightSegments];
                if (isset($flightSegments['@attributes'])) {
                    // Single segment, wrap
                    $flightSegments = [$flightSegments];
                }
                $numSegments = count($flightSegments);
                if ($numSegments === 0) continue;
                $isConnected = $numSegments > 1;

                // Determine overall route and times
                $firstSegment = $flightSegments[0] ?? [];
                $lastSegment = $flightSegments[$numSegments - 1] ?? [];
                $departureCode = $firstSegment['DepartureAirport']['@attributes']['LocationCode'] ?? null;
                $cabinClass = $firstSegment['@attributes']['ResCabinClass'] ?? null;
                $arrivalCode = $lastSegment['ArrivalAirport']['@attributes']['LocationCode'] ?? null;
                $departureDate = Carbon::parse($firstSegment['@attributes']['DepartureDateTime'] ?? null);
                $arrivalDate = Carbon::parse($lastSegment['@attributes']['ArrivalDateTime'] ?? null);

                $flight = Flight::create([
                    'airline'        => $airline,
                    'departure_code' => $departureCode,
                    'arrival_code'   => $arrivalCode,
                    'departure_date' => $departureDate,
                    'arrival_date'   => $arrivalDate,
                    'is_connected'   => $isConnected,
                    'pax_count'      => $paxCount,
                    'cabin_class'    => $cabinClass,
                    'price'          => 0,
                    'price_code'     => 'PKR',
                    'client_id'      => $clientId,
                    'booking_id'     => $booking->id,
                ]);

                // Create segments
                foreach ($flightSegments as $seg) {
                    if (!is_array($seg)) continue;
                    $segAttrs = $seg['@attributes'] ?? [];
                    $depAirport = $seg['DepartureAirport']['@attributes'] ?? [];
                    $arrAirport = $seg['ArrivalAirport']['@attributes'] ?? [];
                    $segDepartureDate = Carbon::parse($segAttrs['DepartureDateTime'] ?? null);
                    $segArrivalDate = Carbon::parse($segAttrs['ArrivalDateTime'] ?? null);
                    $flightDuration = $segDepartureDate->diff($segArrivalDate)->format('%Hh %Im');

                    Segment::create([
                        'flight_id'      => $flight->id,
                        'departure_code' => $depAirport['LocationCode'] ?? null,
                        'arrival_code'   => $arrAirport['LocationCode'] ?? null,
                        'departure_date' => $segDepartureDate,
                        'flight_duration'=> $flightDuration,
                        'arrival_date'   => $segArrivalDate,
                        'flight_number'  => $segAttrs['FlightNumber'] ?? null,
                        'direction'      => $index === 0 ? 'outbound' : 'return',
                    ]);
                }

                $flightsCreated[] = $flight;
            }

            BookingRequestBody::create([
                'booking_id' => $booking->id,
                'airline' => $airline,
                'xml_body' => json_encode($response),
                'client_id' => $clientId,
                'ticket_limit' => $booking->ticket_limit,
                'payment_limit' => $booking->payment_limit,
            ]);

            DB::commit();
            $booking->load('bookingItems.penalties', 'client');
            $advisory = $airReservation['Ticketing']['TicketAdvisory'] ?? 'Flight booked successfully. Please complete payment before the deadline, otherwise it will be canceled...';
            return ['message' => $advisory, 'booking' => $booking];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flight/Segment creation failed for FlyJinnah: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateBookingFieldsFJ(array $data, int $bookingId): Booking
    {
        // $airReservation = $response['AirReservation'] ?? [];
        // $itinerary = $airReservation['AirItinerary']['OriginDestinationOptions']['OriginDestinationOption'] ?? [];
        // $isOneWay = is_array($itinerary) && count($itinerary) === 1;
        // $priceInfo = $airReservation['PriceInfo']['ItinTotalFare'] ?? [];
        // $totalPriceAttrs = $priceInfo['TotalFare']['@attributes'] ?? [];
        // $bookingRef = $airReservation['BookingReferenceID']['@attributes'] ?? [];
        // $ticketingAttrs = $airReservation['Ticketing']['@attributes'] ?? [];
        $booking = Booking::findOrFail($bookingId);
        $timeLimit = isset($data['timeLimit']) ? Carbon::parse($data['timeLimit']) : $booking->ticket_limit;
        try {
            $booking->update([
                // 'is_oneway'         => $isOneWay,
                // 'order_id'          => $bookingRef['ID'] ?? null,
                // 'order_owner'       => null,
                // 'flight_booking_id' => null,
                // 'airline_id'        => null,
                // 'airline'           => 'FlyJinnah',
                'ticket_limit'      => $timeLimit,
                'payment_limit'     => $timeLimit,
                'transaction_id'    => $data['transactionId'] ?? $booking->transaction_id,
                'price_code'        => $data['code'] ?? $booking->price_code,
                'price'             => $data['amount'] ?? $booking->price,
                'status'            => $booking->status !== Booking::STATUS_ISSUED ? Booking::STATUS_CHANGED : $booking->status,
            ]);
            if ($booking->bookingRequest) {
                $booking->bookingRequest()->update([
                    'status' => 'change',
                    'ticket_limit' => $timeLimit,
                    'payment_limit' => $timeLimit,
                    'xml_body' => json_encode($data['response'] ?? []),
                ]);
            }
            return $booking->fresh();
        } catch (\Throwable $e) {
            Log::error('Booking table update failed for FlyJinnah: '.$e->getMessage(), ['booking_id' => $bookingId]);
            throw $e;
        }
    }
    public function issueTicketsFJ(array $data, int $bookingId): Booking
    {
        $booking = Booking::findOrFail($bookingId);
        DB::beginTransaction();
        try {
            $booking->update([
                'status' => Booking::STATUS_ISSUED,
                'transaction_id' => $data['transactionId'],
                'only_search' => false,
                'ticket_limit' => null,
                'payment_limit' => null,
            ]);
            // ----------------------------------------- Update Booking Request Body -----------------------------------------
            if ($booking->bookingRequest) {
                $booking->bookingRequest()->update([
                    'status' => 'change',
                    'ticket_limit' => null,
                    'payment_limit' => null,
                    'xml_body' => json_encode($data ?? []),
                ]);
            }
            // ----------------------------------------- Create Tickets -----------------------------------------
            $tickets = [];
            $ticketMap = [];
            foreach ($data['passengers'] ?? [] as $ticketInfo) {
                $ticket = [
                    'airline' => $booking->airline ?? 'Flyjinnah',
                    'passenger_reference' => $ticketInfo['ref_no'] ?? null,
                    'ticket_no' => $ticketInfo['tickets'][0]['e_ticket_no'] ?? null,
                    'ticket_numbers' => json_encode($ticketInfo['tickets'] ?? []),
                    'type' => $ticketInfo['tickets'][0]['type'] ?? null,
                    'issue_date' => now(),
                    'price_code' => null,
                    'price' => null,
                    'price_reference' => null,
                    'ticket_details' => json_encode($ticketInfo),
                    'client_id' => $booking->client_id,
                    'booking_id' => $booking->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $tickets[] = $ticket;
                $ticketMap[$ticketInfo['ref_no']] = $ticket;
            }
            $newTickets = [];
            if (!empty($tickets)) {
                Ticket::insert($tickets);
                $insertedTickets = Ticket::whereIn('passenger_reference', array_keys($ticketMap))->get()->keyBy('passenger_reference');
                foreach ($ticketMap as $ref => &$ticket) {
                    $ticket['id'] = $insertedTickets[$ref]->id ?? null; // Assign ticket ID
                }
            }
            $ancillaries = [];
            foreach ($data['passengers'] ?? [] as $passenger) {
                $passengerRef = $passenger['ref_no'] ?? null;
                $ticketId = $ticketMap[$passengerRef]['id'] ?? null;

                if (!$ticketId) continue;

                foreach ($passenger['seats'] ?? [] as $seat) {
                    $ancillaries[] = [
                        'ticket_id' => $ticketId,
                        'passenger_reference' => $passengerRef,
                        'type' => 'seat',
                        'details' => json_encode([
                            'seat_number' => $seat['seat_number'] ?? 'N/A',
                            'flight_number' => $seat['flight_number'] ?? null,
                            'departure_date' => $seat['departure_date'] ?? null,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                foreach ($passenger['baggage'] ?? [] as $baggage) {
                    $ancillaries[] = [
                        'ticket_id' => $ticketId,
                        'passenger_reference' => $passengerRef,
                        'type' => 'baggage',
                        'details' => json_encode([
                            'baggage_code' => $baggage['baggage_code'] ?? 'No Bag',
                            'flight_number' => $baggage['flight_number'] ?? null,
                            'departure_date' => $baggage['departure_date'] ?? null,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                foreach ($passenger['meals'] ?? [] as $meal) {
                    $ancillaries[] = [
                        'ticket_id' => $ticketId,
                        'passenger_reference' => $passengerRef,
                        'type' => 'meal',
                        'details' => json_encode([
                            'meal_code' => $meal['meal_code'] ?? null,
                            'meal_quantity' => $meal['meal_quantity'] ?? null,
                            'flight_number' => $meal['flight_number'] ?? null,
                            'departure_date' => $meal['departure_date'] ?? null,
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            if (!empty($ancillaries)) Ancillary::insert($ancillaries);
            DB::commit();
            return $booking->load('bookingItems.penalties', 'client', 'tickets.ancillaries');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flight/Segment creation failed for FlyJinnah: ' . $e->getMessage());
            throw $e;
        }
    }
}