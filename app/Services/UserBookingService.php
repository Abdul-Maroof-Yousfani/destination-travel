<?php

namespace App\Services;

use App\Models\BookingId;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Log;

class UserBookingService
{
    public function createUser(array $userData): array
    {
        $user = $userData['user'];
        $username = $user['userFullName'] ?? '-';
        $userEmail = $user['userEmail'] ?? null;
        $userDetails = BookingId::create([
            'name' => $username,
            'email' => $userEmail,
            'phone_code' => $user['userPhoneCode'] ?? '-',
            'phone' => $user['userPhone'] ?? '-',
            'acceptOffers' => $user['acceptOffers'] ?? false,
            'booking_id' => $userData['bookingRefID'] ?? '-',
            'airline' => $userData['airline'] ?? null,
            'airline_ids' => $userData['airlineIds'] ?? null,
            'ticket_limit' => $userData['ticketLimit'] ?? null,
            'payment_limit' => $userData['paymentLimit'] ?? null,
            // 'is_paid' => false,
            'ip' => request()->ip(),
        ]);
        $emailMsg = null;
        if ($userEmail && $userData['ticketStatusMsg']) {
            try {
                Mail::to($userEmail)->send(new SendMail($username, $bookingRefID, $userData['ticketStatusMsg']));
                $emailMsg = 'Flight details sent to email successfully';
            } catch (\Exception $e) {
                Log::error('Mail sending failed: ' . $e->getMessage());
                $emailMsg = 'Failed to send email';
            }
        }
        return [
            'user' => $userDetails,
            'emailMessage' => $emailMsg,
        ];
    }
}
