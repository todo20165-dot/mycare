@extends('layouts.app')

@section('title', 'إضافة دواء جديد - MyCare')

@section('content')
<div class="admin-medication-create">
    <div class="header">
        <h1>➕ إضافة دواء جديد</h1>
        <p>إضافة دواء جديد لمريض</p>
        <a href="{{ route('admin.medications.index') }}" class="btn btn-secondary">← العودة للقائمة</a>
    </div>

    <div class="card">
        <form action="{{ route('admin.medications.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="user_id">المريض *</label>
                <select id="user_id" name="user_id" required>
                    <option value="">اختر المريض</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('user_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->name }} ({{ $patient->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="name">اسم الدواء *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="dosage">الجرعة *</label>
                <input type="text" id="dosage" name="dosage" value="{{ old('dosage') }}" placeholder="مثال: 500mg" required>
                @error('dosage')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="frequency">التكرار *</label>
                <input type="text" id="frequency" name="frequency" value="{{ old('frequency') }}" placeholder="مثال: مرتين يومياً" required>
                @error('frequency')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="instructions">تعليمات إضافية</label>
                <textarea id="instructions" name="instructions" rows="3">{{ old('instructions') }}</textarea>
                @error('instructions')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    الدواء نشط
                </label>
                @error('is_active')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">إضافة الدواء</button>
                <a href="{{ route('admin.medications.index') }}" class="btn btn-secondary">إلغاء</a>
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
    min-height: 80px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
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