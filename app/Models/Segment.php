<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = [
        'departure_code',
        'arrival_code',
        'departure_date',
        'arrival_date',
        'flight_number',
        'direction',
        'price',
        'price_code',
        'flight_id',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
}
