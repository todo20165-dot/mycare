@extends('layouts.app')

@section('title', 'إدارة المستخدمين - MyCare')

@section('content')
<div class="admin-users">
    <div class="header">
        <h1>👥 إدارة المستخدمين</h1>
        <p>إدارة جميع مستخدمي النظام</p>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">➕ إضافة مستخدم جديد</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($users->isEmpty())
        <div class="card text-center">
            <p class="text-muted">لا توجد مستخدمين</p>
        </div>
    @else
        @foreach($users as $user)
            <div class="card">
                <div class="user-info">
                    <h3>{{ $user->name }}</h3>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p class="text-muted">{{ $user->phone ?? 'لا يوجد رقم هاتف' }}</p>
                    <div class="user-meta">
                        <span class="role-badge role-{{ $user->role }}">
                            {{ $user->getRoleLabel() }}
                        </span>
                        <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </div>
                    <p class="text-muted small">تاريخ التسجيل: {{ $user->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="user-actions">
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">تعديل</a>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="pagination">
            {{ $users->links() }}
        </div>
    @endif
</div>

<style>
.admin-users .card {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.user-info {
    flex: 1;
}

.user-meta {
    display: flex;
    gap: 8px;
    margin: 8px 0;
}

.role-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    color: white;
}

.role-patient { background: var(--secondary); }
.role-doctor { background: var(--primary); }
.role-family_member { background: #9c88ff; }
.role-admin { background: var(--danger); }

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

.user-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 14px;
}
</style>
@endsection