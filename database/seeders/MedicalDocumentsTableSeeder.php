<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalDocument;
use App\Models\User;

class MedicalDocumentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if (rand(0,1)) {
                MedicalDocument::create([
                    'user_id' => $user->id,
                    'uploaded_by' => $user->id,
                    'title' => 'Document '.fake()->word(),
                    'description' => fake()->sentence(),
                    'type' => 'medical_record',
                    'file_path' => 'documents/sample.pdf',
                    'file_name' => 'sample.pdf',
                    'mime_type' => 'application/pdf',
                    'file_size' => 1024,
                    'document_date' => now()->toDateString(),
                ]);
            }
        }
    }
}
