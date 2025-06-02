<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'notes',
        'changes',
        'image',
        'booking_id',
        'user_id',
    ];

    /**
     * Relationships
     */

    // Log belongs to a booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Log may belong to a user (nullable)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
