<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\User;

class ReportsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if (rand(0,3) === 0) continue;
            Report::create([
                'user_id' => $user->id,
                'created_by' => $user->id,
                'type' => 'comprehensive_health',
                'title' => 'Health Report '.fake()->word(),
                'description' => fake()->sentence(),
                'start_date' => now()->subDays(7),
                'end_date' => now(),
                'data' => json_encode(['sample' => true]),
                'status' => 'generated',
            ]);
        }
    }
}
