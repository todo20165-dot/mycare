<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0066cc">
    <title>@yield('title', 'MyCare - إدارة الرعاية الصحية')</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('icons/mycare-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/mycare-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #0066cc;
            --primary-dark: #004499;
            --secondary: #00cc99;
            --light-bg: #f5f5f5;
            --text-dark: #333;
            --text-light: #666;
            --border: #ddd;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            padding-bottom: 80px;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            opacity: 0.9;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .install-card {
            border-left: 4px solid var(--secondary);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .install-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .install-icon {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            border-radius: 14px;
            font-size: 22px;
        }

        .install-card h3 {
            margin-bottom: 6px;
        }

        .install-card p {
            margin-bottom: 0;
            color: var(--text-light);
            font-size: 14px;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card h3 {
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 16px;
        }

        .card p {
            color: var(--text-light);
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: scale(1.02);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: #00b385;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-block {
            width: 100%;
            display: block;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: white;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-around;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.08);
            z-index: 100;
        }

        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px;
            text-decoration: none;
            color: var(--text-light);
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            color: var(--primary);
        }

        .nav-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: var(--text-light);
        }

        .mt-1 { margin-top: 10px; }
        .mt-2 { margin-top: 20px; }
        .mb-1 { margin-bottom: 10px; }
        .mb-2 { margin-bottom: 20px; }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }

        .stat-card .number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .label {
            font-size: 12px;
            opacity: 0.9;
        }

        @media (max-width: 480px) {
            .container {
                border-radius: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💊 MyCare</h1>
            <p>إدارة الرعاية الصحية المنزلية</p>
        </div>

        <div id="install-prompt" class="card install-card" style="display: none;">
            <div class="install-header">
                <span class="install-icon">📲</span>
                <div>
                    <h3>ثبت التطبيق</h3>
                    <p>أضف MyCare إلى الشاشة الرئيسية لتجربة أسرع وأكثر سلاسة.</p>
                </div>
            </div>
            <button type="button" onclick="installApp()" class="btn btn-primary btn-block">تثبيت التطبيق</button>
            <button type="button" onclick="hideInstallPrompt()" class="btn btn-secondary btn-block">إغلاق</button>
        </div>

        <div class="main-content">
            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>خطأ!</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

        @auth
            <div class="bottom-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">🏠</div>
                    <span>الرئيسية</span>
                </a>
                @if(auth()->user()->isPatient())
                    <a href="{{ route('medications.index') }}" class="nav-item {{ request()->routeIs('medications.*') ? 'active' : '' }}">
                        <div class="nav-icon">💊</div>
                        <span>الأدوية</span>
                    </a>
                    <a href="{{ route('vital-signs.index') }}" class="nav-item {{ request()->routeIs('vital-signs.*') ? 'active' : '' }}">
                        <div class="nav-icon">❤️</div>
                        <span>العلامات</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <div class="nav-icon">📊</div>
                        <span>التقارير</span>
                    </a>
                @endif
                <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <div class="nav-icon">🔔</div>
                    <span>الإشعارات</span>
                </a>
                <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                    <div class="nav-icon">👤</div>
                    <span>الملف</span>
                </a>
            </div>
        @endauth
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/pwa-init.js') }}"></script>
    <script src="{{ asset('js/emergency-detection.js') }}"></script>
    @yield('scripts')
</body>
</html>
