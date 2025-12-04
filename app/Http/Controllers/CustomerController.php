<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index() {
        return Customer::all();
    }

    public function store(Request $request){
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'nullable|string|max:20'
        ]);

        $customer = Customer::create($request->only('name','email','phone'));

        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer){
        $customer->update($request->only(['name','email','phone']));
        return response()->json($customer);
    }

    public function destroy(Customer $customer){
        $customer->delete();
        return response()->json(['message'=>'Deleted']);
    }
}
