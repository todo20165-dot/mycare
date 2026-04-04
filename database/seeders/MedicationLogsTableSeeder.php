<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;
use App\Models\MedicationLog;

class MedicationLogsTableSeeder extends Seeder
{
    public function run(): void
    {
        $meds = Medication::all();
        foreach ($meds as $med) {
            $logs = rand(1, 5);
            for ($i = 0; $i < $logs; $i++) {
                MedicationLog::create([
                    'medication_id' => $med->id,
                    'user_id' => $med->user_id,
                    'scheduled_time' => now()->subDays(rand(0, 10))->addHours(rand(0,23)),
                    'taken_time' => (rand(0,1) ? now()->subDays(rand(0,5))->addHours(rand(0,23)) : null),
                    'status' => 'taken',
                    'notes' => null,
                    'dosage_taken' => null,
                ]);
            }
        }
    }
}
