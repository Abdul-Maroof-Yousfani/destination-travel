<?php

namespace App\Models;

use App\Models\Client;
use App\Models\Segment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flight extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['logo'];
    protected $fillable = [
        'airline',
        'departure_code',
        'arrival_code',
        'departure_date',
        'arrival_date',
        'is_connected',
        'pax_count',
        'cabin_class',
        'price',
        'price_code',
        'client_id',
        'booking_id',
    ];

    protected $casts = [
        'departure_date' => 'datetime',
        'arrival_date' => 'datetime',
        'is_connected' => 'boolean',
        'pax_count' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getPaxCountAttribute($value)
    {
        return $value ? json_decode($value, true) : [
            'adults' => 1,
            'children' => 0,
            'infant' => 0,
        ];
    }

    public function setPaxCountAttribute($value)
    {
        $this->attributes['pax_count'] = json_encode($value);
    }

    public function getLogoAttribute()
    {
        $airline = strtolower(trim($this->airline));

        $logos = [
            'emirates'   => 'emiratemini.png',
            'flyjinnah'  => 'flyjinnahmini.png',
            // add more airlines and logos here as needed
        ];

        return $logos[$airline] ?? 'defaultmini.png';
    }

    public function getCabinClassAttribute($value)
    {
        $map = [
            'Y' => 'Economy',
            'C' => 'Business',
            'J' => 'Business',
            'F' => 'First Class',
            'W' => 'Premium Economy',
        ];

        return $map[strtoupper($value)] ?? $value;
    }
    public function segments()
    {
        return $this->hasMany(Segment::class);
    }

    // public function getFlightNosAttribute($value)
    // {
    //     return $value ? json_decode($value, true) : [];
    // }

    // public function setFlightNosAttribute($value)
    // {
    //     $this->attributes['flight_nos'] = json_encode($value);
    // }

    public function getCabinNameWithCodeAttribute(): string
    {
        $code = strtoupper($this->attributes['cabin_class'] ?? '');
        $map = [
            'Y' => 'Economy',
            'W' => 'Premium Economy',
            'C' => 'Business',
            'J' => 'Business',
            'P' => 'First',
            'F' => 'First',
        ];

        $name = $map[$code] ?? 'Unknown';

        return "{$name} ({$code})";
    }

}
