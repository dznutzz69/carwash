<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;

class AppointmentController extends Controller
{
    // ================== ADMIN: LIST ALL APPOINTMENTS ==================
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::with(['user', 'service'])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json(['data' => $appointments]);
    }

    // ================== ADMIN: CREATE APPOINTMENT ==================
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'service_id'  => 'required|exists:services,id',
            'date'        => 'required|date',
            'time'        => 'required|string',
            // ✔ Admin selects a USER who is a customer
            'customer_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->customer_id);

        $appointment = Appointment::create([
            'user_id'    => $user->id, // always store linked user here
            'service_id' => $request->service_id,
            'date'       => $request->date,
            'time'       => $request->time,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => 'Appointment successfully created.',
            'data'    => $appointment,
        ], 201);
    }

    // ================== ADMIN: UPDATE APPOINTMENT ==================
    public function update(Request $request, Appointment $appointment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->update($request->only(['date', 'time', 'status']));

        // Auto-create payment when marked paid
        if ($request->status === 'paid' && !$appointment->payment) {
            Payment::create([
                'appointment_id' => $appointment->id,
                'amount'         => $appointment->service->price,
                'status'         => 'paid',
                'paid_at'        => now(),
            ]);
        }

        return response()->json([
            'message' => 'Updated',
            'data'    => $appointment,
        ]);
    }

    // ================== ADMIN: DELETE APPOINTMENT ==================
    public function destroy(Request $request, Appointment $appointment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // ================== CUSTOMER: BOOK (FLUTTER) ==================
    public function bookAppointment(Request $request)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date'       => 'required|date',
            'time'       => 'required|string',
        ]);

        $appointment = Appointment::create([
            'user_id'    => $request->user()->id,
            'service_id' => $request->service_id,
            'date'       => $request->date,
            'time'       => $request->time,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => 'Booked',
            'data'    => $appointment,
        ], 201);
    }

    // ================== CUSTOMER: VIEW OWN APPOINTMENTS ==================
    public function myAppointments(Request $request)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::with('service')
            ->where('user_id', $request->user()->id)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json(['data' => $appointments]);
    }
}
