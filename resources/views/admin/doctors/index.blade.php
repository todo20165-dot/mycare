@extends('layouts.app')

@section('title', 'إدارة الأطباء - MyCare')

@section('content')
<div class="admin-doctors">
    <div class="header">
        <h1>👨‍⚕️ إدارة الأطباء</h1>
        <p>إدارة أطباء النظام</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($doctors->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا يوجد أطباء مسجلين في النظام</p>
        </div>
    @else
        @foreach($doctors as $doctor)
            <div class="card">
                <div class="doctor-info">
                    <h3>{{ $doctor->name }}</h3>
                    <p class="text-muted">{{ $doctor->email }}</p>
                    <p class="text-muted">{{ $doctor->phone ?? 'لا يوجد رقم هاتف' }}</p>
                    <div class="doctor-stats">
                        <span class="stat">المرضى المرتبطين: {{ $doctor->doctor_patients_count ?? 0 }}</span>
                        <span class="status-badge {{ $doctor->is_active ? 'active' : 'inactive' }}">
                            {{ $doctor->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ التسجيل: {{ $doctor->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="doctor-actions">
                    <a href="{{ route('admin.users.edit', $doctor->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <a href="#" class="btn btn-info btn-sm">عرض المرضى</a>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $doctors->links() }}
        </div>
    @endif
</div>

<style>
.doctor-info {
    flex: 1;
}

.doctor-stats {
    display: flex;
    gap: 12px;
    margin: 8px 0;
    align-items: center;
}

.stat {
    background: var(--light-bg);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 14px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-badge.active {
    background: var(--success);
    color: white;
}

.status-badge.inactive {
    background: var(--danger);
    color: white;
}

.doctor-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.admin-doctors .card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
</style>
@endsection