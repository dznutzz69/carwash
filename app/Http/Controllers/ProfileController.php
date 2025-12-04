<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Get logged-in user info
    public function show()
    {
        return response()->json(Auth::user());
    }

    // Update name/email
    public function update(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->save();

        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }

    // Update password
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }
}
