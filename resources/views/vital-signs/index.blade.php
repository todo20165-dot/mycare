@extends('layouts.app')

@section('title', 'العلامات الحيوية - MyCare')

@section('content')
<div class="vital-signs-list">
    <h2>❤️ العلامات الحيوية</h2>

    @if($vitalSigns->isEmpty())
        <div class="card mt-2">
            <p class="text-center text-muted">لم تسجل أي قياسات بعد</p>
        </div>
        <a href="{{ route('vital-signs.create') }}" class="btn btn-primary btn-block mt-2">+ تسجيل قياس</a>
    @else
        <div class="grid mt-2 mb-2">
            @php
                $types = $vitalSigns->groupBy('type');
                $latestByType = [];
                foreach($types as $type => $signs) {
                    $latestByType[$type] = $signs->first();
                }
            @endphp

            @foreach($latestByType as $type => $sign)
                <div class="stat-card">
                    <div class="number">{{ $sign->value_1 }}{{ isset($sign->value_2) ? '/' . $sign->value_2 : '' }}</div>
                    <div class="label">{{ $sign->getTypeLabel() }}</div>
                    @if($sign->is_abnormal)
                        <div style="color: var(--danger); font-size: 12px; margin-top: 5px;">⚠️ غير طبيعي</div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-2">
            <h3>السجل الكامل</h3>
            @foreach($vitalSigns as $sign)
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h3>{{ $sign->getTypeLabel() }}</h3>
                            <p><strong>{{ $sign->value_1 }}{{ isset($sign->value_2) ? ' / ' . $sign->value_2 : '' }} {{ $sign->unit }}</strong></p>
                            <p class="text-muted">{{ $sign->measured_at->format('d/m/Y H:i') }}</p>
                            @if($sign->is_abnormal)
                                <p style="color: var(--danger); font-weight: bold;">⚠️ قراءة غير طبيعية</p>
                            @endif
                            @if($sign->notes)
                                <p><strong>ملاحظات:</strong> {{ $sign->notes }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $vitalSigns->links() }}
        </div>
    @endif

    <a href="{{ route('vital-signs.create') }}" class="btn btn-primary btn-block mt-2">+ تسجيل قياس جديد</a>
</div>
@endsection
