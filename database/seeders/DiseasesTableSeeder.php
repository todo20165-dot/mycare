<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disease;

class DiseasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diseases = [
            [
                'name' => 'مرض السكري',
                'specialization' => 'endocrinology',
                'description' => 'مرض مزمن يؤثر على كيفية معالجة الجسم لسكر الدم.',
            ],
            [
                'name' => 'ارتفاع ضغط الدم',
                'specialization' => 'cardiology',
                'description' => 'حالة طبية مزمنة يرتفع فيها ضغط الدم في الشرايين بشكل مستمر.',
            ],
            [
                'name' => 'الربو',
                'specialization' => 'general',
                'description' => 'حالة تضيق فيها المسالك الهوائية وتنتفخ وتنتج مخاطاً إضافياً.',
            ],
            [
                'name' => 'التهاب المفاصل',
                'specialization' => 'orthopedics',
                'description' => 'تورم وآلام في واحد أو أكثر من مفاصل الجسم.',
            ],
            [
                'name' => 'الصداع النصفي',
                'specialization' => 'neurology',
                'description' => 'صداع يمكن أن يسبب ألماً شديداً أو شعوراً بالنبض.',
            ],
            [
                'name' => 'الأكزيما',
                'specialization' => 'dermatology',
                'description' => 'حالة تجعل الجلد أحمر ومثيراً للحكة.',
            ],
        ];

        foreach ($diseases as $disease) {
            Disease::create($disease);
        }
    }
}
