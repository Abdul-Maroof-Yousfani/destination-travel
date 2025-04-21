<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightOldController;
use App\Http\Controllers\FlightController;

Route::view('/', 'home.home')->name('home');
Route::get('flights', [FlightController::class, 'search'])->name('flights');
Route::post('getBundles', [FlightController::class, 'getBundles'])->name('get_bundles');
Route::post('flights/booking-details', [FlightController::class, 'bookingDetails'])->name('booking_details');
Route::get('flights/booking', [FlightController::class, 'booking'])->name('flightBooking');
Route::post('flights/bookFlight', [FlightController::class, 'bookFlight'])->name('bookFlight');
Route::post('flights/payment', [FlightController::class, 'payment'])->name('payment');
Route::post('flights/get-seat', [FlightController::class, 'getSeat'])->name('get_seat');
Route::post('flights/get-meal', [FlightController::class, 'getMeal'])->name('get_meal');
Route::post('flights/get-baggage', [FlightController::class, 'getBaggage'])->name('get_baggage');
Route::post('flights/get-final-price', [FlightController::class, 'getFinalPrice'])->name('get_final_price');



Route::redirect('demo', 'demo/searchFlights');
Route::prefix('demo')->group(function () {
    Route::get('flights/search', [FlightOldController::class, 'search']);
    Route::view('searchFlights', 'searchFlights')->name('searchFlights');
    Route::view('booking', 'home.booking')->name('booking');
    Route::get('flights/booking', [FlightOldController::class, 'booking'])->name('bookingPage');
    Route::post('flights/bookingPage', [FlightOldController::class, 'bookingPage'])->name('booking_page');
    Route::post('flights/bookFlight', [FlightOldController::class, 'bookFlight'])->name('demoBookFlight');

    Route::post('flights/getBundles', [FlightOldController::class, 'getBundles'])->name('demo_get_bundles');
});


