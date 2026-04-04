@extends('layouts.app')

@section('title', 'روابط الأسرة - MyCare')

@section('content')
<div class="header">
    <h1>👪 روابط الأسرة</h1>
    <p>عرض وإدارة الروابط العائلية الخاصة بك</p>
</div>

<div class="card">
    <a href="{{ route('family-links.create') }}" class="btn btn-primary btn-block">+ إضافة رابط عائلي جديد</a>
    <a href="{{ route('family-links.pending') }}" class="btn btn-secondary btn-block mt-2">⏳ الطلبات المعلقة</a>
</div>

@if($familyLinks->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد روابط عائلية</p>
    </div>
@else
    @foreach($familyLinks as $link)
        <div class="card">
            <h3>{{ $link->familyMember->name ?? 'اسم غير معروف' }}</h3>
            <p><strong>العلاقة:</strong> {{ ucfirst($link->relationship) }}</p>
            <p><strong>الحالة:</strong> {{ ucfirst($link->status) }}</p>
            <div class="grid">
                @if($link->status === 'approved')
                    <form action="{{ route('family-links.destroy', $link) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل تريد إزالة الرابط العائلي؟')">إلغاء الرابط</button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach

    <div class="card">
        {{ $familyLinks->links() }}
    </div>
@endif
@endsection