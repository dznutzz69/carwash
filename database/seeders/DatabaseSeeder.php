<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================
        // ADMIN + REGISTERED USER
        // ==========================

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone' => '09757979609',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $customer = User::create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '09123456789',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        // ==========================
        // WALK-IN CUSTOMER
        // ==========================
        $walkIn = Customer::create([
            'name' => 'Walk-in Client',
            'email' => null,
            'phone' => '09876543210',
        ]);

        // ==========================
        // CARWASH SERVICES
        // ==========================
        $services = [
            [
                'name' => 'Basic Wash',
                'description' => 'Exterior wash and rinse',
                'price' => 150,
            ],
            [
                'name' => 'Full Body Clean',
                'description' => 'Exterior wash + Interior vacuuming',
                'price' => 350,
            ],
            [
                'name' => 'Premium Detailing',
                'description' => 'Full body wash, wax, interior detailing',
                'price' => 850,
            ],
            [
                'name' => 'Engine Wash',
                'description' => 'Engine bay cleaning and conditioning',
                'price' => 500,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Fetch services
        $basicWash  = Service::where('name', 'Basic Wash')->first();
        $premium    = Service::where('name', 'Premium Detailing')->first();

        // ==========================
        // SAMPLE APPOINTMENTS
        // ==========================

        // Customer mobile booking
        $appt1 = Appointment::create([
            'user_id'    => $customer->id,
            'service_id' => $basicWash->id,
            'date'       => now()->addDay()->toDateString(),
            'time'       => '09:30 AM',
            'status'     => 'pending',
        ]);

        // Walk-in appointment
        $appt2 = Appointment::create([
            'customer_id'=> $walkIn->id,
            'service_id' => $premium->id,
            'date'       => now()->addDay()->toDateString(),
            'time'       => '01:00 PM',
            'status'     => 'approved',
        ]);

        // ==========================
        // PAYMENT SAMPLE
        // ==========================
        Payment::create([
            'appointment_id' => $appt2->id,
            'amount'         => $premium->price,
            'method'         => 'cash',
            'status'         => 'paid',
            'paid_at'        => now(),
        ]);

        $this->command->info("🚗 Carwash system seeded successfully!");
        $this->command->info("Admin Login → admin / password");
        $this->command->info("Customer Login → pedro@example.com / password");
    }
}
