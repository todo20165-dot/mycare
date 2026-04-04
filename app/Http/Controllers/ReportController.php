<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isDoctor()) {
            // الطبيب يرى تقارير المرضى المرتبطين به
            $patientIds = \App\Models\DoctorPatient::where('doctor_id', $user->id)
                ->where('status', 'approved')
                ->pluck('patient_id');
            $reports = \App\Models\Report::whereIn('user_id', $patientIds)
                ->orWhere('created_by', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // المريض يرى تقاريره الخاصة
            $reports = $user->reports()->orderBy('created_at', 'desc')->paginate(10);
        }
        
        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        return view('reports.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id',
            'type' => 'required|in:medication_adherence,vital_signs,comprehensive_health,custom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        
        // تحديد المستخدم المستهدف
        $targetUserId = $request->user_id ?? $user->id;
        
        // التحقق من الصلاحية إذا كان الطبيب ينشئ لمريض
        if ($user->isDoctor() && $targetUserId !== $user->id) {
            $canCreate = \App\Models\DoctorPatient::where('doctor_id', $user->id)
                ->where('patient_id', $targetUserId)
                ->where('status', 'approved')
                ->exists();
            if (!$canCreate) {
                return back()->withErrors(['user_id' => 'لا يمكنك إنشاء تقرير لهذا المريض']);
            }
        } elseif (!$user->isDoctor() && $targetUserId !== $user->id) {
            return back()->withErrors(['user_id' => 'لا يمكنك إنشاء تقرير لمستخدم آخر']);
        }

        $report = \App\Models\Report::create(array_merge(
            $request->only(['type', 'title', 'description', 'start_date', 'end_date']),
            [
                'user_id' => $targetUserId,
                'created_by' => $user->id,
                'status' => 'pending'
            ]
        ));

        // توليد بيانات التقرير
        $this->generateReportData($report);

        return redirect()->route('reports.show', $report)->with('success', 'تم إنشاء التقرير بنجاح');
    }

    public function show(Report $report)
    {
        $this->authorize('view', $report);
        return view('reports.show', compact('report'));
    }

    public function download(Report $report)
    {
        $this->authorize('view', $report);

        // Temporarily return HTML view until PDF library is properly installed
        return view('reports.pdf', compact('report'));
    }

    private function generateReportData(Report $report)
    {
        switch ($report->type) {
            case 'medication_adherence':
                $data = $report->generateMedicationAdherenceData();
                break;
            case 'vital_signs':
                $data = $report->generateVitalSignsData();
                break;
            case 'comprehensive_health':
                $medicationData = $report->generateMedicationAdherenceData();
                $vitalSignsData = $report->generateVitalSignsData();
                $data = [
                    'medications' => $medicationData,
                    'vital_signs' => $vitalSignsData,
                ];
                break;
            default:
                $data = [];
        }

        $report->update([
            'data' => $data,
            'status' => 'generated',
        ]);
    }
}
