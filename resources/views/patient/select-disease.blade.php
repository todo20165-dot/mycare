@extends('layouts.app')
@section('title', 'اختيار المرض - MyCare')
@section('content')
<div class="header">
    <h1>🏥 اختيار المرض</h1>
    <p>يرجى اختيار المرض الذي تعاني منه لتتمكن من الربط مع الطبيب المناسب</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <form action="{{ route('patient.store-disease') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="disease_id">اختر المرض:</label>
            <select name="disease_id" id="disease_id" class="form-control" required onchange="updateDoctors()">
                <option value="">-- اختر المرض --</option>
                @foreach ($diseases as $disease)
                    <option value="{{ $disease->id }}" {{ $selectedDisease && $selectedDisease->id === $disease->id ? 'selected' : '' }}>
                        {{ $disease->name }} ({{ $disease->specialization }})
                    </option>
                @endforeach
            </select>
            @error('disease_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div id="disease-description" class="form-group" style="display: none;">
            <label>وصف المرض:</label>
            <p id="description-text" style="padding: 10px; background: #f5f5f5; border-radius: 4px;"></p>
        </div>

        <button type="submit" class="btn btn-primary btn-block">حفظ الاختيار</button>
    </form>
</div>

@if ($selectedDisease)
    <div class="card mt-3">
        <h3>✅ المرض المختار الحالي</h3>
        <p><strong>{{ $selectedDisease->name }}</strong></p>
        <p class="text-muted">التخصص المطلوب: <strong>{{ $selectedDisease->specialization }}</strong></p>
        <p>{{ $selectedDisease->description }}</p>
        
        <a href="{{ route('patient.search-doctors-by-disease') }}" class="btn btn-secondary btn-block">
            🔍 البحث عن أطباء متخصصين
        </a>
    </div>
@endif

<script>
function updateDoctors() {
    const diseaseId = document.getElementById('disease_id').value;
    const descriptionDiv = document.getElementById('disease-description');
    const descriptionText = document.getElementById('description-text');
    
    if (diseaseId) {
        // العثور على وصف المرض من الخيارات
        const selectedOption = document.querySelector(`#disease_id option[value="${diseaseId}"]`);
        if (selectedOption) {
            // يمكن إضافة وصف إضافي هنا عند الحاجة
            descriptionDiv.style.display = 'block';
        }
    } else {
        descriptionDiv.style.display = 'none';
    }
}

// تحديث الوصف عند تحميل الصفحة إذا كان هناك مرض مختار
document.addEventListener('DOMContentLoaded', function() {
    const diseaseId = document.getElementById('disease_id').value;
    if (diseaseId) {
        updateDoctors();
    }
});
</script>
@endsection
