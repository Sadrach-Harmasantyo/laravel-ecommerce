<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a new admin user if none exists
        if (!User::where('email', 'admin@email.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@email.com',
                'role' => 'admin',
                'password' => Hash::make('rahasia'), // Change this to a secure password
            ]);
        } else {
            // Update an existing user to be an admin
            User::where('email', 'admin@email.com')
                ->update(['role' => 'admin']);
        }
    }
}