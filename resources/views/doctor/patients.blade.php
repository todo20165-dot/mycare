@extends('layouts.app')

@section('title', 'قائمة المرضى - MyCare')

@section('content')
<div class="header">
    <h1>👥 المرضى</h1>
    <p>قائمة المرضى تحت إشرافك</p>
</div>

@if($patients->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد مرضى حالياً</p>
    </div>
@else
    @foreach($patients as $patient)
        <div class="card">
            <h3>{{ $patient->name }}</h3>
            <p class="text-muted">البريد الإلكتروني: {{ $patient->email }}</p>
            <p class="text-muted">رقم الهاتف: {{ $patient->phone ?? 'غير محدد' }}</p>
            
            <div class="grid">
                <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-primary">
                    عرض التفاصيل
                </a>
                <a href="{{ route('doctor.prescriptions.create', $patient->id) }}" class="btn btn-secondary">
                    إضافة وصفة
                </a>
            </div>
        </div>
    @endforeach
@endif
@endsection
