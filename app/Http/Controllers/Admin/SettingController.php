<?php

namespace App\Http\Controllers\Admin;

use App\Models\Airport;
use App\Models\CountryHotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TassProService;

class SettingController extends Controller
{
    public function view()
    {
        $airports = Airport::orderBy('name')->get();
        $countryHotels = CountryHotel::latest()->take(10)->get();
        $airlines = ['emirates', 'pia', 'flyjinnah', 'airblue', 'tasspro'];
        return view('admin.settings.index', compact('airlines', 'airports', 'countryHotels'));
    }
    // ----------------------- logs -----------------------
    public function getAvailableDates(Request $request)
    {
        $request->validate(['airline' => 'required|string']);
        $airline = $request->airline;

        $path = storage_path("logs/{$airline}");
        // dd($path);
        $dates = [];

        if (is_dir($path)) {
            $files = scandir($path);
            // dd($files);
            foreach ($files as $file) {
                // Match both "2025_10_23.log" and "bookings_2025_10_23.txt"
                if (preg_match('/(\d{4})_(\d{2})_(\d{2})\.(log|txt)$/', $file, $matches)) {
                    $date = "{$matches[1]}-{$matches[2]}-{$matches[3]}";
                    $type = str_starts_with($file, 'bookings_') ? 'booking' : 'log';
                    $dates[$date][$type] = $file;
                }
            }
        }

        return response()->json(['availableDates' => $dates]);
    }
    public function downloadFile(Request $request)
    {
        $request->validate([
            'airline' => 'required|string',
            'file' => 'required|string'
        ]);

        $filePath = storage_path("logs/{$request->airline}/{$request->file}");
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }
    // ----------------------- logs -----------------------

    // ----------------------- TassPro Hotel -----------------------
    public function previewCountryInfo(Request $request, TassProService $tassProService)
    {
        $request->validate(['country_code' => 'required|string']);
        $data = $tassProService->getCountryInfo($request->country_code);

        if (!$data || !isset($data['isSuccess']) || !$data['isSuccess']) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch data from API']);
        }

        return response()->json(['success' => true, 'data' => $data['data']]);
    }

    public function storeBulkHotels(Request $request)
    {
        // dd($request->all());
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '512M');

        $request->validate([
            'hotels' => 'required|array',
            'country_code' => 'required|string',
        ]);

        $countryCode = strtoupper($request->country_code);
        $data = collect($request->hotels)
            ->filter(function ($hotel) {
                return isset($hotel['key'], $hotel['value']) 
                    && !is_null($hotel['key']) 
                    && !is_null($hotel['value']);
            })
            ->map(function ($hotel) use ($countryCode) {
                return [
                    'country' => $countryCode,
                    'destinationcode' => $hotel['key'],
                    'city' => $hotel['value'],
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->toArray();

        foreach (array_chunk($data, 500) as $chunk) {
            CountryHotel::upsert(
                $chunk,
                ['country', 'destinationcode'],
                ['city', 'updated_at']
            );
        }

        return response()->json(['success' => true, 'message' => count($data) . ' records processed successfully']);
    }

    public function listHotels()
    {
        $hotels = CountryHotel::latest()->get();
        return response()->json($hotels);
    }

    public function editHotel($id)
    {
        $hotel = CountryHotel::find($id);
        return response()->json($hotel);
    }

    public function updateHotel(Request $request, $id)
    {
        $hotel = CountryHotel::findOrFail($id);
        $hotel->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroyHotel($id)
    {
        CountryHotel::destroy($id);
        return response()->json(['success' => true]);
    }

    public function searchHotels(Request $request)
    {
        $term = $request->term;
        $hotels = CountryHotel::where('city', 'LIKE', "%$term%")
            ->orWhere('destinationcode', 'LIKE', "%$term%")
            ->limit(20)
            ->get(['id', 'city', 'destinationcode']);

        $results = $hotels->map(function ($hotel) {
            return [
                'id' => $hotel->id,
                'text' => "{$hotel->city} ({$hotel->destinationcode})"
            ];
        });

        return response()->json(['results' => $results]);
    }
    // ----------------------- TassPro Hotel -----------------------
}
