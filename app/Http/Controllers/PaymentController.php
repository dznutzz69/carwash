<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    // ADMIN: View all payments
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = Payment::with([
            'appointment.customer',
            'appointment.service'
        ])->latest()->get();

        return response()->json(['data' => $payments]);
    }

    // ADMIN: Create payment
    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount'         => 'required|numeric|min:0',
            'method'         => 'nullable|string',
        ]);

        $validated['status'] = 'pending';

        $payment = Payment::create($validated);

        return response()->json([
            'message' => 'Payment added successfully',
            'data' => $payment
        ], 201);
    }

    // ADMIN: Confirm payment
    public function update(Request $request, Payment $payment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        if ($validated['status'] === 'paid') {
            $validated['paid_at'] = now();
        }

        $payment->update($validated);

        return response()->json([
            'message' => 'Payment updated',
            'data' => $payment
        ]);
    }

    // ADMIN: Delete
    public function destroy(Request $request, Payment $payment)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted']);
    }

    // CUSTOMER: My payments
    public function myPayments(Request $request)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = Payment::whereHas('appointment', function ($q) use ($request) {
            $q->where('customer_id', $request->user()->id);
        })->with(['appointment.service'])->latest()->get();

        return response()->json(['data' => $payments]);
    }
}
