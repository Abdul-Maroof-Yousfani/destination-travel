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
    // public function downloadLogs(Request $request)
    // {
    //     if ($request->type === 'flyjinnah') {
    //         $path = storage_path('logs/flyjinnah_logs.txt');
    //         if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

    //         return response()->download($path, 'flyjinnah_logs.txt', [
    //             'Content-Type' => 'text/plain',
    //         ]);
    //     } else if ($request->type === 'pia') {
    //         $path = storage_path('logs/pia_logs.txt');
    //         if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

    //         return response()->download($path, 'pia_logs.txt', [
    //             'Content-Type' => 'text/plain',
    //         ]);
    //     } else if ($request->type === 'emirates') {
    //         $path = storage_path('logs/emirates_logs.txt');
    //         if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

    //         return response()->download($path, 'emirates_logs.txt', [
    //             'Content-Type' => 'text/plain',
    //         ]);
    //     }
    //     return response()->back()->with('error', 'Invalid log type specified.');
    // }
    // public function downloadLogsBookings(Request $request)
    // {
    //     if ($request->type === 'flyjinnah') {
    //         $path = storage_path('logs/flyjinnah_logs_bookings.txt');
    //         if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

    //         return response()->download($path, 'flyjinnah_logs_bookings.txt', [
    //             'Content-Type' => 'text/plain',
    //         ]);
    //     } else if ($request->type === 'pia') {
    //         $path = storage_path('logs/pia_logs_bookings.txt');
    //         if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

    //         return response()->download($path, 'pia_logs_bookings.txt', [
    //             'Content-Type' => 'text/plain',
    //         ]);
    //     } else if ($request->type === 'emirates') {
    //         $path = storage_path('logs/emirates_logs_bookings.txt');
    //         if (!file_exists($path)) return redirect()->back()->with('error', 'Log file not found.');

    //         return response()->download($path, 'emirates_logs_bookings.txt', [
    //             'Content-Type' => 'text/plain',
    //         ]);
    //     }
    //     return response()->back()->with('error', 'Invalid log type specified.');
    // }

}