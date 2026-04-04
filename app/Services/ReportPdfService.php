<?php

namespace App\Services;

use App\Models\Report;
use App\Models\MedicationLog;
use App\Models\VitalSign;
// use Barryvdh\DomPDF\Facade\Pdf;

/**
 * خدمة توليد التقارير بصيغة PDF
 * تقوم بإنشاء تقارير طبية شاملة بصيغة PDF
 */
class ReportPdfService
{
    /**
     * توليد تقرير الالتزام بالأدوية
     */
    public function generateMedicationComplianceReport(Report $report)
    {
        $data = [
            'report' => $report,
            'title' => 'Medication Adherence Report',
            'compliance_rate' => $report->data['compliance_rate'] ?? 0,
            'total_doses' => $report->data['total_doses'] ?? 0,
            'taken_doses' => $report->data['taken_doses'] ?? 0,
            'missed_doses' => $report->data['missed_doses'] ?? 0,
        ];

        return $data; // Return data instead of PDF for now
    }

    /**
     * توليد تقرير العلامات الحيوية
     */
    public function generateVitalSignsReport(Report $report)
    {
        $data = [
            'report' => $report,
            'title' => 'Vital Signs Report',
            'total_readings' => $report->data['total_readings'] ?? 0,
            'normal_readings' => $report->data['normal_readings'] ?? 0,
            'abnormal_readings' => $report->data['abnormal_readings'] ?? 0,
            'readings' => $report->data['readings'] ?? [],
        ];

        return $data; // Return data instead of PDF for now
    }

    /**
     * توليد التقرير الصحي الشامل
     */
    public function generateComprehensiveReport(Report $report)
    {
        $data = [
            'report' => $report,
            'title' => 'Comprehensive Health Report',
            'medications_count' => $report->data['medications_count'] ?? 0,
            'readings_count' => $report->data['readings_count'] ?? 0,
            'compliance_rate' => $report->data['compliance_rate'] ?? 0,
            'abnormal_readings' => $report->data['abnormal_readings'] ?? 0,
        ];

        return $data; // Return data instead of PDF for now
    }

    /**
     * توليد التقرير الشهري
     */
    public function generateMonthlyReport(Report $report)
    {
        $data = [
            'report' => $report,
            'title' => 'Monthly Report',
            'month' => $report->data['month'] ?? '',
            'year' => $report->data['year'] ?? '',
            'summary' => $report->data['summary'] ?? '',
        ];

        return $data; // Return data instead of PDF for now
    }

    /**
     * حساب معدل الالتزام بالأدوية
     */
    public function calculateComplianceRate($userId, $startDate, $endDate)
    {
        $logs = MedicationLog::where('user_id', $userId)
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
     * الحصول على إحصائيات العلامات الحيوية
     */
    public function getVitalSignsStatistics($userId, $startDate, $endDate)
    {
        $readings = VitalSign::where('user_id', $userId)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->get();

        $normal = 0;
        $abnormal = 0;

        foreach ($readings as $reading) {
            if ($this->isAbnormalReading($reading)) {
                $abnormal++;
            } else {
                $normal++;
            }
        }

        return [
            'total' => $readings->count(),
            'normal' => $normal,
            'abnormal' => $abnormal,
            'readings' => $readings,
        ];
    }

    /**
     * التحقق من كون القراءة غير طبيعية
     */
    private function isAbnormalReading($reading)
    {
        $normalRanges = [
            'blood_pressure' => ['min' => '90/60', 'max' => '120/80'],
            'blood_sugar' => ['min' => 70, 'max' => 100],
            'temperature' => ['min' => 36.5, 'max' => 37.5],
            'heart_rate' => ['min' => 60, 'max' => 100],
            'oxygen_saturation' => ['min' => 95, 'max' => 100],
        ];

        // يمكن توسيع هذه الدالة لتشمل فحوصات أكثر تفصيلاً
        return false;
    }
}
