<?php

namespace App\Http\Controllers;

use App\Models\VitalSign;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VitalSignController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $vitalSigns = $user->vitalSigns()->orderBy('measured_at', 'desc')->paginate(10);
        return view('vital-signs.index', compact('vitalSigns'));
    }

    public function create()
    {
        return view('vital-signs.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:blood_pressure,blood_sugar,temperature,weight,heart_rate,oxygen_saturation',
            'value_1' => 'required|numeric',
            'value_2' => 'nullable|numeric',
            'unit' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $vitalSign = Auth::user()->vitalSigns()->create(array_merge(
            $request->all(),
            ['measured_at' => now()]
        ));

        $vitalSign->checkIfAbnormal();

        if ($vitalSign->is_abnormal) {
            Notification::createVitalSignAlert(Auth::id(), $vitalSign);
        }

        return redirect()->route('vital-signs.show', $vitalSign)->with('success', 'تم تسجيل القياس بنجاح');
    }

    public function show(VitalSign $vitalSign)
    {
        $this->authorize('view', $vitalSign);
        return view('vital-signs.show', compact('vitalSign'));
    }

    public function getChartData($type)
    {
        $user = Auth::user();
        $data = $user->vitalSigns()
            ->where('type', $type)
            ->orderBy('measured_at')
            ->get(['measured_at', 'value_1', 'value_2']);

        return response()->json($data);
    }
}
