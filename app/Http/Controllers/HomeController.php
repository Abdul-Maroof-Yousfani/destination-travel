<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Client;
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
    public function updateClient(Client $client, Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
            'accept_notification' => 'nullable|boolean',
        ]);
        if (!empty($validated['password'])) {
            $validated['original_password'] = null;
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $client->update($validated);

        return redirect()->back()->with('message', 'Client updated successfully.')->with('status', 'success');
    }
}