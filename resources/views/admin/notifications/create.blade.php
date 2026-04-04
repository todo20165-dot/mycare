@extends('layouts.app')

@section('title', 'إرسال إشعار جديد - MyCare')

@section('content')
<div class="admin-notification-create">
    <div class="header">
        <h1>➕ إرسال إشعار جديد</h1>
        <p>إرسال إشعار لمستخدم</p>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">← العودة للقائمة</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.notifications.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="user_id">المستلم *</label>
                <select id="user_id" name="user_id" required>
                    <option value="">اختر المستلم</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }}) - {{ $user->getRoleLabel() }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="title">عنوان الإشعار *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="message">نص الإشعار *</label>
                <textarea id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                @error('message')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="type">نوع الإشعار *</label>
                <select id="type" name="type" required>
                    <option value="">اختر النوع</option>
                    <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>معلومات</option>
                    <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>تحذير</option>
                    <option value="danger" {{ old('type') == 'danger' ? 'selected' : '' }}>خطأ</option>
                    <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>نجاح</option>
                </select>
                @error('type')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">إرسال الإشعار</button>
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">إلغاء</a>
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
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border);
    border-radius: 4px;
    font-size: 16px;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
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