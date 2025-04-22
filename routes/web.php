<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightOldController;
use App\Http\Controllers\FlightController;

Route::view('/', 'home.home')->name('home');
Route::prefix('flights')->group(function () {
    Route::get('/', [FlightController::class, 'search'])->name('flights');
    Route::post('getBundles', [FlightController::class, 'getBundles'])->name('get_bundles');

    Route::post('booking-details', [FlightController::class, 'bookingDetails'])->name('booking_details');
    Route::get('booking', [FlightController::class, 'booking'])->name('flightBooking');
    Route::post('get-seat', [FlightController::class, 'getSeat'])->name('get_seat');
    Route::post('get-meal', [FlightController::class, 'getMeal'])->name('get_meal');
    Route::post('get-baggage', [FlightController::class, 'getBaggage'])->name('get_baggage');

    Route::post('get-final-price', [FlightController::class, 'getFinalPrice'])->name('get_final_price');
    Route::post('payment', [FlightController::class, 'payment'])->name('payment');
    Route::post('bookFlight', [FlightController::class, 'bookFlight'])->name('bookFlight');
});