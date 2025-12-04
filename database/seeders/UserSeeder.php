<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::create([
            'name' => 'Admin',
            'email' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        // Sample customer
        User::create([
            'name' => 'Test Customer',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'customer'
        ]);
    }
}
