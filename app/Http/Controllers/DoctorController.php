<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Medication;
use App\Models\VitalSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $doctor = Auth::user();
        $patients = User::where('role', 'patient')->paginate(10);
        return view('dashboard.doctor', compact('patients'));
    }

    public function patientDetails($patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        $medications = $patient->medications()->get();
        $vitalSigns = $patient->vitalSigns()->orderBy('measured_at', 'desc')->limit(10)->get();
        $adherenceRate = $medications->avg('adherence_rate') ?? 0;

        return view('doctor.patient-detail', compact('patient', 'medications', 'vitalSigns', 'adherenceRate'));
    }

    public function prescriptionsIndex()
    {
        return redirect()->route('doctor.patients.index');
    }

    public function notesIndex()
    {
        return redirect()->route('doctor.patients.index');
    }

    public function messagesIndex()
    {
        return redirect()->route('doctor.patients.index');
    }

    public function createPrescription($patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        $medications = Medication::all();

        return view('doctor.prescriptions-create', compact('patient', 'medications'));
    }

    public function addPrescription(Request $request, $patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        $validated = $request->validate([
            'medication_id' => 'required|exists:medications,id',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $medicationTemplate = Medication::findOrFail($validated['medication_id']);

        $newMedication = $patient->medications()->create([
            'name' => $medicationTemplate->name,
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // إشعار للمريض
        \App\Models\Notification::create([
            'user_id' => $patient->id,
            'title' => 'تمت إضافة وصفة طبية جديدة',
            'message' => "الطبيب " . auth()->user()->name . " أضاف وصفة: {$newMedication->name}",
            'type' => 'message',
            'related_type' => 'Medication',
            'related_id' => $newMedication->id,
        ]);

        return redirect()->route('doctor.patients.show', $patientId)->with('success', 'تم إضافة الوصفة الطبية بنجاح.');
    }

    public function createNote($patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        return view('doctor.notes-create', compact('patient'));
    }

    public function addNote(Request $request, $patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'note_type' => 'required|string',
            'priority' => 'required|string',
            'is_confidential' => 'nullable|boolean',
        ]);

        // هنا يمكنك لاحقاً حفظ الملاحظة إلى جدول ملاحظات إن وجد

        \App\Models\Notification::create([
            'user_id' => $patient->id,
            'title' => 'ملاحظة جديدة من الطبيب',
            'message' => "الطبيب " . auth()->user()->name . " أضاف ملاحظة: {$validated['title']}.",
            'type' => 'message',
            'related_type' => 'DoctorNote',
            'related_id' => null,
        ]);

        return redirect()->route('doctor.patients.show', $patientId)->with('success', 'تم إضافة الملاحظة الطبية بنجاح.');
    }

    public function createMessage($patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        return view('doctor.messages-create', compact('patient'));
    }

    public function sendMessage(Request $request, $patientId)
    {
        $patient = User::findOrFail($patientId);
        $this->authorize('viewPatient', $patient);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // TODO: إضافة إرسال أو حفظ الرسالة في النموذج المناسب لاحقاً

        return redirect()->route('doctor.patients.show', $patientId)->with('success', 'تم إرسال الرسالة بنجاح.');
    }

    public function myPatients()
    {
        $doctor = Auth::user();
        // سيتم تطبيق هذا بعد إضافة جدول العلاقة بين الطبيب والمريض
        return view('doctor.my-patients');
    }
}
