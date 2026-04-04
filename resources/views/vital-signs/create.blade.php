@extends('layouts.app')

@section('title', 'تسجيل قياس جديد - MyCare')

@section('content')
<div class="vital-sign-create">
    <h2>❤️ تسجيل قياس جديد</h2>

    <form method="POST" action="{{ route('vital-signs.store') }}" class="mt-2">
        @csrf

        <div class="form-group">
            <label for="type">نوع القياس *</label>
            <select id="type" name="type" required onchange="updateUnits()">
                <option value="">اختر نوع القياس</option>
                <option value="blood_pressure">ضغط الدم</option>
                <option value="blood_sugar">السكر في الدم</option>
                <option value="temperature">درجة الحرارة</option>
                <option value="weight">الوزن</option>
                <option value="heart_rate">نبضات القلب</option>
                <option value="oxygen_saturation">تشبع الأكسجين</option>
            </select>
        </div>

        <div class="form-group">
            <label for="value_1">القيمة الأولى *</label>
            <input type="number" id="value_1" name="value_1" step="0.01" placeholder="مثال: 120" required>
        </div>

        <div class="form-group">
            <label for="value_2">القيمة الثانية (اختياري)</label>
            <input type="number" id="value_2" name="value_2" step="0.01" placeholder="مثال: 80 (لضغط الدم)">
        </div>

        <div class="form-group">
            <label for="unit">الوحدة *</label>
            <input type="text" id="unit" name="unit" readonly>
        </div>

        <div class="form-group">
            <label for="notes">ملاحظات</label>
            <textarea id="notes" name="notes" rows="3" placeholder="أي ملاحظات إضافية"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">حفظ القياس</button>
        <a href="{{ route('vital-signs.index') }}" class="btn btn-secondary btn-block">إلغاء</a>
    </form>
</div>

<script>
const units = {
    'blood_pressure': 'mmHg',
    'blood_sugar': 'mg/dL',
    'temperature': '°C',
    'weight': 'kg',
    'heart_rate': 'bpm',
    'oxygen_saturation': '%'
};

function updateUnits() {
    const type = document.getElementById('type').value;
    document.getElementById('unit').value = units[type] || '';
    
    // إظهار/إخفاء القيمة الثانية
    const value2 = document.getElementById('value_2');
    if (type === 'blood_pressure') {
        value2.parentElement.style.display = 'block';
    } else {
        value2.parentElement.style.display = 'none';
    }
}
</script>
@endsection
