@extends('layouts.app')

@section('title', 'مرحباً - MyCare')

@section('content')
<div class="welcome-page">
    <div style="text-align: center; padding: 40px 20px;">
        <div style="font-size: 80px; margin-bottom: 20px;">💊</div>
        <h1 style="color: var(--primary); font-size: 32px; margin-bottom: 10px;">MyCare</h1>
        <p style="font-size: 18px; color: var(--text-light); margin-bottom: 30px;">
            إدارة الرعاية الصحية المنزلية بسهولة
        </p>

        <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h2 style="font-size: 20px; margin-bottom: 15px;">المميزات الرئيسية</h2>
            <ul style="text-align: right; list-style: none;">
                <li style="margin-bottom: 10px;">✓ إدارة الأدوية والجرعات</li>
                <li style="margin-bottom: 10px;">✓ تتبع العلامات الحيوية</li>
                <li style="margin-bottom: 10px;">✓ الارتباط العائلي الآمن</li>
                <li style="margin-bottom: 10px;">✓ تقارير صحية شاملة</li>
                <li style="margin-bottom: 10px;">✓ العمل بدون اتصال بالإنترنت</li>
                <li style="margin-bottom: 10px;">✓ تطبيق جوال قابل للتثبيت</li>
            </ul>
        </div>

        <a href="{{ route('register') }}" class="btn btn-primary btn-block" style="padding: 14px; font-size: 16px; margin-bottom: 10px;">
            📝 إنشاء حساب جديد
        </a>

        <p style="margin-bottom: 10px;">أو</p>

        <a href="{{ route('login') }}" class="btn btn-secondary btn-block" style="padding: 14px; font-size: 16px;">
            🔐 تسجيل الدخول
        </a>
    </div>

    <div style="padding: 20px; background: var(--light-bg); margin-top: 30px; border-radius: 8px;">
        <h3 style="color: var(--primary); margin-bottom: 15px;">كيفية البدء؟</h3>
        <div style="text-align: right;">
            <p style="margin-bottom: 10px;"><strong>1. إنشاء حساب</strong> - سجل بسهولة باستخدام بريدك الإلكتروني</p>
            <p style="margin-bottom: 10px;"><strong>2. إضافة الأدوية</strong> - أضف أدويتك والجرعات المقررة</p>
            <p style="margin-bottom: 10px;"><strong>3. تتبع الصحة</strong> - سجل العلامات الحيوية بانتظام</p>
            <p style="margin-bottom: 10px;"><strong>4. الحصول على التقارير</strong> - اعرض تقاريرك الصحية الشاملة</p>
            <p><strong>5. مشاركة البيانات</strong> - شارك بيانات صحتك مع أسرتك وطبيبك</p>
        </div>
    </div>

    <div style="padding: 20px; margin-top: 30px; text-align: center; color: var(--text-light); font-size: 12px;">
        <p>MyCare - تطبيق متكامل لإدارة الرعاية الصحية المنزلية</p>
        <p>© 2026 جميع الحقوق محفوظة</p>
    </div>
</div>
@endsection
