<?php

namespace App\Services;

use App\Models\User;
use App\Models\VitalSign;
use App\Models\MedicationLog;
use Carbon\Carbon;

/**
 * خدمة البيانات المتقدمة
 * تقوم بتحليل البيانات الطبية المتقدمة والإحصائيات
 */
class AdvancedDataService
{
    /**
     * تحليل اتجاهات العلامات الحيوية
     */
    public function analyzeVitalSignsTrends(User $user, $type, $days = 30)
    {
        $startDate = now()->subDays($days);
        $readings = VitalSign::where('user_id', $user->id)
            ->where('type', $type)
            ->whereBetween('recorded_at', [$startDate, now()])
            ->orderBy('recorded_at')
            ->get();

        if ($readings->isEmpty()) {
            return null;
        }

        $values = $readings->pluck('value')->toArray();
        $dates = $readings->pluck('recorded_at')->toArray();

        return [
            'type' => $type,
            'period_days' => $days,
            'readings_count' => count($values),
            'average' => round(array_sum($values) / count($values), 2),
            'min' => min($values),
            'max' => max($values),
            'latest' => end($values),
            'trend' => $this->calculateTrend($values),
            'data' => array_combine($dates, $values),
        ];
    }

    /**
     * حساب الاتجاه (صاعد، هابط، مستقر)
     */
    private function calculateTrend($values)
    {
        if (count($values) < 2) {
            return 'stable';
        }

        $firstHalf = array_slice($values, 0, (int)(count($values) / 2));
        $secondHalf = array_slice($values, (int)(count($values) / 2));

        $firstAverage = array_sum($firstHalf) / count($firstHalf);
        $secondAverage = array_sum($secondHalf) / count($secondHalf);

        $difference = $secondAverage - $firstAverage;
        $percentChange = ($difference / $firstAverage) * 100;

        if ($percentChange > 5) {
            return 'increasing';
        } elseif ($percentChange < -5) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    /**
     * مقارنة البيانات مع المعايير الطبية
     */
    public function compareWithNorms($type, $value)
    {
        $norms = [
            'blood_pressure_systolic' => ['min' => 90, 'max' => 120],
            'blood_pressure_diastolic' => ['min' => 60, 'max' => 80],
            'blood_sugar' => ['min' => 70, 'max' => 100],
            'temperature' => ['min' => 36.5, 'max' => 37.5],
            'heart_rate' => ['min' => 60, 'max' => 100],
            'oxygen_saturation' => ['min' => 95, 'max' => 100],
            'weight' => ['min' => 0, 'max' => 200],
        ];

        if (!isset($norms[$type])) {
            return null;
        }

        $norm = $norms[$type];

        if ($value < $norm['min']) {
            return 'low';
        } elseif ($value > $norm['max']) {
            return 'high';
        } else {
            return 'normal';
        }
    }

    /**
     * الحصول على التنبيهات الصحية
     */
    public function getHealthAlerts(User $user, $days = 7)
    {
        $alerts = [];
        $readings = VitalSign::where('user_id', $user->id)
            ->whereBetween('recorded_at', [now()->subDays($days), now()])
            ->get();

        foreach ($readings as $reading) {
            $status = $this->compareWithNorms($reading->type, $reading->value);
            if ($status !== 'normal') {
                $alerts[] = [
                    'type' => $reading->type,
                    'value' => $reading->value,
                    'status' => $status,
                    'date' => $reading->recorded_at,
                    'message' => $this->getAlertMessage($reading->type, $status),
                ];
            }
        }

        return $alerts;
    }

    /**
     * الحصول على رسالة التنبيه
     */
    private function getAlertMessage($type, $status)
    {
        $messages = [
            'blood_pressure' => [
                'high' => 'ضغط الدم مرتفع. يرجى مراجعة الطبيب.',
                'low' => 'ضغط الدم منخفض. يرجى الاستراحة والاتصال بالطبيب.',
            ],
            'blood_sugar' => [
                'high' => 'السكر في الدم مرتفع. تجنب السكريات.',
                'low' => 'السكر في الدم منخفض. تناول شيئاً حلواً فوراً.',
            ],
            'temperature' => [
                'high' => 'درجة الحرارة مرتفعة. قد تكون مصاباً بحمى.',
                'low' => 'درجة الحرارة منخفضة جداً. تدفأ واتصل بالطبيب.',
            ],
            'heart_rate' => [
                'high' => 'نبضات القلب مرتفعة. استرخ وحاول الهدوء.',
                'low' => 'نبضات القلب منخفضة جداً. اتصل بالطبيب.',
            ],
            'oxygen_saturation' => [
                'low' => 'تشبع الأكسجين منخفض. تنفس بعمق واتصل بالطبيب.',
            ],
        ];

        return $messages[$type][$status] ?? 'قراءة غير طبيعية. يرجى مراجعة الطبيب.';
    }

    /**
     * الحصول على ملخص صحي يومي
     */
    public function getDailyHealthSummary(User $user, $date = null)
    {
        $date = $date ?? now()->toDateString();

        $readings = VitalSign::where('user_id', $user->id)
            ->whereDate('recorded_at', $date)
            ->get();

        $medications = MedicationLog::where('user_id', $user->id)
            ->whereDate('logged_at', $date)
            ->get();

        $abnormalReadings = $readings->filter(function ($reading) {
            return $this->compareWithNorms($reading->type, $reading->value) !== 'normal';
        });

        return [
            'date' => $date,
            'vital_signs_count' => $readings->count(),
            'medications_taken' => $medications->where('status', 'taken')->count(),
            'medications_missed' => $medications->where('status', 'missed')->count(),
            'abnormal_readings_count' => $abnormalReadings->count(),
            'health_status' => $abnormalReadings->isEmpty() ? 'good' : 'needs_attention',
            'readings' => $readings,
            'medications' => $medications,
        ];
    }

    /**
     * الحصول على تقرير صحي أسبوعي
     */
    public function getWeeklyHealthReport(User $user, $week = null)
    {
        $startDate = $week ? Carbon::parse($week)->startOfWeek() : now()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        $readings = VitalSign::where('user_id', $user->id)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->get();

        $medications = MedicationLog::where('user_id', $user->id)
            ->whereBetween('logged_at', [$startDate, $endDate])
            ->get();

        $dailySummaries = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i)->toDateString();
            $dailySummaries[] = $this->getDailyHealthSummary($user, $date);
        }

        return [
            'week_start' => $startDate->toDateString(),
            'week_end' => $endDate->toDateString(),
            'total_readings' => $readings->count(),
            'total_medications' => $medications->count(),
            'compliance_rate' => $medications->count() > 0 
                ? round(($medications->where('status', 'taken')->count() / $medications->count()) * 100, 2)
                : 0,
            'daily_summaries' => $dailySummaries,
        ];
    }

    /**
     * تصدير البيانات إلى CSV
     */
    public function exportToCSV(User $user, $startDate, $endDate)
    {
        $readings = VitalSign::where('user_id', $user->id)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->get();

        $csv = "التاريخ,النوع,القيمة,الوحدة\n";

        foreach ($readings as $reading) {
            $csv .= "{$reading->recorded_at},{$reading->type},{$reading->value},{$reading->unit}\n";
        }

        return $csv;
    }

    /**
     * تصدير البيانات إلى JSON
     */
    public function exportToJSON(User $user, $startDate, $endDate)
    {
        $readings = VitalSign::where('user_id', $user->id)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->get();

        return json_encode([
            'user_id' => $user->id,
            'export_date' => now()->toDateTimeString(),
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'readings' => $readings,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
