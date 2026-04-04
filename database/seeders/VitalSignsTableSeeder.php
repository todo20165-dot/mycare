<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VitalSign;
use App\Models\User;

class VitalSignsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if (rand(0,1)) {
                VitalSign::create([
                    'user_id' => $user->id,
                    'type' => 'blood_pressure',
                    'value_1' => 120,
                    'value_2' => 80,
                    'unit' => 'mmHg',
                    'notes' => null,
                    'is_abnormal' => false,
                    'measured_at' => now()->subDays(rand(0,10)),
                ]);
            }
        }
    }
}
