<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'family_member_id',
        'relationship',
        'status',
        'notes',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // العلاقات
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function familyMember()
    {
        return $this->belongsTo(User::class, 'family_member_id');
    }

    // الدوال المساعدة
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    public function getRelationshipLabel()
    {
        $labels = [
            'parent' => 'الوالد/الوالدة',
            'child' => 'الابن/الابنة',
            'spouse' => 'الزوج/الزوجة',
            'sibling' => 'الأخ/الأخت',
            'other' => 'آخر',
        ];
        return $labels[$this->relationship] ?? $this->relationship;
    }

    public function isActive()
    {
        return $this->status === 'approved';
    }

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
}
