<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * نموذج DoctorPatient
 * يمثل العلاقة بين الطبيب والمريض
 */
class DoctorPatient extends Model
{
    use HasFactory;

    protected $table = 'doctor_patient';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'specialization',
        'status',
        'notes',
        'assigned_at',
        'approved_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * العلاقة مع الطبيب
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * العلاقة مع المريض
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * موافقة الطبيب على المريض
     */
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * رفض الطبيب للمريض
     */
    public function reject()
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * الحصول على حالة العلاقة بالعربية
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'inactive' => 'غير نشط',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * التحقق من كون العلاقة نشطة
     */
    public function isActive()
    {
        return $this->status === 'approved';
    }
}
