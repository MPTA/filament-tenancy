<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create admin user for tenant
        if (tenancy()->initialized) {
            $tenant = tenant();
            // Check if user already exists
            if (!User::where('email', $tenant->email)->exists()) {
                User::factory()->create([
                    'name' => 'Admin',
                    'email' => $tenant->email,
                    'password' => bcrypt('password'),
                ]);
            }
        } else {
            // For central database
            if (!User::where('email', 'test@example.com')->exists()) {
                User::factory()->create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                ]);
            }
        }
    }
}
