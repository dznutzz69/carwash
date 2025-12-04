<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    // -----------------------
    // Admin: List all appointments
    // -----------------------
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::with('customer','service')->get();
        return response()->json(['data' => $appointments]);
    }

    // -----------------------
    // Admin: Create new appointment
    // -----------------------
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $appointment = Appointment::create($request->all());

        return response()->json(['data' => $appointment], 201);
    }

    // -----------------------
    // Admin: Show a specific appointment
    // -----------------------
    public function show(Request $request, Appointment $appointment)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $appointment]);
    }

    // -----------------------
    // Admin: Update appointment
    // -----------------------
    public function update(Request $request, Appointment $appointment)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'customer_id' => 'sometimes|exists:users,id',
            'service_id' => 'sometimes|exists:services,id',
            'date' => 'sometimes|date',
            'time' => 'sometimes|string',
            'status' => 'sometimes|string',
        ]);

        $appointment->update($request->all());

        return response()->json(['data' => $appointment], 200);
    }

    // -----------------------
    // Admin: Delete appointment
    // -----------------------
    public function destroy(Request $request, Appointment $appointment)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully']);
    }

    // -----------------------
    // Customer: View own appointments
    // -----------------------
    public function myAppointments(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $appointments = Appointment::with('service')
            ->where('customer_id', $user->id)
            ->get();

        return response()->json(['data' => $appointments]);
    }

    // -----------------------
    // Customer: Book new appointment
    // -----------------------
    public function bookAppointment(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $appointment = Appointment::create([
            'customer_id' => $user->id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'data' => $appointment
        ], 201);
    }
}