<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * The base URL for the ExchangeRate-API (Open access).
     */
    protected string $apiUrl = 'https://open.er-api.com/v6/latest/';

    /**
     * Default exchange rate to fallback to if the API and Cache fail.
     */
    protected float $defaultRate = 76.0;

    /**
     * Fetch the live exchange rate with caching and fallback mechanisms.
     *
     * @param string $from
     * @param string $to
     * @return float
     */
    public function getExchangeRate(string $from = 'AED', string $to = 'PKR'): float
    {
        // Define a unique cache key for the currency pair
        $cacheKey = "currency_rate_v3_{$from}_{$to}";

        return Cache::remember($cacheKey, 3600, function () use ($from, $to) {
            try {
                // ExchangeRate-API structure: https://open.er-api.com/v6/latest/{BASE}
                // withoutVerifying() is used because of local environment SSL certificate issues
                $response = Http::withoutVerifying()->timeout(5)->get($this->apiUrl . $from);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (isset($data['rates'][$to])) {
                        return (float) $data['rates'][$to];
                    }
                }
                
                Log::warning("Currency API failed to retrieve rate for {$from} to {$to}. Status: " . $response->status() . " Response: " . $response->body());
                return $this->defaultRate;
            } catch (\Exception $e) {
                Log::error("Currency API Exception: " . $e->getMessage());
                return $this->defaultRate;
            }
        });
    }

    /**
     * Convert an amount from one currency to another.
     *
     * @param float|int $amount
     * @param string $from
     * @param string $to
     * @param bool $format
     * @return string|float
     */
    public function convert($amount, string $from = 'AED', string $to = 'PKR', bool $format = true)
    {
        $rate = $this->getExchangeRate($from, $to);
        $convertedAmount = $amount * $rate;

        if ($format) {
            // PKR usually doesn't need decimals for large amounts, using 0 decimal places.
            $decimals = ($to === 'PKR') ? 0 : 2;
            return $to . ' ' . number_format($convertedAmount, $decimals);
        }

        return $convertedAmount;
    }
}
