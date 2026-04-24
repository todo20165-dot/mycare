<?php

namespace App\Http\Controllers;

use App\Models\Disease;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiseaseController extends Controller
{
    /**
     * عرض صفحة اختيار المرض للمريض
     */
    public function selectDisease()
    {
        $user = Auth::user();
        $diseases = Disease::all();
        $selectedDisease = $user->disease;
        
        return view('patient.select-disease', compact('diseases', 'selectedDisease'));
    }

    /**
     * حفظ اختيار المرض للمريض
     */
    public function storeDisease(Request $request)
    {
        $request->validate([
            'disease_id' => 'required|exists:diseases,id',
        ], [
            'disease_id.required' => 'يجب اختيار مرض',
            'disease_id.exists' => 'المرض المختار غير موجود',
        ]);

        $user = Auth::user();
        $user->update(['disease_id' => $request->disease_id]);

        return redirect()->route('patient.select-disease')
            ->with('success', 'تم حفظ اختيار المرض بنجاح');
    }

    /**
     * الحصول على الأطباء المناسبين للمرض المختار (AJAX)
     */
    public function getDoctorsByDisease(Request $request)
    {
        $request->validate([
            'disease_id' => 'required|exists:diseases,id',
        ]);

        $disease = Disease::findOrFail($request->disease_id);
        
        // الحصول على الأطباء الذين تخصصهم يعالج هذا المرض
        $doctors = User::where('role', 'doctor')
            ->where('specialization', $disease->specialization)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'doctors' => $doctors,
            'disease' => $disease,
        ]);
    }

    /**
     * البحث عن أطباء بناءً على المرض المختار
     */
    public function searchDoctorsByDisease(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->disease_id) {
            return redirect()->route('patient.select-disease')
                ->with('warning', 'يجب اختيار مرض أولاً');
        }

        $disease = $user->disease;
        $query = $request->input('q');
        
        // البحث عن الأطباء الذين تخصصهم يعالج مرض المريض
        $doctors = User::where('role', 'doctor')
            ->where('specialization', $disease->specialization)
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%");
            })
            ->paginate(10);

        return view('patient.search-doctors-by-disease', compact('doctors', 'disease', 'query'));
    }

    /**
     * التحقق من أن الطبيب مناسب للمرض
     */
    public function validateDoctorForDisease(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'disease_id' => 'required|exists:diseases,id',
        ]);

        $doctor = User::findOrFail($request->doctor_id);
        $disease = Disease::findOrFail($request->disease_id);

        // التحقق من أن تخصص الطبيب يعالج هذا المرض
        if ($doctor->specialization !== $disease->specialization) {
            return response()->json([
                'valid' => false,
                'message' => 'هذا الطبيب غير متخصص في علاج ' . $disease->name,
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => 'الطبيب متخصص في علاج ' . $disease->name,
        ]);
    }

    /**
     * عرض قائمة الأمراض (للإدارة)
     */
    public function index()
    {
        $diseases = Disease::all();
        return view('admin.diseases.index', compact('diseases'));
    }

    /**
     * إنشاء مرض جديد (للإدارة)
     */
    public function create()
    {
        return view('admin.diseases.create');
    }

    /**
     * حفظ مرض جديد (للإدارة)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:diseases',
            'specialization' => 'required',
            'description' => 'nullable',
        ]);

        Disease::create($request->all());

        return redirect()->route('admin.diseases.index')
            ->with('success', 'تم إضافة المرض بنجاح');
    }

    /**
     * تعديل مرض (للإدارة)
     */
    public function edit(Disease $disease)
    {
        return view('admin.diseases.edit', compact('disease'));
    }

    /**
     * تحديث مرض (للإدارة)
     */
    public function update(Request $request, Disease $disease)
    {
        $request->validate([
            'name' => 'required|unique:diseases,name,' . $disease->id,
            'specialization' => 'required',
            'description' => 'nullable',
        ]);

        $disease->update($request->all());

        return redirect()->route('admin.diseases.index')
            ->with('success', 'تم تحديث المرض بنجاح');
    }

    /**
     * حذف مرض (للإدارة)
     */
    public function destroy(Disease $disease)
    {
        $disease->delete();

        return redirect()->route('admin.diseases.index')
            ->with('success', 'تم حذف المرض بنجاح');
    }
}
