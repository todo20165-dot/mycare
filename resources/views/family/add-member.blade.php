@extends('layouts.app')

@section('title', 'إضافة فرد عائلي - MyCare')

@section('content')
<div class="header">
    <h1>➕ إضافة فرد عائلي جديد</h1>
    <p>السماح لفرد عائلي بعرض بياناتك الطبية</p>
</div>

<div class="card">
    <form action="{{ route('family.invite') }}" method="POST">
        @csrf

        <!-- البريد الإلكتروني -->
        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" name="email" id="email" placeholder="example@email.com" required>
            @error('email')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- نوع العلاقة -->
        <div class="form-group">
            <label for="relationship">نوع العلاقة</label>
            <select name="relationship" id="relationship" required>
                <option value="">-- اختر نوع العلاقة --</option>
                <option value="parent">👨‍👩 الوالد/الوالدة</option>
                <option value="child">👶 الابن/الابنة</option>
                <option value="spouse">💑 الزوج/الزوجة</option>
                <option value="sibling">👫 الأخ/الأخت</option>
                <option value="other">👤 آخر</option>
            </select>
            @error('relationship')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- الملاحظات -->
        <div class="form-group">
            <label for="notes">ملاحظات (اختياري)</label>
            <textarea name="notes" id="notes" placeholder="أضف أي ملاحظات إضافية..." rows="3" maxlength="500"></textarea>
            @error('notes')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <!-- الأزرار -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <a href="{{ route('family.members-list') }}" class="btn btn-secondary btn-block">
                ← رجوع
            </a>
            <button type="submit" class="btn btn-primary btn-block">
                ✓ إرسال الدعوة
            </button>
        </div>
    </form>
</div>

<!-- معلومات مهمة -->
<div class="card" style="background: #E7F3FF; border-left: 4px solid #2196F3;">
    <h3 style="margin-top: 0; color: #1976D2;">ℹ️ معلومات مهمة</h3>
    <ul style="margin: 10px 0; padding-right: 20px; color: #1565C0;">
        <li>يجب أن يكون لفرد العائلة حساب في التطبيق</li>
        <li>سيتم إرسال دعوة له للموافقة على الربط</li>
        <li>بعد الموافقة، سيتمكن من عرض بياناتك الطبية</li>
        <li>يمكنك إلغاء الربط في أي وقت</li>
    </ul>
</div>

<!-- رسائل الأخطاء -->
@if($errors->any())
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        <strong>حدثت أخطاء:</strong>
        <ul style="margin: 10px 0; padding-right: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        ❌ {{ session('error') }}
    </div>
@endif
@endsection
