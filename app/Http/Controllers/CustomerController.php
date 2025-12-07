<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customers = User::where('role', 'customer')
            ->select('id', 'name', 'email', 'created_at')
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
            'name'  => 'required|string|max:255',
            'email' => 'nullable|string|email|unique:users,email',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email ?? null, // walk-in no email
            'password' => bcrypt('password'),
            'role'     => 'customer',
        ]);

        return response()->json(['message' => 'Customer added', 'data' => $user], 201);
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
