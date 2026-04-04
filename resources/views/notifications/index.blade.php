@extends('layouts.app')

@section('title', 'الإشعارات - MyCare')

@section('content')
<div class="notifications-list">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>🔔 الإشعارات</h2>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.mark-all-as-read') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary" style="padding: 8px 12px; font-size: 12px;">تحديد الكل كمقروء</button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="card">
            <p class="text-center text-muted">لا توجد إشعارات</p>
        </div>
    @else
        @foreach($notifications as $notification)
            <div class="card" style="background: {{ $notification->read_at ? 'white' : '#f0f7ff' }};">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h3>{{ $notification->title }}</h3>
                        <p>{{ $notification->message }}</p>
                        <p class="text-muted">{{ $notification->created_at->diffForHumans() }}</p>
                        @if(!$notification->read_at)
                            <span style="background: var(--primary); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">جديد</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('notifications.destroy', $notification) }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="padding: 6px 10px; font-size: 12px;">حذف</button>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="mt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
