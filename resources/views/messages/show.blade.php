@extends('layouts.app')

@section('title', 'المحادثة - MyCare')

@section('content')
<div class="header">
    <h1>{{ $conversation->other_user->name }}</h1>
    <p>المحادثة</p>
</div>

<div class="card" style="height: 400px; overflow-y: auto; margin-bottom: 20px;">
    @foreach($messages as $message)
        <div style="margin-bottom: 15px; text-align: {{ $message->sender_id === auth()->id() ? 'left' : 'right' }};">
            <div style="
                background: {{ $message->sender_id === auth()->id() ? '#E3F2FD' : '#F5F5F5' }};
                padding: 10px 15px;
                border-radius: 10px;
                display: inline-block;
                max-width: 80%;
                word-wrap: break-word;
            ">
                <p style="margin: 0; word-wrap: break-word;">{{ $message->content }}</p>
            </div>
            <p class="text-muted" style="font-size: 12px; margin-top: 5px;">
                {{ $message->created_at->format('H:i') }}
            </p>
        </div>
    @endforeach
</div>

<form action="{{ route('messages.send', $conversation->id) }}" method="POST">
    @csrf
    
    <div class="card">
        <div style="display: flex; gap: 10px;">
            <textarea name="content" id="content" placeholder="اكتب رسالتك..." rows="3" required style="flex: 1;"></textarea>
            <button type="submit" class="btn btn-primary" style="align-self: flex-end;">
                إرسال
            </button>
        </div>
    </div>
</form>
@endsection
