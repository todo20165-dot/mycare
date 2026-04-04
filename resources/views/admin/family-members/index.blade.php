@extends('layouts.app')

@section('title', 'إدارة أفراد العائلة - MyCare')

@section('content')
<div class="admin-family-members">
    <div class="header">
        <h1>👪 إدارة أفراد العائلة</h1>
        <p>إدارة أفراد العائلة في النظام</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($familyMembers->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا يوجد أفراد عائلة مسجلين في النظام</p>
        </div>
    @else
        @foreach($familyMembers as $member)
            <div class="card">
                <div class="member-info">
                    <h3>{{ $member->name }}</h3>
                    <p class="text-muted">{{ $member->email }}</p>
                    <p class="text-muted">{{ $member->phone ?? 'لا يوجد رقم هاتف' }}</p>
                    <div class="member-stats">
                        <span class="stat">الروابط العائلية: {{ $member->family_links_count ?? 0 }}</span>
                        <span class="status-badge {{ $member->is_active ? 'active' : 'inactive' }}">
                            {{ $member->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ التسجيل: {{ $member->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="member-actions">
                    <a href="{{ route('admin.users.edit', $member->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <a href="#" class="btn btn-info btn-sm">عرض الروابط</a>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $familyMembers->links() }}
        </div>
    @endif
</div>

<style>
.member-info {
    flex: 1;
}

.member-stats {
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

.member-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}

.admin-family-members .card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}
</style>
@endsection