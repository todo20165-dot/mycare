@extends('layouts.app')
@section('title', 'البحث عن أطباء - MyCare')
@section('content')
<div class="header">
    <h1>🔍 البحث عن أطباء متخصصين</h1>
    <p>المرض المختار: <strong>{{ $disease->name }}</strong></p>
    <p class="text-muted">التخصص المطلوب: {{ $disease->specialization }}</p>
</div>

<div class="card">
    <form action="{{ route('patient.search-doctors-by-disease') }}" method="GET" class="search-form">
        <div class="form-group">
            <input type="text" name="q" placeholder="ابحث باسم أو بريد إلكتروني..." value="{{ $query ?? '' }}">
        </div>
        <button type="submit" class="btn btn-primary btn-block">
            بحث
        </button>
    </form>
    
    <a href="{{ route('patient.select-disease') }}" class="btn btn-secondary btn-block" style="margin-top: 10px;">
        ← تغيير المرض
    </a>
</div>

@if($doctors->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لم يتم العثور على أطباء متخصصين في علاج {{ $disease->name }}</p>
    </div>
@else
    <div class="section mt-2">
        <h3>الأطباء المتخصصون</h3>
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
                        @if($doctor->bio)
                            <p style="font-size: 14px; color: #666;">{{ $doctor->bio }}</p>
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
    </div>

    <!-- Pagination -->
    <div class="card">
        {{ $doctors->links() }}
    </div>
@endif
@endsection
