<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    AppointmentController,
    ServiceController,
    PaymentController,
    CustomerController,
    AnalyticsController
};

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Redirect root
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Protected
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CUSTOMER ROUTES
    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/payments', [PaymentController::class, 'customerPayments'])->name('customer.payments');
        Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    });

    // ADMIN ONLY
    Route::middleware('role:admin')->group(function () {
        Route::resource('appointments', AppointmentController::class);
        Route::resource('services', ServiceController::class);
        Route::patch('services/{service}/update-status', [ServiceController::class, 'updateStatus'])->name('services.update-status');
        Route::resource('payments', PaymentController::class);

        Route::resource('customers', CustomerController::class);
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::get('/customers/walk-in/create', [CustomerController::class, 'walkIn'])->name('customers.walk-in');

        

        Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
    });
});
