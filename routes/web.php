<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\VitalSignController;
use App\Http\Controllers\FamilyLinkController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorPatientController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\EmergencyController;

// مسارات المصادقة
Route::middleware(['guest', 'no-cache'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// مسارات المستخدمين المصرح لهم
Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');

    // لوحة التحكم
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'patientStats'])->name('dashboard.stats');

    // الأدوية
    Route::resource('medications', MedicationController::class);
    Route::post('/medications/{medication}/log-dose', [MedicationController::class, 'logDose'])->name('medications.log-dose');
    Route::post('/medications/{medication}/mark-missed', [MedicationController::class, 'markMissed'])->name('medications.mark-missed');

    // العلامات الحيوية
    Route::resource('vital-signs', VitalSignController::class);
    Route::get('/vital-signs/chart/{type}', [VitalSignController::class, 'getChartData'])->name('vital-signs.chart');

    // الارتباطات العائلية
    Route::get('/family-links/pending', [FamilyLinkController::class, 'pendingRequests'])->name('family-links.pending');
    Route::resource('family-links', FamilyLinkController::class)->except(['show']);
    Route::post('/family-links/{familyLink}/approve', [FamilyLinkController::class, 'approve'])->name('family-links.approve');
    Route::post('/family-links/{familyLink}/reject', [FamilyLinkController::class, 'reject'])->name('family-links.reject');

    // التقارير
    Route::resource('reports', ReportController::class);
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

    // الإشعارات
    Route::resource('notifications', NotificationController::class, ['only' => ['index', 'destroy']]);
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::get('/notifications/count', [NotificationController::class, 'getCount'])->name('notifications.count');

    // ربط الطبيب للمريض
    Route::get('/patient/search-doctors', [DoctorPatientController::class, 'searchDoctors'])->name('patient.search-doctors');
    Route::get('/patient/doctor/{id}', [DoctorPatientController::class, 'showDoctor'])->name('patient.doctor-profile');
    Route::post('/patient/doctor-patient/request', [DoctorPatientController::class, 'requestConnection'])->name('doctor-patient.request-connection');
    Route::delete('/patient/doctor-patient/{id}/disconnect', [DoctorPatientController::class, 'disconnect'])->name('doctor-patient.disconnect');
    Route::get('/select-disease', [DiseaseController::class, 'selectDisease'])->name('patient.select-disease');
    Route::post('/select-disease', [DiseaseController::class, 'storeDisease'])->name('patient.store-disease');
    Route::get('/search-doctors-by-disease', [DiseaseController::class, 'searchDoctorsByDisease'])->name('patient.search-doctors-by-disease');
// u0645u0633u0627u0631u0627u062a u0627u0644u0637u0648u0627u0631u0626
    Route::get('/emergency', [EmergencyController::class, 'showEmergencyButton'])->name('emergency.button');
    Route::post('/emergency/trigger', [EmergencyController::class, 'triggerEmergency'])->name('emergency.trigger');
    Route::get('/emergency/hospitals', [EmergencyController::class, 'getNearestHospitals'])->name('emergency.hospitals');
    Route::get('/emergency/history', [EmergencyController::class, 'getEmergencyHistory'])->name('emergency.history');

    // لوحة تحكم فرد العائلة
    Route::get('/family/dashboard', [FamilyMemberController::class, 'myPatients'])->name('family.dashboard');
    Route::get('/family/patients', [FamilyMemberController::class, 'myPatients'])->name('family.my-patients');
    Route::get('/family/pending-requests', [FamilyMemberController::class, 'myPendingRequests'])->name('family.my-pending-requests');
    Route::get('/family/patient/{id}', [FamilyMemberController::class, 'viewPatientData'])->name('family.view-patient-data');
    Route::get('/family/patient/{id}/health-report', [FamilyMemberController::class, 'viewHealthReport'])->name('family.view-health-report');
    Route::post('/family/patient/{id}/message', [FamilyMemberController::class, 'sendMessage'])->name('family.send-message');
    Route::delete('/family/patient/{id}/disconnect', [FamilyMemberController::class, 'disconnect'])->name('family.disconnect');

});

// مسارات الطبيب
Route::middleware(['auth'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');

    // المرضى
    Route::get('/patients', [DoctorPatientController::class, 'myPatients'])->name('patients.index');
    Route::get('/patients/{id}', [DoctorController::class, 'patientDetails'])->name('patients.show');

    // الطلبات المعلقة
    Route::get('/pending-requests', [DoctorPatientController::class, 'pendingRequests'])->name('pending-requests');

    // الوصفات
    Route::get('/prescriptions', [DoctorController::class, 'prescriptionsIndex'])->name('prescriptions.index');
    Route::get('/patients/{id}/prescriptions/create', [DoctorController::class, 'createPrescription'])->name('prescriptions.create');
    Route::post('/patients/{id}/prescriptions', [DoctorController::class, 'addPrescription'])->name('prescriptions.store');

    // الملاحظات
    Route::get('/notes', [DoctorController::class, 'notesIndex'])->name('notes.index');
    Route::get('/patients/{id}/notes/create', [DoctorController::class, 'createNote'])->name('notes.create');
    Route::post('/patients/{id}/notes', [DoctorController::class, 'addNote'])->name('notes.store');

    // الرسائل
    Route::get('/messages', [DoctorController::class, 'messagesIndex'])->name('messages.index');
    Route::get('/patients/{id}/messages/create', [DoctorController::class, 'createMessage'])->name('messages.create');
    Route::post('/patients/{id}/messages', [DoctorController::class, 'sendMessage'])->name('messages.store');

    // تعاملات ربط الطبيب بالمريض
    Route::post('/doctor-patient/{id}/approve', [DoctorPatientController::class, 'approveRequest'])->name('doctor-patient.approve');
    Route::post('/doctor-patient/{id}/reject', [DoctorPatientController::class, 'rejectRequest'])->name('doctor-patient.reject');
});

// مسارات الإدارة
// مسارات AJAX للأمراض
Route::middleware(['auth'])->group(function () {
    Route::get('/api/doctors-by-disease', [DiseaseController::class, 'getDoctorsByDisease'])->name('api.doctors-by-disease');
    Route::post('/api/validate-doctor-disease', [DiseaseController::class, 'validateDoctorForDisease'])->name('api.validate-doctor-disease');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // إدارة المستخدمين
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::post('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');

    // إدارة الأطباء
    Route::get('/doctors', [AdminController::class, 'manageDoctors'])->name('doctors.index');

    // إدارة المرضى
    Route::get('/patients', [AdminController::class, 'managePatients'])->name('patients.index');

    // إدارة أفراد العائلة
    Route::get('/family-members', [AdminController::class, 'manageFamilyMembers'])->name('family-members.index');

    // إدارة الأدوية
    Route::get('/medications', [AdminController::class, 'manageMedications'])->name('medications.index');
    Route::get('/medications/create', [AdminController::class, 'createMedication'])->name('medications.create');
    Route::post('/medications', [AdminController::class, 'storeMedication'])->name('medications.store');
    Route::get('/medications/{medication}/edit', [AdminController::class, 'editMedication'])->name('medications.edit');
    // إدارة الأمراض
    Route::resource('diseases', DiseaseController::class);
    Route::post('/medications/{medication}', [AdminController::class, 'updateMedication'])->name('medications.update');
    Route::delete('/medications/{medication}', [AdminController::class, 'deleteMedication'])->name('medications.destroy');

    // إدارة روابط المرضى والأطباء
    Route::get('/doctor-patient-links', [AdminController::class, 'manageDoctorPatientLinks'])->name('doctor-patient-links.index');
    Route::post('/doctor-patient-links/{link}/approve', [AdminController::class, 'approveLink'])->name('doctor-patient-links.approve');
    Route::post('/doctor-patient-links/{link}/reject', [AdminController::class, 'rejectLink'])->name('doctor-patient-links.reject');

    // إدارة روابط العائلة
    Route::get('/family-links', [AdminController::class, 'manageFamilyLinks'])->name('family-links.index');
    Route::post('/family-links/{link}/approve', [AdminController::class, 'approveFamilyLink'])->name('family-links.approve');
    Route::post('/family-links/{link}/reject', [AdminController::class, 'rejectFamilyLink'])->name('family-links.reject');

    // إدارة الإشعارات
    Route::get('/notifications', [AdminController::class, 'manageNotifications'])->name('notifications.index');
    Route::get('/notifications/create', [AdminController::class, 'createNotification'])->name('notifications.create');
    Route::post('/notifications', [AdminController::class, 'storeNotification'])->name('notifications.store');

    // التقارير والإحصائيات
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');

    // إعدادات النظام
    Route::get('/settings', [AdminController::class, 'systemSettings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // أدوات النظام
    Route::get('/tools', [AdminController::class, 'systemTools'])->name('tools');
    Route::post('/tools/clear-cache', [AdminController::class, 'clearCache'])->name('tools.clear-cache');
    Route::post('/tools/refresh-database', [AdminController::class, 'refreshDatabase'])->name('tools.refresh-database');
});

// الصفحة الرئيسية
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
})->name('home');
