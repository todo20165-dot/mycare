@extends('layouts.app')

@section('title', 'تسجيل الدخول - MyCare')

@section('styles')
<style>
    .login-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: calc(100vh - 100px);
    }

    .login-form {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .login-form h2 {
        text-align: center;
        color: var(--primary);
        margin-bottom: 30px;
        font-size: 24px;
    }

    .form-group input {
        padding: 12px;
        font-size: 16px;
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        margin-top: 20px;
    }

    .register-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .register-link a {
        color: var(--primary);
        text-decoration: none;
        font-weight: bold;
    }

    .register-link a:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-form">
        <h2>🔐 تسجيل الدخول</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-login">دخول</button>
        </form>

        <div class="register-link">
            ليس لديك حساب؟ <a href="{{ route('register') }}">سجل الآن</a>
        </div>
    </div>
</div>
@endsection
