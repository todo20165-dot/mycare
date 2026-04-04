@extends('layouts.app')

@section('title', 'ملف الطبيب - MyCare')

@section('content')
<div class="header">
    <h1>👨‍⚕️ {{ $doctor->name }}</h1>
    <p>{{ $doctor->specialization ?? 'طب عام' }}</p>
</div>

<!-- صورة الملف الشخصي -->
<div class="card text-center">
    @if($doctor->profile_image)
        <img src="{{ $doctor->profile_image }}" alt="{{ $doctor->name }}" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin: 0 auto;">
    @else
        <div style="width: 120px; height: 120px; border-radius: 50%; background: #E3F2FD; display: flex; align-items: center; justify-content: center; font-size: 48px; margin: 0 auto;">
            👨‍⚕️
        </div>
    @endif
</div>

<!-- معلومات الطبيب -->
<div class="card">
    <h3>معلومات الاتصال</h3>
    <p><strong>البريد الإلكتروني:</strong> {{ $doctor->email }}</p>
    @if($doctor->phone)
        <p><strong>الهاتف:</strong> {{ $doctor->phone }}</p>
    @endif
    @if($doctor->bio)
        <p><strong>النبذة الشخصية:</strong> {{ $doctor->bio }}</p>
    @endif
</div>

<!-- حالة الربط -->
<div class="card">
    <h3>حالة الربط</h3>
    
    @if(!$connection)
        <!-- لا يوجد ربط -->
        <p class="text-muted">لم تقم بربط حسابك مع هذا الطبيب</p>
        
        <form action="{{ route('doctor-patient.request-connection') }}" method="POST">
            @csrf
            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
            
            <div class="form-group">
                <label for="specialization">التخصص (اختياري)</label>
                <input type="text" name="specialization" id="specialization" placeholder="مثال: أمراض القلب">
            </div>

            <div class="form-group">
                <label for="notes">ملاحظات (اختياري)</label>
                <textarea name="notes" id="notes" placeholder="أي معلومات إضافية" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                طلب الربط
            </button>
        </form>
    @elseif($connection->status === 'pending')
        <!-- طلب معلق -->
        <div style="background: #FFF3CD; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <p style="margin: 0; color: #856404;">
                ⏳ طلب الربط معلق - في انتظار موافقة الطبيب
            </p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #856404;">
                تم الإرسال: {{ $connection->assigned_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <form action="{{ route('doctor-patient.disconnect', $connection->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-secondary btn-block" onclick="return confirm('هل تريد إلغاء الطلب؟')">
                إلغاء الطلب
            </button>
        </form>
    @elseif($connection->status === 'approved')
        <!-- ربط موافق عليه -->
        <div style="background: #D4EDDA; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <p style="margin: 0; color: #155724;">
                ✅ تم الربط بنجاح مع الطبيب
            </p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #155724;">
                تاريخ الموافقة: {{ $connection->approved_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="grid">
            <a href="{{ route('doctor.patients.show', $doctor->id) }}" class="btn btn-primary">
                عرض التفاصيل
            </a>
            <form action="{{ route('doctor-patient.disconnect', $connection->id) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-secondary" onclick="return confirm('هل تريد إلغاء الربط؟')">
                    إلغاء الربط
                </button>
            </form>
        </div>
    @elseif($connection->status === 'rejected')
        <!-- طلب مرفوض -->
        <div style="background: #F8D7DA; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
            <p style="margin: 0; color: #721C24;">
                ❌ تم رفض طلب الربط
            </p>
        </div>

        <form action="{{ route('doctor-patient.request-connection') }}" method="POST">
            @csrf
            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
            
            <div class="form-group">
                <label for="notes">ملاحظات (اختياري)</label>
                <textarea name="notes" id="notes" placeholder="أي معلومات إضافية" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                إعادة محاولة الربط
            </button>
        </form>
    @endif
</div>

<!-- رسائل الخطأ والنجاح -->
@if($errors->any())
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="card" style="background: #D4EDDA; color: #155724;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        {{ session('error') }}
    </div>
@endif
@endsection
