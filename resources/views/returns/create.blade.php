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
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:760px; border:1px solid #f1f5f9; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.info-box { background:#f8fafc; border:1.5px dashed #cbd5e1; border-radius:12px; padding:14px 18px; margin-bottom:22px; font-size:13px; color:#475569; }
.category-tab-btn { flex:1; padding:12px 18px; border:2px solid #e2e8f0; border-radius:12px; background:#f8fafc; font-weight:700; font-size:13.5px; color:#475569; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all 0.15s; }
.category-tab-btn.active { border-color:#2563eb; background:#eff6ff; color:#1e40af; box-shadow:0 2px 6px rgba(37,99,235,0.12); }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="rotate-ccw" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Form Retur Barang</h1>
        <p class="page-subtitle">Pencatatan retur dari pelanggan (Sales Return) atau klaim retur ke supplier (Purchase Return)</p>
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

    <form method="POST" action="{{ route('returns.store') }}" id="returnForm">
        @csrf

        {{-- Return Category Selector --}}
        <div class="form-group">
            <label class="form-label" style="font-size:13.5px;color:#0f172a;">Pilih Jenis Retur:</label>
            <input type="hidden" name="return_category" id="returnCategoryInput" value="{{ old('return_category', 'Customer') }}">
            <div style="display:flex;gap:12px;">
                <div class="category-tab-btn {{ old('return_category', 'Customer') === 'Customer' ? 'active' : '' }}" id="tabCustomer" onclick="setReturnCategory('Customer')">
                    <i data-lucide="users" style="width:18px;height:18px;"></i>
                    <span>1. Retur dari Pelanggan (Sales Return)</span>
                </div>
                <div class="category-tab-btn {{ old('return_category') === 'Supplier' ? 'active' : '' }}" id="tabSupplier" onclick="setReturnCategory('Supplier')">
                    <i data-lucide="factory" style="width:18px;height:18px;"></i>
                    <span>2. Retur ke Supplier (Purchase Return)</span>
                </div>
            </div>
        </div>

        {{-- Info Box Dynamic --}}
        <div class="info-box" id="infoBoxCustomer" style="{{ old('return_category', 'Customer') === 'Customer' ? '' : 'display:none;' }}">
            <strong><i data-lucide="info" style="width:15px;height:15px;vertical-align:middle;margin-top:-2px;margin-right:4px;color:#2563eb;"></i> Alur Retur dari Pelanggan:</strong>
            <ul style="margin:6px 0 0; padding-left:18px; line-height:1.6;">
                <li><strong>Kondisi Rusak/Bocor:</strong> Tabung dimasukkan ke <strong>Stok Rusak/Karantina (Damaged Stock)</strong> agar tidak sengaja terjual lagi.</li>
                <li><strong>Kondisi Bagus:</strong> Tabung langsung ditambahkan kembali ke <strong>Stok Siap Jual</strong>.</li>
            </ul>
        </div>

        <div class="info-box" id="infoBoxSupplier" style="{{ old('return_category') === 'Supplier' ? '' : 'display:none;' }};background:#fdf4ff;border-color:#f0abfc;color:#701a75;">
            <strong><i data-lucide="info" style="width:15px;height:15px;vertical-align:middle;margin-top:-2px;margin-right:4px;color:#a855f7;"></i> Alur Retur ke Supplier (Klaim Tabung Rusak):</strong>
            <ul style="margin:6px 0 0; padding-left:18px; line-height:1.6;">
                <li>Mengeluarkan tabung rusak dari <strong>Stok Rusak / Karantina (Damaged Stock)</strong> untuk dikembalikan ke Supplier mitra.</li>
                <li>Jika <strong>Ganti Barang (Exchange)</strong>: Supplier memberikan tabung gas baru yang bagus sehingga <strong>Stok Siap Jual</strong> bertambah.</li>
            </ul>
        </div>

        {{-- SECTION CUSTOMER FIELDS --}}
        <div id="customerSection" style="{{ old('return_category', 'Customer') === 'Customer' ? '' : 'display:none;' }}">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">Referensi Order Penjualan (Opsional)</label>
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

            <div class="form-group">
                <label class="form-label">Pelanggan <span style="color:#ef4444;">*</span></label>
                <select name="customer_id" id="customerSelect" class="form-input">
                    <option value="" disabled selected>-- Pilih Pelanggan --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ (old('customer_id', $selectedOrder->customer_id ?? ($selectedDelivery->order->customer_id ?? '')) == $c->id) ? 'selected' : '' }}>
                            {{ $c->customer_name }} ({{ $c->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- SECTION SUPPLIER FIELDS --}}
        <div id="supplierSection" style="{{ old('return_category') === 'Supplier' ? '' : 'display:none;' }}">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="form-label">Supplier Tujuan Retur <span style="color:#ef4444;">*</span></label>
                    <select name="supplier_id" id="supplierSelect" class="form-input">
                        <option value="" disabled selected>-- Pilih Supplier --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ (old('supplier_id', $selectedPurchase->supplier_id ?? '') == $s->id) ? 'selected' : '' }}>
                                {{ $s->name }} ({{ $s->phone ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 form-group">
                    <label class="form-label">Referensi Faktur Pembelian (PO) (Opsional)</label>
                    <select name="purchase_id" id="purchaseSelect" class="form-input">
                        <option value="">-- Tanpa Ref Pembelian --</option>
                        @foreach($purchases as $pch)
                            <option value="{{ $pch->id }}" 
                                data-supplier="{{ $pch->supplier_id }}" 
                                data-product="{{ $pch->product_id }}"
                                data-qty="{{ $pch->quantity }}"
                                {{ (old('purchase_id', $selectedPurchase->id ?? '') == $pch->id) ? 'selected' : '' }}>
                                PO #{{ $pch->id }} - {{ $pch->supplier->name ?? 'Supplier' }} (Qty: {{ $pch->quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- COMMON PRODUCT & QUANTITY FIELDS --}}
        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Produk Gas <span style="color:#ef4444;">*</span></label>
                <select name="product_id" id="productSelect" class="form-input" required>
                    <option value="" disabled selected>-- Pilih Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-cost="{{ $p->cost_price ?? $p->price }}" data-damaged="{{ $p->damaged_stock ?? 0 }}" {{ (old('product_id', $selectedOrder->product_id ?? ($selectedDelivery->order->product_id ?? ($selectedPurchase->product_id ?? ''))) == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }} (Stok Siap Jual: {{ $p->stock }} | Rusak: {{ $p->damaged_stock ?? 0 }} Tabung)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Jumlah Tabung yang Diretur (Qty) <span style="color:#ef4444;">*</span></label>
                <input type="number" name="quantity" class="form-input" min="1" value="{{ old('quantity', 1) }}" placeholder="Contoh: 1" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Kondisi Barang <span style="color:#ef4444;">*</span></label>
                <select name="condition" id="conditionSelect" class="form-input" required>
                    <option value="Damaged" {{ old('condition', 'Damaged') == 'Damaged' ? 'selected' : '' }}>🚨 Rusak / Cacat / Bocor (Damaged Product)</option>
                    <option value="Good" {{ old('condition') == 'Good' ? 'selected' : '' }}>✅ Bagus / Utuh (Bisa Dijual Kembali)</option>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Jenis Penyelesaian Retur <span style="color:#ef4444;">*</span></label>
                <select name="return_type" id="returnTypeSelect" class="form-input" required>
                    <option value="Exchange" {{ old('return_type', 'Exchange') == 'Exchange' ? 'selected' : '' }}>🔄 Ganti Barang Baru (Exchange)</option>
                    <option value="Refund" {{ old('return_type') == 'Refund' ? 'selected' : '' }}>💵 Pengembalian Dana / Klaim Cash (Refund)</option>
                    <option value="Credit" {{ old('return_type') == 'Credit' ? 'selected' : '' }}>📝 Potong Nota / Tagihan (Credit)</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label class="form-label">Nominal Refund / Potong Nota (Rp)</label>
                <input type="number" name="refund_amount" id="refundAmountInput" class="form-input" min="0" value="{{ old('refund_amount', 0) }}" placeholder="0 jika hanya ganti barang">
            </div>

            <div class="col-md-6 form-group">
                <label class="form-label">Tanggal Retur <span style="color:#ef4444;">*</span></label>
                <input type="date" name="return_date" class="form-input" value="{{ old('return_date', date('Y-m-d')) }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 form-group">
                <label class="form-label">Status Persetujuan <span style="color:#ef4444;">*</span></label>
                <select name="status" class="form-input" required>
                    <option value="Approved" selected>✅ Disetujui Langsung (Approved & Otomatis Update Stok)</option>
                    <option value="Pending">⏳ Menunggu Konfirmasi (Pending)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Alasan Retur / Catatan Tambahan</label>
            <textarea name="reason" class="form-input" rows="3" placeholder="Contoh: Tabung mengalami kebocoran pada katup seal saat diterima.">{{ old('reason') }}</textarea>
        </div>

        <div style="display:flex;gap:10px;margin-top:12px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Simpan Retur Barang</button>
            <a href="{{ route('returns.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script>
function setReturnCategory(cat) {
    $('#returnCategoryInput').val(cat);
    
    if (cat === 'Customer') {
        $('#tabCustomer').addClass('active');
        $('#tabSupplier').removeClass('active');
        $('#customerSection').slideDown(150);
        $('#supplierSection').slideUp(150);
        $('#infoBoxCustomer').slideDown(150);
        $('#infoBoxSupplier').slideUp(150);
        $('#customerSelect').prop('required', true);
        $('#supplierSelect').prop('required', false);
    } else {
        $('#tabSupplier').addClass('active');
        $('#tabCustomer').removeClass('active');
        $('#supplierSection').slideDown(150);
        $('#customerSection').slideUp(150);
        $('#infoBoxSupplier').slideDown(150);
        $('#infoBoxCustomer').slideUp(150);
        $('#supplierSelect').prop('required', true);
        $('#customerSelect').prop('required', false);
    }
    calculateRefund();
}

function calculateRefund() {
    const selectedOption = $('#productSelect').find('option:selected');
    const cat = $('#returnCategoryInput').val();
    const price = cat === 'Supplier' 
        ? (parseFloat(selectedOption.data('cost')) || parseFloat(selectedOption.data('price')) || 0)
        : (parseFloat(selectedOption.data('price')) || 0);
        
    const qty = parseInt($('input[name="quantity"]').val()) || 0;
    const returnType = $('#returnTypeSelect').val();

    if (returnType === 'Refund' || returnType === 'Credit') {
        $('#refundAmountInput').val(price * qty);
    } else if (returnType === 'Exchange') {
        $('#refundAmountInput').val(0);
    }
}

$(document).ready(function() {
    // Initial category setup
    const initialCat = $('#returnCategoryInput').val() || 'Customer';
    setReturnCategory(initialCat);

    // Auto-fill when Order is selected
    $('#orderSelect').on('change', function() {
        const opt = $(this).find('option:selected');
        const custId = opt.data('customer');
        const prodId = opt.data('product');
        const qty = opt.data('qty');

        if (custId) $('#customerSelect').val(custId);
        if (prodId) $('#productSelect').val(prodId);
        if (qty) $('input[name="quantity"]').val(1);
        calculateRefund();
    });

    // Auto-fill when Delivery is selected
    $('#deliverySelect').on('change', function() {
        const opt = $(this).find('option:selected');
        const custId = opt.data('customer');
        const prodId = opt.data('product');

        if (custId) $('#customerSelect').val(custId);
        if (prodId) $('#productSelect').val(prodId);
        calculateRefund();
    });

    // Auto-fill when Purchase is selected
    $('#purchaseSelect').on('change', function() {
        const opt = $(this).find('option:selected');
        const suppId = opt.data('supplier');
        const prodId = opt.data('product');

        if (suppId) $('#supplierSelect').val(suppId);
        if (prodId) $('#productSelect').val(prodId);
        calculateRefund();
    });

    // Recalculate on product, qty, return type change
    $('#productSelect, input[name="quantity"], #returnTypeSelect').on('change input', function() {
        calculateRefund();
    });

    calculateRefund();
});
</script>
@endsection
