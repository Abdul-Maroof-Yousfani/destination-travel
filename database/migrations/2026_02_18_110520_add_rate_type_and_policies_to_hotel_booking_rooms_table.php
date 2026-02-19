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
        Schema::table('hotel_booking_rooms', function (Blueprint $table) {
            $table->string('rate_type')->nullable()->after('meal_plan');
            $table->json('cancel_policies')->nullable()->after('rate_keys');
        });

        Schema::table('payments', function (Blueprint $table) {

            $table->dropForeign(['booking_id']);

            $table->unsignedBigInteger('booking_id')->nullable()->change();

            $table->foreign('booking_id')
                  ->references('id')
                  ->on('bookings')
                  ->onDelete('cascade');

            $table->unsignedBigInteger('hotel_booking_id')
                  ->nullable()
                  ->after('client_id');

            $table->foreign('hotel_booking_id')
                  ->references('id')
                  ->on('hotel_bookings')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotel_booking_rooms', function (Blueprint $table) {
            $table->dropColumn(['rate_type', 'cancel_policies']);
        });
        
        Schema::table('payments', function (Blueprint $table) {

            $table->dropForeign(['hotel_booking_id']);
            $table->dropColumn('hotel_booking_id');

            $table->dropForeign(['booking_id']);

            $table->unsignedBigInteger('booking_id')->nullable(false)->change();

            $table->foreign('booking_id')
                  ->references('id')
                  ->on('bookings')
                  ->onDelete('cascade');
        });
    }
};
