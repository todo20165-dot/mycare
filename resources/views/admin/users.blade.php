@extends('layouts.app')

@section('title', 'إدارة المستخدمين - MyCare')

@section('content')
<div class="header">
    <h1>👥 إدارة المستخدمين</h1>
    <p>إدارة جميع مستخدمي النظام</p>
</div>

<div class="card">
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-block">
        + إضافة مستخدم جديد
    </a>
</div>

@if($users->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد مستخدمين</p>
    </div>
@else
    @foreach($users as $user)
        <div class="card">
            <h3>{{ $user->name }}</h3>
            <p class="text-muted">{{ $user->email }}</p>
            <p class="text-muted">
                <strong>الدور:</strong>
                <span style="background: #e0e0e0; padding: 4px 8px; border-radius: 4px;">
                    {{ $user->role }}
                </span>
            </p>
            <p class="text-muted">
                <strong>تاريخ التسجيل:</strong> {{ $user->created_at->format('d/m/Y') }}
            </p>
            
            <div class="grid">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                    تعديل
                </a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                        حذف
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endif
@endsection
