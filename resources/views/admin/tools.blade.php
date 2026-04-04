@extends('layouts.app')

@section('title', 'أدوات النظام - MyCare')

@section('content')
<div class="admin-tools">
    <div class="header">
        <h1>🛠️ أدوات النظام</h1>
        <p>أدوات مساعدة لإدارة النظام</p>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← العودة للوحة التحكم</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <h3>🧹 تنظيف النظام</h3>
        <p class="text-muted">تنظيف الذاكرة المؤقتة والملفات المؤقتة</p>

        <form action="{{ route('admin.tools.clear-cache') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-warning" onclick="return confirm('هل أنت متأكد من مسح الذاكرة المؤقتة؟')">
                🧹 مسح الذاكرة المؤقتة
            </button>
        </form>
    </div>

    <div class="card">
        <h3>🔄 إعادة تهيئة قاعدة البيانات</h3>
        <p class="text-muted">إعادة إنشاء قاعدة البيانات من البداية مع البيانات التجريبية</p>
        <div class="alert alert-danger">
            ⚠️ تحذير: هذا الإجراء سيحذف جميع البيانات الحالية!
        </div>

        <form action="{{ route('admin.tools.refresh-database') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد؟ سيتم حذف جميع البيانات!')">
                🔄 إعادة تهيئة قاعدة البيانات
            </button>
        </form>
    </div>

    <div class="card">
        <h3>📊 معلومات النظام</h3>
        <div class="system-info">
            <div class="info-item">
                <strong>إصدار PHP:</strong> {{ PHP_VERSION }}
            </div>
            <div class="info-item">
                <strong>إصدار Laravel:</strong> {{ app()->version() }}
            </div>
            <div class="info-item">
                <strong>نظام التشغيل:</strong> {{ php_uname() }}
            </div>
            <div class="info-item">
                <strong>مساحة القرص:</strong>
                @php
                    $bytes = disk_free_space("/");
                    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
                    $base = 1024;
                    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
                    echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . ' متاحة';
                @endphp
            </div>
        </div>
    </div>

    <div class="card">
        <h3>📋 سجلات النظام</h3>
        <p class="text-muted">عرض آخر 50 سطر من سجل الأخطاء</p>
        <a href="#" class="btn btn-info" onclick="showLogs()">📋 عرض السجلات</a>

        <div id="logs-container" style="display: none; margin-top: 20px;">
            <pre style="background: #f5f5f5; padding: 10px; border-radius: 4px; font-size: 12px; max-height: 400px; overflow-y: auto;">
                @php
                    $logFile = storage_path('logs/laravel.log');
                    if (file_exists($logFile)) {
                        $lines = file($logFile);
                        $lastLines = array_slice($lines, -50);
                        echo implode('', $lastLines);
                    } else {
                        echo 'ملف السجل غير موجود';
                    }
                @endphp
            </pre>
        </div>
    </div>
</div>

<script>
function showLogs() {
    const container = document.getElementById('logs-container');
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
}
</script>

<style>
.system-info {
    display: grid;
    gap: 10px;
}

.info-item {
    padding: 10px;
    background: var(--light-bg);
    border-radius: 4px;
}

.card {
    margin-bottom: 20px;
}

.card h3 {
    margin-bottom: 10px;
    color: var(--primary);
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin: 15px 0;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
@endsection