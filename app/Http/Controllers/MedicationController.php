<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\MedicationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MedicationController extends Controller
{
    /**
     * عرض قائمة الأدوية
     */
    public function index()
    {
        $user = Auth::user();
        $medications = $user->medications()->orderBy('created_at', 'desc')->paginate(10);
        return view('medications.index', compact('medications'));
    }

    /**
     * عرض صفحة إضافة دواء جديد
     */
    public function create()
    {
        return view('medications.create');
    }

    /**
     * حفظ دواء جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|in:once_daily,twice_daily,three_times_daily,four_times_daily,every_6_hours,every_8_hours,every_12_hours,as_needed',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'reason' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $medication = Auth::user()->medications()->create($request->all());
        $this->generateScheduledDoses($medication);

        return redirect()->route('medications.show', $medication)->with('success', 'تم إضافة الدواء بنجاح');
    }

    /**
     * عرض تفاصيل الدواء
     */
    public function show(Medication $medication)
    {
        $this->authorize('view', $medication);
        $logs = $medication->logs()->orderBy('scheduled_time', 'desc')->paginate(10);
        return view('medications.show', compact('medication', 'logs'));
    }

    /**
     * عرض صفحة تعديل الدواء
     */
    public function edit(Medication $medication)
    {
        $this->authorize('update', $medication);
        return view('medications.edit', compact('medication'));
    }

    /**
     * تحديث الدواء
     */
    public function update(Request $request, Medication $medication)
    {
        $this->authorize('update', $medication);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'frequency' => 'required|in:once_daily,twice_daily,three_times_daily,four_times_daily,every_6_hours,every_8_hours,every_12_hours,as_needed',
            'end_date' => 'nullable|date',
            'reason' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $medication->update($request->all());
        return redirect()->route('medications.show', $medication)->with('success', 'تم تحديث الدواء بنجاح');
    }

    /**
     * حذف الدواء
     */
    public function destroy(Medication $medication)
    {
        $this->authorize('delete', $medication);
        $medication->delete();
        return redirect()->route('medications.index')->with('success', 'تم حذف الدواء بنجاح');
    }

    /**
     * تسجيل جرعة
     */
    public function logDose(Request $request, Medication $medication)
    {
        $this->authorize('view', $medication);
        $log = MedicationLog::findOrFail($request->log_id);
        $log->markAsTaken();
        return response()->json(['success' => true, 'message' => 'تم تسجيل الجرعة بنجاح']);
    }

    /**
     * تحديد جرعة كمفقودة
     */
    public function markMissed(Request $request, Medication $medication)
    {
        $this->authorize('view', $medication);
        $log = MedicationLog::findOrFail($request->log_id);
        $log->markAsMissed();
        return response()->json(['success' => true, 'message' => 'تم تحديد الجرعة كمفقودة']);
    }

    /**
     * إنشاء جرعات مجدولة
     */
    private function generateScheduledDoses(Medication $medication)
    {
        $startDate = $medication->start_date;
        $endDate = $medication->end_date ?? $startDate->addMonths(3);
        $currentDate = $startDate;
        $dosesPerDay = $this->getFrequencyDosesPerDay($medication->frequency);

        while ($currentDate <= $endDate) {
            for ($i = 0; $i < $dosesPerDay; $i++) {
                $scheduledTime = $currentDate->copy()->addHours($i * (24 / $dosesPerDay));
                MedicationLog::create([
                    'medication_id' => $medication->id,
                    'user_id' => $medication->user_id,
                    'scheduled_time' => $scheduledTime,
                    'status' => 'pending',
                ]);
            }
            $currentDate->addDay();
        }
    }

    /**
     * الحصول على عدد الجرعات في اليوم
     */
    private function getFrequencyDosesPerDay($frequency)
    {
        $frequencies = [
            'once_daily' => 1,
            'twice_daily' => 2,
            'three_times_daily' => 3,
            'four_times_daily' => 4,
            'every_6_hours' => 4,
            'every_8_hours' => 3,
            'every_12_hours' => 2,
            'as_needed' => 1,
        ];
        return $frequencies[$frequency] ?? 1;
    }
}
