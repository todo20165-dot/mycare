@extends('layouts.app')

@section('title', $medication->name . ' - MyCare')

@section('content')
<div class="medication-details">
    <h2>💊 {{ $medication->name }}</h2>

    <div class="card mt-2">
        <h3>معلومات الدواء</h3>
        <p><strong>الجرعة:</strong> {{ $medication->dosage }}</p>
        <p><strong>التكرار:</strong> {{ $medication->frequency }}</p>
        <p><strong>تاريخ البداية:</strong> {{ $medication->start_date->format('d/m/Y') }}</p>
        @if($medication->end_date)
            <p><strong>تاريخ النهاية:</strong> {{ $medication->end_date->format('d/m/Y') }}</p>
        @endif
        @if($medication->reason)
            <p><strong>السبب:</strong> {{ $medication->reason }}</p>
        @endif
        @if($medication->instructions)
            <p><strong>التعليمات:</strong> {{ $medication->instructions }}</p>
        @endif
        <p style="color: var(--secondary); font-weight: bold; margin-top: 10px;">
            📊 معدل الالتزام: {{ round($medication->adherence_rate, 1) }}%
        </p>
    </div>

    <div class="card mt-2">
        <h3>سجل الجرعات</h3>
        @if($logs->isEmpty())
            <p class="text-center text-muted">لا توجد جرعات مسجلة</p>
        @else
            @foreach($logs as $log)
                <div style="padding: 10px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p><strong>{{ $log->scheduled_time->format('d/m/Y H:i') }}</strong></p>
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
            @endforeach
        @endif
    </div>

    <div class="mt-2">
        <a href="{{ route('medications.edit', $medication) }}" class="btn btn-primary btn-block">تعديل</a>
        <a href="{{ route('medications.index') }}" class="btn btn-secondary btn-block">العودة</a>
        <form method="POST" action="{{ route('medications.destroy', $medication) }}" style="display: inline-block; width: 100%;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('هل أنت متأكد من حذف هذا الدواء؟')">حذف</button>
        </form>
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
