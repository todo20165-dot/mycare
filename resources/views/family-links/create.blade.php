@extends('layouts.app')

@section('title', 'طلب رابط عائلي - MyCare')

@section('content')
<div class="header">
    <h1>➕ طلب رابط عائلي</h1>
    <p>أرسل طلب ربط إلى أحد أفراد العائلة</p>
</div>

<form action="{{ route('family-links.store') }}" method="POST">
    @csrf

    <div class="card">
        <div class="form-group">
            <label for="family_member_email">البريد الإلكتروني لفرد العائلة</label>
            <input type="email" id="family_member_email" name="family_member_email" value="{{ old('family_member_email') }}" required>
        </div>

        <div class="form-group">
            <label for="relationship">العلاقة</label>
            <select id="relationship" name="relationship" required>
                <option value="">-- اختر العلاقة --</option>
                <option value="parent" {{ old('relationship') == 'parent' ? 'selected' : '' }}>والد / والدة</option>
                <option value="child" {{ old('relationship') == 'child' ? 'selected' : '' }}>ابن / ابنة</option>
                <option value="spouse" {{ old('relationship') == 'spouse' ? 'selected' : '' }}>زوج / زوجة</option>
                <option value="sibling" {{ old('relationship') == 'sibling' ? 'selected' : '' }}>أخ / أخت</option>
                <option value="other" {{ old('relationship') == 'other' ? 'selected' : '' }}>أخرى</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-block">إرسال الطلب</button>
    </div>
</form>

@if($errors->any())
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        <ul>
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

@endsection