<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;

// --------- PUBLIC (NO AUTH) ---------
Route::post('register', [AuthController::class, 'register']); // Flutter customer registration
Route::post('login',    [AuthController::class, 'login']);    // Admin & customer login
Route::middleware('auth:sanctum')->group(function () {
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('dashboard/chart', [DashboardController::class, 'chartData']);
});


// --------- PROTECTED (Sanctum) ---------
Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me',      [AuthController::class, 'me']);

    // ===== SERVICES =====
    // Admin manages, customers can list
    Route::get('services',         [ServiceController::class, 'index']);   // all roles
    Route::post('services',        [ServiceController::class, 'store']);   // admin
    Route::put('services/{id}',    [ServiceController::class, 'update']);  // admin
    Route::delete('services/{id}', [ServiceController::class, 'destroy']); // admin

    // ===== CUSTOMERS (Users with role = customer) =====
    // Admin only
    Route::get('customers',                 [CustomerController::class, 'index']);
    Route::post('customers',                [CustomerController::class, 'store']);   // walk-in creation
    Route::put('customers/{customer}',      [CustomerController::class, 'update']);
    Route::delete('customers/{customer}',   [CustomerController::class, 'destroy']);

    // ===== APPOINTMENTS =====
    // Admin: see all, create for walk-in/registered, edit, delete
    Route::get('appointments',                      [AppointmentController::class, 'index']);
    Route::post('appointments',                     [AppointmentController::class, 'store']);
    Route::put('appointments/{appointment}',        [AppointmentController::class, 'update']);
    Route::delete('appointments/{appointment}',     [AppointmentController::class, 'destroy']);

    // Customer (Flutter): own appointments & booking
    Route::get('my-appointments',   [AppointmentController::class, 'myAppointments']);
    Route::post('book-appointment', [AppointmentController::class, 'bookAppointment']);

    // ===== PAYMENTS =====
    Route::get('payments',          [PaymentController::class, 'index']);      // admin
    Route::post('payments',         [PaymentController::class, 'store']);      // admin
    Route::put('payments/{payment}',[PaymentController::class, 'update']);     // admin
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy']); // admin

    Route::get('my-payments',       [PaymentController::class, 'myPayments']); // customer

    
});
