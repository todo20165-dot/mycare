@extends('layouts.app')

@section('title', 'التقارير - MyCare')

@section('content')
<div class="header">
    <h1>📊 التقارير</h1>
    <p>عرض وإدارة التقارير الطبية</p>
</div>

<div class="card">
    <a href="{{ route('reports.create') }}" class="btn btn-primary btn-block">
        + إنشاء تقرير جديد
    </a>
</div>

@if($reports->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد تقارير</p>
    </div>
@else
    @foreach($reports as $report)
        <div class="card">
            <h3>{{ $report->title }}</h3>
            <p class="text-muted">
                <strong>النوع:</strong> {{ $report->getTypeLabel() }}
            </p>
            <p class="text-muted">
                <strong>الفترة:</strong> {{ $report->start_date->format('d/m/Y') }} - {{ $report->end_date->format('d/m/Y') }}
            </p>
            <p class="text-muted">
                <strong>تاريخ الإنشاء:</strong> {{ $report->created_at->format('d/m/Y H:i') }}
            </p>
            
            <div class="grid">
                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-primary">
                    عرض
                </a>
                <a href="{{ route('reports.download', $report->id) }}" class="btn btn-secondary">
                    تحميل PDF
                </a>
            </div>
        </div>
    @endforeach
@endif
@endsection
