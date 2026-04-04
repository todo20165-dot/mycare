<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\VitalSign;
use App\Models\DoctorPatient;
use App\Models\FamilyLink;
use App\Models\Notification;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $patients = User::where('role', 'patient')->count();
        $doctors = User::where('role', 'doctor')->count();
        $familyMembers = User::where('role', 'family_member')->count();
        $medications = Medication::count();
        $vitalSigns = VitalSign::count();
        $activeLinks = DoctorPatient::where('status', 'active')->count();
        $pendingRequests = DoctorPatient::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'patients',
            'doctors',
            'familyMembers',
            'medications',
            'vitalSigns',
            'activeLinks',
            'pendingRequests'
        ));
    }

    // إدارة المستخدمين
    public function manageUsers()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:patient,family_member,doctor,admin',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'تم إضافة المستخدم بنجاح');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:patient,family_member,doctor,admin',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['name', 'email', 'role', 'phone', 'is_active']));
        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم بنجاح');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }

    // إدارة الأطباء
    public function manageDoctors()
    {
        $doctors = User::where('role', 'doctor')->withCount(['doctorPatients' => function($query) {
            $query->where('status', 'active');
        }])->paginate(15);
        return view('admin.doctors.index', compact('doctors'));
    }

    // إدارة المرضى
    public function managePatients()
    {
        $patients = User::where('role', 'patient')->withCount(['doctorPatients' => function($query) {
            $query->where('status', 'active');
        }])->paginate(15);
        return view('admin.patients.index', compact('patients'));
    }

    // إدارة أفراد العائلة
    public function manageFamilyMembers()
    {
        $familyMembers = User::where('role', 'family_member')->withCount('familyLinks')->paginate(15);
        return view('admin.family-members.index', compact('familyMembers'));
    }

    // إدارة الأدوية
    public function manageMedications()
    {
        $medications = Medication::with('user')->paginate(15);
        return view('admin.medications.index', compact('medications'));
    }

    public function createMedication()
    {
        $patients = User::where('role', 'patient')->get();
        return view('admin.medications.create', compact('patients'));
    }

    public function storeMedication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'instructions' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Medication::create($request->all());
        return redirect()->route('admin.medications.index')->with('success', 'تم إضافة الدواء بنجاح');
    }

    public function editMedication(Medication $medication)
    {
        $patients = User::where('role', 'patient')->get();
        return view('admin.medications.edit', compact('medication', 'patients'));
    }

    public function updateMedication(Request $request, Medication $medication)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'instructions' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $medication->update($request->all());
        return redirect()->route('admin.medications.index')->with('success', 'تم تحديث الدواء بنجاح');
    }

    public function deleteMedication(Medication $medication)
    {
        $medication->delete();
        return redirect()->route('admin.medications.index')->with('success', 'تم حذف الدواء بنجاح');
    }

    // إدارة روابط المرضى والأطباء
    public function manageDoctorPatientLinks()
    {
        $links = DoctorPatient::with(['doctor', 'patient'])->paginate(15);
        return view('admin.doctor-patient-links.index', compact('links'));
    }

    public function approveLink(DoctorPatient $link)
    {
        $link->update(['status' => 'active']);
        return redirect()->back()->with('success', 'تم قبول الطلب بنجاح');
    }

    public function rejectLink(DoctorPatient $link)
    {
        $link->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'تم رفض الطلب');
    }

    // إدارة روابط العائلة
    public function manageFamilyLinks()
    {
        $links = FamilyLink::with(['familyMember', 'patient'])->paginate(15);
        return view('admin.family-links.index', compact('links'));
    }

    public function approveFamilyLink(FamilyLink $link)
    {
        $link->update(['status' => 'active']);
        return redirect()->back()->with('success', 'تم قبول الطلب بنجاح');
    }

    public function rejectFamilyLink(FamilyLink $link)
    {
        $link->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'تم رفض الطلب');
    }

    // إدارة الإشعارات
    public function manageNotifications()
    {
        $notifications = Notification::with('user')->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function createNotification()
    {
        $users = User::all();
        return view('admin.notifications.create', compact('users'));
    }

    public function storeNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,danger,success',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Notification::create($request->all());
        return redirect()->route('admin.notifications.index')->with('success', 'تم إرسال الإشعار بنجاح');
    }

    // التقارير والإحصائيات
    public function statistics()
    {
        $stats = [
            'total_users' => User::count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_doctors' => User::where('role', 'doctor')->count(),
            'total_family_members' => User::where('role', 'family_member')->count(),
            'total_medications' => Medication::count(),
            'total_medication_logs' => MedicationLog::count(),
            'total_vital_signs' => VitalSign::count(),
            'active_doctor_patient_links' => DoctorPatient::where('status', 'active')->count(),
            'pending_doctor_patient_requests' => DoctorPatient::where('status', 'pending')->count(),
            'active_family_links' => FamilyLink::where('status', 'active')->count(),
            'pending_family_requests' => FamilyLink::where('status', 'pending')->count(),
            'average_adherence' => Medication::avg('adherence_rate') ?? 0,
            'users_by_role' => User::selectRaw('role, count(*) as count')->groupBy('role')->get(),
        ];

        return view('admin.statistics', compact('stats'));
    }

    public function reports()
    {
        $reports = Report::with('user')->paginate(15);
        return view('admin.reports.index', compact('reports'));
    }

    // إعدادات النظام
    public function systemSettings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'timezone' => 'nullable|string',
            'debug_mode' => 'nullable|boolean',
            'mail_driver' => 'nullable|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // حفظ الإعدادات في session (للتجريب)
        // في التطبيق الحقيقي، يجب حفظها في ملف .env أو قاعدة البيانات
        session([
            'admin_settings' => [
                'app_name' => $request->app_name,
                'app_url' => $request->app_url,
                'timezone' => $request->timezone,
                'debug_mode' => $request->boolean('debug_mode'),
                'mail_driver' => $request->mail_driver,
                'mail_host' => $request->mail_host,
                'mail_port' => $request->mail_port,
                'mail_username' => $request->mail_username,
                'mail_password' => $request->mail_password,
                'mail_from_address' => $request->mail_from_address,
            ]
        ]);

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    // أدوات النظام
    public function systemTools()
    {
        return view('admin.tools');
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('route:clear');
        \Artisan::call('view:clear');

        return back()->with('success', 'تم مسح الذاكرة المؤقتة بنجاح');
    }

    public function refreshDatabase()
    {
        \Artisan::call('migrate:fresh --seed');

        return back()->with('success', 'تم تحديث قاعدة البيانات بنجاح');
    }
}
