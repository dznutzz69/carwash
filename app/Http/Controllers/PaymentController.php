<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    // ADMIN: View all payments (walk-in + online)
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = Payment::with([
            'appointment.user',     // Flutter user
            'appointment.customer', // Walk-in
            'appointment.service'
        ])
        ->latest()
        ->get();

        return response()->json(['data' => $payments]);
    }

    // ADMIN: Create payment manually (optional)
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $data['status'] = $data['status'] ?? 'pending';

        $payment = Payment::create($data);

        return response()->json(['data' => $payment], 201);
    }

    // ADMIN: Update payment (mark as paid)
    public function update(Request $request, Payment $payment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'method' => 'sometimes|nullable|string',
            'status' => 'sometimes|string',
            'paid_at'=> 'sometimes|nullable|date',
        ]);

        $payment->update($data);

        return response()->json(['data' => $payment]);
    }

    // ADMIN: Delete payment
    public function destroy(Request $request, Payment $payment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payment->delete();

        return response()->json(['message' => 'Deleted']);
    }

    // CUSTOMER: My payments
    public function myPayments(Request $request)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = Payment::whereHas('appointment', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id)
              ->orWhere('customer_id', $request->user()->id);
        })
        ->with(['appointment.service'])
        ->latest()
        ->get();

        return response()->json(['data' => $payments]);
    }
}
