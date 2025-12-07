<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Payment;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        $appointments = Appointment::with(['user','customer','service'])->latest()->get();
        return response()->json(['data'=>$appointments]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $appointment = Appointment::create([
            'customer_id' => $request->customer_id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        return response()->json(['data'=>$appointment],201);
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        $appointment->update($request->only(['date','time','status']));

        // Auto-create payment when marked as done
        if ($request->status === 'done' && !$appointment->payment) {
            Payment::create([
                'appointment_id' => $appointment->id,
                'amount' => $appointment->service->price,
                'status' => 'pending'
            ]);
        }

        return response()->json(['data'=>$appointment]);
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        $appointment->delete();
        return response()->json(['message'=>'Deleted']);
    }

    // CUSTOMER MOBILE BOOKING
    public function bookAppointment(Request $request)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json(['error'=>'Unauthorized'],403);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $appointment = Appointment::create([
            'user_id' => $request->user()->id,
            'service_id' => $request->service_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        return response()->json(['message'=>'Booked','data'=>$appointment],201);
    }
}
