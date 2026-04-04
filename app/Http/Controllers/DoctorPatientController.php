<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DoctorPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * متحكم إدارة ربط الأطباء بالمرضى
 * يتعامل مع طلبات الربط والموافقة والرفض
 */
class DoctorPatientController extends Controller
{
    /**
     * عرض قائمة المرضى للطبيب الحالي
     */
    public function myPatients()
    {
        $doctor = Auth::user();
        $patients = $doctor->patients()->paginate(10);

        return view('doctor.my-patients', compact('patients'));
    }

    /**
     * عرض طلبات الربط المعلقة للطبيب
     */
    public function pendingRequests()
    {
        $doctor = Auth::user();
        $requests = $doctor->patientRequests()
            ->where('status', 'pending')
            ->paginate(10);

        return view('doctor.pending-requests', compact('requests'));
    }

    /**
     * إرسال طلب ربط من المريض إلى الطبيب
     */
    public function requestConnection(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $patient = Auth::user();
        $doctor = User::findOrFail($request->doctor_id);

        // التحقق من أن المستخدم طبيب
        if (!$doctor->isDoctor()) {
            return back()->with('error', 'المستخدم المحدد ليس طبيباً');
        }

        // التحقق من عدم وجود طلب سابق
        $existing = DoctorPatient::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'يوجد طلب ربط سابق مع هذا الطبيب');
        }

        // إنشاء طلب ربط جديد
        $doctorPatient = DoctorPatient::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'status' => 'pending',
            'specialization' => $request->input('specialization'),
            'notes' => $request->input('notes'),
        ]);

        // إرسال إشعار للطبيب
        $this->notifyDoctor($doctor, $patient);

        return back()->with('success', 'تم إرسال طلب الربط بنجاح');
    }

    /**
     * موافقة الطبيب على طلب ربط من مريض
     */
    public function approveRequest($id)
    {
        $doctorPatient = DoctorPatient::findOrFail($id);

        // التحقق من أن الطبيب الحالي هو من يملك الطلب
        if ($doctorPatient->doctor_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $doctorPatient->approve();

        // إرسال إشعار للمريض
        $this->notifyPatientApproved($doctorPatient->patient);

        return back()->with('success', 'تم قبول طلب الربط');
    }

    /**
     * رفض الطبيب لطلب ربط من مريض
     */
    public function rejectRequest($id)
    {
        $doctorPatient = DoctorPatient::findOrFail($id);

        // التحقق من أن الطبيب الحالي هو من يملك الطلب
        if ($doctorPatient->doctor_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $doctorPatient->reject();

        // إرسال إشعار للمريض
        $this->notifyPatientRejected($doctorPatient->patient);

        return back()->with('success', 'تم رفض طلب الربط');
    }

    /**
     * إلغاء الربط بين الطبيب والمريض
     */
    public function disconnect($id)
    {
        $doctorPatient = DoctorPatient::findOrFail($id);

        // التحقق من أن الطبيب أو المريض الحالي هو من يملك الربط
        if ($doctorPatient->doctor_id !== Auth::id() && $doctorPatient->patient_id !== Auth::id()) {
            return back()->with('error', 'غير مصرح لك بهذا الإجراء');
        }

        $doctorPatient->update(['status' => 'inactive']);

        return back()->with('success', 'تم إلغاء الربط بنجاح');
    }

    /**
     * البحث عن أطباء
     */
    public function searchDoctors(Request $request)
    {
        $query = $request->input('q');
        $specialization = $request->input('specialization');

        $doctors = User::where('role', 'doctor')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            });

        if ($specialization) {
            $doctors->where('specialization', $specialization);
        }

        $doctors = $doctors->paginate(10);

        return view('patient.search-doctors', compact('doctors', 'query', 'specialization'));
    }

    /**
     * عرض تفاصيل الطبيب
     */
    public function showDoctor($id)
    {
        $doctor = User::where('role', 'doctor')->findOrFail($id);
        $patient = Auth::user();

        // التحقق من وجود ربط سابق
        $connection = DoctorPatient::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->first();

        return view('patient.doctor-profile', compact('doctor', 'connection'));
    }

    /**
     * إرسال إشعار للطبيب بطلب ربط جديد
     */
    private function notifyDoctor(User $doctor, User $patient)
    {
        // يمكن إضافة إرسال بريد إلكتروني أو إشعار هنا
        \Log::info("طلب ربط جديد من {$patient->name} للطبيب {$doctor->name}");
    }

    /**
     * إرسال إشعار للمريض بقبول الطلب
     */
    private function notifyPatientApproved(User $patient)
    {
        // يمكن إضافة إرسال بريد إلكتروني أو إشعار هنا
        \Log::info("تم قبول طلب الربط للمريض {$patient->name}");
    }

    /**
     * إرسال إشعار للمريض برفض الطلب
     */
    private function notifyPatientRejected(User $patient)
    {
        // يمكن إضافة إرسال بريد إلكتروني أو إشعار هنا
        \Log::info("تم رفض طلب الربط للمريض {$patient->name}");
    }
}
