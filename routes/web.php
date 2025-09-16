<?php

use App\Models\User;
// use Livewire\Livewire;
use App\Livewire\Admin\BookingList;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Filament\Notifications\Notification;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AgentPermissionController;

// Livewire::routes();
Route::view('/', 'home.home')->name('home');
Route::view('terms-and-conditions', 'home.pages.terms-and-conditions')->name('terms-and-conditions');
Route::view('about-us', 'home.pages.about-us')->name('about-us');

// new route----------------------------
Route::view('search-booking', 'home.pages.search-booking')->name('search.booking');
Route::get('view-booking-details', [HomeController::class, 'viewBookingDetails'])->name('view.booking.details');
Route::post('search-booking', [HomeController::class, 'searchBooking'])->name('search.booking.submit');
// -------------------------------------

Route::get('get-airport', [HomeController::class, 'airports'])->name('airport');
Route::post('verify-client', [FlightController::class, 'verifyClient'])->name('verify.client');
Route::view('mail', 'emails.sendBookingId')->name('mail');
Route::prefix('flights')->group(function () {
    Route::get('/', [FlightController::class, 'search'])->name('flights');
    Route::post('getBundles', [FlightController::class, 'getBundles'])->name('get_bundles');

    Route::post('booking-details', [FlightController::class, 'bookingDetails'])->name('booking_details');
    Route::get('booking', [FlightController::class, 'booking'])->name('flightBooking');
    Route::post('get-seat', [FlightController::class, 'getSeat'])->name('get_seat');
    Route::post('get-meal', [FlightController::class, 'getMeal'])->name('get_meal');
    Route::post('get-baggage', [FlightController::class, 'getBaggage'])->name('get_baggage');

    // Route::post('get-final-price', [FlightController::class, 'getFinalPrice'])->name('get_final_price');
    Route::post('payment', [FlightController::class, 'payment'])->name('payment');
    Route::post('bookFlight', [FlightController::class, 'bookFlight'])->name('bookFlight');
});
Route::post('confirm-booking', [FlightController::class, 'confirmBooking'])->name('confirm.booking');
Route::post('order-cancel', [FlightController::class, 'orderCancel'])->name('cancel.booking');
Route::post('fetch-flight-details', [FlightController::class, 'fetchDetails'])->name('fetch.flight.details');

// Profile
Route::post('update/client/{client}', [HomeController::class, 'updateClient'])->name('update.client');

// -------------------------------------ADMIN----------------------------------------------
Route::get('admin/login', 'App\Http\Controllers\Admin\AdminAuthController@loginPage')->name('admin.login');
Route::post('admin/login', 'App\Http\Controllers\Admin\AdminAuthController@login')->name('admin.login.submit');
Route::post('login', 'App\Http\Controllers\AuthController@login')->name('login.submit');
Route::post('register', 'App\Http\Controllers\AuthController@register')->name('register.submit');

Route::redirect('admin', 'login')->name('admin.home');
Route::redirect('agent', 'admin/login')->name('agent.home');
Route::view('login', 'login')->name('login');
Route::view('register', 'register')->name('register');
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'admin.dashboard')->name('dashboard');

    Route::get('download-logs', [AdminDashboardController::class, 'downloadLogs'])->name('download.logs.all');
    Route::get('download-logs/bookings', [AdminDashboardController::class, 'downloadLogsBookings'])->name('download.logs.bookings');

    Route::get('orders', [OrderController::class, 'list'])->name('orders');
    Route::get('orders-fetch', [OrderController::class, 'fetch'])->name('orders.fetch');
    Route::get('orders/{booking}', [OrderController::class, 'details'])->name('orders.details');

    Route::get('agents', [AgentController::class, 'index'])->name('agents');
    Route::get('agents/{agent}/edit-permision', [AgentController::class, 'editPermission'])->name('agents.edit.permission');
    Route::post('agents/{agent}/update-permision', [AgentController::class, 'updatePermissions'])->name('agents.update.permission');

    Route::post('agents', [AgentController::class, 'store'])->name('agents.store');
    Route::post('agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::get('agents/{agent}/delete', [AgentController::class, 'destroy'])->name('agents.destroy');
    Route::get('agents/{agent}/login', [AgentController::class, 'loginAs'])->name('agents.login');
});
Route::post('admin/logout', 'App\Http\Controllers\Admin\AdminAuthController@logout')->name('admin.logout');
// -------------------------------------ADMIN----------------------------------------------

// -------------------------------------AGENT----------------------------------------------
// Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
//     Route::view('dashboard', 'agent.dashboard')->name('dashboard');
//     Route::view('orders', 'agent.orders.list')->name('orders');
//     Route::view('order/{order}', 'agent.orders.manage')->name('order');
// });
// -------------------------------------AGENT----------------------------------------------
// -------------------------------------BOTH----------------------------------------------

Route::middleware(['auth', 'role:admin|agent'])->group(function () {
    Route::post('log/add', [OrderController::class, 'logStore'])->name('log.add');
    Route::get('booking/{booking}/logs', [OrderController::class, 'logHistory'])->name('booking.logs');
    Route::post('payment/store', [OrderController::class, 'paymentStore'])->name('payment.store');
    Route::put('payment/{payment}', [OrderController::class, 'paymentUpdate'])->name('payment.update');
    Route::delete('payment/{payment}', [OrderController::class, 'paymentDestroy'])->name('payment.destroy');
});






// -------------------------------------BOTH----------------------------------------------
// Route::view('flights', 'admin.flights')->name('admin.flights');
    // Route::view('users', 'admin.users')->name('admin.users');
    // Route::view('bookings', 'admin.bookings')->name('admin.bookings');
    // Route::view('settings', 'admin.settings')->name('admin.settings');
    // Route::view('notifications', 'admin.notifications')->name('admin.notifications');

Route::get('send-notification', 
    function () {
        $recipient = User::find(1);
        // dd($recipient);
        Notification::make()
            ->title('New Message')
            ->body('You have a new message.')
            ->sendToDatabase($recipient);
        return 'Notification sent!';
    }
);
