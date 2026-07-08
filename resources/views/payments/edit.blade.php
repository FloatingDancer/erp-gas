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
        <h1 class="page-title"><i data-lucide="edit" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Edit Payment</h1>
        <p class="page-subtitle">Perbarui data pembayaran</p>
    </div>
    <a href="{{ route('payments.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:15px;height:15px;margin-right:2px;"></i> Kembali</a>
</div>

<div class="form-card">
    @if($errors->any())
        <div class="alert-error">
            <strong><i data-lucide="alert-triangle" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> Ada kesalahan:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payments.update', $payment->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Invoice</label>
            <select name="invoice_id" id="invoiceSelect" class="form-input" required>
                @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}"
                            data-amount="{{ $inv->total_amount }}"
                            {{ $payment->invoice_id == $inv->id ? 'selected' : '' }}>
                        INV-{{ str_pad($inv->id, 4, '0', STR_PAD_LEFT) }}
                        — Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" id="amountField" class="form-input"
                   readonly value="{{ $payment->amount }}">
            <p class="form-hint">Jumlah pembayaran diambil otomatis dari invoice yang dipilih.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Metode Pembayaran</label>
            <select name="method" class="form-input" required>
                <option value="Cash"     {{ $payment->method === 'Cash'     ? 'selected' : '' }}>Cash</option>
                <option value="Transfer" {{ $payment->method === 'Transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="E-Wallet" {{ $payment->method === 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
            </select>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:15px;height:15px;margin-right:4px;"></i> Update Payment</button>
            <a href="{{ route('payments.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('invoiceSelect');
    const amt = document.getElementById('amountField');
    function update() {
        const opt = sel.options[sel.selectedIndex];
        if (opt) amt.value = opt.getAttribute('data-amount') || '';
    }
    sel.addEventListener('change', update);
});
</script>
@endsection