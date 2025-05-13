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
        Schema::table('booking_ids', function (Blueprint $table) {
            $table->string('airline')->after('accept_notifications')->nullable();
            $table->json('airline_ids')->after('airline')->nullable();
            $table->boolean('is_paid')->after('booking_id')->default(false);
            $table->timestamp('ticket_limit')->after('is_paid')->nullable();
            $table->timestamp('payment_limit')->after('ticket_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_ids', function (Blueprint $table) {
            $table->dropColumn([
                'airline',
                'airline_ids',
                'is_paid',
                'ticket_limit',
                'payment_limit',
            ]);
        });
    }
};
