@extends('layouts.app')

@section('title', 'الرسائل - MyCare')

@section('content')
<div class="header">
    <h1>💬 الرسائل</h1>
    <p>محادثاتك مع الطاقم الطبي</p>
</div>

<div class="card">
    <a href="{{ route('messages.create') }}" class="btn btn-primary btn-block">
        + رسالة جديدة
    </a>
</div>

@if($conversations->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد رسائل</p>
    </div>
@else
    @foreach($conversations as $conversation)
        <div class="card" onclick="location.href='{{ route('messages.show', $conversation->id) }}';" style="cursor: pointer;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3>{{ $conversation->other_user->name }}</h3>
                    <p class="text-muted">{{ Str::limit($conversation->last_message->content ?? 'لا توجد رسائل', 50) }}</p>
                </div>
                @if($conversation->unread_count > 0)
                    <span style="background: #4CAF50; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                        {{ $conversation->unread_count }}
                    </span>
                @endif
            </div>
            <p class="text-muted" style="font-size: 12px; margin-top: 8px;">
                {{ $conversation->last_message->created_at->format('d/m/Y H:i') ?? '' }}
            </p>
        </div>
    @endforeach
@endif
@endsection
