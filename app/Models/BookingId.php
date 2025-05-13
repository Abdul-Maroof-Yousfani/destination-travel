<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingId extends Model
{
    use HasFactory;

    protected $table = 'booking_ids';

    protected $fillable = [
        'name',
        'phone_code',
        'phone',
        'email',
        'accept_notifications',
        'booking_id',
        'ip',
        'airline',
        'airline_ids',
        'is_paid',
        'ticket_limit',
        'payment_limit',
    ];

    protected $casts = [
        'ticket_limit' => 'datetime',
        'payment_limit' => 'datetime',
        'airline_ids' => 'array',
        'is_paid' => 'boolean',
    ];
}
