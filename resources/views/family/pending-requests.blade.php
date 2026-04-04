@extends('layouts.app')

@section('title', 'الطلبات المعلقة - MyCare')

@section('content')
<div class="header">
    <h1>⏳ الطلبات المعلقة</h1>
    <p>الأشخاص الذين ينتظرون موافقتك</p>
</div>

@if($requests->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد طلبات معلقة</p>
    </div>
@else
    @foreach($requests as $request)
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <h3>{{ $request->familyMember->name }}</h3>
                    <p class="text-muted">
                        {{ $request->getRelationshipLabel() }}
                    </p>
                    <p style="font-size: 14px;">📧 {{ $request->familyMember->email }}</p>
                    @if($request->familyMember->phone)
                        <p style="font-size: 14px;">📱 {{ $request->familyMember->phone }}</p>
                    @endif
                    @if($request->notes)
                        <p style="font-size: 12px; color: #666; margin-top: 10px;">
                            <strong>ملاحظات:</strong> {{ $request->notes }}
                        </p>
                    @endif
                    <p style="font-size: 11px; color: #999; margin-top: 10px;">
                        تاريخ الطلب: {{ $request->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div style="text-align: center; margin-right: 15px;">
                    @if($request->familyMember->profile_image)
                        <img src="{{ $request->familyMember->profile_image }}" alt="{{ $request->familyMember->name }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 60px; height: 60px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            👤
                        </div>
                    @endif
                </div>
            </div>

            <!-- الإجراءات -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                <form action="{{ route('family.approve', $request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-block" onclick="return confirm('هل تريد قبول هذا الطلب؟')">
                        ✅ قبول
                    </button>
                </form>

                <form action="{{ route('family.reject', $request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('هل تريد رفض هذا الطلب؟')">
                        ❌ رفض
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="card">
        {{ $requests->links() }}
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
