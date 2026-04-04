@extends('layouts.app')

@section('title', 'تفاصيل المريض - MyCare')

@section('content')
<div class="header">
    <h1>{{ $patient->name }}</h1>
    <p>معلومات المريض والسجل الطبي</p>
</div>

<div class="card">
    <h3>📋 معلومات المريض</h3>
    <p><strong>الاسم:</strong> {{ $patient->name }}</p>
    <p><strong>البريد الإلكتروني:</strong> {{ $patient->email }}</p>
    <p><strong>رقم الهاتف:</strong> {{ $patient->phone ?? 'غير محدد' }}</p>
    <p><strong>تاريخ الميلاد:</strong> {{ $patient->date_of_birth ?? 'غير محدد' }}</p>
    <p><strong>الجنس:</strong> {{ $patient->gender ?? 'غير محدد' }}</p>
</div>

<div class="card">
    <h3>💊 الأدوية الحالية</h3>
    @if($patient->medications->isEmpty())
        <p class="text-muted">لا توجد أدوية مسجلة</p>
    @else
        @foreach($patient->medications as $medication)
            <div style="padding: 10px; border-bottom: 1px solid #ddd;">
                <p><strong>{{ $medication->name }}</strong></p>
                <p class="text-muted">الجرعة: {{ $medication->dosage }}</p>
                <p class="text-muted">التكرار: {{ $medication->frequency }}</p>
            </div>
        @endforeach
    @endif
</div>

<div class="card">
    <h3>❤️ آخر العلامات الحيوية</h3>
    @if($patient->vitalSigns->isEmpty())
        <p class="text-muted">لا توجد قياسات مسجلة</p>
    @else
        @foreach($patient->vitalSigns->take(5) as $vital)
            <div style="padding: 10px; border-bottom: 1px solid #ddd;">
                <p><strong>{{ $vital->type }}</strong>: {{ $vital->value }} {{ $vital->unit }}</p>
                <p class="text-muted">{{ $vital->created_at->format('d/m/Y H:i') }}</p>
            </div>
        @endforeach
    @endif
</div>

<div class="card">
    <h3>📝 الملاحظات الطبية</h3>
    <a href="{{ route('doctor.notes.create', $patient->id) }}" class="btn btn-primary btn-block">
        إضافة ملاحظة جديدة
    </a>
</div>

<div class="card">
    <h3>💊 الوصفات الطبية</h3>
    <a href="{{ route('doctor.prescriptions.create', $patient->id) }}" class="btn btn-primary btn-block">
        إضافة وصفة جديدة
    </a>
</div>

<div class="card">
    <h3>💬 إرسال رسالة</h3>
    <a href="{{ route('doctor.messages.create', $patient->id) }}" class="btn btn-primary btn-block">
        إرسال رسالة
    </a>
</div>
@endsection
