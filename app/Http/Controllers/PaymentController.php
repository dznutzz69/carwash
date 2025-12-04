<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    // -----------------------
    // Admin: List all payments
    // -----------------------
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = Payment::with('appointment')->get();
        return response()->json(['data' => $payments]);
    }

    // -----------------------
    // Admin: Create payment
    // -----------------------
    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'status' => 'required|string'
        ]);

        $payment = Payment::create($request->all());

        return response()->json(['data' => $payment], 201);
    }

    // -----------------------
    // Admin: Update payment
    // -----------------------
    public function update(Request $request, Payment $payment)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payment->update($request->all());

        return response()->json(['data' => $payment]);
    }

    // -----------------------
    // Admin: Delete payment
    // -----------------------
    public function destroy(Request $request, Payment $payment)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted']);
    }

    // -----------------------
    // Customer: View own payments
    // -----------------------
    public function myPayments(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payments = Payment::whereHas('appointment', function ($q) use ($user) {
            $q->where('customer_id', $user->id);
        })->get();

        return response()->json(['data' => $payments]);
    }
}
