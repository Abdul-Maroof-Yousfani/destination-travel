<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\View\Components\SessionTimeoutContainer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(250);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::statement("SET time_zone='+05:00'");
    }
}