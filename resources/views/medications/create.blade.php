@extends('layouts.app')

@section('title', 'إضافة دواء جديد - MyCare')

@section('content')
<div class="medication-create">
    <h2>💊 إضافة دواء جديد</h2>

    <form method="POST" action="{{ route('medications.store') }}" class="mt-2">
        @csrf

        <div class="form-group">
            <label for="name">اسم الدواء *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="dosage">الجرعة *</label>
            <input type="text" id="dosage" name="dosage" placeholder="مثال: 500 ملغ" value="{{ old('dosage') }}" required>
        </div>

        <div class="form-group">
            <label for="frequency">التكرار *</label>
            <select id="frequency" name="frequency" required>
                <option value="">اختر التكرار</option>
                <option value="once_daily">مرة واحدة يومياً</option>
                <option value="twice_daily">مرتين يومياً</option>
                <option value="three_times_daily">ثلاث مرات يومياً</option>
                <option value="four_times_daily">أربع مرات يومياً</option>
                <option value="every_6_hours">كل 6 ساعات</option>
                <option value="every_8_hours">كل 8 ساعات</option>
                <option value="every_12_hours">كل 12 ساعة</option>
                <option value="as_needed">حسب الحاجة</option>
            </select>
        </div>

        <div class="form-group">
            <label for="start_date">تاريخ البداية *</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
        </div>

        <div class="form-group">
            <label for="end_date">تاريخ النهاية (اختياري)</label>
            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}">
        </div>

        <div class="form-group">
            <label for="reason">سبب الدواء</label>
            <input type="text" id="reason" name="reason" placeholder="مثال: ارتفاع ضغط الدم" value="{{ old('reason') }}">
        </div>

        <div class="form-group">
            <label for="instructions">تعليمات خاصة</label>
            <textarea id="instructions" name="instructions" rows="3" placeholder="مثال: تناول مع الطعام">{{ old('instructions') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">إضافة الدواء</button>
        <a href="{{ route('medications.index') }}" class="btn btn-secondary btn-block">إلغاء</a>
    </form>
</div>
@endsection
