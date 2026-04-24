@extends('layouts.app')

@section('title', 'إضافة مرض جديد - MyCare')

@section('content')
<div class="header">
    <h1>➕ إضافة مرض جديد</h1>
    <p>أضف مرضاً جديداً ليتمكن المرضى من اختياره عند البحث عن الطبيب.</p>
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

<div class="card">
    <form action="{{ route('admin.diseases.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">اسم المرض</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="specialization">التخصص الطبي</label>
            <input type="text" name="specialization" id="specialization" class="form-control" value="{{ old('specialization') }}" required>
            <small class="text-muted">مثال: باطنية، أطفال، أعصاب، عيون</small>
        </div>

        <div class="form-group">
            <label for="description">وصف المرض</label>
            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">حفظ المرض</button>
    </form>
</div>
@endsection