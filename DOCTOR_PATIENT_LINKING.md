# نظام ربط الأطباء بالمرضى - MyCare

وثيقة شاملة توضح كيفية عمل نظام ربط الأطباء بالمرضى في تطبيق MyCare.

---

## 📋 نظرة عامة

نظام ربط الأطباء بالمرضى يسمح للمرضى بربط حساباتهم مع أطبائهم، مما يتيح للأطباء:
- عرض قائمة مرضاهم
- الوصول إلى بيانات المريض الطبية
- إضافة وصفات طبية وملاحظات
- تتبع العلامات الحيوية والأدوية

---

## 🔄 سير العملية

### 1. البحث عن الطبيب (المريض)

**الخطوة الأولى:** يقوم المريض بالبحث عن الطبيب المناسب

```
المريض → البحث عن الأطباء → اختيار الطبيب → عرض الملف الشخصي
```

**الصفحة:** `/patient/search-doctors`

**المميزات:**
- البحث باسم الطبيب أو البريد الإلكتروني
- تصفية حسب التخصص
- عرض معلومات الاتصال

### 2. إرسال طلب الربط (المريض)

**الخطوة الثانية:** يرسل المريض طلب ربط للطبيب

```
المريض → يملأ نموذج الطلب → يرسل الطلب → الطلب معلق
```

**البيانات المطلوبة:**
- `doctor_id` - معرف الطبيب
- `specialization` - التخصص (اختياري)
- `notes` - ملاحظات إضافية (اختياري)

**الحالة:** `pending` (معلق)

### 3. عرض الطلبات المعلقة (الطبيب)

**الخطوة الثالثة:** يرى الطبيب الطلبات المعلقة

```
الطبيب → لوحة التحكم → الطلبات المعلقة → عرض التفاصيل
```

**الصفحة:** `/doctor/pending-requests`

**المعلومات المعروضة:**
- اسم المريض
- البريد الإلكتروني والهاتف
- الملاحظات
- تاريخ الطلب

### 4. الموافقة أو الرفض (الطبيب)

**الخطوة الرابعة:** يقرر الطبيب قبول أو رفض الطلب

#### أ) قبول الطلب:
```
الطبيب → يضغط "قبول" → تحديث الحالة إلى "approved" → إشعار للمريض
```

**النتيجة:**
- الحالة: `approved`
- تاريخ الموافقة: `approved_at`
- المريض يظهر في قائمة مرضى الطبيب

#### ب) رفض الطلب:
```
الطبيب → يضغط "رفض" → تحديث الحالة إلى "rejected" → إشعار للمريض
```

**النتيجة:**
- الحالة: `rejected`
- المريض يمكنه إعادة محاولة الربط

### 5. الربط النشط

**بعد الموافقة:**
- الطبيب يرى المريض في قائمة مرضاه
- الطبيب يمكنه الوصول إلى بيانات المريض
- المريض يرى الطبيب في قائمة أطبائه

---

## 🗄️ البنية قاعدة البيانات

### جدول `doctor_patient`

```sql
CREATE TABLE doctor_patient (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    doctor_id BIGINT NOT NULL,
    patient_id BIGINT NOT NULL,
    specialization VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected', 'inactive') DEFAULT 'pending',
    notes TEXT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_doctor_patient (doctor_id, patient_id),
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_status (status)
);
```

### الأعمدة:

| العمود | النوع | الوصف |
|-------|-------|-------|
| `id` | BIGINT | معرف فريد |
| `doctor_id` | BIGINT | معرف الطبيب |
| `patient_id` | BIGINT | معرف المريض |
| `specialization` | VARCHAR | تخصص الطبيب |
| `status` | ENUM | حالة الربط |
| `notes` | TEXT | ملاحظات إضافية |
| `assigned_at` | TIMESTAMP | تاريخ الطلب |
| `approved_at` | TIMESTAMP | تاريخ الموافقة |

---

## 📱 النماذج (Models)

### نموذج `DoctorPatient`

```php
class DoctorPatient extends Model
{
    // العلاقات
    public function doctor() { ... }
    public function patient() { ... }
    
    // الدوال
    public function approve() { ... }
    public function reject() { ... }
    public function isActive() { ... }
    public function getStatusLabel() { ... }
}
```

### تحديثات نموذج `User`

```php
class User extends Authenticatable
{
    // الأطباء المرتبطون (للمريض)
    public function doctors() { ... }
    
    // المرضى المرتبطون (للطبيب)
    public function patients() { ... }
    
    // جميع طلبات الربط
    public function doctorRequests() { ... }
    public function patientRequests() { ... }
}
```

---

## 🎮 المتحكم (Controller)

### `DoctorPatientController`

**الدوال الرئيسية:**

1. **`myPatients()`** - عرض مرضى الطبيب
   ```
   GET /doctor/my-patients
   ```

2. **`pendingRequests()`** - عرض الطلبات المعلقة
   ```
   GET /doctor/pending-requests
   ```

3. **`requestConnection()`** - إرسال طلب ربط
   ```
   POST /doctor-patient/request-connection
   ```

4. **`approveRequest()`** - قبول الطلب
   ```
   POST /doctor-patient/{id}/approve
   ```

5. **`rejectRequest()`** - رفض الطلب
   ```
   POST /doctor-patient/{id}/reject
   ```

6. **`disconnect()`** - إلغاء الربط
   ```
   DELETE /doctor-patient/{id}/disconnect
   ```

7. **`searchDoctors()`** - البحث عن أطباء
   ```
   GET /patient/search-doctors
   ```

8. **`showDoctor()`** - عرض ملف الطبيب
   ```
   GET /patient/doctor/{id}
   ```

---

## 🛣️ المسارات (Routes)

```php
// مسارات المريض
Route::middleware('auth')->group(function () {
    Route::get('/patient/search-doctors', [DoctorPatientController::class, 'searchDoctors'])->name('patient.search-doctors');
    Route::get('/patient/doctor/{id}', [DoctorPatientController::class, 'showDoctor'])->name('patient.doctor-profile');
    Route::post('/doctor-patient/request-connection', [DoctorPatientController::class, 'requestConnection'])->name('doctor-patient.request-connection');
});

// مسارات الطبيب
Route::middleware(['auth', 'doctor'])->group(function () {
    Route::get('/doctor/my-patients', [DoctorPatientController::class, 'myPatients'])->name('doctor.my-patients');
    Route::get('/doctor/pending-requests', [DoctorPatientController::class, 'pendingRequests'])->name('doctor.pending-requests');
    Route::post('/doctor-patient/{id}/approve', [DoctorPatientController::class, 'approveRequest'])->name('doctor-patient.approve');
    Route::post('/doctor-patient/{id}/reject', [DoctorPatientController::class, 'rejectRequest'])->name('doctor-patient.reject');
    Route::delete('/doctor-patient/{id}/disconnect', [DoctorPatientController::class, 'disconnect'])->name('doctor-patient.disconnect');
});
```

---

## 📄 الصفحات (Views)

### 1. صفحة البحث عن الأطباء
**الملف:** `resources/views/patient/search-doctors.blade.php`

**المميزات:**
- نموذج بحث متقدم
- تصفية حسب التخصص
- عرض معلومات الطبيب
- ترقيم الصفحات

### 2. صفحة ملف الطبيب
**الملف:** `resources/views/patient/doctor-profile.blade.php`

**المميزات:**
- عرض معلومات الطبيب
- إرسال طلب ربط
- عرض حالة الربط
- إلغاء الربط

### 3. صفحة الطلبات المعلقة
**الملف:** `resources/views/doctor/pending-requests.blade.php`

**المميزات:**
- عرض جميع الطلبات المعلقة
- معلومات المريض
- أزرار القبول والرفض
- ترقيم الصفحات

### 4. صفحة مرضى الطبيب
**الملف:** `resources/views/doctor/my-patients.blade.php`

**المميزات:**
- عرض جميع المرضى المرتبطين
- معلومات سريعة عن كل مريض
- رابط للعرض التفصيلي
- إحصائيات سريعة

---

## 🔐 الصلاحيات

### صلاحيات المريض:
- ✅ البحث عن الأطباء
- ✅ عرض ملف الطبيب
- ✅ إرسال طلب ربط
- ✅ إلغاء الطلب
- ✅ إلغاء الربط

### صلاحيات الطبيب:
- ✅ عرض الطلبات المعلقة
- ✅ قبول الطلب
- ✅ رفض الطلب
- ✅ عرض مرضاه
- ✅ إلغاء الربط

### صلاحيات الإدارة:
- ✅ عرض جميع الروابط
- ✅ حذف الروابط
- ✅ إدارة الطلبات

---

## 📊 حالات الربط

| الحالة | الوصف | الإجراءات المتاحة |
|--------|-------|------------------|
| `pending` | الطلب معلق | الموافقة، الرفض، الإلغاء |
| `approved` | الربط موافق عليه | عرض البيانات، الإلغاء |
| `rejected` | الطلب مرفوض | إعادة المحاولة |
| `inactive` | الربط ملغى | إعادة الربط |

---

## 🔔 الإشعارات

### إشعارات المريض:
- ✅ عند قبول الطلب
- ✅ عند رفض الطلب
- ✅ عند إضافة وصفة طبية جديدة
- ✅ عند إضافة ملاحظة طبية

### إشعارات الطبيب:
- ✅ عند استقبال طلب ربط جديد
- ✅ عند تحديث بيانات المريض

---

## 💡 أمثلة الاستخدام

### مثال 1: المريض يبحث عن طبيب

```php
// في DoctorPatientController
public function searchDoctors(Request $request)
{
    $query = $request->input('q');
    $doctors = User::where('role', 'doctor')
        ->where('name', 'like', "%{$query}%")
        ->paginate(10);
    
    return view('patient.search-doctors', compact('doctors'));
}
```

### مثال 2: المريض يرسل طلب ربط

```php
public function requestConnection(Request $request)
{
    $patient = Auth::user();
    $doctor = User::findOrFail($request->doctor_id);
    
    DoctorPatient::create([
        'doctor_id' => $doctor->id,
        'patient_id' => $patient->id,
        'status' => 'pending',
    ]);
    
    return back()->with('success', 'تم إرسال الطلب');
}
```

### مثال 3: الطبيب يقبل الطلب

```php
public function approveRequest($id)
{
    $doctorPatient = DoctorPatient::findOrFail($id);
    $doctorPatient->approve();
    
    return back()->with('success', 'تم قبول الطلب');
}
```

### مثال 4: عرض مرضى الطبيب

```php
public function myPatients()
{
    $doctor = Auth::user();
    $patients = $doctor->patients()->paginate(10);
    
    return view('doctor.my-patients', compact('patients'));
}
```

---

## 🔗 العلاقات في Eloquent

### الحصول على أطباء المريض:
```php
$patient = User::find($patientId);
$doctors = $patient->doctors; // جميع الأطباء الموافق عليهم
```

### الحصول على مرضى الطبيب:
```php
$doctor = User::find($doctorId);
$patients = $doctor->patients; // جميع المرضى الموافق عليهم
```

### الحصول على جميع الطلبات:
```php
$patient = User::find($patientId);
$requests = $patient->doctorRequests; // جميع الطلبات

$doctor = User::find($doctorId);
$requests = $doctor->patientRequests; // جميع طلبات المرضى
```

---

## 🛡️ الأمان

### التحقق من الصلاحيات:
```php
// التحقق من أن الطبيب يملك الطلب
if ($doctorPatient->doctor_id !== Auth::id()) {
    return back()->with('error', 'غير مصرح');
}

// التحقق من أن المستخدم طبيب
if (!$doctor->isDoctor()) {
    return back()->with('error', 'المستخدم ليس طبيباً');
}
```

### منع التكرار:
```php
// التحقق من عدم وجود ربط سابق
$existing = DoctorPatient::where('doctor_id', $doctor->id)
    ->where('patient_id', $patient->id)
    ->first();

if ($existing) {
    return back()->with('error', 'يوجد ربط سابق');
}
```

---

## 📈 الإحصائيات

### إحصائيات الطبيب:
```php
$doctor = User::find($doctorId);

$totalPatients = $doctor->patients()->count();
$pendingRequests = $doctor->patientRequests()
    ->where('status', 'pending')
    ->count();
$rejectedRequests = $doctor->patientRequests()
    ->where('status', 'rejected')
    ->count();
```

### إحصائيات المريض:
```php
$patient = User::find($patientId);

$totalDoctors = $patient->doctors()->count();
$pendingRequests = $patient->doctorRequests()
    ->where('status', 'pending')
    ->count();
```

---

## 🚀 الميزات المستقبلية

- [ ] تقييم الطبيب من قبل المريض
- [ ] نظام الملاحظات الخاصة بين الطبيب والمريض
- [ ] جدولة المواعيد
- [ ] نظام الفيديو للاستشارات
- [ ] تقارير شهرية للطبيب
- [ ] نظام الرسائل الفورية

---

## 📞 الدعم

للمزيد من المعلومات أو الإبلاغ عن مشاكل:
- البريد الإلكتروني: support@mycare.app
- الموقع: https://mycare.app

---

**آخر تحديث:** مارس 2026

**الإصدار:** 1.2.0
