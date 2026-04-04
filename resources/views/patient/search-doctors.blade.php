@extends('layouts.app')

@section('title', 'البحث عن أطباء - MyCare')

@section('content')
<div class="header">
    <h1>🔍 البحث عن أطباء</h1>
    <p>ابحث عن الطبيب المناسب لك</p>
</div>

<div class="card">
    <form action="{{ route('patient.search-doctors') }}" method="GET" class="search-form">
        <div class="form-group">
            <input type="text" name="q" placeholder="ابحث باسم أو بريد إلكتروني..." value="{{ $query ?? '' }}">
        </div>

        <div class="form-group">
            <select name="specialization">
                <option value="">-- جميع التخصصات --</option>
                <option value="general" {{ ($specialization ?? '') === 'general' ? 'selected' : '' }}>طب عام</option>
                <option value="cardiology" {{ ($specialization ?? '') === 'cardiology' ? 'selected' : '' }}>أمراض القلب</option>
                <option value="neurology" {{ ($specialization ?? '') === 'neurology' ? 'selected' : '' }}>الأعصاب</option>
                <option value="endocrinology" {{ ($specialization ?? '') === 'endocrinology' ? 'selected' : '' }}>الغدد الصماء</option>
                <option value="orthopedics" {{ ($specialization ?? '') === 'orthopedics' ? 'selected' : '' }}>العظام</option>
                <option value="dermatology" {{ ($specialization ?? '') === 'dermatology' ? 'selected' : '' }}>الجلدية</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            بحث
        </button>
    </form>
</div>

@if($doctors->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لم يتم العثور على أطباء</p>
    </div>
@else
    @foreach($doctors as $doctor)
        <div class="card" onclick="location.href='{{ route('patient.doctor-profile', $doctor->id) }}';" style="cursor: pointer;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3>{{ $doctor->name }}</h3>
                    <p class="text-muted">{{ $doctor->specialization ?? 'طب عام' }}</p>
                    <p style="font-size: 14px;">📧 {{ $doctor->email }}</p>
                    @if($doctor->phone)
                        <p style="font-size: 14px;">📱 {{ $doctor->phone }}</p>
                    @endif
                </div>
                <div style="text-align: center;">
                    @if($doctor->profile_image)
                        <img src="{{ $doctor->profile_image }}" alt="{{ $doctor->name }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            👨‍⚕️
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="card">
        {{ $doctors->links() }}
    </div>
@endif
@endsection
