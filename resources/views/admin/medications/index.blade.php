@extends('layouts.app')

@section('title', 'إدارة الأدوية - MyCare')

@section('content')
<div class="admin-medications">
    <div class="header">
        <h1>💊 إدارة الأدوية</h1>
        <p>إدارة أدوية المرضى</p>
        <a href="{{ route('admin.medications.create') }}" class="btn btn-primary">➕ إضافة دواء جديد</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($medications->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا توجد أدوية مسجلة</p>
        </div>
    @else
        @foreach($medications as $medication)
            <div class="card">
                <div class="medication-info">
                    <h3>{{ $medication->name }}</h3>
                    <p class="text-muted">المريض: {{ $medication->user->name }}</p>
                    <p class="text-muted">الجرعة: {{ $medication->dosage }}</p>
                    <p class="text-muted">التكرار: {{ $medication->frequency }}</p>
                    @if($medication->instructions)
                        <p class="text-muted">تعليمات: {{ $medication->instructions }}</p>
                    @endif
                    <div class="medication-status">
                        <span class="status-badge {{ $medication->is_active ? 'active' : 'inactive' }}">
                            {{ $medication->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ الإضافة: {{ $medication->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="medication-actions">
                    <a href="{{ route('admin.medications.edit', $medication->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <form action="{{ route('admin.medications.destroy', $medication->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا الدواء؟')">
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $medications->links() }}
        </div>
    @endif
</div>

<style>
.medication-info {
    flex: 1;
}

.medication-status {
    margin: 8px 0;
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

.medication-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.admin-medications .card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
</style>
@endsection