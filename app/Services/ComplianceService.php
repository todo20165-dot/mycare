<?php

namespace App\Services;

use App\Models\User;
use App\Models\MedicationLog;
use App\Models\Medication;
use Carbon\Carbon;

/**
 * خدمة حساب الامتثال والإحصائيات
 * تقوم بحساب معدلات الالتزام بالأدوية والإحصائيات المتقدمة
 */
class ComplianceService
{
    /**
     * حساب معدل الالتزام اليومي
     */
    public function calculateDailyCompliance(User $user, $date = null)
    {
        $date = $date ?? now()->toDateString();

        $logs = MedicationLog::where('user_id', $user->id)
            ->whereDate('logged_at', $date)
            ->get();

        if ($logs->isEmpty()) {
            return 0;
        }

        $taken = $logs->where('status', 'taken')->count();
        $total = $logs->count();

        return round(($taken / $total) * 100, 2);
    }

    /**
     * حساب معدل الالتزام الأسبوعي
     */
    public function calculateWeeklyCompliance(User $user, $week = null)
    {
        $startDate = $week ? Carbon::parse($week)->startOfWeek() : now()->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        return $this->calculateComplianceForPeriod($user, $startDate, $endDate);
    }

    /**
     * حساب معدل الالتزام الشهري
     */
    public function calculateMonthlyCompliance(User $user, $month = null)
    {
        $startDate = $month ? Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        return $this->calculateComplianceForPeriod($user, $startDate, $endDate);
    }

    /**
     * حساب معدل الالتزام لفترة زمنية
     */
    public function calculateComplianceForPeriod(User $user, $startDate, $endDate)
    {
        $logs = MedicationLog::where('user_id', $user->id)
            ->whereBetween('logged_at', [$startDate, $endDate])
            ->get();

        if ($logs->isEmpty()) {
            return 0;
        }

        $taken = $logs->where('status', 'taken')->count();
        $total = $logs->count();

        return round(($taken / $total) * 100, 2);
    }

    /**
     * الحصول على إحصائيات الامتثال المفصلة
     */
    public function getComplianceStatistics(User $user, $days = 30)
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        $logs = MedicationLog::where('user_id', $user->id)
            ->whereBetween('logged_at', [$startDate, $endDate])
            ->get();

        $taken = $logs->where('status', 'taken')->count();
        $missed = $logs->where('status', 'missed')->count();
        $total = $logs->count();

        return [
            'total_doses' => $total,
            'taken_doses' => $taken,
            'missed_doses' => $missed,
            'compliance_rate' => $total > 0 ? round(($taken / $total) * 100, 2) : 0,
            'period_days' => $days,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ];
    }

    /**
     * الحصول على الأدوية الأكثر التزاماً
     */
    public function getMostCompliedMedications(User $user, $limit = 5)
    {
        $medications = Medication::where('user_id', $user->id)
            ->with(['logs' => function ($query) {
                $query->where('status', 'taken');
            }])
            ->get()
            ->map(function ($medication) {
                $totalLogs = $medication->logs()->count();
                $takenLogs = $medication->logs()->where('status', 'taken')->count();
                $compliance = $totalLogs > 0 ? round(($takenLogs / $totalLogs) * 100, 2) : 0;

                return [
                    'id' => $medication->id,
                    'name' => $medication->name,
                    'compliance' => $compliance,
                    'taken' => $takenLogs,
                    'total' => $totalLogs,
                ];
            })
            ->sortByDesc('compliance')
            ->take($limit);

        return $medications;
    }

    /**
     * الحصول على الأدوية الأقل التزاماً
     */
    public function getLeastCompliedMedications(User $user, $limit = 5)
    {
        $medications = Medication::where('user_id', $user->id)
            ->with(['logs' => function ($query) {
                $query->where('status', 'taken');
            }])
            ->get()
            ->map(function ($medication) {
                $totalLogs = $medication->logs()->count();
                $takenLogs = $medication->logs()->where('status', 'taken')->count();
                $compliance = $totalLogs > 0 ? round(($takenLogs / $totalLogs) * 100, 2) : 0;

                return [
                    'id' => $medication->id,
                    'name' => $medication->name,
                    'compliance' => $compliance,
                    'taken' => $takenLogs,
                    'total' => $totalLogs,
                ];
            })
            ->sortBy('compliance')
            ->take($limit);

        return $medications;
    }

    /**
     * الحصول على الاتجاهات اليومية
     */
    public function getDailyTrends(User $user, $days = 30)
    {
        $trends = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $compliance = $this->calculateDailyCompliance($user, $date);
            $trends[] = [
                'date' => $date,
                'compliance' => $compliance,
            ];
        }

        return $trends;
    }

    /**
     * التنبؤ بمعدل الالتزام المستقبلي (بناءً على الاتجاهات)
     */
    public function predictFutureCompliance(User $user, $days = 30)
    {
        $trends = $this->getDailyTrends($user, $days);
        $values = array_column($trends, 'compliance');

        if (empty($values)) {
            return 0;
        }

        // حساب المتوسط المتحرك
        $average = array_sum($values) / count($values);

        return round($average, 2);
    }

    /**
     * تحديد الأيام ذات الامتثال المنخفض
     */
    public function identifyLowComplianceDays(User $user, $threshold = 50, $days = 30)
    {
        $trends = $this->getDailyTrends($user, $days);
        $lowComplianceDays = array_filter($trends, function ($trend) use ($threshold) {
            return $trend['compliance'] < $threshold;
        });

        return $lowComplianceDays;
    }

    /**
     * الحصول على ملخص الامتثال
     */
    public function getComplianceSummary(User $user)
    {
        $dailyCompliance = $this->calculateDailyCompliance($user);
        $weeklyCompliance = $this->calculateWeeklyCompliance($user);
        $monthlyCompliance = $this->calculateMonthlyCompliance($user);
        $statistics = $this->getComplianceStatistics($user);

        return [
            'daily' => $dailyCompliance,
            'weekly' => $weeklyCompliance,
            'monthly' => $monthlyCompliance,
            'statistics' => $statistics,
            'status' => $this->getComplianceStatus($monthlyCompliance),
        ];
    }

    /**
     * الحصول على حالة الامتثال
     */
    public function getComplianceStatus($compliance)
    {
        if ($compliance >= 90) {
            return 'excellent';
        } elseif ($compliance >= 75) {
            return 'good';
        } elseif ($compliance >= 50) {
            return 'fair';
        } else {
            return 'poor';
        }
    }

    /**
     * الحصول على رسالة حالة الامتثال
     */
    public function getComplianceMessage($compliance)
    {
        $status = $this->getComplianceStatus($compliance);

        $messages = [
            'excellent' => 'ممتاز! أنت تلتزم بتناول أدويتك بشكل مثالي.',
            'good' => 'جيد جداً! استمر في الالتزام بتناول أدويتك.',
            'fair' => 'يمكنك تحسين الالتزام. حاول تناول أدويتك في المواعيد المحددة.',
            'poor' => 'تحتاج إلى تحسين الالتزام. تواصل مع طبيبك إذا واجهت مشاكل.',
        ];

        return $messages[$status] ?? '';
    }
}
