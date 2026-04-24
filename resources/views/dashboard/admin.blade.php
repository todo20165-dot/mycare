@extends('layouts.app')

@section('title', 'لوحة تحكم الإدارة - MyCare')

@section('content')
<div class="dashboard-admin">
    <div class="welcome-section">
        <h2>👋 مرحباً {{ auth()->user()->name }}</h2>
        <p>إدارة النظام والمستخدمين</p>
    </div>

    <div class="quick-stats mt-2 mb-2">
        <div class="grid">
            <div class="stat-card">
                <div class="number">--</div>
                <div class="label">إجمالي المستخدمين</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary) 0%, #00b385 100%);">
                <div class="number">--</div>
                <div class="label">الأطباء</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);">
                <div class="number">--</div>
                <div class="label">المرضى</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);">
                <div class="number">--</div>
                <div class="label">أفراد العائلة</div>
            </div>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>👥 إدارة المستخدمين</h3>
        <div class="grid">
            <a href="#" class="btn btn-primary btn-block">👨‍⚕️ إدارة الأطباء</a>
            <a href="#" class="btn btn-secondary btn-block">👤 إدارة المرضى</a>
            <a href="#" class="btn btn-info btn-block">👪 إدارة أفراد العائلة</a>
            <a href="#" class="btn btn-warning btn-block">📊 إحصائيات المستخدمين</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🧾 إدارة الأمراض</h3>
        <div class="grid">
            <a href="{{ route('admin.diseases.index') }}" class="btn btn-primary btn-block">عرض الأمراض</a>
            <a href="{{ route('admin.diseases.create') }}" class="btn btn-secondary btn-block">➕ إضافة مرض جديد</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>📋 إدارة النظام</h3>
        <div class="grid">
            <a href="#" class="btn btn-info btn-block">🔔 إدارة الإشعارات</a>
            <a href="#" class="btn btn-warning btn-block">⚙️ إعدادات النظام</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🔗 روابط المرضى والأطباء</h3>
        <div class="grid">
            <a href="#" class="btn btn-primary btn-block">🔗 مراجعة طلبات الربط</a>
            <a href="#" class="btn btn-secondary btn-block">📋 روابط نشطة</a>
            <a href="#" class="btn btn-info btn-block">📊 إحصائيات الروابط</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>📈 التقارير والإحصائيات</h3>
        <div class="grid">
            <a href="#" class="btn btn-primary btn-block">📊 تقرير الامتثال</a>
            <a href="#" class="btn btn-secondary btn-block">❤️ تقرير العلامات الحيوية</a>
            <a href="#" class="btn btn-info btn-block">📈 إحصائيات عامة</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🛠️ أدوات النظام</h3>
        <div class="grid">
            <a href="#" class="btn btn-warning btn-block">🔄 تحديث قاعدة البيانات</a>
            <a href="#" class="btn btn-danger btn-block">🗑️ تنظيف البيانات</a>
            <a href="#" class="btn btn-info btn-block">📋 سجلات النظام</a>
        </div>
    </div>
</div>
@endsection