<?php
// kia connected, return flight ki details bh save krwani hain
// user ka unique mail hoga ya nh

namespace App\Services;

use Carbon\Carbon;
use App\Mail\SendMail;
use App\Models\Client;
use App\Models\BookingId;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserBookingService
{
    public function createUser(array $userData): array
    {
        // dd($userData);
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
    // public function createUser($user)
    // {
    //     $password = Str::random(12);

    //     $username = $user['userFullName'] ?? '-';
    //     $userEmail = $user['userEmail'] ?? null;
    //     $user = Client::create([
    //         'name' => $username,
    //         'email' => $userEmail,
    //         'phone_code' => $user['userPhoneCode'] ?? '-',
    //         'phone' => $user['userPhone'] ?? '-',
    //         'acceptOffers' => $user['acceptOffers'] ?? false,
    //         'password' => Hash::make($password),
    //         'original_password' => $password,
    //         'ip' => request()->ip(),
    //     ]);
    //     return $user;
    // }
    public function createPassengers(array $passengers, int $clientId)
    {
        $created = [];
        $passengers = isset($passengers[0]) ? $passengers : [$passengers];
        foreach ($passengers as $data) {
            $created[] = Passenger::create([
                'title' => $data['title'],
                'given_name' => $data['name'],
                'surname' => $data['surname'],
                'dob' => Carbon::parse($data['dob']),
                'nationality' => $data['nationality'],
                'passport_no' => $data['passportNumber'],
                'passport_exp' => Carbon::parse($data['passportExpiry']),
                'client_id' => $clientId,
            ]);
        }

        return $created;
    }
    public function createFlight(array $passengers, int $clientId)
    {
        $created = [];
        $passengers = isset($passengers[0]) ? $passengers : [$passengers];
        foreach ($passengers as $data) {
            $created[] = Passenger::create([
                'title' => $data['title'],
                'given_name' => $data['name'],
                'surname' => $data['surname'],
                'dob' => Carbon::parse($data['dob']),
                'nationality' => $data['nationality'],
                'passport_no' => $data['passportNumber'],
                'passport_exp' => Carbon::parse($data['passportExpiry']),
                'client_id' => $clientId,
            ]);
        }

        return $created;
    }










    public function sendEmailToUser($user)
    {
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
