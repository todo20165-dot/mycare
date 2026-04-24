@extends('layouts.app')

@section('title', 'إدارة الأمراض - MyCare')

@section('content')
<div class="header">
    <h1>💉 إدارة الأمراض</h1>
    <p>إضافة وتحرير وحذف الأمراض التي تظهر للمرضى عند اختيار المرض.</p>
</div>

<div class="card">
    <a href="{{ route('admin.diseases.create') }}" class="btn btn-primary btn-block">
        + إضافة مرض جديد
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($diseases->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد أمراض مسجلة حالياً.</p>
    </div>
@else
    @foreach($diseases as $disease)
        <div class="card">
            <h3>{{ $disease->name }}</h3>
            <p class="text-muted"><strong>التخصص:</strong> {{ $disease->specialization }}</p>
            <p>{{ $disease->description }}</p>
            <div class="grid" style="grid-template-columns: repeat(2, minmax(120px, 1fr)); gap: 10px;">
                <a href="{{ route('admin.diseases.edit', $disease->id) }}" class="btn btn-primary">
                    تعديل
                </a>
                <form action="{{ route('admin.diseases.destroy', $disease->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المرض؟')">
                        حذف
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endif
@endsection