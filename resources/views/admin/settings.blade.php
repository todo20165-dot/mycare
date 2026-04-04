@extends('layouts.app')

@section('title', 'إعدادات النظام - MyCare')

@section('content')
<div class="admin-settings">
    <div class="header">
        <h1>⚙️ إعدادات النظام</h1>
        <p>إدارة إعدادات النظام العامة</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h3>🔧 إعدادات عامة</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="app_name">اسم التطبيق</label>
                <input type="text" id="app_name" name="app_name" value="{{ old('app_name', session('admin_settings.app_name', config('app.name'))) }}">
            </div>

            <div class="form-group">
                <label for="app_url">رابط التطبيق</label>
                <input type="url" id="app_url" name="app_url" value="{{ old('app_url', session('admin_settings.app_url', config('app.url'))) }}">
            </div>

            <div class="form-group">
                <label for="timezone">المنطقة الزمنية</label>
                <select id="timezone" name="timezone">
                    <option value="UTC" {{ old('timezone', session('admin_settings.timezone', config('app.timezone'))) == 'UTC' ? 'selected' : '' }}>UTC</option>
                    <option value="Asia/Riyadh" {{ old('timezone', session('admin_settings.timezone', config('app.timezone'))) == 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh</option>
                    <option value="Asia/Kuwait" {{ old('timezone', session('admin_settings.timezone', config('app.timezone'))) == 'Asia/Kuwait' ? 'selected' : '' }}>Asia/Kuwait</option>
                    <option value="Asia/Dubai" {{ old('timezone', session('admin_settings.timezone', config('app.timezone'))) == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                </select>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="debug_mode" value="1" {{ old('debug_mode', session('admin_settings.debug_mode', config('app.debug'))) ? 'checked' : '' }}>
                    وضع المطور (Debug Mode)
                </label>
            </div>

            <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
        </form>
    </div>

    <div class="card">
        <h3>📧 إعدادات البريد الإلكتروني</h3>
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="mail_driver">نوع البريد</label>
                <select id="mail_driver" name="mail_driver">
                    <option value="smtp" {{ old('mail_driver', session('admin_settings.mail_driver', config('mail.mailers.smtp.transport'))) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="mailgun" {{ old('mail_driver', session('admin_settings.mail_driver', config('mail.mailers.mailgun.transport'))) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                </select>
            </div>

            <div class="form-group">
                <label for="mail_host">خادم البريد</label>
                <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', session('admin_settings.mail_host', config('mail.mailers.smtp.host'))) }}">
            </div>

            <div class="form-group">
                <label for="mail_port">منفذ البريد</label>
                <input type="number" id="mail_port" name="mail_port" value="{{ old('mail_port', session('admin_settings.mail_port', config('mail.mailers.smtp.port'))) }}">
            </div>

            <div class="form-group">
                <label for="mail_username">اسم المستخدم</label>
                <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', session('admin_settings.mail_username', config('mail.mailers.smtp.username'))) }}">
            </div>

            <div class="form-group">
                <label for="mail_password">كلمة المرور</label>
                <input type="password" id="mail_password" name="mail_password" value="{{ old('mail_password', session('admin_settings.mail_password', config('mail.mailers.smtp.password'))) }}">
            </div>

            <div class="form-group">
                <label for="mail_from_address">البريد المرسل منه</label>
                <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', session('admin_settings.mail_from_address', config('mail.from.address'))) }}">
            </div>

            <button type="submit" class="btn btn-primary">حفظ إعدادات البريد</button>
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

.card {
    margin-bottom: 20px;
}

.card h3 {
    margin-bottom: 20px;
    color: var(--primary);
}
</style>
@endsection