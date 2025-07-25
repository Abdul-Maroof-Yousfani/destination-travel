<?php

namespace App\Services;

use App\Models\Flight;
use App\Models\Booking;
use App\Models\Segment;
use App\Models\Passenger;
use App\Models\BookingItem;
use Illuminate\Support\Carbon;
use App\Models\BookingRequestBody;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlightBookingService
{
    public function handleEmiratesBooking(array $response, int $clientId, $cabinClass): array
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
    public function updateBookingFieldsFromResponse(array $response, int $bookingId): Booking
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
}
