<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'dosage',
        'frequency',
        'start_time',
        'start_date',
        'end_date',
        'reason',
        'side_effects',
        'instructions',
        'is_active',
        'adherence_rate',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(MedicationLog::class);
    }

    // الدوال المساعدة
    public function calculateAdherenceRate()
    {
        $totalLogs = $this->logs()->count();
        if ($totalLogs === 0) {
            return 0;
        }
        $takenLogs = $this->logs()->where('status', 'taken')->count();
        return round(($takenLogs / $totalLogs) * 100, 2);
    }

    public function updateAdherenceRate()
    {
        $this->update(['adherence_rate' => $this->calculateAdherenceRate()]);
    }

    public function getNextScheduledTime()
    {
        return $this->logs()
            ->where('status', 'pending')
            ->orderBy('scheduled_time')
            ->first();
    }
}
