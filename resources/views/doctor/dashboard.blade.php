@extends('layouts.app')

@section('title', 'لوحة تحكم الطبيب - MyCare')

@section('content')
<div class="header">
    <h1>👨‍⚕️ لوحة تحكم الطبيب</h1>
    <p>إدارة المرضى والوصفات الطبية</p>
</div>

<div class="card">
    <h3>📊 الإحصائيات</h3>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $totalPatients ?? 0 }}</div>
            <div class="label">عدد المرضى</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $activePrescriptions ?? 0 }}</div>
            <div class="label">وصفات نشطة</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>🔔 الإشعارات</h3>
    <p>الرسائل غير المقروءة: <strong id="unread-count">{{ $unreadCount ?? 0 }}</strong></p>
    <div id="notification-message" style="display: none;" class="alert alert-success"></div>
    @if(!empty($unreadNotifications) && $unreadNotifications->count() > 0)
        <div id="unread-list">
            <ul>
                @foreach($unreadNotifications as $notification)
                    <li style="margin-bottom: 8px;">
                        <strong>{{ $notification->title }}</strong> - {{ Str::limit($notification->message, 70) }}
                        <br>
                        <span style="color: #777; font-size: 12px;">{{ $notification->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
            <button id="mark-all-read" class="btn btn-success btn-block">تحديد الكل كمقروء</button>
        </div>
    @else
        <p class="text-muted">لا توجد إشعارات جديدة</p>
    @endif
    <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-block">عرض جميع الإشعارات</a>
</div>

<div class="card">
    <h3>👥 المرضى</h3>
    <a href="{{ route('doctor.patients.index') }}" class="btn btn-primary btn-block">
        عرض جميع المرضى
    </a>
</div>

<div class="card">
    <h3>💊 الوصفات الطبية</h3>
    <a href="{{ route('doctor.prescriptions.index') }}" class="btn btn-primary btn-block">
        إدارة الوصفات
    </a>
</div>

<div class="card">
    <h3>📋 الملاحظات الطبية</h3>
    <a href="{{ route('doctor.notes.index') }}" class="btn btn-primary btn-block">
        عرض الملاحظات
    </a>
</div>

<div class="card">
    <h3>📊 التقارير</h3>
    <a href="{{ route('reports.index') }}" class="btn btn-primary btn-block">
        إدارة التقارير
    </a>
</div>

<div class="card">
    <h3>� طلبات الربط المعلقة</h3>
    <a href="{{ route('doctor.pending-requests') }}" class="btn btn-primary btn-block">
        عرض الطلبات المعلقة
    </a>
</div>

<div class="card">
    <h3>�💬 الرسائل</h3>
    <a href="{{ route('doctor.messages.index') }}" class="btn btn-primary btn-block">
        الرسائل ({{ $unreadMessages ?? 0 }})
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch('{{ route("notifications.mark-all-as-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const messageDiv = document.getElementById('notification-message');
                    messageDiv.textContent = 'تم تحديد جميع الإشعارات كمقروءة بنجاح';
                    messageDiv.className = 'alert alert-success';
                    messageDiv.style.display = 'block';
                    document.getElementById('unread-count').textContent = '0';
                    const unreadList = document.getElementById('unread-list');
                    if (unreadList) unreadList.style.display = 'none';
                    setTimeout(() => {
                        messageDiv.style.display = 'none';
                    }, 3000);
                }
            })
            .catch(error => {
                const messageDiv = document.getElementById('notification-message');
                messageDiv.textContent = 'حدث خطأ أثناء تحديد الإشعارات كمقروءة';
                messageDiv.className = 'alert alert-danger';
                messageDiv.style.display = 'block';
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 3000);
            });
        });
    }
});
</script>
@endsection
