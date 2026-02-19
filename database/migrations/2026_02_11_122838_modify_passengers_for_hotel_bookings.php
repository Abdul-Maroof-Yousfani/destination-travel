<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify passengers table
        Schema::table('passengers', function (Blueprint $table) {
            $table->unsignedBigInteger('hotel_booking_room_id')->nullable()->after('client_id');
            $table->boolean('is_lead_pax')->default(false)->after('hotel_booking_room_id');
            
            $table->foreign('hotel_booking_room_id')->references('id')->on('hotel_booking_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passengers', function (Blueprint $table) {
            $table->dropForeign(['hotel_booking_room_id']);
            $table->dropColumn(['hotel_booking_room_id', 'is_lead_pax']);
        });
    }
};
