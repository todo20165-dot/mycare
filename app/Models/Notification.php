<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'related_type',
        'related_id',
        'read_at',
        'is_email_sent',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_email_sent' => 'boolean',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // الدوال المساعدة
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public function getTypeLabel()
    {
        $labels = [
            'medication_reminder' => 'تذكير بالأدوية',
            'vital_sign_alert' => 'تنبيه العلامات الحيوية',
            'appointment_reminder' => 'تذكير الموعد',
            'system_notification' => 'إشعار النظام',
            'message' => 'رسالة',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public static function createMedicationReminder($userId, $medication)
    {
        return self::create([
            'user_id' => $userId,
            'title' => 'تذكير بالأدوية',
            'message' => 'حان وقت تناول دواء ' . $medication->name,
            'type' => 'medication_reminder',
            'related_type' => 'Medication',
            'related_id' => $medication->id,
        ]);
    }

    public static function createVitalSignAlert($userId, $vitalSign)
    {
        return self::create([
            'user_id' => $userId,
            'title' => 'تنبيه العلامات الحيوية',
            'message' => 'قراءة ' . $vitalSign->getTypeLabel() . ' غير طبيعية',
            'type' => 'vital_sign_alert',
            'related_type' => 'VitalSign',
            'related_id' => $vitalSign->id,
        ]);
    }
}
