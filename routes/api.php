<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    CustomerController,
    ServiceController,
    AppointmentController,
    PaymentController
};

// -------------------
// Auth routes
// -------------------
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// -------------------
// Admin routes (protected by Sanctum, role checked in controller)
// -------------------
Route::middleware('auth:sanctum')->group(function() {
    // Services CRUD
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('services', [ServiceController::class, 'store']);
    Route::put('services/{id}', [ServiceController::class, 'update']);
    Route::delete('services/{id}', [ServiceController::class, 'destroy']);

    // Appointments CRUD
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::put('appointments/{id}', [AppointmentController::class, 'update']);
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy']);

    // Payments CRUD
    Route::get('payments', [PaymentController::class, 'index']);
    Route::post('payments', [PaymentController::class, 'store']);
    Route::put('payments/{id}', [PaymentController::class, 'update']);
    Route::delete('payments/{id}', [PaymentController::class, 'destroy']);

    // Customers CRUD
    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customers', [CustomerController::class, 'store']);
    Route::put('customers/{id}', [CustomerController::class, 'update']);
    Route::delete('customers/{id}', [CustomerController::class, 'destroy']);
});

// -------------------
// Customer routes (protected by Sanctum, role checked in controller)
// -------------------
Route::middleware('auth:sanctum')->group(function() {
    Route::get('my-appointments', [AppointmentController::class, 'myAppointments']);
    Route::get('my-payments', [PaymentController::class, 'myPayments']);
    Route::post('book-appointment', [AppointmentController::class, 'bookAppointment']);
});