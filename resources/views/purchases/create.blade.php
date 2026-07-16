@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
.btn-secondary-custom { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#374151; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-secondary-custom:hover { background:#e2e8f0; color:#111827; }
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:680px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="plus-circle" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Buat Purchase Order</h1>
        <p class="page-subtitle">Ajukan PO baru ke supplier</p>
    </div>
    <a href="{{ route('purchases.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Kembali</a>
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

    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Supplier Mitra</label>
            <select name="supplier_id" class="form-input" required>
                <option value="" disabled selected>-- Pilih Supplier --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Produk Gas</label>
            <select name="product_id" class="form-input" required>
                <option value="" disabled selected>-- Pilih Produk --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }} (Stok Saat Ini: {{ $p->stock }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Jumlah Pembelian (Qty)</label>
            <input type="number" name="quantity" class="form-input" min="1" placeholder="Masukkan jumlah tabung" required>
        </div>
        <div class="form-group">
            <label class="form-label">Harga Beli Per Tabung (Rp)</label>
            <input type="number" name="purchase_price" class="form-input" min="0" placeholder="Masukkan harga beli grosir" required>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Simpan PO</button>
            <a href="{{ route('purchases.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('select[name="product_id"]').change(function() {
        const selectedOption = $(this).find('option:selected');
        const sellingPrice = parseFloat(selectedOption.data('price')) || 0;
        const purchasePrice = Math.max(0, sellingPrice - 3000);
        $('input[name="purchase_price"]').val(purchasePrice);
    });
});
</script>
@endsection
