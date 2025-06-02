<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pnr',
        'ticket_no',
        'tax',
        'discount',
        'merchant_fee',
        'service_fee',
        'status',
        'refund_status',
        'payment_method',
        'transaction_id',
        'passenger_id',
        'client_id',
        'flight_id',
        'booking_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // 🔗 Relationships
    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
