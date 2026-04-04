@extends('layouts.app')

@section('title', 'أفراد العائلة - MyCare')

@section('content')
<div class="header">
    <h1>👨‍👩‍👧‍👦 أفراد العائلة</h1>
    <p>الأشخاص المصرح لهم بعرض بياناتك الطبية</p>
</div>

<!-- زر إضافة فرد عائلي -->
<div class="card">
    <a href="{{ route('family.add-member') }}" class="btn btn-primary btn-block">
        ➕ إضافة فرد عائلي جديد
    </a>
</div>

<!-- الطلبات المعلقة -->
@php
    $pendingCount = Auth::user()->familyLinksAsPatient()->where('status', 'pending')->count();
@endphp

@if($pendingCount > 0)
    <div class="card" style="background: #FFF3CD; border-left: 4px solid #FFC107;">
        <p style="margin: 0;">
            ⏳ لديك <strong>{{ $pendingCount }}</strong> طلب(ات) معلقة
        </p>
        <a href="{{ route('family.pending-requests') }}" style="font-size: 12px; color: #856404;">
            عرض الطلبات المعلقة →
        </a>
    </div>
@endif

<!-- قائمة أفراد العائلة -->
@if($familyMembers->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد أفراد عائلة مرتبطين بك</p>
        <p style="font-size: 12px; color: #999;">
            أضف أفراد عائلتك للسماح لهم بعرض بياناتك الطبية
        </p>
    </div>
@else
    @foreach($familyMembers as $link)
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <h3>{{ $link->familyMember->name }}</h3>
                    <p class="text-muted">
                        {{ $link->getRelationshipLabel() }}
                    </p>
                    <p style="font-size: 14px;">📧 {{ $link->familyMember->email }}</p>
                    @if($link->familyMember->phone)
                        <p style="font-size: 14px;">📱 {{ $link->familyMember->phone }}</p>
                    @endif
                    @if($link->notes)
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">
                            <strong>ملاحظات:</strong> {{ $link->notes }}
                        </p>
                    @endif
                    <p style="font-size: 11px; color: #999; margin-top: 10px;">
                        تاريخ الموافقة: {{ $link->approved_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div style="text-align: center; margin-right: 15px;">
                    @if($link->familyMember->profile_image)
                        <img src="{{ $link->familyMember->profile_image }}" alt="{{ $link->familyMember->name }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            👤
                        </div>
                    @endif
                </div>
            </div>

            <!-- الإجراءات -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <form action="{{ route('family.disconnect', $link->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary btn-block" onclick="return confirm('هل تريد إلغاء هذا الربط؟')">
                        ❌ إلغاء
                    </button>
                </form>
                <button class="btn btn-primary btn-block" onclick="alert('سيتم إضافة المزيد من الخيارات قريباً')">
                    ⚙️ خيارات
                </button>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="card">
        {{ $familyMembers->links() }}
    </div>
@endif

<!-- رسائل النجاح والأخطاء -->
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
