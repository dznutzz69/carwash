<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'customers'     => User::where('role', 'customer')->count(),
            'appointments'  => Appointment::count(),
            'completed'     => Appointment::where('status', 'done')->count(),
            'revenue'       => Payment::where('status', 'paid')->sum('amount'),
        ]);
    }

    public function chartData(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Group by month
        $data = Payment::where('status', 'paid')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }
}
