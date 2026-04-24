@extends('layouts.app')

@section('title', 'تعديل المرض - MyCare')

@section('content')
<div class="header">
    <h1>✏️ تعديل المرض</h1>
    <p>قم بتحديث بيانات المرض لتظهر بشكل صحيح للمرضى.</p>
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
    <form action="{{ route('admin.diseases.update', $disease->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">اسم المرض</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $disease->name) }}" required>
        </div>

        <div class="form-group">
            <label for="specialization">التخصص الطبي</label>
            <input type="text" name="specialization" id="specialization" class="form-control" value="{{ old('specialization', $disease->specialization) }}" required>
            <small class="text-muted">مثال: باطنية، أطفال، أعصاب، عيون</small>
        </div>

        <div class="form-group">
            <label for="description">وصف المرض</label>
            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $disease->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">تحديث المرض</button>
    </form>
</div>
@endsection