<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'profile_image',
        'bio',
        'disease_id',
        'is_active',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login' => 'datetime',
    ];

    // العلاقات
    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function medicationLogs()
    {
        return $this->hasMany(MedicationLog::class);
    }

    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    public function familyLinksAsPatient()
    {
        return $this->hasMany(FamilyLink::class, 'patient_id');
    }

    public function familyLinksAsMember()
    {
        return $this->hasMany(FamilyLink::class, 'family_member_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function createdReports()
    {
        return $this->hasMany(Report::class, 'created_by');
    }

    public function medicalDocuments()
    {
        return $this->hasMany(MedicalDocument::class);
    }
    
    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(MedicalDocument::class, 'uploaded_by');
    }

    /**
     * الأطباء المرتبطون بهذا المستخدم (إذا كان مريضاً)
     */
    public function doctors()
    {
        return $this->belongsToMany(User::class, 'doctor_patient', 'patient_id', 'doctor_id')
            ->withPivot('specialization', 'status', 'notes', 'assigned_at', 'approved_at')
            ->wherePivot('status', 'approved');
    }

    /**
     * المرضى المرتبطون بهذا المستخدم (إذا كان طبيباً)
     */
    public function patients()
    {
        return $this->belongsToMany(User::class, 'doctor_patient', 'doctor_id', 'patient_id')
            ->withPivot('specialization', 'status', 'notes', 'assigned_at', 'approved_at')
            ->wherePivot('status', 'approved');
    }

    /**
     * جميع طلبات الربط مع الأطباء (بما فيها المعلقة والمرفوضة)
     */
    public function doctorRequests()
    {
        return $this->hasMany(DoctorPatient::class, 'patient_id');
    }

    /**
     * جميع المرضى المطلوبين من قبل هذا الطبيب
     */
    public function doctorPatients()
    {
        return $this->hasMany(DoctorPatient::class, 'doctor_id');
    }

    public function familyLinks()
    {
        return $this->hasMany(FamilyLink::class, 'family_member_id');
    }

    // الدوال المساعدة
    public function isPatient()
    {
        return $this->role === 'patient';
    }

    public function isDoctor()
    {
        return $this->role === 'doctor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isFamilyMember()
    {
        return $this->role === 'family_member';
    }

    public function getRoleLabel()
    {
        return match($this->role) {
            'admin' => 'مدير',
            'doctor' => 'طبيب',
            'patient' => 'مريض',
            'family_member' => 'فرد عائلة',
            default => 'غير محدد'
        };
    }
}
