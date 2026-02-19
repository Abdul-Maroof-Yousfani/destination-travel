<?php

namespace App\Services;

use Carbon\Carbon;
use SimpleXMLElement;
use GuzzleHttp\Client;
use App\Models\Booking;
use App\Services\HelperService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Exception\RequestException;

class TassProService
{
    protected $helperService;
    protected $config;
    protected $url;
    protected $apiKey;
    protected $logPath;
    protected $logPathBooking;
    protected $regenerateLogs = true;

    public function __construct(HelperService $helperService)
    {
        $this->helperService = $helperService;
        $this->config        = config('services.tasspro');

        $logDir = storage_path('logs/tasspro');
        if (!is_dir($logDir)) mkdir($logDir, 0755, true);

        $this->logPath        = $logDir . '/' . now()->format('Y_m_d') . '.log';
        $this->logPathBooking = $logDir . '/bookings_' . now()->format('Y_m_d') . '.log';

        $this->url           = $this->config['url'];
        $this->apiKey        = $this->config['apikey'];
    }
    public function sendRequest($endpoint, $request, $isBooking = false)
    {
        try {
            if ($this->regenerateLogs) {
                // dd($endpoint, $request, $isBooking);
                file_put_contents($this->logPath, "{$endpoint} Request:\n" . json_encode($request) . "\n\n\n", FILE_APPEND);
                if ($isBooking) {
                    file_put_contents($this->logPathBooking, "{$endpoint} Request:\n" . json_encode($request) . "\n\n\n", FILE_APPEND);
                }
            }
            $response = $this->helperService->postJson($this->url . '/' . $endpoint, ['apikey' => $this->apiKey], $request);
            if ($this->regenerateLogs) {
                file_put_contents($this->logPath, "{$endpoint} Response:\n" . json_encode($response?->body()) . "\n\n\n\n\n\n", FILE_APPEND);
                if ($isBooking) {
                    file_put_contents($this->logPathBooking, "{$endpoint} Response:\n" . json_encode($response?->body()) . "\n\n\n\n\n\n", FILE_APPEND);
                }
            }

            if (!$response || !$response->successful()) {
                \Log::error('TassPro request failed', [
                    'status' => $response?->status(),
                    'response' => $response?->body()
                ]);
                return ['error' => "TassPro request failed ({$endpoint}).", 'details' => $response?->body()];
            }

            return $response->json();
        } catch (RequestException $e) {
            $response = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Log::error('TassPro API Request Error', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            throw new \Exception('API request failed: ' . $e->getMessage());
        }
    }

    public function getCountryInfo($countryCode)
    {
        try {
            $endpoint = 'country-info';
            $request = ['countryCode' => strtoupper($countryCode)];
            $response = $this->sendRequest($endpoint, $request);
            return $response;
        } catch (\Exception $e) {
            Log::error("TassPro getCountryInfo Exception: " . $e->getMessage());
            return null;
        }
    }

    public function getRoomDetails($sessionId, $hotelCode, $rooms, $currency = 'AED')
    {
        try {
            $endpoint = 'RoomDetails';
            $request = [
                'SessionId' => $sessionId,
                'SearchParameter' => [
                    'HotelCode' => $hotelCode,
                    'Currency'  => $currency,
                    'Rooms'     => $rooms
                ]
            ];

            $response = $this->sendRequest($endpoint, $request);
            return $response;
        } catch (\Exception $e) {
            Log::error("TassPro getRoomDetails Exception: " . $e->getMessage());
            return null;
        }
    }

    public function searchHotels($params)
    {
        // dd($params);
        try {
            $endpoint = 'search';

            $request = [
                'SearchParameter' => [
                    'DestinationCode' => $params['destination_code'] ?? '',
                    'CountryCode'     => $params['country_code'] ?? '',
                    'Nationality'     => $params['nationality'] ?? $params['country_code'] ?? '',
                    'Currency'        => $params['currency'] ?? 'AED',
                    'CheckInDate'     => $params['check_in'] ?? '',
                    'CheckOutDate'    => $params['check_out'] ?? '',
                    'Rooms'           => $params['rooms'] ?? [],
                    'TassProInfo'     => [
                        'CustomerCode' => $this->config['customer_code'] ?? '',
                        'RegionID'     => $this->config['region_id'] ?? '',
                    ]
                ]
            ];
            // dd($payload);

            $response = $this->sendRequest($endpoint, $request);
            // dd($response->json(), $payload);

            return $response;
        } catch (\Exception $e) {
            Log::error("TassPro searchHotels Exception: " . $e->getMessage());
            return null;
        }
    }

    public function preBook($sessionId, $hotelCode, $groupCode, $rateKeys, $currency = 'AED')
    {
        try {
            $endpoint = 'PreBook';
            $request = [
                'SessionId' => $sessionId,
                'SearchParameter' => [
                    'HotelCode' => $hotelCode,
                    'GroupCode' => (int)$groupCode,
                    'Currency'  => $currency,
                    'RateKeys'  => [
                        'RateKey' => (array)$rateKeys
                    ]
                ]
            ];
            $response = $this->sendRequest($endpoint, $request);
            // dd($response->json());

            return $response;
        } catch (\Exception $e) {
            Log::error("TassPro preBook Exception: " . $e->getMessage());
            return null;
        }
    }

    public function bookHotel($payload)
    {
        try {
            $endpoint = 'book';
            // dd($payload);
            $response = $this->sendRequest($endpoint, $payload, true);

            return $response;
        } catch (\Exception $e) {
            Log::error("TassPro bookHotel Exception: " . $e->getMessage());
            return null;
        }
    }

    public function cancelHotel($payload)
    {
        try {
            $endpoint = 'cancel';
            return $this->sendRequest($endpoint, $payload, true);
        } catch (\Exception $e) {
            Log::error("TassPro cancelHotel Exception: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}
