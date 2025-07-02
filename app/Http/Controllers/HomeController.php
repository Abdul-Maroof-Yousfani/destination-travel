<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Airport;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function airports(Request $request)
    {
        $search = $request->input('term');

        $query = Airport::query();

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('code', 'LIKE', '%' . $search . '%');
        } else {
            return response()->json(['results' => []]);
        }

        $airports = $query->orderBy('name')->limit(20)->get();

        $results = $airports->map(function ($airport) {
            return [
                'id' => $airport->code,
                'text' => $airport->name . ' (' . $airport->code . ')',
            ];
        });

        return response()->json(['results' => $results]);
    }
}