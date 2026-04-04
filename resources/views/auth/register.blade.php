@extends('layouts.app')

@section('title', 'التسجيل الجديد - MyCare')

@section('styles')
<style>
    .register-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: calc(100vh - 100px);
    }

    .register-form {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .register-form h2 {
        text-align: center;
        color: var(--primary);
        margin-bottom: 30px;
        font-size: 24px;
    }

    .form-group input,
    .form-group select {
        padding: 12px;
        font-size: 16px;
    }

    .btn-register {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        margin-top: 20px;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .login-link a {
        color: var(--primary);
        text-decoration: none;
        font-weight: bold;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="register-container">
    <div class="register-form">
        <h2>📝 إنشاء حساب جديد</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name">الاسم الكامل</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="phone">رقم الهاتف (اختياري)</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
            </div>

            <div class="form-group">
                <label for="role">نوع الحساب</label>
                <select id="role" name="role" required>
                    <option value="">اختر نوع الحساب</option>
                    <option value="patient">مريض</option>
                    <option value="family_member">فرد عائلة</option>
                    <option value="doctor">طبيب</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">تأكيد كلمة المرور</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary btn-register">إنشاء حساب</button>
        </form>

        <div class="login-link">
            هل لديك حساب بالفعل؟ <a href="{{ route('login') }}">دخول</a>
        </div>
    </div>
</div>
@endsection
