@extends('layouts.app')

@section('title', 'طلبات روابط عائلية معلقة - MyCare')

@section('content')
<div class="header">
    <h1>⏳ طلبات روابط عائلية معلقة</h1>
    <p>يمكنك متابعة حالة طلباتك أو إلغاؤها</p>
</div>

@if($pendingLinks->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد طلبات معلقة</p>
    </div>
@else
    @foreach($pendingLinks as $link)
        <div class="card">
            <h3>{{ $link->familyMember->name ?? 'اسم غير معروف' }}</h3>
            <p><strong>العلاقة:</strong> {{ ucfirst($link->relationship) }}</p>
            <p><strong>التاريخ:</strong> {{ $link->created_at->format('d/m/Y H:i') }}</p>
            <div class="grid">
                <form action="{{ route('family-links.destroy', $link) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل تريد إلغاء هذا الطلب؟')">إلغاء الطلب</button>
                </form>
            </div>
        </div>
    @endforeach

    <div class="card">
        {{ $pendingLinks->links() }}
    </div>
@endif
@endsection