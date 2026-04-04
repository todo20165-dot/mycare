<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FamilyLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * متحكم إدارة الارتباطات العائلية
 * يتعامل مع ربط أفراد العائلة بالمريض
 */
class FamilyMemberController extends Controller
{
    /**
     * عرض قائمة أفراد العائلة للمريض
     */
    public function index()
    {
        $patient = Auth::user();
        
        // الأفراد المرتبطون بالمريض (كمريض)
        $familyMembers = $patient->familyLinksAsPatient()
            ->where('status', 'approved')
            ->paginate(10);

        return view('family.members-list', compact('familyMembers'));
    }

    /**
     * عرض الطلبات المعلقة للمريض
     */
    public function pendingRequests()
    {
        $patient = Auth::user();
        $requests = $patient->familyLinksAsPatient()
            ->where('status', 'pending')
            ->paginate(10);

        return view('family.pending-requests', compact('requests'));
    }

    /**
     * صفحة إضافة فرد عائلي جديد
     */
    public function create()
    {
        return view('family.add-member');
    }

    /**
     * إرسال دعوة لفرد عائلي
     */
    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'relationship' => 'required|in:parent,child,spouse,sibling,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $patient = Auth::user();
        $familyMember = User::where('email', $request->email)->firstOrFail();

        // التحقق من عدم وجود ربط سابق
        $existing = FamilyLink::where('patient_id', $patient->id)
            ->where('family_member_id', $familyMember->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'يوجد ربط سابق مع هذا الشخص');
        }

        // إنشاء طلب ربط جديد
        $familyLink = FamilyLink::create([
            'patient_id' => $patient->id,
            'family_member_id' => $familyMember->id,
            'relationship' => $request->relationship,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // إرسال إشعار
        $this->notifyFamilyMember($familyMember, $patient, $request->relationship);

        return back()->with('success', 'تم إرسال الدعوة بنجاح');
    }

    /**
     * قبول طلب ربط عائلي
     */
    public function approve($id)
    {
        $familyLink = FamilyLink::findOrFail($id);

        // التحقق من أن فرد العائلة الحالي هو من يملك الطلب
        if ($familyLink->family_member_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $familyLink->approve();

        // إرسال إشعار للمريض
        $this->notifyPatientApproved($familyLink->patient);

        return back()->with('success', 'تم قبول الطلب');
    }

    /**
     * رفض طلب ربط عائلي
     */
    public function reject($id)
    {
        $familyLink = FamilyLink::findOrFail($id);

        // التحقق من أن فرد العائلة الحالي هو من يملك الطلب
        if ($familyLink->family_member_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $familyLink->reject();

        return back()->with('success', 'تم رفض الطلب');
    }

    /**
     * إلغاء الربط العائلي
     */
    public function disconnect($id)
    {
        $familyLink = FamilyLink::findOrFail($id);

        // التحقق من الصلاحيات
        if ($familyLink->patient_id !== Auth::id() && $familyLink->family_member_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $familyLink->update(['status' => 'inactive']);

        return back()->with('success', 'تم إلغاء الربط');
    }

    /**
     * عرض بيانات المريض (لفرد العائلة)
     */
    public function viewPatientData($patientId)
    {
        $familyMember = Auth::user();
        $patient = User::findOrFail($patientId);

        // التحقق من وجود ربط موافق عليه
        $familyLink = FamilyLink::where('patient_id', $patient->id)
            ->where('family_member_id', $familyMember->id)
            ->where('status', 'approved')
            ->firstOrFail();

        // الحصول على بيانات المريض
        $medications = $patient->medications()->paginate(10);
        $vitalSigns = $patient->vitalSigns()->latest()->paginate(10);
        $medicationLogs = $patient->medicationLogs()->latest()->paginate(10);

        return view('family.patient-data', compact('patient', 'medications', 'vitalSigns', 'medicationLogs', 'familyLink'));
    }

    /**
     * عرض قائمة المرضى لفرد العائلة
     */
    public function myPatients()
    {
        $familyMember = Auth::user();
        
        // جميع المرضى المرتبطين بهذا فرد العائلة
        $patients = $familyMember->familyLinksAsMember()
            ->where('status', 'approved')
            ->with('patient')
            ->paginate(10);

        return view('family.my-patients', compact('patients'));
    }

    /**
     * عرض الطلبات المعلقة لفرد العائلة
     */
    public function myPendingRequests()
    {
        $familyMember = Auth::user();
        $requests = $familyMember->familyLinksAsMember()
            ->where('status', 'pending')
            ->with('patient')
            ->paginate(10);

        return view('family.my-pending-requests', compact('requests'));
    }

    /**
     * عرض تقرير صحي للمريض
     */
    public function viewHealthReport($patientId)
    {
        $familyMember = Auth::user();
        $patient = User::findOrFail($patientId);

        // التحقق من الصلاحيات
        $familyLink = FamilyLink::where('patient_id', $patient->id)
            ->where('family_member_id', $familyMember->id)
            ->where('status', 'approved')
            ->firstOrFail();

        // حساب الإحصائيات
        $medicationCount = $patient->medications()->count();
        $vitalSignsCount = $patient->vitalSigns()->count();
        $complianceRate = $this->calculateCompliance($patient);
        $recentVitalSigns = $patient->vitalSigns()->latest()->take(5)->get();

        return view('family.health-report', compact('patient', 'medicationCount', 'vitalSignsCount', 'complianceRate', 'recentVitalSigns', 'familyLink'));
    }

    /**
     * تحميل تقرير PDF
     */
    public function downloadReport($patientId)
    {
        $familyMember = Auth::user();
        $patient = User::findOrFail($patientId);

        // التحقق من الصلاحيات
        FamilyLink::where('patient_id', $patient->id)
            ->where('family_member_id', $familyMember->id)
            ->where('status', 'approved')
            ->firstOrFail();

        // توليد التقرير
        // يمكن استخدام DOMPDF أو مكتبة أخرى
        \Log::info("تقرير تم تحميله من قبل {$familyMember->name} للمريض {$patient->name}");

        return back()->with('success', 'تم تحميل التقرير');
    }

    /**
     * إرسال رسالة للمريض
     */
    public function sendMessage(Request $request, $patientId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $familyMember = Auth::user();
        $patient = User::findOrFail($patientId);

        // التحقق من الصلاحيات
        FamilyLink::where('patient_id', $patient->id)
            ->where('family_member_id', $familyMember->id)
            ->where('status', 'approved')
            ->firstOrFail();

        // حفظ الرسالة
        \Log::info("رسالة من {$familyMember->name} إلى {$patient->name}: {$request->message}");

        return back()->with('success', 'تم إرسال الرسالة');
    }

    /**
     * حساب معدل الالتزام بالأدوية
     */
    private function calculateCompliance($patient)
    {
        $logs = $patient->medicationLogs()->get();
        
        if ($logs->isEmpty()) {
            return 0;
        }

        $taken = $logs->where('status', 'taken')->count();
        $total = $logs->count();

        return round(($taken / $total) * 100, 2);
    }

    /**
     * إرسال إشعار لفرد العائلة
     */
    private function notifyFamilyMember(User $familyMember, User $patient, $relationship)
    {
        \Log::info("دعوة عائلية من {$patient->name} إلى {$familyMember->name} كـ {$relationship}");
    }

    /**
     * إرسال إشعار للمريض بقبول الطلب
     */
    private function notifyPatientApproved(User $patient)
    {
        \Log::info("تم قبول طلب ربط عائلي للمريض {$patient->name}");
    }
}
