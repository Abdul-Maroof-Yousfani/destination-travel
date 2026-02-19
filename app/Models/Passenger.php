<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Passenger extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'passenger_reference',
        'type',
        'title',
        'given_name',
        'surname',
        'dob',
        'nationality',
        'passport_no',
        'passport_exp',
        'client_id',
        'hotel_booking_room_id',
        'is_lead_pax',
    ];

    protected $casts = [
        'dob' => 'date',
        'passport_exp' => 'date',
        'is_lead_pax' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function room()
    {
        return $this->belongsTo(HotelBookingRoom::class, 'hotel_booking_room_id');
    }
}
