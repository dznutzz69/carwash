<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customers = Customer::latest()->get();

        return response()->json(['data' => $customers]);
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
        ]);

        $customer = Customer::create($data);

        return response()->json(['data' => $customer], 201);
    }

    public function update(Request $request, Customer $customer)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|nullable|string|max:50',
        ]);

        $customer->update($data);

        return response()->json(['data' => $customer]);
    }

    public function destroy(Request $request, Customer $customer)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
