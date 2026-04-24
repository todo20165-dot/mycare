@extends('layouts.app')
@section('title', 'زر الطوارئ - MyCare')
@section('content')
<div class="header">
    <h1>🚨 زر الطوارئ</h1>
    <p>اضغط على الزر أدناه في حالة الطوارئ</p>
</div>

<div class="emergency-button-container" style="text-align: center; padding: 40px 20px;">
    <button id="emergencyBtn" class="btn btn-danger" style="
        width: 200px;
        height: 200px;
        border-radius: 50%;
        font-size: 48px;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
        transition: all 0.3s ease;
        border: none;
        background: linear-gradient(135deg, #ff4444 0%, #cc0000 100%);
        color: white;
    " onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
        🚨
    </button>
    <p style="margin-top: 20px; font-size: 16px; color: #666;">اضغط الزر للإبلاغ عن حالة طوارئ</p>
</div>

<style>
    .emergency-map-panel {
        display: grid;
        gap: 20px;
        grid-template-columns: 1fr;
    }

    .emergency-map-card {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .emergency-map-card h3 {
        margin: 0;
        padding: 18px 20px;
        background: #ffffff;
        border-bottom: 1px solid #eef2f7;
    }

    .emergency-map-card .map-container {
        width: 100%;
        min-height: 420px;
        position: relative;
    }

    .map-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1000;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 8px;
        display: flex;
        gap: 4px;
    }

    .map-control-btn {
        padding: 6px 12px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }

    .map-control-btn:hover {
        background: #f5f5f5;
    }

    .map-control-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }

    .emergency-info-panel {
        display: grid;
        gap: 16px;
    }

    .emergency-info-panel .card {
        padding: 18px;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .emergency-info-panel .card h3 {
        margin-top: 0;
    }

    @media (min-width: 980px) {
        .emergency-map-panel {
            grid-template-columns: 1.7fr 1fr;
        }
    }
</style>

<div class="emergency-map-panel">
    <div class="emergency-map-card">
        <h3>📍 موقعك الحالي</h3>
        <div id="map" class="map-container">
            <div class="map-controls">
                <button class="map-control-btn active" data-layer="streets">خريطة عادية</button>
                <button class="map-control-btn" data-layer="satellite">قمر صناعي</button>
                <button class="map-control-btn" data-layer="terrain">تضاريس</button>
            </div>
        </div>
        <div id="location-info" style="padding: 16px; background: #f9fbff; border-top: 1px solid #eef2f7;">
            <p id="location-text">جاري الحصول على موقعك...</p>
        </div>
    </div>

    <div class="emergency-info-panel">
        <div class="card" id="nearest-hospital-card" style="display: none;">
            <h3>🏥 أقرب مستشفى</h3>
            <div id="nearest-hospital-details"></div>
        </div>

        <div class="card" id="route-summary-card" style="display: none;">
            <h3>🛣️ معلومات الطريق الأقصر</h3>
            <div id="route-summary"></div>
        </div>
    </div>
</div>

<div class="card" id="hospitals-section" style="display: none; padding: 18px; margin-top: 20px;">
    <h3>🏥 المستشفيات والمراكز الصحية القريبة</h3>
    <div id="hospitals-list"></div>
</div>

<div class="card" style="margin-top: 20px; padding: 18px;">
    <h3>📋 سجل حالات الطوارئ</h3>
    <a href="{{ route('emergency.history') }}" class="btn btn-secondary btn-block">عرض السجل الكامل</a>
</div>

<!-- مكتبة Leaflet للخريطة -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<script>
let map;
let userMarker;
let hospitalMarkers = [];
let routeLayer = null;
let currentLayer = 'streets';
let baseLayers = {};
let userLocation = { latitude: null, longitude: null };

const userIcon = L.icon({
    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
});

const hospitalIcon = L.divIcon({
    className: 'hospital-marker-icon',
    html: '<div style="background:#d32f2f;border:2px solid #fff;border-radius:50%;width:18px;height:18px;box-shadow:0 0 8px rgba(0,0,0,0.25);"></div>',
    iconSize: [24, 24],
    iconAnchor: [12, 12],
});

// تهيئة الخريطة
function initMap() {
    map = L.map('map', {
        zoomControl: true,
        attributionControl: true,
    }).setView([0, 0], 2);

    // إضافة الطبقات المختلفة
    baseLayers = {
        streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
            maxZoom: 19,
        }),
        terrain: L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
            maxZoom: 17,
        })
    };

    // إضافة الطبقة الافتراضية وعناصر التحكم
    baseLayers.streets.addTo(map);
    L.control.layers({
        'خريطة عادية': baseLayers.streets,
        'قمر صناعي': baseLayers.satellite,
        'تضاريس': baseLayers.terrain,
    }, null, {
        collapsed: true,
        position: 'topright',
    }).addTo(map);
    L.control.scale({ position: 'bottomleft', metric: true, imperial: false }).addTo(map);

    // إضافة معالج الأحداث لأزرار التحكم
    document.querySelectorAll('.map-control-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const layerType = this.getAttribute('data-layer');
            switchMapLayer(layerType);
        });
    });
}

function switchMapLayer(layerType) {
    if (baseLayers[layerType]) {
        // إزالة الطبقة الحالية
        map.eachLayer(function(layer) {
            if (layer instanceof L.TileLayer) {
                map.removeLayer(layer);
            }
        });

        // إضافة الطبقة الجديدة
        baseLayers[layerType].addTo(map);
        currentLayer = layerType;

        // تحديث حالة الأزرار
        document.querySelectorAll('.map-control-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-layer="${layerType}"]`).classList.add('active');

        // إعادة إضافة العلامات والمسارات
        if (userMarker) {
            userMarker.addTo(map);
        }
        hospitalMarkers.forEach(marker => marker.addTo(map));
        if (routeLayer) {
            routeLayer.addTo(map);
        }
    }
}

function clearRoute() {
    if (routeLayer) {
        map.removeLayer(routeLayer);
        routeLayer = null;
    }
}

function clearHospitalMarkers() {
    hospitalMarkers.forEach(marker => map.removeLayer(marker));
    hospitalMarkers = [];
}

// الحصول على موقع المستخدم
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLocation.latitude = position.coords.latitude;
                userLocation.longitude = position.coords.longitude;

                map.setView([userLocation.latitude, userLocation.longitude], 14);

                if (userMarker) {
                    userMarker.setLatLng([userLocation.latitude, userLocation.longitude]);
                } else {
                    userMarker = L.marker([userLocation.latitude, userLocation.longitude], {
                        title: 'موقعك الحالي',
                        icon: userIcon,
                    }).addTo(map);
                }

                userMarker.bindPopup('📍 موقعك الحالي').openPopup();
                updateLocationInfo();
                getNearestHospitals();
            },
            function(error) {
                document.getElementById('location-text').textContent = 'لم يتمكن من الحصول على الموقع: ' + error.message;
            },
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 12000,
            }
        );
    } else {
        document.getElementById('location-text').textContent = 'المتصفح لا يدعم خدمة تحديد الموقع';
    }
}

// تحديث معلومات الموقع
function updateLocationInfo() {
    const locationText = document.getElementById('location-text');
    locationText.innerHTML = `
        <strong>الموقع الحالي:</strong><br>
        خط العرض: ${userLocation.latitude.toFixed(6)}<br>
        خط الطول: ${userLocation.longitude.toFixed(6)}
    `;
}

// الحصول على أقرب المستشفيات
function getNearestHospitals() {
    if (!userLocation.latitude || !userLocation.longitude) return;

    const hospitalsList = document.getElementById('hospitals-list');
    hospitalsList.innerHTML = '<div class="alert alert-info">جاري البحث عن أقرب المستشفيات والمراكز الصحية...</div>';
    document.getElementById('hospitals-section').style.display = 'block';
    document.getElementById('nearest-hospital-card').style.display = 'none';

    const url = '{{ route("emergency.hospitals") }}?latitude=' + encodeURIComponent(userLocation.latitude) + '&longitude=' + encodeURIComponent(userLocation.longitude);

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.hospitals) && data.hospitals.length > 0) {
                displayHospitals(data.hospitals);
            } else {
                hospitalsList.innerHTML = '<div class="alert alert-warning">لم يتم العثور على مستشفيات أو مراكز صحية قريبة في المنطقة الحالية.</div>';
                document.getElementById('nearest-hospital-card').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            hospitalsList.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء جلب بيانات المستشفيات من OpenStreetMap. يرجى المحاولة مرة أخرى.</div>';
            document.getElementById('nearest-hospital-card').style.display = 'none';
        });
}

// عرض المستشفيات على الخريطة والقائمة
function displayHospitals(hospitals) {
    const hospitalsList = document.getElementById('hospitals-list');
    hospitalsList.innerHTML = '';
    clearHospitalMarkers();
    clearRoute();

    const summaryHeader = document.createElement('div');
    summaryHeader.className = 'card';
    summaryHeader.style.marginBottom = '14px';
    summaryHeader.style.padding = '14px';
    summaryHeader.style.background = '#e8f7ff';
    summaryHeader.style.border = '1px solid #cde7ff';
    summaryHeader.innerHTML = `<strong>تم العثور على ${hospitals.length} نتيجة قريبة حسب موقعك الحالي.</strong>`;
    hospitalsList.appendChild(summaryHeader);

    const bounds = L.latLngBounds([[userLocation.latitude, userLocation.longitude]]);

    hospitals.forEach((hospital, index) => {
        const marker = L.marker([hospital.latitude, hospital.longitude], {
            icon: hospitalIcon,
            title: hospital.name,
        }).addTo(map).bindPopup(`
            <strong>${hospital.name}</strong><br>
            ${hospital.address}<br>
            ${hospital.distance}
        `);

        hospitalMarkers.push(marker);
        bounds.extend(marker.getLatLng());

        const hospitalCard = document.createElement('div');
        hospitalCard.className = 'card';
        hospitalCard.style.marginBottom = '12px';
        hospitalCard.style.border = index === 0 ? '2px solid #dc3545' : '1px solid #e8e8e8';
        hospitalCard.innerHTML = `
            <h4 style="margin-top: 0;">${hospital.name}</h4>
            <p><strong>المسافة:</strong> ${hospital.distance}</p>
            <p><strong>الوقت المتوقع:</strong> ${hospital.eta} دقيقة تقريباً</p>
            <p><strong>العنوان:</strong> ${hospital.address}</p>
            <p><strong>الهاتف:</strong> <a href="tel:${hospital.phone}">${hospital.phone}</a></p>
        `;

        const routeButton = document.createElement('button');
        routeButton.type = 'button';
        routeButton.className = 'btn btn-primary';
        routeButton.style.marginTop = '8px';
        routeButton.textContent = 'عرض الطريق إلى هذا المكان';
        routeButton.addEventListener('click', () => displayNearestHospital(hospital));

        hospitalCard.appendChild(routeButton);
        hospitalsList.appendChild(hospitalCard);
    });

    if (bounds.isValid()) {
        map.fitBounds(bounds.pad(0.25));
    }

    if (hospitals.length > 0) {
        displayNearestHospital(hospitals[0]);
    }

    document.getElementById('hospitals-section').style.display = 'block';
}

function displayNearestHospital(hospital) {
    const hospitalCard = document.getElementById('nearest-hospital-card');
    const details = document.getElementById('nearest-hospital-details');

    details.innerHTML = `
        <p><strong>المستشفى:</strong> ${hospital.name}</p>
        <p><strong>المسافة:</strong> ${hospital.distance}</p>
        <p><strong>الوقت المتوقع:</strong> ${hospital.eta} دقيقة</p>
        <p><strong>العنوان:</strong> ${hospital.address}</p>
        <p><strong>الهاتف:</strong> <a href="tel:${hospital.phone}">${hospital.phone}</a></p>
    `;
    hospitalCard.style.display = 'block';
    fetchRouteToHospital(hospital);
}

function fetchRouteToHospital(hospital) {
    const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${userLocation.longitude},${userLocation.latitude};${hospital.longitude},${hospital.latitude}?overview=full&geometries=geojson&alternatives=false&steps=false`;

    fetch(osrmUrl)
        .then(response => response.json())
        .then(data => {
            if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                displayRoute(route.geometry, hospital, route.distance, route.duration);
            } else {
                fallbackRoute(hospital);
            }
        })
        .catch(() => {
            fallbackRoute(hospital);
        });
}

function displayRoute(geometry, hospital, distanceMeters, durationSeconds) {
    clearRoute();

    routeLayer = L.geoJSON(geometry, {
        style: {
            color: '#ff4444',
            weight: 5,
            opacity: 0.8,
        },
    }).addTo(map);

    const bounds = routeLayer.getBounds();
    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    const distanceKm = (distanceMeters / 1000).toFixed(2);
    const durationMin = Math.max(1, Math.round(durationSeconds / 60));

    const summary = document.getElementById('route-summary');
    summary.innerHTML = `
        <p><strong>المستشفى الأقرب:</strong> ${hospital.name}</p>
        <p><strong>المسافة الواقعية:</strong> ${distanceKm} كم</p>
        <p><strong>الوقت المتوقع للوصول:</strong> ${durationMin} دقيقة</p>
        <p>الخريطة تعرض الطريق الأقصر حالياً من موقعك إلى المستشفى.</p>
    `;
    document.getElementById('route-summary-card').style.display = 'block';
}

function fallbackRoute(hospital) {
    clearRoute();

    routeLayer = L.layerGroup([
        L.polyline(
            [
                [userLocation.latitude, userLocation.longitude],
                [hospital.latitude, hospital.longitude],
            ],
            {
                color: '#ff4444',
                dashArray: '10, 8',
                weight: 4,
                opacity: 0.7,
            }
        ),
    ]).addTo(map);

    const bounds = routeLayer.getBounds();
    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    const distanceKm = hospital.distance_km.toFixed(2);
    const durationMin = Math.max(1, Math.round((hospital.distance_km / 50) * 60));

    const summary = document.getElementById('route-summary');
    summary.innerHTML = `
        <p><strong>المستشفى الأقرب:</strong> ${hospital.name}</p>
        <p><strong>المسافة التقريبية:</strong> ${distanceKm} كم</p>
        <p><strong>الوقت المتوقع للوصول:</strong> ${durationMin} دقيقة تقريبياً</p>
        <p>تم عرض الطريق الأقصر تقريبياً بناءً على الموقع الحالي.</p>
    `;
    document.getElementById('route-summary-card').style.display = 'block';
}

// تفعيل زر الطوارئ
function triggerEmergencyAlertButton() {
    if (!userLocation.latitude || !userLocation.longitude) {
        alert('يرجى الانتظار حتى يتم الحصول على موقعك');
        return;
    }

    if (!confirm('هل أنت متأكد من أنك تريد تفعيل تنبيه الطوارئ؟')) {
        return;
    }

    fetch('{{ route("emergency.trigger") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            latitude: userLocation.latitude,
            longitude: userLocation.longitude,
            address: document.getElementById('location-text').textContent,
        }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ تم إرسال تنبيه الطوارئ بنجاح!\nتم إخطار أهلك والأطباء المرتبطين بك');
                setTimeout(() => location.reload(), 2000);
            }
        })
        .catch(error => {
            console.error('خطأ:', error);
            alert('حدث خطأ في إرسال التنبيه');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    getLocation();
    document.getElementById('emergencyBtn').addEventListener('click', triggerEmergencyAlertButton);
    setInterval(getLocation, 15000);
});
</script>
@endsection
