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
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable();
            $table->string('reference')->nullable();
            $table->string('pnr')->nullable();
            $table->string('booking_no')->nullable();
            $table->string('confirmation_no')->nullable();
            $table->string('hotel_name')->nullable();
            $table->string('hotel_code')->nullable();
            $table->string('city')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->string('currency')->default('AED');
            $table->decimal('total_net', 12, 2)->default(0);
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_tax', 12, 2)->default(0);
            $table->string('session_id')->nullable();
            $table->string('destination_code')->nullable();
            $table->string('group_code')->nullable();
            $table->string('nationality')->nullable();
            $table->string('status')->default('initial'); // initial, confirmed, failed, cancelled
            
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('set null');
            
            $table->text('remarks')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hotel_booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_booking_id')->constrained('hotel_bookings')->onDelete('cascade');
            $table->string('room_identifier');
            $table->string('room_name');
            $table->string('meal_plan')->nullable();
            $table->json('rate_keys')->nullable();
            $table->decimal('net_price', 12, 2)->default(0);
            $table->decimal('gross_price', 12, 2)->default(0);
            $table->decimal('tax_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_booking_rooms');
        Schema::dropIfExists('hotel_bookings');
    }
};
