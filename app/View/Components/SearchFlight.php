<?php

namespace App\View\Components;

use Closure;
use App\Models\Airport;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class SearchFlight extends Component
{
    // public $airports;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // $this->airports = Cache::rememberForever('airports_list', function () {
        //     return Airport::orderBy('name')->pluck('name', 'code')->toArray();
        // });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-flight');
    }
}
// How to clear the cache manually if needed:
// php artisan cache:forget airports_list
