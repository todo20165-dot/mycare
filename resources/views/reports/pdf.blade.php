<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $report->title }}</title>
    <style>
        body {
            font-family: 'Tahoma', 'DejaVu Sans', serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
            line-height: 1.6;
            direction: rtl;
            unicode-bidi: embed;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: right;
            direction: rtl;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            color: #0066cc;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $report->title }}</h1>
        <p>{{ $report->getTypeLabel() }}</p>
    </div>

    <div class="info">
        <p><strong>Patient:</strong> {{ $report->user->name }}</p>
        <p><strong>Period:</strong> {{ $report->start_date->format('d/m/Y') }} - {{ $report->end_date->format('d/m/Y') }}</p>
        <p><strong>Created At:</strong> {{ $report->created_at->format('d/m/Y H:i') }}</p>
        <p><strong>Created By:</strong> {{ $report->creator->name }}</p>
    </div>

    @if($report->description)
        <div class="section">
            <h2>Description</h2>
            <p>{{ $report->description }}</p>
        </div>
    @endif

    @if($report->type === 'medication_adherence' && isset($report->data) && count($report->data) > 0)
        <div class="section">
            <h2>Medication Adherence</h2>
            <table>
                <thead>
                    <tr>
                        <th>Medication</th>
                        <th>Adherence Rate (%)</th>
                        <th>Total Doses</th>
                        <th>Taken</th>
                        <th>Missed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->data as $medication)
                        <tr>
                            <td>{{ $medication['medication_name'] }}</td>
                            <td>{{ $medication['adherence_rate'] }}</td>
                            <td>{{ $medication['total_doses'] }}</td>
                            <td>{{ $medication['taken_doses'] }}</td>
                            <td>{{ $medication['missed_doses'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($report->type === 'vital_signs' && isset($report->data) && count($report->data) > 0)
        @foreach($report->data as $type => $signs)
            <div class="section">
                <h2>{{ $type }}</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Value 1</th>
                            <th>Value 2</th>
                            <th>Unit</th>
                            <th>Date</th>
                            <th>Abnormal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($signs as $sign)
                            <tr>
                                <td>{{ $sign['value_1'] }}</td>
                                <td>{{ $sign['value_2'] ?? '-' }}</td>
                                <td>{{ $sign['unit'] }}</td>
                                <td>{{ $sign['measured_at'] }}</td>
                                <td>{{ $sign['is_abnormal'] ? 'Yes' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif

    @if($report->type === 'comprehensive_health')
        @if(isset($report->data['medications']) && count($report->data['medications']) > 0)
            <div class="section">
                <h2>Medications</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Medication</th>
                            <th>Adherence Rate (%)</th>
                            <th>Total Doses</th>
                            <th>Taken</th>
                            <th>Missed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->data['medications'] as $medication)
                            <tr>
                                <td>{{ $medication['medication_name'] }}</td>
                                <td>{{ $medication['adherence_rate'] }}</td>
                                <td>{{ $medication['total_doses'] }}</td>
                                <td>{{ $medication['taken_doses'] }}</td>
                                <td>{{ $medication['missed_doses'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(isset($report->data['vital_signs']) && count($report->data['vital_signs']) > 0)
            @foreach($report->data['vital_signs'] as $type => $signs)
                <div class="section">
                    <h2>{{ $type }}</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Value 1</th>
                                <th>Value 2</th>
                                <th>Unit</th>
                                <th>Date</th>
                                <th>Abnormal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($signs as $sign)
                                <tr>
                                    <td>{{ $sign['value_1'] }}</td>
                                    <td>{{ $sign['value_2'] ?? '-' }}</td>
                                    <td>{{ $sign['unit'] }}</td>
                                    <td>{{ $sign['measured_at'] }}</td>
                                    <td>{{ $sign['is_abnormal'] ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
        @endif
    @endif

    <div class="footer">
        <p>This report was created by MyCare system</p>
        <p>{{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>