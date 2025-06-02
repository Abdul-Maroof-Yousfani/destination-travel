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
        Schema::create('ticket_details', function (Blueprint $table) {
            $table->id();
            $table->string('pnr')->nullable();
            $table->string('ticket_no')->nullable();
            $table->string('tax')->nullable();
            $table->string('discount')->nullable();
            $table->string('merchant_fee')->nullable();
            $table->string('service_fee')->nullable();
            $table->enum('status', ['success', 'fail'])->default('success');
            $table->string('refund_status')->nullable();
            $table->string('payment_method')->nullable(); // like: credit card, cash, ezpaisa etc.
            $table->string('transaction_id')->nullable(); // of payment gateway
            $table->unsignedBigInteger('passenger_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('flight_id');
            $table->unsignedBigInteger('booking_id');

            // Foreign keys
            $table->foreign('passenger_id')->references('id')->on('passengers')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('flight_id')->references('id')->on('flights')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_details');
    }
};
