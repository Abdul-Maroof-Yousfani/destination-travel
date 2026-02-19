<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HotelBooking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'pnr',
        'source',
        'booking_no',
        'confirmation_no',
        'hotel_name',
        'hotel_code',
        'city',
        'check_in',
        'check_out',
        'currency',
        'total_net',
        'total_gross',
        'total_tax',
        'session_id',
        'destination_code',
        'group_code',
        'nationality',
        'status',
        'client_id',
        'agent_id',
        'remarks',
        'raw_response'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'raw_response' => 'array',
        'total_net' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'total_tax' => 'decimal:2',
    ];

    public function rooms()
    {
        return $this->hasMany(HotelBookingRoom::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function errorLogs()
    {
        return $this->hasMany(ErrorLog::class, 'hotel_booking_id');
    }

    public function bookingRequest()
    {
        return $this->hasOne(BookingRequestBody::class, 'hotel_booking_id');
    }

    public static function getStatuses(): array
    {
        return [
            'initial',
            'pending',
            'confirmed',
            'failed',
            'cancelled'
        ];
    }

    public function cancelResponses()
    {
        return $this->hasMany(CancelResponse::class);
    }

    public function getCancelledAtAttribute(): ?string
    {
        return $this->cancelResponses()
            ->latest('created_at')
            ->value('created_at')?->toDateTimeString();
    }


    public function getHotelSummary(): string
    {
        $hotelName = $this->hotel_name ?? 'N/A';
        $location = $this->city ?? 'N/A';
        $checkIn = $this->check_in ? $this->check_in->format('d M') : 'N/A';
        $checkOut = $this->check_out ? $this->check_out->format('d M') : 'N/A';

        return "HOTEL\n{$hotelName}\n{$location} ({$checkIn} - {$checkOut})";
    }
}
