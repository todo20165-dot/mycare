@extends('layouts.app')

@section('title', 'مرضاي - MyCare')

@section('content')
<div class="header">
    <h1>👥 مرضاي</h1>
    <p>عدد المرضى: {{ $patients->total() }}</p>
</div>

@if($patients->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد مرضى مرتبطين بك حالياً</p>
    </div>
@else
    @foreach($patients as $patient)
        <div class="card" onclick="location.href='{{ route('doctor.patients.show', $patient->id) }}';" style="cursor: pointer;">
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
                        <img src="{{ $patient->profile_image }}" alt="{{ $patient->name }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            👤
                        </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #999;">الأدوية</p>
                    <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold;">{{ $patient->medications->count() }}</p>
                </div>
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #999;">آخر قياس</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px;">
                        @php
                            $latestVital = $patient->vitalSigns->sortByDesc('measured_at')->first();
                        @endphp
                        {{ $latestVital?->recorded_at?->format('d/m') ?? 'لا يوجد' }}
                    </p>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="card">
        {{ $patients->links() }}
    </div>
@endif
@endsection
