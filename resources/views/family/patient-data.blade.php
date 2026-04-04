@extends('layouts.app')

@section('title', 'بيانات ' . $patient->name . ' - MyCare')

@section('content')
<div class="header">
    <h1>📋 بيانات {{ $patient->name }}</h1>
    <p>{{ $familyLink->getRelationshipLabel() }}</p>
</div>

<!-- معلومات المريض -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3>{{ $patient->name }}</h3>
            <p class="text-muted">{{ $patient->email }}</p>
            @if($patient->phone)
                <p style="font-size: 14px;">📱 {{ $patient->phone }}</p>
            @endif
        </div>
        <div style="text-align: center;">
            @if($patient->profile_image)
                <img src="{{ $patient->profile_image }}" alt="{{ $patient->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            @else
                <div style="width: 80px; height: 80px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    👤
                </div>
            @endif
        </div>
    </div>
</div>

<!-- التبويبات -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;">
    <button class="tab-btn active" onclick="showTab('medications')">
        💊 الأدوية
    </button>
    <button class="tab-btn" onclick="showTab('vital-signs')">
        ❤️ العلامات الحيوية
    </button>
</div>

<!-- تبويب الأدوية -->
<div id="medications-tab" class="tab-content" style="display: block;">
    <h2 style="font-size: 18px; margin-bottom: 15px;">💊 الأدوية الحالية</h2>
    
    @if($medications->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا توجد أدوية مسجلة</p>
        </div>
    @else
        @foreach($medications as $medication)
            <div class="card">
                <h3>{{ $medication->name }}</h3>
                <p class="text-muted">{{ $medication->dosage }} - {{ $medication->frequency }}</p>
                <p style="font-size: 14px;">
                    <strong>السبب:</strong> {{ $medication->reason ?? 'لم يتم تحديده' }}
                </p>
                <p style="font-size: 14px;">
                    <strong>ملاحظات:</strong> {{ $medication->notes ?? 'لا توجد ملاحظات' }}
                </p>
                <p style="font-size: 12px; color: #999;">
                    بدء التاريخ: {{ $medication->start_date->format('d/m/Y') }}
                </p>
            </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="card">
            {{ $medications->links() }}
        </div>
    @endif
</div>

<!-- تبويب العلامات الحيوية -->
<div id="vital-signs-tab" class="tab-content" style="display: none;">
    <h2 style="font-size: 18px; margin-bottom: 15px;">❤️ آخر العلامات الحيوية</h2>
    
    @if($vitalSigns->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا توجد قياسات مسجلة</p>
        </div>
    @else
        @foreach($vitalSigns as $sign)
            <div class="card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <p style="margin: 0; font-size: 12px; color: #999;">{{ $sign->sign_type }}</p>
                        <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold;">
                            {{ $sign->value }} {{ $sign->unit }}
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-size: 12px; color: #999;">التاريخ</p>
                        <p style="margin: 5px 0 0 0; font-size: 14px;">
                            {{ $sign->recorded_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                
                @if($sign->notes)
                    <p style="font-size: 12px; color: #666; margin-top: 10px;">
                        <strong>ملاحظات:</strong> {{ $sign->notes }}
                    </p>
                @endif

                <!-- مؤشر الحالة -->
                @if($sign->is_abnormal)
                    <div style="margin-top: 10px; padding: 10px; background: #F8D7DA; color: #721C24; border-radius: 5px; font-size: 12px;">
                        ⚠️ قراءة غير طبيعية
                    </div>
                @else
                    <div style="margin-top: 10px; padding: 10px; background: #D4EDDA; color: #155724; border-radius: 5px; font-size: 12px;">
                        ✅ قراءة طبيعية
                    </div>
                @endif
            </div>
        @endforeach
        
        <!-- Pagination -->
        <div class="card">
            {{ $vitalSigns->links() }}
        </div>
    @endif
</div>

<!-- الإجراءات -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px;">
    <a href="{{ route('family.my-patients') }}" class="btn btn-secondary btn-block">
        ← رجوع
    </a>
    <a href="{{ route('family.view-health-report', $patient->id) }}" class="btn btn-primary btn-block">
        📊 التقرير الصحي
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

<script>
function showTab(tabName) {
    // إخفاء جميع التبويبات
    document.getElementById('medications-tab').style.display = 'none';
    document.getElementById('vital-signs-tab').style.display = 'none';
    
    // إزالة الفئة النشطة من جميع الأزرار
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // عرض التبويب المختار
    if (tabName === 'medications') {
        document.getElementById('medications-tab').style.display = 'block';
        document.querySelectorAll('.tab-btn')[0].classList.add('active');
    } else if (tabName === 'vital-signs') {
        document.getElementById('vital-signs-tab').style.display = 'block';
        document.querySelectorAll('.tab-btn')[1].classList.add('active');
    }
}
</script>

<style>
.tab-btn {
    padding: 12px 20px;
    border: none;
    background: #f0f0f0;
    color: #333;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.tab-btn.active {
    background: #2196F3;
    color: white;
}

.tab-btn:hover {
    background: #1976D2;
    color: white;
}

.tab-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
@endsection
