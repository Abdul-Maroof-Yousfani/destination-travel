<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryHotel extends Model
{
    protected $table = 'countryhotels';

    protected $fillable = [
        'order_by',
        'country',
        'nationality',
        'destinationcode',
        'city',
        'status',
        'is_local',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_local' => 'boolean',
    ];
}
