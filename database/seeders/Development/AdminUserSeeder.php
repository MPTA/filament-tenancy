<?php

namespace Database\Seeders\Development;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'masihparvaz@gmail.com',
            ],
            [
                'name' => 'admin',
                'email' => 'masihparvaz@gmail.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user created successfully!');
        $this->command->line('   Email: masihparvaz@gmail.com');
        $this->command->line('   Password: password');
    }
}
