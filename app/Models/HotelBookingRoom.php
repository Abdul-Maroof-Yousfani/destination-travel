<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelBookingRoom extends Model
{
    protected $fillable = [
        'hotel_booking_id',
        'room_identifier',
        'room_name',
        'meal_plan',
        'rate_keys',
        'net_price',
        'gross_price',
        'tax_price',
        'rate_type',
        'cancel_policies'
    ];

    protected $casts = [
        'rate_keys' => 'array',
        'net_price' => 'decimal:2',
        'gross_price' => 'decimal:2',
        'tax_price' => 'decimal:2',
        'cancel_policies' => 'array'
    ];

    public function booking()
    {
        return $this->belongsTo(HotelBooking::class, 'hotel_booking_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'hotel_booking_room_id');
    }
}
