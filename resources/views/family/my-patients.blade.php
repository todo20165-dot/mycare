@extends('layouts.app')

@section('title', 'الأشخاص المرتبطين - MyCare')

@section('content')
<div class="header">
    <h1>👥 الأشخاص المرتبطين</h1>
    <p>الأشخاص الذين أنت مسؤول عن صحتهم</p>
</div>

<!-- الطلبات المعلقة -->
@php
    $pendingCount = Auth::user()->familyLinksAsMember()->where('status', 'pending')->count();
@endphp

@if($pendingCount > 0)
    <div class="card" style="background: #FFF3CD; border-left: 4px solid #FFC107;">
        <p style="margin: 0;">
            ⏳ لديك <strong>{{ $pendingCount }}</strong> طلب(ات) معلقة
        </p>
        <a href="{{ route('family.my-pending-requests') }}" style="font-size: 12px; color: #856404;">
            عرض الطلبات المعلقة →
        </a>
    </div>
@endif

<!-- قائمة الأشخاص -->
@if($patients->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد أشخاص مرتبطين بك</p>
        <p style="font-size: 12px; color: #999;">
            انتظر حتى يرسل لك شخص ما دعوة للربط
        </p>
    </div>
@else
    @foreach($patients as $link)
        <div class="card" onclick="location.href='{{ route('family.view-patient-data', $link->patient->id) }}';" style="cursor: pointer;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <h3>{{ $link->patient->name }}</h3>
                    <p class="text-muted">
                        {{ $link->getRelationshipLabel() }}
                    </p>
                    <p style="font-size: 14px;">📧 {{ $link->patient->email }}</p>
                    @if($link->patient->phone)
                        <p style="font-size: 14px;">📱 {{ $link->patient->phone }}</p>
                    @endif
                </div>
                <div style="text-align: center; margin-right: 15px;">
                    @if($link->patient->profile_image)
                        <img src="{{ $link->patient->profile_image }}" alt="{{ $link->patient->name }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            👤
                        </div>
                    @endif
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #999;">الأدوية</p>
                    <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold; color: #2196F3;">
                        {{ $link->patient->medications->count() }}
                    </p>
                </div>
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #999;">القياسات</p>
                    <p style="margin: 5px 0 0 0; font-size: 18px; font-weight: bold; color: #4CAF50;">
                        {{ $link->patient->vitalSigns->count() }}
                    </p>
                </div>
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 12px; color: #999;">آخر قياس</p>
                    <p style="margin: 5px 0 0 0; font-size: 14px;">
                        {{ $link->patient->vitalSigns->latest()->first()?->recorded_at?->format('d/m') ?? 'لا يوجد' }}
                    </p>
                </div>
            </div>

            <!-- الإجراءات -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <a href="{{ route('family.view-health-report', $link->patient->id) }}" class="btn btn-primary btn-block" onclick="event.stopPropagation();">
                    📊 التقرير
                </a>
                <form action="{{ route('family.disconnect', $link->id) }}" method="POST" style="display: inline;" onclick="event.stopPropagation();">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary btn-block" onclick="return confirm('هل تريد إلغاء هذا الربط؟')">
                        ❌ إلغاء
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="card">
        {{ $patients->links() }}
    </div>
@endif

<!-- رسائل النجاح -->
@if(session('success'))
    <div class="card" style="background: #D4EDDA; color: #155724;">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        ❌ {{ session('error') }}
    </div>
@endif
@endsection
