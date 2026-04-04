<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'medication_id',
        'user_id',
        'scheduled_time',
        'taken_time',
        'status',
        'notes',
        'dosage_taken',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'taken_time' => 'datetime',
    ];

    // العلاقات
    public function medication()
    {
        return $this->belongsTo(Medication::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // الدوال المساعدة
    public function markAsTaken()
    {
        $this->update([
            'status' => 'taken',
            'taken_time' => now(),
        ]);
        $this->medication->updateAdherenceRate();
    }

    public function markAsMissed()
    {
        $this->update([
            'status' => 'missed',
        ]);
        $this->medication->updateAdherenceRate();
    }

    public function markAsSkipped()
    {
        $this->update([
            'status' => 'skipped',
        ]);
    }
}
