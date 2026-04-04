@extends('layouts.app')

@section('title', 'طلبات الربط - MyCare')

@section('content')
<div class="header">
    <h1>📋 طلبات الربط المعلقة</h1>
    <p>عدد الطلبات: {{ $requests->total() }}</p>
</div>

@if($requests->isEmpty())
    <div class="card text-center">
        <p class="text-muted">لا توجد طلبات ربط معلقة</p>
    </div>
@else
    @foreach($requests as $request)
        <div class="card request-card" id="request-{{ $request->id }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3>{{ $request->patient->name }}</h3>
                    <p class="text-muted">{{ $request->patient->email }}</p>
                    @if($request->patient->phone)
                        <p style="font-size: 14px;">📱 {{ $request->patient->phone }}</p>
                    @endif
                    @if($request->notes)
                        <p style="font-size: 14px; margin-top: 10px;">
                            <strong>ملاحظات:</strong> {{ $request->notes }}
                        </p>
                    @endif
                    <p style="font-size: 12px; color: #999; margin-top: 10px;">
                        تاريخ الطلب: {{ $request->assigned_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div>
                    <span class="status" style="color: #ff9800; font-weight: bold;">قيد الانتظار</span>
                </div>
            </div>

            <div class="grid" style="margin-top: 15px;">
                <form action="{{ route('doctor.doctor-patient.approve', $request->id) }}" method="POST" class="request-action-form" data-action="approve" data-request-id="{{ $request->id }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success action-btn">
                        ✅ قبول
                    </button>
                </form>

                <form action="{{ route('doctor.doctor-patient.reject', $request->id) }}" method="POST" class="request-action-form" data-action="reject" data-request-id="{{ $request->id }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger action-btn">
                        ❌ رفض
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="card">
        {{ $requests->links() }}
    </div>
@endif

<!-- رسائل النجاح -->
@if(session('success'))
    <div class="card" style="background: #D4EDDA; color: #155724;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="card" style="background: #F8D7DA; color: #721C24;">
        {{ session('error') }}
    </div>
@endif

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.request-action-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const action = form.dataset.action;
                const requestId = form.dataset.requestId;
                const confirmation = action === 'approve'
                    ? 'هل تريد قبول هذا الطلب؟'
                    : 'هل تريد رفض هذا الطلب؟';

                if (!confirm(confirmation)) {
                    return;
                }

                const card = document.querySelector('#request-' + requestId);
                const statusLabel = card.querySelector('.status');
                const buttons = card.querySelectorAll('.action-btn');

                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.style.opacity = '0.6';
                });

                if (statusLabel) {
                    statusLabel.textContent = action === 'approve' ? 'تمت الموافقة' : 'تم الرفض';
                    statusLabel.style.color = action === 'approve' ? '#28a745' : '#dc3545';
                }

                // قم بإرسال النموذج بعد تحديث الواجهة
                form.submit();
            });
        });
    });
</script>
@endsection
