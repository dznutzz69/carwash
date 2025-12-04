<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Admin dashboard stats
        $totalCustomers = User::where('role', 'customer')->count();
        $totalAppointments = Appointment::count();
        $totalPayments = Payment::sum('amount');
        $totalServices = Service::count();

        return response()->json([
            'totalCustomers' => $totalCustomers,
            'totalAppointments' => $totalAppointments,
            'totalPayments' => $totalPayments,
            'totalServices' => $totalServices,
        ]);
    }
}
