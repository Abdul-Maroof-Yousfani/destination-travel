<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingId extends Model
{
    use HasFactory;

    protected $table = 'booking_ids';

    protected $fillable = [ 'name', 'phone_code', 'phone', 'email', 'accept_notifications', 'booking_id', 'ip' ];

    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';

    protected $dates = ['created_at', 'updated_at'];
}
