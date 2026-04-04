# نظام الارتباط العائلي - MyCare

وثيقة شاملة توضح كيفية عمل نظام الارتباط العائلي في تطبيق MyCare.

---

## 📋 نظرة عامة

نظام الارتباط العائلي يسمح للمريض بربط حسابه مع أفراد عائلته، مما يتيح لهم:
- عرض بيانات المريض الطبية
- تتبع الأدوية والعلامات الحيوية
- الحصول على تقارير صحية
- تقديم الدعم والرعاية

---

## 🔄 سير العملية الكاملة

### **المرحلة 1️⃣: المريض يضيف فرد عائلي**

```
المريض → أفراد العائلة → إضافة فرد جديد → ملء البيانات → إرسال الدعوة
```

**الصفحة:** `/family/add-member`

**البيانات المطلوبة:**
- البريد الإلكتروني
- نوع العلاقة (والد، ابن، زوج، أخ، آخر)
- ملاحظات (اختياري)

---

### **المرحلة 2️⃣: فرد العائلة يستقبل الدعوة**

```
فرد العائلة → الطلبات المعلقة → يرى الطلب → يقرر القبول أو الرفض
```

**الصفحة:** `/family/my-pending-requests`

**المعلومات المعروضة:**
- اسم المريض
- نوع العلاقة
- البريد الإلكتروني والهاتف
- الملاحظات
- تاريخ الطلب

---

### **المرحلة 3️⃣: فرد العائلة يقبل أو يرفض**

#### ✅ **إذا قبل:**
```
الحالة: approved (موافق عليه)
تاريخ الموافقة: approved_at
النتيجة: يظهر المريض في قائمة مرضاه
```

#### ❌ **إذا رفض:**
```
الحالة: rejected (مرفوض)
النتيجة: المريض يمكنه إعادة محاولة الدعوة
```

---

### **المرحلة 4️⃣: الوصول إلى بيانات المريض**

بعد الموافقة، يمكن لفرد العائلة:

```
1. عرض قائمة الأدوية
2. عرض العلامات الحيوية
3. عرض سجل الجرعات
4. الحصول على تقرير صحي
5. تحميل التقرير كـ PDF
```

---

## 🗄️ البنية قاعدة البيانات

### جدول `family_links`

```sql
CREATE TABLE family_links (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    patient_id BIGINT NOT NULL,
    family_member_id BIGINT NOT NULL,
    relationship VARCHAR(50),
    status ENUM('pending', 'approved', 'rejected', 'inactive') DEFAULT 'pending',
    notes TEXT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_family_link (patient_id, family_member_id),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (family_member_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient_id (patient_id),
    INDEX idx_family_member_id (family_member_id),
    INDEX idx_status (status)
);
```

### الأعمدة:

| العمود | النوع | الوصف |
|-------|-------|-------|
| `id` | BIGINT | معرف فريد |
| `patient_id` | BIGINT | معرف المريض |
| `family_member_id` | BIGINT | معرف فرد العائلة |
| `relationship` | VARCHAR | نوع العلاقة |
| `status` | ENUM | حالة الربط |
| `notes` | TEXT | ملاحظات إضافية |
| `approved_at` | TIMESTAMP | تاريخ الموافقة |

---

## 📱 الواجهات (Views)

### **واجهات المريض:**

#### 1️⃣ قائمة أفراد العائلة
**الملف:** `resources/views/family/members-list.blade.php`

**المميزات:**
- عرض جميع أفراد العائلة الموافق عليهم
- عرض الطلبات المعلقة
- إلغاء الربط
- معلومات الاتصال

#### 2️⃣ إضافة فرد عائلي جديد
**الملف:** `resources/views/family/add-member.blade.php`

**المميزات:**
- نموذج إدخال البيانات
- اختيار نوع العلاقة
- إضافة ملاحظات
- تحقق من الصحة

#### 3️⃣ الطلبات المعلقة
**الملف:** `resources/views/family/pending-requests.blade.php`

**المميزات:**
- عرض جميع الطلبات المعلقة
- معلومات المتقدم
- أزرار القبول والرفض
- ترقيم الصفحات

---

### **واجهات فرد العائلة:**

#### 1️⃣ قائمة المرضى
**الملف:** `resources/views/family/my-patients.blade.php`

**المميزات:**
- عرض جميع المرضى المرتبطين
- إحصائيات سريعة (أدوية، قياسات)
- رابط التقرير الصحي
- إلغاء الربط

#### 2️⃣ الطلبات المعلقة
**الملف:** `resources/views/family/my-pending-requests.blade.php`

**المميزات:**
- عرض جميع الطلبات المعلقة
- معلومات المريض
- أزرار القبول والرفض

#### 3️⃣ بيانات المريض
**الملف:** `resources/views/family/patient-data.blade.php`

**المميزات:**
- تبويبات: أدوية، علامات حيوية
- عرض الأدوية الحالية
- عرض آخر القياسات
- مؤشرات الحالة الطبيعية/غير الطبيعية

#### 4️⃣ التقرير الصحي
**الملف:** `resources/views/family/health-report.blade.php`

**المميزات:**
- إحصائيات شاملة
- معدل الالتزام بالأدوية
- آخر العلامات الحيوية
- تحميل PDF
- ملاحظات مهمة

---

## 🎮 المتحكم (Controller)

### `FamilyMemberController`

**الدوال الرئيسية:**

#### **دوال المريض:**

1. **`index()`** - قائمة أفراد العائلة
   ```
   GET /family/members-list
   ```

2. **`create()`** - صفحة إضافة فرد جديد
   ```
   GET /family/add-member
   ```

3. **`invite()`** - إرسال دعوة
   ```
   POST /family/invite
   ```

4. **`pendingRequests()`** - الطلبات المعلقة
   ```
   GET /family/pending-requests
   ```

5. **`approve()`** - قبول الطلب
   ```
   POST /family/{id}/approve
   ```

6. **`reject()`** - رفض الطلب
   ```
   POST /family/{id}/reject
   ```

7. **`disconnect()`** - إلغاء الربط
   ```
   DELETE /family/{id}/disconnect
   ```

#### **دوال فرد العائلة:**

1. **`myPatients()`** - قائمة المرضى
   ```
   GET /family/my-patients
   ```

2. **`myPendingRequests()`** - الطلبات المعلقة
   ```
   GET /family/my-pending-requests
   ```

3. **`viewPatientData()`** - بيانات المريض
   ```
   GET /family/patient/{id}/data
   ```

4. **`viewHealthReport()`** - التقرير الصحي
   ```
   GET /family/patient/{id}/report
   ```

5. **`downloadReport()`** - تحميل التقرير
   ```
   GET /family/patient/{id}/report/download
   ```

6. **`sendMessage()`** - إرسال رسالة
   ```
   POST /family/patient/{id}/message
   ```

---

## 🛣️ المسارات (Routes)

```php
// مسارات المريض
Route::middleware('auth')->group(function () {
    Route::get('/family/members-list', [FamilyMemberController::class, 'index'])->name('family.members-list');
    Route::get('/family/add-member', [FamilyMemberController::class, 'create'])->name('family.add-member');
    Route::post('/family/invite', [FamilyMemberController::class, 'invite'])->name('family.invite');
    Route::get('/family/pending-requests', [FamilyMemberController::class, 'pendingRequests'])->name('family.pending-requests');
    Route::post('/family/{id}/approve', [FamilyMemberController::class, 'approve'])->name('family.approve');
    Route::post('/family/{id}/reject', [FamilyMemberController::class, 'reject'])->name('family.reject');
    Route::delete('/family/{id}/disconnect', [FamilyMemberController::class, 'disconnect'])->name('family.disconnect');
});

// مسارات فرد العائلة
Route::middleware('auth')->group(function () {
    Route::get('/family/my-patients', [FamilyMemberController::class, 'myPatients'])->name('family.my-patients');
    Route::get('/family/my-pending-requests', [FamilyMemberController::class, 'myPendingRequests'])->name('family.my-pending-requests');
    Route::get('/family/patient/{id}/data', [FamilyMemberController::class, 'viewPatientData'])->name('family.view-patient-data');
    Route::get('/family/patient/{id}/report', [FamilyMemberController::class, 'viewHealthReport'])->name('family.view-health-report');
    Route::get('/family/patient/{id}/report/download', [FamilyMemberController::class, 'downloadReport'])->name('family.download-report');
    Route::post('/family/patient/{id}/message', [FamilyMemberController::class, 'sendMessage'])->name('family.send-message');
});
```

---

## 📊 أنواع العلاقات

| الرمز | العلاقة | الوصف |
|------|--------|-------|
| `parent` | 👨‍👩 الوالد/الوالدة | الآباء والأمهات |
| `child` | 👶 الابن/الابنة | الأطفال |
| `spouse` | 💑 الزوج/الزوجة | الزوج أو الزوجة |
| `sibling` | 👫 الأخ/الأخت | الإخوة والأخوات |
| `other` | 👤 آخر | علاقات أخرى |

---

## 🔐 الصلاحيات

### صلاحيات المريض:
- ✅ إضافة أفراد عائلة
- ✅ عرض الطلبات المعلقة
- ✅ قبول الطلبات
- ✅ رفض الطلبات
- ✅ إلغاء الربط
- ✅ عرض قائمة أفراد العائلة

### صلاحيات فرد العائلة:
- ✅ عرض الطلبات المعلقة
- ✅ قبول الطلبات
- ✅ رفض الطلبات
- ✅ عرض قائمة المرضى
- ✅ عرض بيانات المريض
- ✅ عرض التقرير الصحي
- ✅ تحميل التقرير
- ✅ إلغاء الربط

---

## 📊 حالات الربط

| الحالة | الوصف | الإجراءات |
|--------|-------|----------|
| `pending` | الطلب معلق | قبول، رفض، إلغاء |
| `approved` | موافق عليه | عرض البيانات، إلغاء |
| `rejected` | مرفوض | إعادة الدعوة |
| `inactive` | ملغى | إعادة الربط |

---

## 💡 أمثلة الاستخدام

### مثال 1: المريض يضيف والده

```php
// في FamilyMemberController
public function invite(Request $request)
{
    $patient = Auth::user();
    $familyMember = User::where('email', $request->email)->firstOrFail();
    
    FamilyLink::create([
        'patient_id' => $patient->id,
        'family_member_id' => $familyMember->id,
        'relationship' => 'parent',
        'status' => 'pending',
    ]);
    
    return back()->with('success', 'تم إرسال الدعوة');
}
```

### مثال 2: فرد العائلة يقبل الطلب

```php
public function approve($id)
{
    $familyLink = FamilyLink::findOrFail($id);
    $familyLink->approve();
    
    return back()->with('success', 'تم قبول الطلب');
}
```

### مثال 3: عرض بيانات المريض

```php
public function viewPatientData($patientId)
{
    $familyMember = Auth::user();
    $patient = User::findOrFail($patientId);
    
    // التحقق من الصلاحيات
    FamilyLink::where('patient_id', $patient->id)
        ->where('family_member_id', $familyMember->id)
        ->where('status', 'approved')
        ->firstOrFail();
    
    $medications = $patient->medications()->paginate(10);
    $vitalSigns = $patient->vitalSigns()->latest()->paginate(10);
    
    return view('family.patient-data', compact('patient', 'medications', 'vitalSigns'));
}
```

---

## 🔗 العلاقات في Eloquent

### الحصول على أفراد العائلة:
```php
$patient = User::find($patientId);
$familyMembers = $patient->familyLinksAsPatient()
    ->where('status', 'approved')
    ->get();
```

### الحصول على المرضى:
```php
$familyMember = User::find($familyMemberId);
$patients = $familyMember->familyLinksAsMember()
    ->where('status', 'approved')
    ->get();
```

### الحصول على جميع الطلبات:
```php
$patient = User::find($patientId);
$requests = $patient->familyLinksAsPatient()
    ->where('status', 'pending')
    ->get();
```

---

## 📈 الإحصائيات

### إحصائيات المريض:
```php
$patient = User::find($patientId);

$totalFamilyMembers = $patient->familyLinksAsPatient()
    ->where('status', 'approved')
    ->count();

$pendingRequests = $patient->familyLinksAsPatient()
    ->where('status', 'pending')
    ->count();
```

### إحصائيات فرد العائلة:
```php
$familyMember = User::find($familyMemberId);

$totalPatients = $familyMember->familyLinksAsMember()
    ->where('status', 'approved')
    ->count();

$pendingRequests = $familyMember->familyLinksAsMember()
    ->where('status', 'pending')
    ->count();
```

---

## 🚀 الميزات المستقبلية

- [ ] نظام الرسائل الفورية
- [ ] تنبيهات مخصصة
- [ ] جدولة المواعيد
- [ ] مشاركة الملاحظات
- [ ] تقارير شهرية
- [ ] نظام الأدوار المتقدم
- [ ] تقييم الرعاية

---

## 📞 الدعم

للمزيد من المعلومات:
- البريد الإلكتروني: support@mycare.app
- الموقع: https://mycare.app

---

**آخر تحديث:** مارس 2026

**الإصدار:** 1.3.0
