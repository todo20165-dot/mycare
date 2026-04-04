@extends('layouts.app')

@section('title', 'روابط الأطباء والمرضى - MyCare')

@section('content')
<div class="admin-doctor-patient-links">
    <div class="header">
        <h1>🔗 روابط الأطباء والمرضى</h1>
        <p>إدارة طلبات الربط بين الأطباء والمرضى</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($links->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا توجد روابط</p>
        </div>
    @else
        @foreach($links as $link)
            <div class="card">
                <div class="link-info">
                    <h3>{{ $link->patient->name }} ←→ {{ $link->doctor->name }}</h3>
                    <p class="text-muted">الطبيب: {{ $link->doctor->email }}</p>
                    <p class="text-muted">المريض: {{ $link->patient->email }}</p>
                    <div class="link-status">
                        <span class="status-badge status-{{ $link->status }}">
                            @if($link->status == 'pending')
                                معلق
                            @elseif($link->status == 'active')
                                نشط
                            @elseif($link->status == 'rejected')
                                مرفوض
                            @endif
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ الطلب: {{ $link->created_at->format('d/m/Y H:i') }}</p>
                    @if($link->status == 'pending')
                        <p class="text-muted small">الرسالة: {{ $link->message ?? 'لا توجد رسالة' }}</p>
                    @endif
                </div>

                @if($link->status == 'pending')
                <div class="link-actions">
                    <form action="{{ route('admin.doctor-patient-links.approve', $link->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">قبول</button>
                    </form>
                    <form action="{{ route('admin.doctor-patient-links.reject', $link->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">رفض</button>
                    </form>
                </div>
                @endif
            </div>
        @endforeach

        <div class="pagination">
            {{ $links->links() }}
        </div>
    @endif
</div>

<style>
.link-info {
    flex: 1;
}

.link-status {
    margin: 8px 0;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    color: white;
}

.status-pending { background: var(--warning); }
.status-active { background: var(--success); }
.status-rejected { background: var(--danger); }

.link-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.admin-doctor-patient-links .card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
</style>
@endsection