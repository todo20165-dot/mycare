<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'value_1',
        'value_2',
        'unit',
        'notes',
        'is_abnormal',
        'measured_at',
    ];

    protected $casts = [
        'measured_at' => 'datetime',
        'is_abnormal' => 'boolean',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // الدوال المساعدة
    public function getTypeLabel()
    {
        $labels = [
            'blood_pressure' => 'ضغط الدم',
            'blood_sugar' => 'السكر في الدم',
            'temperature' => 'درجة الحرارة',
            'weight' => 'الوزن',
            'heart_rate' => 'نبضات القلب',
            'oxygen_saturation' => 'تشبع الأكسجين',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function checkIfAbnormal()
    {
        $abnormal = false;
        
        switch ($this->type) {
            case 'blood_pressure':
                // ضغط الدم الطبيعي: أقل من 120/80
                if ($this->value_1 >= 140 || $this->value_2 >= 90) {
                    $abnormal = true;
                }
                break;
            case 'blood_sugar':
                // السكر الطبيعي: 70-100 صيام
                if ($this->value_1 < 70 || $this->value_1 > 200) {
                    $abnormal = true;
                }
                break;
            case 'temperature':
                // درجة الحرارة الطبيعية: 36.5-37.5
                if ($this->value_1 < 36 || $this->value_1 > 38) {
                    $abnormal = true;
                }
                break;
            case 'heart_rate':
                // نبضات القلب الطبيعية: 60-100
                if ($this->value_1 < 60 || $this->value_1 > 100) {
                    $abnormal = true;
                }
                break;
            case 'oxygen_saturation':
                // تشبع الأكسجين الطبيعي: أكثر من 95%
                if ($this->value_1 < 95) {
                    $abnormal = true;
                }
                break;
        }

        $this->update(['is_abnormal' => $abnormal]);
        return $abnormal;
    }
}
