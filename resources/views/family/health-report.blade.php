@extends('layouts.app')

@section('title', 'التقرير الصحي - MyCare')

@section('content')
<div class="header">
    <h1>📊 التقرير الصحي</h1>
    <p>{{ $patient->name }} - {{ $familyLink->getRelationshipLabel() }}</p>
</div>

<!-- معلومات المريض -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3>{{ $patient->name }}</h3>
            <p class="text-muted">{{ $patient->email }}</p>
        </div>
        <div style="text-align: center;">
            @if($patient->profile_image)
                <img src="{{ $patient->profile_image }}" alt="{{ $patient->name }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;">
            @else
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    👤
                </div>
            @endif
        </div>
    </div>
</div>

<!-- الإحصائيات الرئيسية -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <p style="margin: 0; font-size: 12px; opacity: 0.9;">الأدوية</p>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: bold;">{{ $medicationCount }}</p>
    </div>
    
    <div class="card" style="text-align: center; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <p style="margin: 0; font-size: 12px; opacity: 0.9;">القياسات</p>
        <p style="margin: 10px 0 0 0; font-size: 28px; font-weight: bold;">{{ $vitalSignsCount }}</p>
    </div>
</div>

<!-- معدل الالتزام -->
<div class="card">
    <h3>💊 معدل الالتزام بالأدوية</h3>
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="flex: 1;">
            <div style="background: #e0e0e0; height: 20px; border-radius: 10px; overflow: hidden;">
                <div style="background: linear-gradient(90deg, #4CAF50, #8BC34A); height: 100%; width: {{ $complianceRate }}%; transition: width 0.3s ease;"></div>
            </div>
        </div>
        <div style="font-size: 24px; font-weight: bold; color: #4CAF50; min-width: 60px;">
            {{ $complianceRate }}%
        </div>
    </div>
    <p style="font-size: 12px; color: #999; margin-top: 10px;">
        @if($complianceRate >= 80)
            ✅ التزام ممتاز
        @elseif($complianceRate >= 60)
            ⚠️ التزام جيد
        @else
            ❌ التزام منخفض
        @endif
    </p>
</div>

<!-- آخر العلامات الحيوية -->
<div class="card">
    <h3>❤️ آخر العلامات الحيوية</h3>
    
    @if($recentVitalSigns->isEmpty())
        <p class="text-muted">لا توجد قياسات مسجلة</p>
    @else
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            @foreach($recentVitalSigns as $sign)
                <div style="padding: 10px; background: #f5f5f5; border-radius: 8px;">
                    <p style="margin: 0; font-size: 11px; color: #999; text-transform: uppercase;">{{ $sign->sign_type }}</p>
                    <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">
                        {{ $sign->value }} {{ $sign->unit }}
                    </p>
                    <p style="margin: 3px 0 0 0; font-size: 10px; color: #999;">
                        {{ $sign->recorded_at->format('d/m H:i') }}
                    </p>
                    @if($sign->is_abnormal)
                        <p style="margin: 5px 0 0 0; font-size: 10px; color: #d32f2f;">⚠️ غير طبيعي</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- الملاحظات الطبية -->
<div class="card">
    <h3>📝 ملاحظات مهمة</h3>
    <ul style="margin: 10px 0; padding-right: 20px;">
        <li>تم إنشاء هذا التقرير في {{ now()->format('d/m/Y H:i') }}</li>
        <li>معدل الالتزام محسوب بناءً على آخر 30 يوم</li>
        <li>يجب استشارة الطبيب للقراءات غير الطبيعية</li>
        <li>هذا التقرير للمرجعية فقط ولا يغني عن الاستشارة الطبية</li>
    </ul>
</div>

<!-- الإجراءات -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px;">
    <a href="{{ route('family.my-patients') }}" class="btn btn-secondary btn-block">
        ← رجوع
    </a>
    <a href="{{ route('family.download-report', $patient->id) }}" class="btn btn-primary btn-block">
        📥 تحميل PDF
    </a>
</div>

<!-- رسائل النجاح -->
@if(session('success'))
    <div class="card" style="background: #D4EDDA; color: #155724; margin-top: 20px;">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="card" style="background: #F8D7DA; color: #721C24; margin-top: 20px;">
        ❌ {{ session('error') }}
    </div>
@endif
@endsection
