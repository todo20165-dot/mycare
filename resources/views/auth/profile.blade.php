@extends('layouts.app')

@section('title', 'الملف الشخصي - MyCare')

@section('content')
<div class="profile-page">
    <h2>👤 الملف الشخصي</h2>

    <div class="card mt-2">
        <h3>معلومات الحساب</h3>
        <p><strong>الاسم:</strong> {{ auth()->user()->name }}</p>
        <p><strong>البريد الإلكتروني:</strong> {{ auth()->user()->email }}</p>
        @if(auth()->user()->phone)
            <p><strong>الهاتف:</strong> {{ auth()->user()->phone }}</p>
        @endif
        <p><strong>نوع الحساب:</strong> 
            @if(auth()->user()->isPatient())
                مريض
            @elseif(auth()->user()->isDoctor())
                طبيب
            @elseif(auth()->user()->isAdmin())
                مسؤول
            @else
                فرد عائلة
            @endif
        </p>
        <p><strong>حالة الحساب:</strong> 
            @if(auth()->user()->is_active)
                <span style="color: var(--success);">✓ نشط</span>
            @else
                <span style="color: var(--danger);">✗ معطل</span>
            @endif
        </p>
        @if(auth()->user()->last_login)
            <p><strong>آخر دخول:</strong> {{ auth()->user()->last_login->diffForHumans() }}</p>
        @endif
    </div>

    <div class="card mt-2">
        <h3>تحديث البيانات</h3>
        <form method="POST" action="{{ route('profile') }}">
            @csrf

            <div class="form-group">
                <label for="name">الاسم الكامل</label>
                <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required>
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" required>
            </div>

            <div class="form-group">
                <label for="phone">رقم الهاتف</label>
                <input type="tel" id="phone" name="phone" value="{{ auth()->user()->phone }}">
            </div>

            <div class="form-group">
                <label for="bio">السيرة الذاتية</label>
                <textarea id="bio" name="bio" rows="3">{{ auth()->user()->bio }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">حفظ التغييرات</button>
        </form>
    </div>

    <div class="card mt-2">
        <h3>تغيير كلمة المرور</h3>
        <form method="POST" action="{{ route('change-password') }}">
            @csrf

            <div class="form-group">
                <label for="current_password">كلمة المرور الحالية</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور الجديدة</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">تغيير كلمة المرور</button>
        </form>
    </div>

    <div class="card mt-2">
        <h3>خروج من الحساب</h3>
        <p class="text-muted">انقر الزر أدناه للخروج من حسابك</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger btn-block">تسجيل الخروج</button>
        </form>
    </div>
</div>
@endsection
