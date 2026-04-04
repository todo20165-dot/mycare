@extends('layouts.app')

@section('title', 'إضافة وصفة طبية - MyCare')

@section('content')
<div class="header">
    <h1>💊 إضافة وصفة طبية جديدة</h1>
    <p>للمريض: {{ $patient->name }}</p>
</div>

<form action="{{ route('doctor.prescriptions.store', $patient->id) }}" method="POST">
    @csrf
    
    <div class="card">
        <h3>معلومات الوصفة</h3>
        
        <div class="form-group">
            <label for="medication_id">اختر الدواء</label>
            <select name="medication_id" id="medication_id" required>
                <option value="">-- اختر دواء --</option>
                @foreach($medications as $medication)
                    <option value="{{ $medication->id }}">{{ $medication->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="dosage">الجرعة</label>
            <input type="text" name="dosage" id="dosage" placeholder="مثال: 500mg" required>
        </div>

        <div class="form-group">
            <label for="frequency">التكرار</label>
            <select name="frequency" id="frequency" required>
                <option value="">-- اختر التكرار --</option>
                <option value="once_daily">مرة يومياً</option>
                <option value="twice_daily">مرتين يومياً</option>
                <option value="three_times_daily">ثلاث مرات يومياً</option>
                <option value="four_times_daily">أربع مرات يومياً</option>
                <option value="every_6_hours">كل 6 ساعات</option>
                <option value="every_8_hours">كل 8 ساعات</option>
                <option value="every_12_hours">كل 12 ساعة</option>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date">تاريخ البداية</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>

        <div class="form-group">
            <label for="end_date">تاريخ النهاية</label>
            <input type="date" name="end_date" id="end_date">
        </div>

        <div class="form-group">
            <label for="instructions">تعليمات خاصة</label>
            <textarea name="instructions" id="instructions" placeholder="أي تعليمات خاصة للمريض" rows="4"></textarea>
        </div>

        <div class="form-group">
            <label for="notes">ملاحظات طبية</label>
            <textarea name="notes" id="notes" placeholder="ملاحظات إضافية" rows="4"></textarea>
        </div>
    </div>

    <div class="card">
        <button type="submit" class="btn btn-primary btn-block">
            إضافة الوصفة
        </button>
        <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-secondary btn-block">
            إلغاء
        </a>
    </div>
</form>
@endsection
