@extends('layouts.app')

@section('title', 'عرض التقرير - MyCare')

@section('content')
<div class="header">
    <h1>{{ $report->title }}</h1>
    <p>{{ $report->type }} - {{ $report->created_at->format('d/m/Y') }}</p>
</div>

<div class="card">
    <h3>معلومات التقرير</h3>
    <p><strong>النوع:</strong> {{ $report->getTypeLabel() }}</p>
    <p><strong>الفترة:</strong> {{ $report->start_date->format('d/m/Y') }} - {{ $report->end_date->format('d/m/Y') }}</p>
    <p><strong>تاريخ الإنشاء:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>الحالة:</strong> 
        <span style="background: #4CAF50; color: white; padding: 4px 8px; border-radius: 4px;">
            {{ $report->status }}
        </span>
    </p>
</div>

<div class="card">
    <h3>بيانات التقرير</h3>
    
    @if($report->data)
        @if($report->type === 'medication_adherence')
            @if(count($report->data) > 0)
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="border: 1px solid #ddd; padding: 8px;">الدواء</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">معدل الالتزام</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">إجمالي الجرعات</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">المأخوذة</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">المفقودة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->data as $medication)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['medication_name'] }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['adherence_rate'] }}%</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['total_doses'] }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['taken_doses'] }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['missed_doses'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد بيانات</p>
            @endif
        @elseif($report->type === 'vital_signs')
            @if(count($report->data) > 0)
                @foreach($report->data as $type => $signs)
                    <h4>{{ $type }}</h4>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="border: 1px solid #ddd; padding: 8px;">القيمة 1</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">القيمة 2</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">الوحدة</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">التاريخ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">غير طبيعي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($signs as $sign)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['value_1'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['value_2'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['unit'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['measured_at'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['is_abnormal'] ? 'نعم' : 'لا' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @else
                <p>لا توجد بيانات</p>
            @endif
        @elseif($report->type === 'comprehensive_health')
            @if(isset($report->data['medications']) && count($report->data['medications']) > 0)
                <h4>الأدوية</h4>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="border: 1px solid #ddd; padding: 8px;">الدواء</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">معدل الالتزام</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">إجمالي الجرعات</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">المأخوذة</th>
                            <th style="border: 1px solid #ddd; padding: 8px;">المفقودة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->data['medications'] as $medication)
                            <tr>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['medication_name'] }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['adherence_rate'] }}%</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['total_doses'] }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['taken_doses'] }}</td>
                                <td style="border: 1px solid #ddd; padding: 8px;">{{ $medication['missed_doses'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if(isset($report->data['vital_signs']) && count($report->data['vital_signs']) > 0)
                <h4>العلامات الحيوية</h4>
                @foreach($report->data['vital_signs'] as $type => $signs)
                    <h5>{{ $type }}</h5>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="border: 1px solid #ddd; padding: 8px;">القيمة 1</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">القيمة 2</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">الوحدة</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">التاريخ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">غير طبيعي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($signs as $sign)
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['value_1'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['value_2'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['unit'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['measured_at'] }}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $sign['is_abnormal'] ? 'نعم' : 'لا' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            @endif
        @else
            <pre>{{ json_encode($report->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif
    @else
        <p>البيانات قيد الإعداد</p>
    @endif
</div>

<div class="card">
    <h3>الملاحظات</h3>
    <p>{{ $report->notes ?? 'لا توجد ملاحظات' }}</p>
</div>

<div class="card">
    <div class="grid">
        <a href="{{ route('reports.download', $report->id) }}" class="btn btn-primary">
            📥 تحميل PDF
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            ← العودة
        </a>
    </div>
</div>
@endsection
