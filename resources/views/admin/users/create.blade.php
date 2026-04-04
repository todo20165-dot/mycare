@extends('layouts.app')

@section('title', 'إضافة مستخدم جديد - MyCare')

@section('content')
<div class="admin-user-create">
    <div class="header">
        <h1>➕ إضافة مستخدم جديد</h1>
        <p>إضافة مستخدم جديد إلى النظام</p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">← العودة للقائمة</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">الاسم الكامل *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone">رقم الهاتف</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور *</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">تأكيد كلمة المرور *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <div class="form-group">
                <label for="role">الدور *</label>
                <select id="role" name="role" required>
                    <option value="">اختر الدور</option>
                    <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>مريض</option>
                    <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>طبيب</option>
                    <option value="family_member" {{ old('role') == 'family_member' ? 'selected' : '' }}>فرد عائلة</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مدير</option>
                </select>
                @error('role')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    الحساب نشط
                </label>
                @error('is_active')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border);
    border-radius: 4px;
    font-size: 16px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
}

.error {
    color: var(--danger);
    font-size: 14px;
    margin-top: 5px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}
</style>
@endsection