@extends('layouts.app')
@section('content')
<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.page-title{font-size:22px;font-weight:700;color:#0f172a;margin:0;}
.page-subtitle{font-size:13px;color:#64748b;margin:2px 0 0;}
.btn-primary-custom{display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-primary-custom:hover{background:#1d4ed8;color:white;}
.btn-secondary-custom{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#374151;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.btn-secondary-custom:hover{background:#e2e8f0;color:#111827;}
.form-card{background:white;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,0.06);padding:32px;max-width:680px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-input{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;outline:none;font-family:inherit;}
.form-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.form-input:read-only{background:#f8fafc;color:#64748b;cursor:default;}
.form-group{margin-bottom:20px;}
.form-hint{font-size:11.5px;color:#94a3b8;margin-top:4px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13.5px;}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">✏️ Edit Invoice</h1>
        <p class="page-subtitle">Perbarui data faktur</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="btn-secondary-custom">← Kembali</a>
</div>

<div class="form-card">
    @if($errors->any())
        <div class="alert-error">
            <strong>⚠️ Ada kesalahan:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Order</label>
            <select name="order_id" id="orderSelect" class="form-input" required>
                @foreach($orders as $o)
                    <option value="{{ $o->id }}"
                            data-total="{{ $o->total_amount }}"
                            {{ $invoice->order_id == $o->id ? 'selected' : '' }}>
                        Order #{{ $o->id }} — Rp {{ number_format($o->total_amount, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Total Amount</label>
            <input type="number" name="total_amount" id="totalAmount" class="form-input"
                   readonly value="{{ $invoice->total_amount }}">
            <p class="form-hint">Jumlah total otomatis diambil dari order yang dipilih.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-input" required>
                <option value="Unpaid" {{ $invoice->status === 'Unpaid' ? 'selected' : '' }}>⏳ Unpaid</option>
                <option value="Paid"   {{ $invoice->status === 'Paid'   ? 'selected' : '' }}>✅ Paid</option>
            </select>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom">💾 Update Invoice</button>
            <a href="{{ route('invoices.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('orderSelect');
    const amt = document.getElementById('totalAmount');
    function update() {
        const opt = sel.options[sel.selectedIndex];
        if (opt) amt.value = opt.getAttribute('data-total') || '';
    }
    sel.addEventListener('change', update);
});
</script>
@endsection