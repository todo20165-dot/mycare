<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'type',
        'title',
        'description',
        'start_date',
        'end_date',
        'data',
        'file_path',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'data' => 'json',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // الدوال المساعدة
    public function getTypeLabel()
    {
        $labels = [
            'medication_adherence' => 'Medication Adherence Report',
            'vital_signs' => 'Vital Signs Report',
            'comprehensive_health' => 'Comprehensive Health Report',
            'custom' => 'Custom Report',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'قيد الإعداد',
            'generated' => 'تم الإنشاء',
            'sent' => 'تم الإرسال',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function generateMedicationAdherenceData()
    {
        $medications = $this->user->medications()
            ->whereBetween('created_at', [$this->start_date, $this->end_date])
            ->get();

        $data = [];
        foreach ($medications as $medication) {
            $data[] = [
                'medication_name' => $medication->name,
                'adherence_rate' => $medication->adherence_rate,
                'total_doses' => $medication->logs()->count(),
                'taken_doses' => $medication->logs()->where('status', 'taken')->count(),
                'missed_doses' => $medication->logs()->where('status', 'missed')->count(),
            ];
        }

        return $data;
    }

    public function generateVitalSignsData()
    {
        $vitalSigns = $this->user->vitalSigns()
            ->whereBetween('measured_at', [$this->start_date, $this->end_date])
            ->orderBy('measured_at')
            ->get()
            ->groupBy('type');

        $data = [];
        foreach ($vitalSigns as $type => $signs) {
            $data[$type] = $signs->map(function ($sign) {
                return [
                    'value_1' => $sign->value_1,
                    'value_2' => $sign->value_2,
                    'unit' => $sign->unit,
                    'measured_at' => $sign->measured_at,
                    'is_abnormal' => $sign->is_abnormal,
                ];
            })->toArray();
        }

        return $data;
    }
}
