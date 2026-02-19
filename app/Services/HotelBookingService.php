<?php

namespace App\Services;

use App\Models\HotelBooking;
use App\Models\ErrorLog;
use App\Models\BookingRequestBody;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HotelBookingService
{
    public function __construct(
        protected TassProService $tassProService
    ) {}

    /**
     * Confirms a hotel booking with TassPro API.
     * 
     * @param HotelBooking $booking
     * @return array
     */
    public function confirmBooking(HotelBooking $booking): array
    {
        $booking->load(['rooms.passengers', 'bookingRequest']);

        $roomsPayload = [];
        foreach ($booking->rooms as $room) {
            $adultCount = $room->passengers->where('type', 'adult')->count();
            $childCount = $room->passengers->where('type', 'child')->count();
            $childAgesArr = [];

            foreach ($room->passengers->where('type', 'child') as $pax) {
                $paxAge = $pax->age;
                if (!$paxAge && $pax->dob) {
                    $paxAge = Carbon::parse($pax->dob)->age;
                }
                if (!$paxAge) $paxAge = 8; // Fallback

                $childAgesArr[] = [
                    'Identifier' => (string)(count($childAgesArr) + 1),
                    'Text' => (string)$paxAge
                ];
            }

            $guests = [];
            foreach ($room->passengers as $pax) {
                // Ensure age is set for TassPro (Guest node)
                $paxAge = $pax->age;
                if (!$paxAge && $pax->dob) {
                    $paxAge = Carbon::parse($pax->dob)->age;
                }

                // Final fallback based on type
                if (!$paxAge) {
                    $paxAge = (strtolower($pax->type) === 'adult') ? 30 : 8;
                }

                $guests[] = [
                    'Title' => [
                        'Code' => '',
                        'Text' => $pax->title
                    ],
                    'FirstName' => $pax->given_name,
                    'LastName' => $pax->surname,
                    'IsLeadPAX' => (bool)$pax->is_lead_pax,
                    'Type' => ucfirst($pax->type), // "Adult" or "Child"
                    'Age' => (int)$paxAge
                ];
            }

            $roomsPayload[] = [
                'RoomIdentifier' => (int)$room->room_identifier,
                'Adult' => (int)$adultCount,
                'Children' => $childCount > 0 ? [
                    'Count' => (string)$childCount,
                    'ChildAge' => $childAgesArr
                ] : null,
                'RateKey' => $room->rate_keys[0] ?? '',
                'Guests' => [
                    'Guest' => $guests
                ],
                'Price' => [
                    'Gross' => (float)$room->gross_price,
                    'Net' => (float)$room->net_price,
                    'Tax' => (float)$room->tax_price
                ]
            ];
        }

        $payload = [
            'SessionId' => $booking->session_id,
            'HotelCode' => (string)$booking->hotel_code,
            'DestinationCode' => $booking->destination_code,
            'CountryCode' => $booking->nationality ?? 'AE',
            'Currency' => $booking->currency,
            'Nationality' => $booking->nationality ?? 'AE',
            'CustomerRefNumber' => $booking->reference . '-' . time(),
            'GroupCode' => (int)$booking->group_code,
            'Rooms' => [
                'Room' => $roomsPayload
            ]
        ];

        $result = $this->tassProService->bookHotel($payload);

        // Update Request Body with attempt

        if (isset($result['errorInfo']) && !empty($result['errorInfo'])) {
            $errorMsg = $result['errorInfo']['description'] ?? 'Booking failed';

            // Log Error
            ErrorLog::create([
                'hotel_booking_id' => $booking->id,
                'client_id' => $booking->client_id,
                'details' => $result,
                'error_type' => 'ticketing',
                'error_message' => $errorMsg,
            ]);

            return ['success' => false, 'message' => $errorMsg, 'details' => $result];
        }
        if ($booking->bookingRequest) {
            $booking->bookingRequest->update([
                'xml_body' => json_encode(['payload' => $payload, 'response' => $result]),
            ]);
        }

        if ($result && (isset($result['adsConfirmationNumber']) || isset($result['bookingConfirmationId']))) {
            $booking->update([
                'status' => 'confirmed',
                'pnr' => $result['adsConfirmationNumber'] ?? $result['bookingConfirmationId'] ?? null,
                'booking_no' => $result['tassProBookingNo'] ?? null,
                'confirmation_no' => $result['supplierConfirmationNumber'] ?? $result['bookingConfirmationId'] ?? null,
                'raw_response' => json_encode($result) // Keep a copy of the direct response
            ]);

            return ['success' => true, 'message' => 'Booking confirmed successfully', 'data' => $result];
        }

        return ['success' => false, 'message' => 'Unexpected response from API', 'data' => $result];
    }

    /**
     * Refreshes the price from API and compares with saved price.
     * 
     * @param HotelBooking $booking
     * @return array
     */
    public function checkPriceChange(HotelBooking $booking): array
    {
        $booking->load('rooms');

        $rateKeys = [];
        foreach ($booking->rooms as $room) {
            if ($room->rate_keys && is_array($room->rate_keys)) {
                $rateKeys = array_merge($rateKeys, $room->rate_keys);
            }
        }

        $result = $this->tassProService->preBook(
            $booking->session_id,
            $booking->hotel_code,
            $booking->group_code,
            $rateKeys,
            $booking->currency ?? 'AED'
        );
        // dd($result);

        if (!$result || isset($result['error'])) {
            return [
                'success' => false,
                'message' => $result['error'] ?? 'API error during price check.',
                'details' => $result
            ];
        }
        $isPriceChanged = $result['isPriceChanged'];

        $totalNet = 0;
        $totalGross = 0;
        $totalTax = 0;
        $currency = 'AED';

        foreach ($result['hotel']['rooms']['room'] as $room) {
            $totalNet += $room['price']['supplierNet'] ?? 0;
            $totalGross += $room['price']['supplierGross'] ?? 0;
            $totalTax += $room['price']['supplierTax'] ?? 0;

            $currency = $room['price']['supplierCurrency'] ?? $currency;
        }

        $oldPrice = (float)$booking->total_net;
        $diff = $totalNet - $oldPrice;
        $percentChange = $oldPrice > 0 ? ($diff / $oldPrice) * 100 : 0;

        return [
            'success' => true,
            'comparison' => [
                'old_price' => number_format($oldPrice, 2),
                'new_price' => number_format($totalNet, 2),
                'old_price_code' => $booking->currency,
                'new_price_code' => $currency,
                'difference' => number_format($diff, 2),
                'difference_label' => $diff > 0 ? 'Increase' : ($diff < 0 ? 'Decrease' : 'No Change'),
                'percent_change' => number_format($percentChange, 2),
            ],
            'data' => $result
        ];
    }
}
