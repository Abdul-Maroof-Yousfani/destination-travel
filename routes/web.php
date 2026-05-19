<?php

use App\Models\User;
// use Livewire\Livewire;
use App\Livewire\Admin\BookingList;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use Filament\Notifications\Notification;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use Illuminate\Support\Facades\Artisan;

// Livewire::routes();
Route::get('clear-cache/{key}', function ($key) {
    if ($key !== env('CACHE_CLEAR_KEY')) {
        abort(403, 'Unauthorized');
    }
    Artisan::call('optimize:clear');

    return 'All caches cleared successfully.';
});

Route::post('/set-currency', function (\Illuminate\Http\Request $request) {
    session(['currency' => $request->currency]);
    return response()->json(['success' => true]);
})->name('set-currency');
Route::view('/', 'home.home')->name('home');
Route::get('currency-example', [\App\Http\Controllers\CurrencyExampleController::class, 'index'])->name('currency.example');
Route::view('login', 'login')->name('login');
Route::view('register', 'register')->name('register');
Route::view('terms-and-conditions', 'home.pages.terms-and-conditions')->name('terms-and-conditions');
Route::view('about-us', 'home.pages.about-us')->name('about-us');
Route::view('contact-us', 'home.pages.contact-us')->name('contact-us');
Route::view('company', 'home.pages.company')->name('company');
Route::get('profile', [HomeController::class, 'profile'])->name('profile')->middleware('auth:client');
Route::post('profile/passenger/{passenger}/update', [HomeController::class, 'updatePassenger'])->name('passenger.update')->middleware('auth:client');
Route::view('search-booking', 'home.pages.search-booking')->name('search.booking');
Route::get('view-booking-details', [HomeController::class, 'viewBookingDetails'])->name('view.booking.details');
Route::get('view-hotel-booking-details', [HomeController::class, 'viewHotelBookingDetails'])->name('view.hotel.booking.details');
Route::post('search-booking', [HomeController::class, 'searchBooking'])->name('search.booking.submit');

Route::get('get-airport', [HomeController::class, 'airports'])->name('airport');
Route::post('verify-client', [FlightController::class, 'verifyClient'])->name('verify.client');
Route::view('mail', 'emails.sendBookingId')->name('mail');
Route::prefix('flights')->group(function () {
    Route::get('/', [FlightController::class, 'search'])->name('flights');
    Route::post('getBundles', [FlightController::class, 'getBundles'])->name('get_bundles');

    Route::post('booking-details', [FlightController::class, 'bookingDetails'])->name('booking_details');
    Route::get('booking', [FlightController::class, 'booking'])->name('flightBooking');
    Route::post('get-seat', [FlightController::class, 'getSeat'])->name('get_seat');
    Route::post('confirm-seats', [FlightController::class, 'confirmSeats'])->name('confirm_seats');
    Route::post('confirm-ancillaries', [FlightController::class, 'confirmAncillaries'])->name('confirm_ancillaries');
    Route::post('get-meal', [FlightController::class, 'getMeal'])->name('get_meal');
    Route::post('get-baggage', [FlightController::class, 'getBaggage'])->name('get_baggage');

    // Route::post('get-final-price', [FlightController::class, 'getFinalPrice'])->name('get_final_price');
    Route::post('payment', [FlightController::class, 'payment'])->name('payment');
    Route::post('bookFlight', [FlightController::class, 'bookFlight'])->name('bookFlight');
});
Route::post('confirm-booking', [FlightController::class, 'confirmBooking'])->name('confirm.booking');
Route::post('order-cancel', [FlightController::class, 'orderCancel'])->name('cancel.booking');
Route::post('fetch-flight-details', [FlightController::class, 'fetchDetails'])->name('fetch.flight.details');

// Hotel Routes
Route::prefix('hotels')->name('hotels.')->group(function () {
    Route::get('/', [HotelController::class, 'index'])->name('index');
    Route::get('/search', [HotelController::class, 'search'])->name('search');
    Route::get('/suggestions', [HotelController::class, 'searchSuggestions'])->name('suggestions');
    Route::match(['get', 'post'], '/show', [HotelController::class, 'show'])->name('show');
    Route::match(['get', 'post'], '/checkout', [HotelController::class, 'checkout'])->name('checkout');
    Route::post('/save-booking', [HotelController::class, 'saveBooking'])->name('saveBooking');
    Route::get('/payment/{id}', [HotelController::class, 'payment'])->name('payment');
    Route::get('/booking', [HotelController::class, 'bookingFlow'])->name('booking');
    Route::post('/confirm-booking/{id}', [HotelController::class, 'confirmBooking'])->name('confirmBooking');
    Route::get('/confirmation-partial/{id}', [HotelController::class, 'confirmationPartial'])->name('confirmationPartial');
});

// Profile
Route::post('update/client/{client}', [HomeController::class, 'updateClient'])->name('update.client');

// -------------------------------------ADMIN----------------------------------------------
Route::get('admin/login', 'App\Http\Controllers\Admin\AdminAuthController@loginPage')->name('admin.login');
Route::post('admin/login', 'App\Http\Controllers\Admin\AdminAuthController@login')->name('admin.login.submit');
Route::post('login', 'App\Http\Controllers\AuthController@login')->name('login.submit');
Route::post('register', 'App\Http\Controllers\AuthController@register')->name('register.submit');

Route::redirect('admin', 'admin/login')->name('admin.home');

Route::middleware(['auth:web'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('permission:view dashboard');
    Route::resource('roles', RoleController::class)->except(['show'])->middleware('permission:manage roles');

    Route::middleware(['permission:manage bookings'])->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'list'])->name('index');
        Route::get('fetch', [OrderController::class, 'fetch'])->name('fetch');
        Route::get('{booking}', [OrderController::class, 'details'])->name('details')->middleware('permission:view bookings');
        Route::post('log/add', [OrderController::class, 'logStore'])->name('log.add');
        Route::get('booking/{booking}/logs', [OrderController::class, 'logHistory'])->name('booking.logs');
        Route::put('booking/{booking}/update', [OrderController::class, 'update'])->name('booking.update')->middleware('permission:booking actions');

        Route::delete('{booking}', [OrderController::class, 'destroy'])->name('booking.destroy')->middleware('permission:delete bookings');

        Route::middleware(['permission:manage payment'])->prefix('payment')->name('payment.')->group(function () {
            Route::post('store', [OrderController::class, 'paymentStore'])->name('store');
            Route::put('{payment}', [OrderController::class, 'paymentUpdate'])->name('update');
            Route::delete('{payment}', [OrderController::class, 'paymentDestroy'])->name('destroy');
        });

        Route::post('ticket/create', [OrderController::class, 'ticketStore'])->name('ticket.create');

        // Hotel Admin Routes
        Route::get('hotel/{booking}', [OrderController::class, 'hotelDetails'])->name('hotel.details');
        Route::put('hotel/{booking}/update', [OrderController::class, 'hotelUpdate'])->name('hotel.update');
        Route::delete('hotel/{booking}/delete', [OrderController::class, 'hotelDestroy'])->name('hotel.destroy');
        Route::post('hotel/{booking}/confirm', [OrderController::class, 'confirmHotel'])->name('hotel.confirm');
        Route::post('hotel/{booking}/prebook', [OrderController::class, 'preBookHotel'])->name('hotel.prebook'); // Pre-book check
        Route::post('hotel/{booking}/cancel', [OrderController::class, 'cancelHotel'])->name('hotel.cancel');
    });

    Route::middleware(['permission:manage agents'])->prefix('agents')->name('agents.')->group(function () {
        Route::get('/', [AgentController::class, 'index'])->name('index');
        Route::post('/', [AgentController::class, 'store'])->name('store');
        Route::post('{agent}', [AgentController::class, 'update'])->name('update');
        Route::get('{agent}/delete', [AgentController::class, 'destroy'])->name('destroy');
        Route::get('{agent}/login', [AgentController::class, 'loginAs'])->name('login');
        Route::post('{agent}/update-permision', [AgentController::class, 'updatePermissions'])->name('update.permission');
    });

    Route::get('settings', [SettingController::class, 'view'])->name('settings')->middleware('permission:manage setting');

    Route::middleware(['permission:manage airports'])->prefix('airports')->name('airports.')->group(function () {
        Route::get('/', [AirportController::class, 'index'])->name('index');
        Route::get('list', [AirportController::class, 'list'])->name('list');
        Route::get('single', [AirportController::class, 'single'])->name('single');
        Route::post('/', [AirportController::class, 'store'])->name('store');
        Route::put('{id}', [AirportController::class, 'update'])->name('update');
        Route::delete('{id}', [AirportController::class, 'destroy'])->name('destroy');
        Route::get('{id}', [AirportController::class, 'show'])->name('show');
    });

    Route::middleware(['permission:download logs'])->prefix('logs')->name('logs.')->group(function () {
        Route::post('dates', [SettingController::class, 'getAvailableDates'])->name('dates');
        Route::get('download', [SettingController::class, 'downloadFile'])->name('download');
    });

    Route::middleware(['permission:manage setting'])->prefix('country-hotels')->name('country-hotels.')->group(function () {
        Route::post('preview', [SettingController::class, 'previewCountryInfo'])->name('preview');
        Route::post('store-bulk', [SettingController::class, 'storeBulkHotels'])->name('store-bulk');
        Route::post('search', [SettingController::class, 'searchHotels'])->name('search');
        Route::get('list', [SettingController::class, 'listHotels'])->name('list');
        Route::get('{id}/edit', [SettingController::class, 'editHotel'])->name('edit');
        Route::post('{id}/update', [SettingController::class, 'updateHotel'])->name('update');
        Route::delete('{id}', [SettingController::class, 'destroyHotel'])->name('destroy');
    });

    Route::resource('clients', ClientController::class)->middleware('permission:manage users');
    Route::post('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status')->middleware('permission:manage users');
});
Route::post('admin/logout', 'App\Http\Controllers\Admin\AdminAuthController@logout')->name('admin.logout');
// -------------------------------------ADMIN----------------------------------------------

Route::get(
    'send-notification',
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
