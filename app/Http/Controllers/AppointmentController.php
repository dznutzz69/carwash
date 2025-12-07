<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    private function ensureAdmin(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    private function ensureCustomer(Request $request)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }

    // -------------------------
    // ADMIN: Get all appointments
    // -------------------------
    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $appointments = Appointment::with(['user', 'customer', 'service'])
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json($appointments); 
        // Vue expects nested relations exactly like this structure.
    }

    // -------------------------
    // ADMIN: Create appointment (walk-in)
    // -------------------------
    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'service_id'  => 'required|exists:services,id',
            'date'        => 'required|date',
            'time'        => 'required|string',
        ]);

        $appointment = Appointment::create([
            'customer_id' => $request->customer_id, // walk-in
            'service_id'  => $request->service_id,
            'date'        => $request->date,
            'time'        => $request->time,
            'status'      => 'pending',
        ]);

        return response()->json($appointment, 201);
    }

    // -------------------------
    // ADMIN: Update (confirm/change status)
    // -------------------------
    public function update(Request $request, Appointment $appointment)
    {
        $this->ensureAdmin($request);

        $request->validate([
            'status' => 'sometimes|string',
            'date'   => 'sometimes|date',
            'time'   => 'sometimes|string',
        ]);

        $appointment->update($request->only(['status','date','time']));

        return response()->json($appointment);
    }

    // -------------------------
    // ADMIN: Delete
    // -------------------------
    public function destroy(Request $request, Appointment $appointment)
    {
        $this->ensureAdmin($request);

        $appointment->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // -------------------------
    // CUSTOMER: List my bookings (Flutter)
    // -------------------------
    public function myAppointments(Request $request)
    {
        $this->ensureCustomer($request);

        $appointments = Appointment::with('service')
            ->where('user_id', $request->user()->id)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        return response()->json(['data' => $appointments]);
    }

    // -------------------------
    // CUSTOMER: Book using Flutter
    // -------------------------
    public function bookAppointment(Request $request)
    {
        $this->ensureCustomer($request);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date'       => 'required|date',
            'time'       => 'required|string',
        ]);

        $appointment = Appointment::create([
            'user_id'    => $request->user()->id, // Flutter booking user
            'service_id' => $request->service_id,
            'date'       => $request->date,
            'time'       => $request->time,
            'status'     => 'pending',
        ]);

        return response()->json([
            'message' => 'Appointment booked successfully',
            'data'    => $appointment
        ], 201);
    }
}
