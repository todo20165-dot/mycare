/**
 * نظام الكشف الذكي عن الخطر
 * يستخدم Device Motion و Accelerometer لكشف السقوط والحركات غير الطبيعية
 */

class SmartEmergencyDetection {
    constructor() {
        this.accelerationThreshold = 28; // عتبة التسارع للكشف عن السقوط
        this.motionSamples = [];
        this.maxSamples = 80;
        this.riskLevel = 0;
        this.isEnabled = false;
        this.lastAlertTime = 0;
        this.alertCooldown = 15000; // 15 ثانية بين التنبيهات
        this.riskThreshold = 0.8; // 80% احتمالية خطر
        this.recentHighImpact = false;
    }

    /**
     * تفعيل الكشف الذكي عن الخطر
     */
    enable() {
        if (!this.isEnabled) {
            this.isEnabled = true;
            this.setupDeviceMotionListener();
            this.setupAccelerometerListener();
            console.log('✅ تم تفعيل الكشف الذكي عن الخطر');
        }
    }

    /**
     * تعطيل الكشف الذكي عن الخطر
     */
    disable() {
        this.isEnabled = false;
        window.removeEventListener('devicemotion', this.handleDeviceMotion);
        console.log('❌ تم تعطيل الكشف الذكي عن الخطر');
    }

    /**
     * إعداد مستمع حركة الجهاز
     */
    setupDeviceMotionListener() {
        window.addEventListener('devicemotion', (event) => {
            this.handleDeviceMotion(event);
        });
    }

    /**
     * إعداد مستمع مقياس التسارع
     */
    setupAccelerometerListener() {
        if ('Accelerometer' in window) {
            try {
                const accel = new Accelerometer({ frequency: 60 });
                accel.addEventListener('reading', () => {
                    this.analyzeAcceleration(accel.x, accel.y, accel.z);
                });
                accel.start();
            } catch (error) {
                console.log('مقياس التسارع غير متاح:', error);
            }
        }
    }

    /**
     * معالجة حركة الجهاز
     */
    handleDeviceMotion = (event) => {
        if (!this.isEnabled) return;

        const acceleration = event.acceleration;
        if (acceleration) {
            this.analyzeAcceleration(acceleration.x, acceleration.y, acceleration.z);
        }
    }

    /**
     * تحليل بيانات التسارع
     */
    analyzeAcceleration(x, y, z) {
        const magnitude = Math.sqrt(x * x + y * y + z * z);

        this.motionSamples.push({
            magnitude,
            x,
            y,
            z,
            timestamp: Date.now(),
        });

        if (this.motionSamples.length > this.maxSamples) {
            this.motionSamples.shift();
        }

        this.analyzePatterns();
    }

    /**
     * تحليل أنماط الحركة
     */
    analyzePatterns() {
        if (this.motionSamples.length < 15) return;

        const recentSamples = this.motionSamples.slice(-30);
        const fallRisk = this.detectFall(recentSamples);
        const immobilityRisk = this.detectImmobility(recentSamples);
        const abnormalMovementRisk = this.detectAbnormalMovement(recentSamples);

        this.riskLevel = Math.max(fallRisk, immobilityRisk, abnormalMovementRisk);

        if (this.riskLevel >= this.riskThreshold) {
            this.triggerEmergencyAlert();
        }
    }

    /**
     * الكشف عن السقوط المفاجئ
     */
    detectFall(samples) {
        if (samples.length < 10) return 0;

        const magnitudes = samples.map(s => s.magnitude);
        const peak = Math.max(...magnitudes);
        if (peak < this.accelerationThreshold) return 0;

        const peakIndex = magnitudes.indexOf(peak);
        const afterPeak = samples.slice(peakIndex + 1, peakIndex + 12);
        const lowAfterPeak = afterPeak.some(s => s.magnitude < 4);

        if (lowAfterPeak) {
            this.recentHighImpact = true;
            return Math.min((peak - this.accelerationThreshold) / (this.accelerationThreshold * 1.5), 1);
        }

        return 0;
    }

    /**
     * الكشف عن توقف الحركة
     */
    detectImmobility(samples) {
        if (samples.length < 12) return 0;

        const lowMovementCount = samples.filter(s => s.magnitude < 3).length;
        const timeSpan = samples[samples.length - 1].timestamp - samples[0].timestamp;

        if (lowMovementCount >= 10 && timeSpan > 5000 && this.recentHighImpact) {
            return 0.85;
        }

        return 0;
    }

    /**
     * الكشف عن الحركات غير الطبيعية
     */
    detectAbnormalMovement(samples) {
        if (samples.length < 10) return 0;

        const magnitudes = samples.map(s => s.magnitude);
        const mean = magnitudes.reduce((a, b) => a + b, 0) / magnitudes.length;
        const variance = magnitudes.reduce((sum, val) => sum + Math.pow(val - mean, 2), 0) / magnitudes.length;
        const stdDev = Math.sqrt(variance);

        if (stdDev > 20) {
            return Math.min(stdDev / 40, 1);
        }

        return 0;
    }

    /**
     * تفعيل تنبيه الطوارئ
     */
    triggerEmergencyAlert() {
        const now = Date.now();

        if (now - this.lastAlertTime < this.alertCooldown) {
            return;
        }

        this.lastAlertTime = now;
        console.warn(`⚠️ تم كشف احتمالية خطر: ${(this.riskLevel * 100).toFixed(1)}%`);

        if (this.riskLevel > 0.8) {
            this.sendEmergencyNotification();
        }
    }

    /**
     * إرسال إشعار الطوارئ
     */
    sendEmergencyNotification() {
        if (!navigator.geolocation) return;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                fetch('/emergency/trigger', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        latitude,
                        longitude,
                        address: `تم الكشف التلقائي عن حالة طوارئ (احتمالية: ${(this.riskLevel * 100).toFixed(1)}%)`,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('🚨 تم الكشف عن حالة طوارئ وإرسال التنبيه تلقائياً!');
                        }
                    })
                    .catch(error => console.error('خطأ:', error));
            },
            (error) => {
                console.error('خطأ في الحصول على الموقع:', error);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 12000,
            }
        );
    }

    getRiskLevel() {
        return this.riskLevel;
    }

    getStatus() {
        return {
            enabled: this.isEnabled,
            riskLevel: this.riskLevel,
            samplesCount: this.motionSamples.length,
        };
    }
}

const emergencyDetection = new SmartEmergencyDetection();

document.addEventListener('DOMContentLoaded', function() {
    if (typeof DeviceMotionEvent !== 'undefined' && typeof DeviceMotionEvent.requestPermission === 'function') {
        DeviceMotionEvent.requestPermission()
            .then(permissionState => {
                if (permissionState === 'granted') {
                    emergencyDetection.enable();
                }
            })
            .catch(console.error);
    } else {
        emergencyDetection.enable();
    }
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = SmartEmergencyDetection;
}
