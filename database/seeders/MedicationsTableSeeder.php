<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;
use App\Models\User;

class MedicationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $count = rand(0, 3);
            for ($i = 0; $i < $count; $i++) {
                Medication::create([
                    'user_id' => $user->id,
                    'name' => 'Medication '.fake()->word(),
                    'description' => fake()->sentence(),
                    'dosage' => rand(1, 500). 'mg',
                    'frequency' => 'once_daily',
                    'start_date' => now()->subDays(rand(0, 30))->toDateString(),
                    'end_date' => null,
                    'reason' => fake()->word(),
                    'is_active' => true,
                ]);
            }
        }
    }
}
