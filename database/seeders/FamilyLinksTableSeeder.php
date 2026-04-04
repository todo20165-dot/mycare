<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamilyLink;
use App\Models\User;

class FamilyLinksTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $count = $users->count();
        if ($count < 2) {
            return;
        }

        // create some family links between random users
        for ($i = 0; $i < 10; $i++) {
            $a = $users->random();
            $b = $users->random();
            if ($a->id === $b->id) continue;
            FamilyLink::firstOrCreate([
                'patient_id' => $a->id,
                'family_member_id' => $b->id,
            ],[
                'relationship' => 'other',
                'status' => 'approved',
            ]);
        }
    }
}
