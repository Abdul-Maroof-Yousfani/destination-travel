<?php

namespace App\Services;

use App\Services\PiaService;
use App\Services\EmiratesService;
use App\Services\FlyJinnahService;

class FlightAggregatorService
{
    protected $services;
    public function __construct(
        PiaService $pia,
        EmiratesService $emirate,
        FlyJinnahService $flyjinnah
    ) {
        $this->services = [ $pia, $emirate, $flyjinnah ];
    }
    public function searchAllFlights($params) {
        $flights = collect();

        foreach ($this->services as $service) {
            $flights = $flights->merge($service->searchFlights($params));
        }
        dd($flights);

        return $flights->values();
        // return $flights->sortBy('price')->values(); // Optional: sort by price
    }
}
