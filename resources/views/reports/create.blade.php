@extends('layouts.app')

@section('title', 'إنشاء تقرير - MyCare')

@section('content')
<div class="header">
    <h1>📊 إنشاء تقرير جديد</h1>
    <p>اختر نوع التقرير المطلوب</p>
</div>

<form action="{{ route('reports.store') }}" method="POST">
    @csrf
    
    @if(Auth::user()->isDoctor())
        <div class="card">
            <div class="form-group">
                <label for="user_id">اختيار المريض</label>
                <select name="user_id" id="user_id" required>
                    <option value="">-- اختر المريض --</option>
                    @php
                        $approvedPatients = \App\Models\DoctorPatient::where('doctor_id', Auth::id())
                            ->where('status', 'approved')
                            ->with('patient')
                            ->get();
                    @endphp
                    @foreach($approvedPatients as $link)
                        <option value="{{ $link->patient->id }}">{{ $link->patient->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif
    
    <div class="card">
        <div class="form-group">
            <label for="type">نوع التقرير</label>
            <select name="type" id="type" required>
                <option value="">-- اختر نوع التقرير --</option>
                <option value="medication_adherence">تقرير الالتزام بالأدوية</option>
                <option value="vital_signs">تقرير العلامات الحيوية</option>
                <option value="comprehensive_health">التقرير الصحي الشامل</option>
                <option value="custom">تقرير مخصص</option>
            </select>
        </div>
    </div>

    <div class="card">
        <div class="form-group">
            <label for="title">عنوان التقرير</label>
            <input type="text" name="title" id="title" required>
        </div>
    </div>

    <div class="card">
        <div class="form-group">
            <label for="description">وصف التقرير (اختياري)</label>
            <textarea name="description" id="description" rows="3"></textarea>
        </div>
    </div>

    <div class="card">
        <div class="form-group">
            <label for="start_date">تاريخ البداية</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>
    </div>

    <div class="card">
        <div class="form-group">
            <label for="end_date">تاريخ النهاية</label>
            <input type="date" name="end_date" id="end_date" required>
        </div>
    </div>

    <div class="card">
        <button type="submit" class="btn btn-primary btn-block">
            إنشاء التقرير
        </button>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-block">
            إلغاء
        </a>
    </div>
</form>
@endsection
