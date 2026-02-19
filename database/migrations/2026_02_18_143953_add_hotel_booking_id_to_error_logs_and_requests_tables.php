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
        Schema::table('error_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable()->change();
            $table->unsignedBigInteger('hotel_booking_id')->nullable()->after('booking_id');
            $table->foreign('hotel_booking_id')->references('id')->on('hotel_bookings')->onDelete('cascade');
        });

        Schema::table('booking_request_bodies', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable()->change();
            $table->unsignedBigInteger('hotel_booking_id')->nullable()->after('booking_id');
            $table->foreign('hotel_booking_id')->references('id')->on('hotel_bookings')->onDelete('cascade');
        });

        Schema::table('cancel_responses', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_id')->nullable()->change();
            $table->unsignedBigInteger('hotel_booking_id')->nullable()->after('booking_id');
            $table->foreign('hotel_booking_id')->references('id')->on('hotel_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_request_bodies', function (Blueprint $table) {
            $table->dropForeign(['hotel_booking_id']);
            $table->dropColumn('hotel_booking_id');
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
        });

        Schema::table('error_logs', function (Blueprint $table) {
            $table->dropForeign(['hotel_booking_id']);
            $table->dropColumn('hotel_booking_id');
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
        });

        Schema::table('cancel_responses', function (Blueprint $table) {
            $table->dropForeign(['hotel_booking_id']);
            $table->dropColumn('hotel_booking_id');
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
        });
    }
};
