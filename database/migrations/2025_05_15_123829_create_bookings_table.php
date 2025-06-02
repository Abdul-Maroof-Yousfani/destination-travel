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
        // Store the booking information when the user is view cards payment tab :)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_ref')->nullable(); // external reference ID
            $table->string('booking_ref_owner')->nullable(); // external reference owner like: EK, QR, etc.
            $table->dateTime('ticket_limit')->nullable(); // ticketing deadline
            $table->dateTime('payment_limit')->nullable(); // payment deadline
            $table->enum('status', ['initial', 'pending', 'issued'])->default('initial');
            $table->string('airline');
            $table->string('airline_id')->nullable();
            $table->string('airline_code')->nullable();
            $table->string('transaction_id')->nullable(); // for payment gateway

            $table->unsignedBigInteger('flight_id');
            $table->foreign('flight_id')->references('id')->on('flights')->onDelete('cascade');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
