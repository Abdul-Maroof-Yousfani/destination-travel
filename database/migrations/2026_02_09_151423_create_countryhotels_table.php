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
        Schema::create('countryhotels', function (Blueprint $table) {
            $table->id();
            $table->integer('order_by')->nullable();
            $table->string('country', 100)->nullable();
            $table->string('nationality')->nullable();
            $table->string('destinationcode', 100)->nullable();
            $table->string('city')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_local')->default(true);
            $table->timestamps();

            $table->unique(['country', 'destinationcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countryhotels');
    }
};
