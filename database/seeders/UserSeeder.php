<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Demo user for testing
        User::create([
            'name' => 'Demo User',
            'email' => 'demo@shadowme.app',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        // Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@shadowme.app',
            'password' => bcrypt('admin123'),
            'is_admin' => true,
        ]);

        // Create test users
        User::factory(5)->create();
    }
}
