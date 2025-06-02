<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    // $initialBookings = Booking::initial()->get();
    // $issuedCount = Booking::issued()->count();
    // $bookings = Booking::where('status', Booking::STATUS_PENDING)->get();

    public const STATUS_INITIAL = 'initial';
    public const STATUS_PENDING = 'pending';
    public const STATUS_ISSUED = 'issued';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_ref',
        'booking_ref_owner',
        'ticket_limit',
        'payment_limit',
        'status',
        'airline',
        'airline_id',
        'airline_code',
        'transaction_id',
        'flight_id',
        'client_id',
    ];

    protected $casts = [
        'ticket_limit' => 'datetime',
        'payment_limit' => 'datetime',
    ];

    public static function getStatuses(): array
    {
        return [
            self::STATUS_INITIAL,
            self::STATUS_PENDING,
            self::STATUS_ISSUED,
        ];
    }

    public function scopeInitial($query)
    {
        return $query->where('status', self::STATUS_INITIAL);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }
    
    public function logs()
    {
        return $this->hasMany(Log::class);
    }

}
