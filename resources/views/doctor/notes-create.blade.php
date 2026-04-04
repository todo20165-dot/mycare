@extends('layouts.app')

@section('title', 'إضافة ملاحظة طبية - MyCare')

@section('content')
<div class="header">
    <h1>📝 إضافة ملاحظة طبية جديدة</h1>
    <p>للمريض: {{ $patient->name }}</p>
</div>

<form action="{{ route('doctor.notes.store', $patient->id) }}" method="POST">
    @csrf
    
    <div class="card">
        <div class="form-group">
            <label for="title">عنوان الملاحظة</label>
            <input type="text" name="title" id="title" placeholder="عنوان الملاحظة" required>
        </div>

        <div class="form-group">
            <label for="content">محتوى الملاحظة</label>
            <textarea name="content" id="content" placeholder="اكتب الملاحظة الطبية هنا" rows="8" required></textarea>
        </div>

        <div class="form-group">
            <label for="note_type">نوع الملاحظة</label>
            <select name="note_type" id="note_type" required>
                <option value="">-- اختر النوع --</option>
                <option value="diagnosis">التشخيص</option>
                <option value="treatment">العلاج</option>
                <option value="follow_up">المتابعة</option>
                <option value="observation">ملاحظة</option>
                <option value="warning">تحذير</option>
                <option value="recommendation">توصية</option>
            </select>
        </div>

        <div class="form-group">
            <label for="priority">الأولوية</label>
            <select name="priority" id="priority" required>
                <option value="low">منخفضة</option>
                <option value="normal">عادية</option>
                <option value="high">عالية</option>
                <option value="urgent">عاجلة</option>
            </select>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_confidential" value="1">
                ملاحظة سرية (للطبيب فقط)
            </label>
        </div>
    </div>

    <div class="card">
        <button type="submit" class="btn btn-primary btn-block">
            حفظ الملاحظة
        </button>
        <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-secondary btn-block">
            إلغاء
        </a>
    </div>
</form>
@endsection
