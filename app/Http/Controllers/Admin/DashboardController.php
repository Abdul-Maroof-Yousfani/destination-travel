<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.dashboard');
    }
    public function bookingDetails()
    {
        return view('admin.dashboard');
    }
    public function downloadLogs()
    {
        $path = storage_path('logs/emirates_logs.txt');
        if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

        return response()->download($path, 'emirates_logs.txt', [
            'Content-Type' => 'text/plain',
        ]);
    }
    public function downloadLogsBookings()
    {
        $path = storage_path('logs/emirates_logs_bookings.txt');
        if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

        return response()->download($path, 'emirates_logs_bookings.txt', [
            'Content-Type' => 'text/plain',
        ]);
    }

}