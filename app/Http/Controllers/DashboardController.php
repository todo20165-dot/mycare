<?php

namespace App\Http\Controllers;

use App\Models\DoctorPatient;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\Notification;
use App\Models\VitalSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * عرض لوحة التحكم الرئيسية
     */
    public function index()
    {
        $user = Auth::user();

        // إعادة التوجيه حسب دور المستخدم
        if ($user->isDoctor()) {
            return $this->doctorDashboard();
        } elseif ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isFamilyMember()) {
            return $this->familyDashboard();
        } else {
            return $this->patientDashboard();
        }
    }

    /**
     * لوحة تحكم المريض
     */
    private function patientDashboard()
    {
        $user = Auth::user();

        $medications = $user->medications()->where('is_active', true)->get();
        $todayLogs = $user->medicationLogs()
            ->whereDate('scheduled_time', today())
            ->get();
        $pendingLogs = $todayLogs->where('status', 'pending');
        $takenLogs = $todayLogs->where('status', 'taken');

        $recentVitalSigns = $user->vitalSigns()
            ->orderBy('measured_at', 'desc')
            ->limit(5)
            ->get();

        $unreadNotifications = $user->notifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $adherenceRate = $medications->avg('adherence_rate') ?? 0;

        return view('dashboard.patient', compact(
            'medications',
            'todayLogs',
            'pendingLogs',
            'takenLogs',
            'recentVitalSigns',
            'unreadNotifications',
            'adherenceRate'
        ));
    }

    /**
     * لوحة تحكم الطبيب
     */
    private function doctorDashboard()
    {
        $user = Auth::user();

        // حساب عدد المرضى المعتمدين
        $totalPatients = DoctorPatient::where('doctor_id', $user->id)
            ->where('status', 'approved')
            ->count();

        // حساب الوصفات النشطة للمرضى المرتبطين
        $approvedPatientIds = DoctorPatient::where('doctor_id', $user->id)
            ->where('status', 'approved')
            ->pluck('patient_id');

        $activePrescriptions = Medication::whereIn('user_id', $approvedPatientIds)
            ->where('is_active', true)
            ->count();

        $unreadNotifications = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unreadCount = $unreadNotifications->count();

        return view('doctor.dashboard', compact('totalPatients', 'activePrescriptions', 'unreadNotifications', 'unreadCount'));
    }

    /**     * لوحة تحكم فرد العائلة
     */
    private function familyDashboard()
    {
        $familyMember = Auth::user();

        $patients = $familyMember->familyLinksAsMember()
            ->where('status', 'approved')
            ->with('patient')
            ->paginate(10);

        $pendingRequests = $familyMember->familyLinksAsMember()
            ->where('status', 'pending')
            ->count();

        return view('family.my-patients', compact('patients', 'pendingRequests'));
    }

    /**     * لوحة تحكم الإدارة
     */
    private function adminDashboard()
    {
        // إعادة توجيه إلى AdminController
        return redirect()->route('admin.dashboard');
    }

    /**
     * عرض إحصائيات المريض
     */
    public function patientStats()
    {
        $user = Auth::user();

        $totalMedications = $user->medications()->count();
        $activeMedications = $user->medications()->where('is_active', true)->count();
        $totalVitalSigns = $user->vitalSigns()->count();
        $abnormalVitalSigns = $user->vitalSigns()->where('is_abnormal', true)->count();

        $medicationLogs = $user->medicationLogs()
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->get();

        $takenCount = $medicationLogs->where('status', 'taken')->count();
        $missedCount = $medicationLogs->where('status', 'missed')->count();
        $adherenceRate = $medicationLogs->count() > 0 
            ? round(($takenCount / $medicationLogs->count()) * 100, 2)
            : 0;

        return response()->json([
            'total_medications' => $totalMedications,
            'active_medications' => $activeMedications,
            'total_vital_signs' => $totalVitalSigns,
            'abnormal_vital_signs' => $abnormalVitalSigns,
            'adherence_rate' => $adherenceRate,
            'taken_count' => $takenCount,
            'missed_count' => $missedCount,
        ]);
    }
}
