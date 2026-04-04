@extends('layouts.app')

@section('title', 'إرسال رسالة - MyCare')

@section('content')
<div class="header">
    <h1>💬 إرسال رسالة</h1>
    <p>للمريض: {{ $patient->name }}</p>
</div>

<form action="{{ route('doctor.messages.store', $patient->id) }}" method="POST">
    @csrf

    <div class="card">
        <div class="form-group">
            <label for="message">نص الرسالة</label>
            <textarea name="message" id="message" rows="6" required></textarea>
        </div>
    </div>

    <div class="card">
        <button type="submit" class="btn btn-primary btn-block">
            إرسال
        </button>
        <a href="{{ route('doctor.patients.show', $patient->id) }}" class="btn btn-secondary btn-block">
            إلغاء
        </a>
    </div>
</form>
@endsection