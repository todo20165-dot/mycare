@extends('layouts.app')

@section('title', 'الإحصائيات - MyCare')

@section('content')
<div class="header">
    <h1>📊 الإحصائيات الشاملة</h1>
    <p>تحليل شامل لنشاط النظام</p>
</div>

<div class="card">
    <h3>👥 إحصائيات المستخدمين</h3>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $stats['total_users'] ?? 0 }}</div>
            <div class="label">إجمالي المستخدمين</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $stats['total_patients'] ?? 0 }}</div>
            <div class="label">عدد المرضى</div>
        </div>
    </div>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $stats['total_doctors'] ?? 0 }}</div>
            <div class="label">عدد الأطباء</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $stats['total_admins'] ?? 0 }}</div>
            <div class="label">عدد المسؤولين</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>💊 إحصائيات الأدوية</h3>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $stats['total_medications'] ?? 0 }}</div>
            <div class="label">إجمالي الأدوية</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $stats['total_doses'] ?? 0 }}</div>
            <div class="label">إجمالي الجرعات</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>❤️ إحصائيات العلامات الحيوية</h3>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $stats['total_vital_signs'] ?? 0 }}</div>
            <div class="label">إجمالي القياسات</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $stats['abnormal_readings'] ?? 0 }}</div>
            <div class="label">قراءات غير طبيعية</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>📋 إحصائيات التقارير</h3>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $stats['total_reports'] ?? 0 }}</div>
            <div class="label">إجمالي التقارير</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $stats['generated_today'] ?? 0 }}</div>
            <div class="label">تقارير اليوم</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>🔔 إحصائيات الإشعارات</h3>
    <div class="grid">
        <div class="stat-card">
            <div class="number">{{ $stats['total_notifications'] ?? 0 }}</div>
            <div class="label">إجمالي الإشعارات</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $stats['unread_notifications'] ?? 0 }}</div>
            <div class="label">إشعارات غير مقروءة</div>
        </div>
    </div>
</div>

<div class="card">
    <h3>📈 معدل الالتزام بالأدوية</h3>
    <p class="text-muted">متوسط معدل الالتزام: <strong>{{ $stats['average_compliance'] ?? 0 }}%</strong></p>
</div>
@endsection
