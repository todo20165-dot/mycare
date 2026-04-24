<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DiseasesTableSeeder::class,
            UsersTableSeeder::class,
            MedicationsTableSeeder::class,
            MedicationLogsTableSeeder::class,
            FamilyLinksTableSeeder::class,
            MedicalDocumentsTableSeeder::class,
            ReportsTableSeeder::class,
            NotificationsTableSeeder::class,
            VitalSignsTableSeeder::class,
        ]);
    }
}
