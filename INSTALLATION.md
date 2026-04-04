# دليل التثبيت الكامل - MyCare

هذا الدليل يشرح خطوات التثبيت والإعداد الكاملة لتطبيق MyCare.

## المتطلبات الأساسية

قبل البدء، تأكد من أن لديك:

- **PHP 8.0 أو أعلى** - تحقق بـ: `php -v`
- **Composer** - تحقق بـ: `composer --version`
- **Git** - تحقق بـ: `git --version`
- **متصفح حديث** يدعم PWA (Chrome, Firefox, Edge, Safari)

## خطوات التثبيت

### الخطوة 1: استنساخ المشروع

```bash
# استنساخ المشروع من GitHub
git clone https://github.com/yourusername/mycare-app-laravel.git

# الدخول إلى مجلد المشروع
cd mycare-app-laravel
```

### الخطوة 2: تثبيت المتطلبات

```bash
# تثبيت جميع مكتبات PHP المطلوبة
composer install

# إذا واجهت مشكلة، حاول:
composer install --no-dev
```

### الخطوة 3: إعداد ملف البيئة

```bash
# نسخ ملف البيئة الافتراضي
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate
```

### الخطوة 4: إعداد قاعدة البيانات

#### خيار 1: استخدام SQLite (موصى به للتطوير)

```bash
# إنشاء ملف قاعدة البيانات
touch database/database.sqlite

# تحديث ملف .env
# تأكد من أن DB_CONNECTION=sqlite

# تشغيل الـ Migrations
php artisan migrate
```

#### خيار 2: استخدام MySQL

```bash
# تحديث ملف .env بـ:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=mycare
# DB_USERNAME=root
# DB_PASSWORD=your_password

# إنشاء قاعدة البيانات
mysql -u root -p -e "CREATE DATABASE mycare;"

# تشغيل الـ Migrations
php artisan migrate
```

### الخطوة 5: تشغيل التطبيق

```bash
# بدء خادم التطوير
php artisan serve

# سيظهر:
# Laravel development server started: http://127.0.0.1:8000
```

ثم افتح المتصفح على: **http://localhost:8000**

## التحقق من التثبيت

بعد التثبيت، تأكد من:

1. ✅ الصفحة الرئيسية تحمل بدون أخطاء
2. ✅ يمكنك التسجيل وإنشاء حساب جديد
3. ✅ يمكنك تسجيل الدخول
4. ✅ Service Worker مسجل (افتح Developer Tools → Application → Service Workers)
5. ✅ يمكنك تثبيت التطبيق كـ PWA

## حل المشاكل الشائعة

### مشكلة: "SQLSTATE[HY000]: General error: 1 unable to open database file"

**الحل:**
```bash
# تأكد من وجود المجلد
mkdir -p database

# أعط صلاحيات الكتابة
chmod 755 database
chmod 644 database/database.sqlite

# أعد تشغيل الـ Migrations
php artisan migrate:fresh
```

### مشكلة: "No application encryption key has been generated"

**الحل:**
```bash
php artisan key:generate
```

### مشكلة: "Class not found" أو أخطاء Autoload

**الحل:**
```bash
# أعد توليد Autoloader
composer dump-autoload

# أو
composer install
```

### مشكلة: Service Worker لا يعمل

**الحل:**
1. تأكد من أن التطبيق يعمل على HTTPS أو localhost
2. امسح ذاكرة التخزين المؤقت: Ctrl+Shift+Delete
3. أعد تحميل الصفحة: Ctrl+F5
4. افتح Developer Tools وتحقق من الأخطاء

### مشكلة: الأيقونات لا تظهر بشكل صحيح

**الحل:**
```bash
# تأكد من أن الملفات موجودة في public/
ls -la public/

# إذا لم تكن موجودة، أعد نسخ الملفات
cp -r resources/images/* public/images/
```

## الإعدادات الإضافية

### تفعيل البريد الإلكتروني

```bash
# في ملف .env، حدث:
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@mycare.app
MAIL_FROM_NAME="MyCare App"
```

### تفعيل تخزين الملفات

```bash
# في ملف .env:
FILESYSTEM_DISK=local

# أو استخدم S3:
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket
```

### تفعيل الجلسات

```bash
# في ملف .env:
SESSION_DRIVER=file
# أو
SESSION_DRIVER=database
# ثم قم بـ: php artisan session:table && php artisan migrate
```

## التطوير والاختبار

### تشغيل الاختبارات

```bash
# تشغيل جميع الاختبارات
php artisan test

# تشغيل اختبار محدد
php artisan test tests/Unit/UserTest.php
```

### مسح ذاكرة التخزين المؤقت

```bash
# مسح جميع الـ Caches
php artisan cache:clear

# مسح الـ Config Cache
php artisan config:clear

# مسح الـ Route Cache
php artisan route:clear

# مسح الـ View Cache
php artisan view:clear

# أو استخدم:
php artisan optimize:clear
```

### إعادة تعيين قاعدة البيانات

```bash
# حذف جميع الجداول وإعادة الـ Migrations
php artisan migrate:fresh

# مع إضافة بيانات اختبار
php artisan migrate:fresh --seed
```

## الإنتاج (Production)

### قبل النشر

```bash
# تحديث المتطلبات
composer install --no-dev --optimize-autoloader

# توليد Autoloader المحسّن
composer dump-autoload --optimize

# مسح الـ Caches
php artisan optimize:clear

# توليد Config Cache
php artisan config:cache

# توليد Route Cache
php artisan route:cache
```

### إعدادات الإنتاج

```bash
# في ملف .env:
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mycare.app

# تفعيل HTTPS
FORCE_HTTPS=true
```

## الدعم والمساعدة

إذا واجهت مشاكل:

1. تحقق من [الأسئلة الشائعة](FAQ.md)
2. اقرأ [دليل استكشاف الأخطاء](TROUBLESHOOTING.md)
3. تواصل معنا عبر: support@mycare.app

---

**آخر تحديث:** مارس 2026
