<?php

use App\Services\CurrencyService;

if (!function_exists('convertCurrency')) {
    /**
     * Helper function to convert currency using the CurrencyService.
     *
     * @param float|int $amount
     * @param string $from
     * @param string $to
     * @param bool $format
     * @return string|float
     */
    function convertCurrency($amount, string $from = 'AED', ?string $to = null, bool $format = true)
    {
        // Use the session currency if $to is not explicitly provided, defaulting to PKR
        $to = $to ?? session('currency', 'PKR');

        // Resolve the service from the container
        $currencyService = app(CurrencyService::class);
        
        return $currencyService->convert($amount, $from, $to, $format);
    }
}
