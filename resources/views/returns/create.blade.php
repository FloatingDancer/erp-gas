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
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:740px; border:1px solid #f1f5f9; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.info-box { background:#f8fafc; border:1.5px dashed #cbd5e1; border-radius:12px; padding:14px 18px; margin-bottom:22px; font-size:13px; color:#475569; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="rotate-ccw" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Form Retur Barang</h1>
        <p class="page-subtitle">Catat retur produk dari pelanggan atau produk rusak/bocor</p>
    </div>
    <a href="{{ route('returns.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Kembali</a>
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

    <div class="info-box">
        <strong><i data-lucide="info" style="width:15px;height:15px;vertical-align:middle;margin-top:-2px;margin-right:4px;color:#2563eb;"></i> Alur Pengaruh Stok Otomatis:</strong>
        <ul style="margin:6px 0 0; padding-left:18px; line-height:1.6;">
            <li><strong>Kondisi Bagus:</strong> Barang langsung ditambahkan kembali ke <strong>Stok Siap Jual</strong>.</li>
            <li><strong>Kondisi Rusak/Bocor:</strong> Barang dimasukkan ke <strong>Stok Rusak/Karantina (Damaged Stock)</strong> agar tidak sengaja terjual lagi ke pelanggan.</li>
        </ul>
    </div>

    <form method="POST" action="{{ route('returns.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Referensi Order (Opsional)</label>
                <select name="order_id" id="orderSelect" class="form-input">
                    <option value="">-- Tanpa Ref Order (Direct Return) --</option>
                    @foreach($orders as $ord)
                        <option value="{{ $ord->id }}" 
                            data-customer="{{ $ord->customer_id }}" 
                            data-product="{{ $ord->product_id }}"
                            data-qty="{{ $ord->quantity }}"
                            {{ (old('order_id', $selectedOrder->id ?? '') == $ord->id) ? 'selected' : '' }}>
                            Order #{{ $ord->id }} - {{ $ord->customer->customer_name ?? 'Pelanggan' }} ({{ $ord->product->name ?? 'Produk' }} Qty: {{ $ord->quantity }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Referensi Delivery / Pengiriman (Opsional)</label>
                <select name="delivery_id" id="deliverySelect" class="form-input">
                    <option value="">-- Tanpa Ref Delivery --</option>
                    @foreach($deliveries as $del)
                        <option value="{{ $del->id }}" 
                            data-customer="{{ $del->order->customer_id ?? '' }}" 
                            data-product="{{ $del->order->product_id ?? '' }}"
                            {{ (old('delivery_id', $selectedDelivery->id ?? '') == $del->id) ? 'selected' : '' }}>
                            Pengiriman #{{ $del->id }} (Order #{{ $del->order_id }}) - Driver: {{ $del->driver_name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Pelanggan <span style="color:#ef4444;">*</span></label>
                <select name="customer_id" id="customerSelect" class="form-input" required>
                    <option value="" disabled selected>-- Pilih Pelanggan --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id', $selectedOrder->customer_id ?? ($selectedDelivery->order->customer_id ?? '')) == $c->id) ? 'selected' : '' }}>
                            {{ $c->customer_name }} ({{ $c->phone }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Produk Gas <span style="color:#ef4444;">*</span></label>
                <select name="product_id" id="productSelect" class="form-input" required>
                    <option value="" disabled selected>-- Pilih Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ (old('product_id', $selectedOrder->product_id ?? ($selectedDelivery->order->product_id ?? '')) == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }} | Stok: {{ $p->stock }} | Rusak: {{ $p->damaged_stock ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Jumlah Tabung yang Diretur (Qty) <span style="color:#ef4444;">*</span></label>
                <input type="number" name="quantity" class="form-input" min="1" value="{{ old('quantity', 1) }}" placeholder="Contoh: 1" required>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Kondisi Barang <span style="color:#ef4444;">*</span></label>
                <select name="condition" class="form-input" required>
                    <option value="Damaged" {{ old('condition') == 'Damaged' ? 'selected' : '' }}>🚨 Rusak / Cacat / Bocor (Damaged Product)</option>
                    <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>✅ Bagus / Utuh (Bisa Dijual Kembali)</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Jenis Penyelesaian Retur <span style="color:#ef4444;">*</span></label>
                <select name="return_type" class="form-input" required>
                    <option value="Exchange" {{ old('return_type') == 'Exchange' ? 'selected' : '' }}>🔄 Ganti Barang Baru (Exchange)</option>
                    <option value="Refund" {{ old('return_type') == 'Refund' ? 'selected' : '' }}>💵 Pengembalian Dana (Refund)</option>
                    <option value="Credit" {{ old('return_type') == 'Credit' ? 'selected' : '' }}>📝 Potong Nota / Piutang (Credit)</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Nominal Refund / Potong Nota (Rp)</label>
                <input type="number" name="refund_amount" class="form-input" min="0" value="{{ old('refund_amount', 0) }}" placeholder="0 jika hanya ganti barang">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Tanggal Retur <span style="color:#ef4444;">*</span></label>
                <input type="date" name="return_date" class="form-input" value="{{ old('return_date', date('Y-m-d')) }}" required>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Status Persetujuan <span style="color:#ef4444;">*</span></label>
                <select name="status" class="form-input" required>
                    <option value="Approved" selected>✅ Disetujui Langsung (Approved & Update Stok)</option>
                    <option value="Pending">⏳ Menunggu Konfirmasi (Pending)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Alasan Retur / Detail Kerusakan Tabung</label>
            <textarea name="reason" class="form-input" rows="3" placeholder="Contoh: Tabung mengalami kebocoran pada katup seal saat diterima pelanggan.">{{ old('reason') }}</textarea>
        </div>

        <div style="display:flex;gap:10px;margin-top:12px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Simpan Retur Barang</button>
            <a href="{{ route('returns.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    function calculateRefund() {
        const selectedOption = $('#productSelect').find('option:selected');
        const price = parseFloat(selectedOption.data('price')) || 0;
        const qty = parseInt($('input[name="quantity"]').val()) || 0;
        const returnType = $('select[name="return_type"]').val();

        if (returnType === 'Refund' || returnType === 'Credit') {
            $('input[name="refund_amount"]').val(price * qty);
        } else if (returnType === 'Exchange') {
            $('input[name="refund_amount"]').val(0);
        }
    }

    $('#orderSelect').change(function() {
        const opt = $(this).find('option:selected');
        const custId = opt.data('customer');
        const prodId = opt.data('product');
        const qty = opt.data('qty');
        
        if (custId) $('#customerSelect').val(custId);
        if (prodId) $('#productSelect').val(prodId);
        if (qty) $('input[name="quantity"]').val(qty);
        calculateRefund();
    });

    $('#deliverySelect').change(function() {
        const opt = $(this).find('option:selected');
        const custId = opt.data('customer');
        const prodId = opt.data('product');
        
        if (custId) $('#customerSelect').val(custId);
        if (prodId) $('#productSelect').val(prodId);
        calculateRefund();
    });

    $('#productSelect').on('change', calculateRefund);
    $('input[name="quantity"]').on('input change keyup', calculateRefund);
    $('select[name="return_type"]').on('change', calculateRefund);

    if ($('#productSelect').val()) {
        calculateRefund();
    }
});
</script>
@endsection
