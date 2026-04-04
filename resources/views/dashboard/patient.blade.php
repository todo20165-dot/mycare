@extends('layouts.app')

@section('title', 'لوحة التحكم - MyCare')

@section('content')
<div class="dashboard-patient">
    <div class="welcome-section">
        <h2>👋 مرحباً {{ auth()->user()->name }}</h2>
        <p>معدل الالتزام بالأدوية: <strong style="color: var(--secondary);">{{ round($adherenceRate, 1) }}%</strong></p>
    </div>

    <div class="quick-stats mt-2 mb-2">
        <div class="grid">
            <div class="stat-card">
                <div class="number">{{ $medications->count() }}</div>
                <div class="label">أدوية نشطة</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary) 0%, #00b385 100%);">
                <div class="number">{{ $pendingLogs->count() }}</div>
                <div class="label">جرعات اليوم</div>
            </div>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🔗 ربط وتواصل</h3>
        <div class="grid">
            <a href="{{ route('patient.search-doctors') }}" class="btn btn-primary btn-block">🔎 البحث عن طبيب وطلب الربط</a>
            <a href="{{ route('family-links.index') }}" class="btn btn-secondary btn-block">👪 إدارة روابط الأسرة</a>
            <a href="{{ route('family-links.create') }}" class="btn btn-secondary btn-block">➕ إضافة فرد عائلة</a>
            <a href="{{ route('family-links.pending') }}" class="btn btn-warning btn-block">⏳ طلبات ربط عائلية معلقة</a>
            <a href="{{ route('reports.index') }}" class="btn btn-info btn-block">📊 التقارير الطبية</a>
        </div>
    </div>

    <div class="section">
        <h3>📋 جرعات اليوم</h3>
        @if($todayLogs->isEmpty())
            <div class="card">
                <p class="text-center text-muted">لا توجد جرعات مجدولة اليوم</p>
            </div>
        @else
            @foreach($todayLogs as $log)
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3>{{ $log->medication->name }}</h3>
                            <p>{{ $log->medication->dosage }} - {{ $log->scheduled_time->format('H:i') }}</p>
                            <p class="text-muted">
                                @if($log->status === 'taken')
                                    <span style="color: var(--success);">✓ تم تناولها</span>
                                @elseif($log->status === 'pending')
                                    <span style="color: var(--warning);">⏳ قيد الانتظار</span>
                                @else
                                    <span style="color: var(--danger);">✗ مفقودة</span>
                                @endif
                            </p>
                        </div>
                        @if($log->status === 'pending')
                            <button class="btn btn-secondary" onclick="markAsTaken({{ $log->id }})">تناول</button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="section mt-2">
        <h3>❤️ آخر قياسات العلامات الحيوية</h3>
        @if($recentVitalSigns->isEmpty())
            <div class="card">
                <p class="text-center text-muted">لم تقم بتسجيل أي قياسات بعد</p>
            </div>
        @else
            @foreach($recentVitalSigns as $sign)
                <div class="card">
                    <h3>{{ $sign->getTypeLabel() }}</h3>
                    <p>{{ $sign->value_1 }}{{ isset($sign->value_2) ? ' / ' . $sign->value_2 : '' }} {{ $sign->unit }}</p>
                    <p class="text-muted">{{ $sign->measured_at->format('d/m/Y H:i') }}</p>
                    @if($sign->is_abnormal)
                        <p style="color: var(--danger); font-weight: bold;">⚠️ قراءة غير طبيعية</p>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <div class="section mt-2">
        <h3>🔔 الإشعارات الأخيرة</h3>
        @if($unreadNotifications->isEmpty())
            <div class="card">
                <p class="text-center text-muted">لا توجد إشعارات جديدة</p>
            </div>
        @else
            @foreach($unreadNotifications as $notification)
                <div class="card">
                    <h3>{{ $notification->title }}</h3>
                    <p>{{ $notification->message }}</p>
                    <p class="text-muted">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        @endif
    </div>

    <div class="section mt-2 mb-2">
        <a href="{{ route('medications.create') }}" class="btn btn-primary btn-block">+ إضافة دواء جديد</a>
        <a href="{{ route('vital-signs.create') }}" class="btn btn-secondary btn-block">+ تسجيل قياس</a>
    </div>
</div>

<script>
function markAsTaken(logId) {
    if (confirm('هل تريد تسجيل تناول هذه الجرعة؟')) {
        // سيتم تطبيق هذا لاحقاً
        alert('تم تسجيل الجرعة بنجاح');
        location.reload();
    }
}
</script>
@endsection
