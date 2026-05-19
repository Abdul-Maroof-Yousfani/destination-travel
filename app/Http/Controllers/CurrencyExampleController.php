<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CurrencyService;

class CurrencyExampleController extends Controller
{
    protected CurrencyService $currencyService;

    /**
     * Inject CurrencyService via the constructor.
     *
     * @param CurrencyService $currencyService
     */
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * Show an example of currency conversion.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch current rates from PKR
        $aedToPkr = $this->currencyService->getExchangeRate('AED', 'PKR');
        $usdToPkr = $this->currencyService->getExchangeRate('USD', 'PKR');
        $sarToPkr = $this->currencyService->getExchangeRate('SAR', 'PKR');

        // Fetch reverse rates (usually more useful for display: 1 AED = X PKR)
        $pkrToAed = $this->currencyService->getExchangeRate('PKR', 'AED');
        $pkrToUsd = $this->currencyService->getExchangeRate('PKR', 'USD');
        $pkrToSar = $this->currencyService->getExchangeRate('PKR', 'SAR');

        return view('currency-example', compact(
            'aedToPkr',
            'usdToPkr',
            'sarToPkr',
            'pkrToAed',
            'pkrToUsd',
            'pkrToSar'
        ));
    }
}
