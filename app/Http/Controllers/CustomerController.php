<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customers = User::where('role', 'customer')
            ->select('id', 'name', 'email', 'phone', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:15|unique:users,phone',
            'password' => 'required|string|min:5',
            'email'    => 'nullable|email|unique:users,email,NULL,id,email,NOT_NULL'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email ?: null,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $user
        ], 201);
    }

    public function destroy(Request $request, User $customer)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted']);
    }
}
