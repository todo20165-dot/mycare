/**
 * MyCare - تطبيق إدارة الرعاية الصحية المنزلية
 * ملف JavaScript الرئيسي
 */

// تسجيل Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('Service Worker registered:', registration);
            })
            .catch(error => {
                console.log('Service Worker registration failed:', error);
            });
    });
}

// التحقق من توفر PWA
if ('beforeinstallprompt' in window) {
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallPrompt();
    });
    
    window.addEventListener('appinstalled', () => {
        console.log('PWA installed');
        hideInstallPrompt();
    });
}

/**
 * عرض رسالة تثبيت التطبيق
 */
function showInstallPrompt() {
    const installPrompt = document.getElementById('install-prompt');
    if (installPrompt) {
        installPrompt.style.display = 'block';
    }
}

/**
 * إخفاء رسالة تثبيت التطبيق
 */
function hideInstallPrompt() {
    const installPrompt = document.getElementById('install-prompt');
    if (installPrompt) {
        installPrompt.style.display = 'none';
    }
}

/**
 * التعامل مع الاتصال والانقطاع
 */
window.addEventListener('online', () => {
    console.log('Back online');
    showNotification('تم استعادة الاتصال بالإنترنت', 'success');
    syncOfflineData();
});

window.addEventListener('offline', () => {
    console.log('Offline');
    showNotification('بدون اتصال بالإنترنت - البيانات ستُحفظ محلياً', 'warning');
});

/**
 * عرض إشعار
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px;
        background-color: ${getNotificationColor(type)};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * الحصول على لون الإشعار
 */
function getNotificationColor(type) {
    const colors = {
        'success': '#00cc99',
        'danger': '#ff3333',
        'warning': '#ffaa00',
        'info': '#0066cc'
    };
    return colors[type] || colors['info'];
}

/**
 * مزامنة البيانات المحفوظة محلياً
 */
function syncOfflineData() {
    // سيتم تطبيق هذا لاحقاً
    console.log('Syncing offline data...');
}

/**
 * حفظ البيانات محلياً
 */
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (error) {
        console.error('Error saving to localStorage:', error);
        return false;
    }
}

/**
 * استرجاع البيانات من التخزين المحلي
 */
function getFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (error) {
        console.error('Error reading from localStorage:', error);
        return null;
    }
}

/**
 * حذف البيانات من التخزين المحلي
 */
function removeFromLocalStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (error) {
        console.error('Error removing from localStorage:', error);
        return false;
    }
}

/**
 * تنسيق التاريخ
 */
function formatDate(date, format = 'dd/mm/yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year);
}

/**
 * تنسيق الوقت
 */
function formatTime(date) {
    const d = new Date(date);
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return `${hours}:${minutes}`;
}

/**
 * التحقق من صحة البريد الإلكتروني
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * تحويل النص إلى عنوان URL صديق
 */
function slugify(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * حساب الفرق بين التاريخين
 */
function daysBetween(date1, date2) {
    const d1 = new Date(date1);
    const d2 = new Date(date2);
    const diffTime = Math.abs(d2 - d1);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

/**
 * تحويل الأرقام إلى صيغة مقروءة
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * إضافة تأثيرات الحركة
 */
document.addEventListener('DOMContentLoaded', () => {
    // إضافة تأثير الانتقال السلس للروابط
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // إضافة فئة نشطة للروابط الحالية
    const currentLocation = location.pathname;
    document.querySelectorAll('a').forEach(link => {
        if (link.getAttribute('href') === currentLocation) {
            link.classList.add('active');
        }
    });
});

// تصدير الدوال للاستخدام العام
window.MyCare = {
    showNotification,
    saveToLocalStorage,
    getFromLocalStorage,
    removeFromLocalStorage,
    formatDate,
    formatTime,
    isValidEmail,
    slugify,
    daysBetween,
    formatNumber,
    syncOfflineData
};
