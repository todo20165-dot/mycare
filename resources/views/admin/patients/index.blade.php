@extends('layouts.app')

@section('title', 'إدارة المرضى - MyCare')

@section('content')
<div class="admin-patients">
    <div class="header">
        <h1>👤 إدارة المرضى</h1>
        <p>إدارة مرضى النظام</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($patients->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا يوجد مرضى مسجلين في النظام</p>
        </div>
    @else
        @foreach($patients as $patient)
            <div class="card">
                <div class="patient-info">
                    <h3>{{ $patient->name }}</h3>
                    <p class="text-muted">{{ $patient->email }}</p>
                    <p class="text-muted">{{ $patient->phone ?? 'لا يوجد رقم هاتف' }}</p>
                    <div class="patient-stats">
                        <span class="stat">الأطباء المرتبطين: {{ $patient->doctor_patients_count ?? 0 }}</span>
                        <span class="status-badge {{ $patient->is_active ? 'active' : 'inactive' }}">
                            {{ $patient->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ التسجيل: {{ $patient->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="patient-actions">
                    <a href="{{ route('admin.users.edit', $patient->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <a href="#" class="btn btn-info btn-sm">عرض التفاصيل</a>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $patients->links() }}
        </div>
    @endif
</div>

<style>
.patient-info {
    flex: 1;
}

.patient-stats {
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

.patient-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.admin-patients .card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
</style>
@endsection