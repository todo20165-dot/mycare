<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // create a few users
        User::factory()->count(10)->create();

        // create an admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@mycare.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // create a test patient user
        User::factory()->create([
            'name' => 'Test Patient',
            'email' => 'patient@example.com',
            'role' => 'patient',
        ]);
    }
}
