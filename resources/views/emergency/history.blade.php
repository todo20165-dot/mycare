@extends('layouts.app')
@section('title', 'سجل حالات الطوارئ - MyCare')
@section('content')
<div class="header">
    <h1>📋 سجل حالات الطوارئ</h1>
    <p>جميع حالات الطوارئ المسجلة</p>
</div>

<a href="{{ route('emergency.button') }}" class="btn btn-primary btn-block" style="margin-bottom: 20px;">
    ← العودة إلى زر الطوارئ
</a>

@if($emergencies->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد حالات طوارئ مسجلة</p>
    </div>
@else
    @foreach($emergencies as $emergency)
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3>{{ $emergency->title }}</h3>
                    <p>{{ $emergency->message }}</p>
                    <p class="text-muted">{{ $emergency->created_at->format('d/m/Y H:i') }}</p>
                    
                    @if($emergency->data)
                        @php
                            $data = json_decode($emergency->data, true);
                        @endphp
                        <div style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 4px;">
                            @if(isset($data['latitude']) && isset($data['longitude']))
                                <p><strong>📍 الموقع:</strong> {{ $data['latitude'] }}, {{ $data['longitude'] }}</p>
                            @endif
                            @if(isset($data['address']))
                                <p><strong>📮 العنوان:</strong> {{ $data['address'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
                <div style="text-align: center;">
                    <span style="display: inline-block; padding: 8px 16px; background: #ff4444; color: white; border-radius: 20px; font-size: 12px;">
                        🚨 طوارئ
                    </span>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
