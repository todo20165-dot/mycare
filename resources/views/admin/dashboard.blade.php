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
                <div class="number">{{ $totalUsers }}</div>
                <div class="label">إجمالي المستخدمين</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--secondary) 0%, #00b385 100%);">
                <div class="number">{{ $patients }}</div>
                <div class="label">المرضى</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);">
                <div class="number">{{ $doctors }}</div>
                <div class="label">الأطباء</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);">
                <div class="number">{{ $familyMembers }}</div>
                <div class="label">أفراد العائلة</div>
            </div>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>👥 إدارة المستخدمين</h3>
        <div class="grid">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-block">👤 جميع المستخدمين</a>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary btn-block">👨‍⚕️ الأطباء</a>
            <a href="{{ route('admin.patients.index') }}" class="btn btn-info btn-block">👤 المرضى</a>
            <a href="{{ route('admin.family-members.index') }}" class="btn btn-warning btn-block">👪 أفراد العائلة</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-block">➕ إضافة مستخدم جديد</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🔗 إدارة الروابط والعلاقات</h3>
        <div class="grid">
            <a href="{{ route('admin.doctor-patient-links.index') }}" class="btn btn-primary btn-block">🔗 روابط الأطباء والمرضى</a>
            <a href="{{ route('admin.family-links.index') }}" class="btn btn-secondary btn-block">👪 روابط العائلة</a>
        </div>
        <div class="grid mt-1">
            <div class="stat-card">
                <div class="number">{{ $activeLinks }}</div>
                <div class="label">روابط نشطة</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);">
                <div class="number">{{ $pendingRequests }}</div>
                <div class="label">طلبات معلقة</div>
            </div>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>💊 إدارة الأدوية والرعاية</h3>
        <div class="grid">
            <a href="{{ route('admin.medications.index') }}" class="btn btn-primary btn-block">💊 الأدوية</a>
            <a href="{{ route('admin.medications.create') }}" class="btn btn-success btn-block">➕ إضافة دواء</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🧾 إدارة الأمراض</h3>
        <div class="grid">
            <a href="{{ route('admin.diseases.index') }}" class="btn btn-primary btn-block">🧾 عرض الأمراض</a>
            <a href="{{ route('admin.diseases.create') }}" class="btn btn-secondary btn-block">➕ إضافة مرض جديد</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>🔔 الإشعارات والتواصل</h3>
        <div class="grid">
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-primary btn-block">🔔 الإشعارات</a>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-success btn-block">➕ إرسال إشعار</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>📊 التقارير والإحصائيات</h3>
        <div class="grid">
            <a href="{{ route('admin.statistics') }}" class="btn btn-primary btn-block">📊 الإحصائيات الشاملة</a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-block">📋 التقارير</a>
        </div>
    </div>

    <div class="section mt-2 mb-2">
        <h3>⚙️ إعدادات النظام</h3>
        <div class="grid">
            <a href="{{ route('admin.settings') }}" class="btn btn-primary btn-block">⚙️ الإعدادات</a>
            <a href="{{ route('admin.tools') }}" class="btn btn-warning btn-block">🛠️ أدوات النظام</a>
        </div>
    </div>
</div>
@endsection
