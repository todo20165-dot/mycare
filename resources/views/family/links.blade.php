@extends('layouts.app')

@section('title', 'الارتباطات العائلية - MyCare')

@section('content')
<div class="header">
    <h1>👨‍👩‍👧‍👦 الارتباطات العائلية</h1>
    <p>إدارة الأفراد العائليين والصلاحيات</p>
</div>

<div class="card">
    <a href="{{ route('family-links.create') }}" class="btn btn-primary btn-block">
        + إضافة فرد عائلي جديد
    </a>
</div>

<div class="card">
    <h3>📥 الطلبات المعلقة</h3>
    @if($pendingRequests->isEmpty())
        <p class="text-muted">لا توجد طلبات معلقة</p>
    @else
        @foreach($pendingRequests as $request)
            <div style="padding: 10px; border-bottom: 1px solid #ddd;">
                <p><strong>{{ $request->requester->name }}</strong> يطلب الوصول</p>
                <p class="text-muted">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                <div class="grid">
                    <form action="{{ route('family-links.approve', $request->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">موافقة</button>
                    </form>
                    <form action="{{ route('family-links.reject', $request->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">رفض</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</div>

<div class="card">
    <h3>✅ الأفراد المصرح لهم</h3>
    @if($approvedLinks->isEmpty())
        <p class="text-muted">لا توجد أفراد مصرح لهم</p>
    @else
        @foreach($approvedLinks as $link)
            <div style="padding: 10px; border-bottom: 1px solid #ddd;">
                <p><strong>{{ $link->familyMember->name }}</strong></p>
                <p class="text-muted">العلاقة: {{ $link->relationship }}</p>
                <p class="text-muted">الصلاحيات: {{ $link->permissions }}</p>
                <form action="{{ route('family-links.revoke', $link->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                        إلغاء الصلاحية
                    </button>
                </form>
            </div>
        @endforeach
    @endif
</div>
@endsection
