@extends('layouts.app')

@section('title', 'الأدوية - MyCare')

@section('content')
<div class="medications-list">
    <h2>💊 الأدوية</h2>

    @if($medications->isEmpty())
        <div class="card mt-2">
            <p class="text-center text-muted">لم تضف أي أدوية بعد</p>
        </div>
        <a href="{{ route('medications.create') }}" class="btn btn-primary btn-block mt-2">+ إضافة دواء</a>
    @else
        @foreach($medications as $medication)
            <div class="card mt-2">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3>{{ $medication->name }}</h3>
                        <p><strong>الجرعة:</strong> {{ $medication->dosage }}</p>
                        <p><strong>التكرار:</strong> {{ $medication->frequency }}</p>
                        @if($medication->reason)
                            <p><strong>السبب:</strong> {{ $medication->reason }}</p>
                        @endif
                        <p style="color: var(--secondary); font-weight: bold;">
                            📊 معدل الالتزام: {{ round($medication->adherence_rate, 1) }}%
                        </p>
                        @if($medication->is_active)
                            <span style="background: var(--success); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">نشط</span>
                        @else
                            <span style="background: #ccc; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">غير نشط</span>
                        @endif
                    </div>
                </div>
                <div style="margin-top: 10px; display: flex; gap: 10px;">
                    <a href="{{ route('medications.show', $medication) }}" class="btn btn-secondary" style="flex: 1; text-align: center;">عرض</a>
                    <a href="{{ route('medications.edit', $medication) }}" class="btn btn-primary" style="flex: 1; text-align: center;">تعديل</a>
                </div>
            </div>
        @endforeach

        <div class="mt-2">
            {{ $medications->links() }}
        </div>
    @endif
</div>
@endsection
