@extends('layouts.app')

@section('title', 'إدارة الإشعارات - MyCare')

@section('content')
<div class="admin-notifications">
    <div class="header">
        <h1>🔔 إدارة الإشعارات</h1>
        <p>إدارة إشعارات النظام</p>
        <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">➕ إرسال إشعار جديد</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا توجد إشعارات</p>
        </div>
    @else
        @foreach($notifications as $notification)
            <div class="card">
                <div class="notification-info">
                    <h3>{{ $notification->title }}</h3>
                    <p>{{ $notification->message }}</p>
                    <p class="text-muted">المستلم: {{ $notification->user->name }} ({{ $notification->user->email }})</p>
                    <div class="notification-meta">
                        <span class="type-badge type-{{ $notification->type }}">
                            @if($notification->type == 'info')
                                معلومات
                            @elseif($notification->type == 'warning')
                                تحذير
                            @elseif($notification->type == 'danger')
                                خطأ
                            @elseif($notification->type == 'success')
                                نجاح
                            @endif
                        </span>
                        <span class="read-status {{ $notification->read_at ? 'read' : 'unread' }}">
                            {{ $notification->read_at ? 'مقروء' : 'غير مقروء' }}
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ الإرسال: {{ $notification->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<style>
.notification-info {
    flex: 1;
}

.notification-meta {
    display: flex;
    gap: 12px;
    margin: 8px 0;
    align-items: center;
}

.type-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    color: white;
}

.type-info { background: var(--primary); }
.type-warning { background: var(--warning); }
.type-danger { background: var(--danger); }
.type-success { background: var(--success); }

.read-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.read-status.read {
    background: var(--success);
    color: white;
}

.read-status.unread {
    background: var(--warning);
    color: white;
}
</style>
@endsection