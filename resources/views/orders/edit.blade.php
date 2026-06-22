@extends('layouts.app')
@section('content')
<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.page-title{font-size:22px;font-weight:700;color:#0f172a;margin:0;}
.page-subtitle{font-size:13px;color:#64748b;margin:2px 0 0;}
.btn-primary-custom{display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-primary-custom:hover{background:#1d4ed8;color:white;}
.btn-secondary-custom{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#374151;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-secondary-custom:hover{background:#e2e8f0;color:#111827;}
.card-clean{background:white;border-radius:16px;border:none;box-shadow:0 1px 4px rgba(0,0,0,0.06);}
.tbl-wrap{overflow-x:auto;}
table.modern-table{width:100%;border-collapse:collapse;}
table.modern-table thead tr{background:#f8fafc;border-bottom:2px solid #e2e8f0;}
table.modern-table thead th{padding:12px 16px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;white-space:nowrap;}
table.modern-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background 0.12s;}
table.modern-table tbody tr:hover{background:#f8fafc;}
table.modern-table tbody td{padding:13px 16px;font-size:13.5px;color:#374151;vertical-align:middle;}
.badge-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;}
.badge-orange{background:#ffedd5;color:#c2410c;}
.badge-green{background:#dcfce7;color:#15803d;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-gray{background:#f1f5f9;color:#475569;}
.action-edit{display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.action-edit:hover{background:#fef08a;}
.action-delete{display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#b91c1c;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;cursor:pointer;}
.action-delete:hover{background:#fecaca;}
.form-card{background:white;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,0.06);padding:32px;max-width:680px;}
.form-label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.form-input{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;transition:border-color 0.15s;outline:none;font-family:inherit;}
.form-input:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.form-group{margin-bottom:20px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13.5px;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:20px;}
.empty-state{text-align:center;padding:60px 20px;color:#94a3b8;}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px;}

/* Select2 Custom Styles */
.select2-container {
    width: 100% !important;
}
.select2-container--default .select2-selection--single {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    height: 41px !important;
    background-color: #fff !important;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none !important;
    display: flex !important;
    align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #1e293b !important;
    padding-left: 14px !important;
    padding-right: 30px !important;
    font-size: 14px !important;
    line-height: 38px !important;
    margin: 0 !important;
    width: 100% !important;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #64748b !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 14px !important;
    width: 16px !important;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: center !important;
    background-size: 16px !important;
    transition: transform 0.15s ease;
}
.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow {
    transform: rotate(180deg) !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    display: none !important;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12) !important;
}
.select2-dropdown {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    overflow: hidden;
    z-index: 9999;
}
.select2-container--default .select2-search--dropdown {
    padding: 8px 10px !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
    font-size: 13.5px;
    outline: none !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #3b82f6 !important;
}
.select2-container--default .select2-results > .select2-results__options {
    max-height: 180px !important;
    overflow-y: auto !important;
}
.select2-container--default .select2-results__option {
    padding: 8px 14px !important;
    font-size: 13.5px;
    color: #374151;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #2563eb !important;
    color: #fff !important;
}
.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #f1f5f9;
    color: #1e293b;
}
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="page-header">
  <div>
    <h1 class="page-title">✏️ Edit Order</h1>
    <p class="page-subtitle">Perbarui data pesanan</p>
  </div>
  <a href="{{ route('orders.index') }}" class="btn-secondary-custom">← Kembali</a>
</div>

@if(session('error'))
  <div class="alert-error">⚠️ {{ session('error') }}</div>
@endif

<div class="form-card">
  @if($errors->any())
    <div class="alert-error">
      <strong>⚠️ Ada kesalahan:</strong>
      <ul style="margin:6px 0 0;padding-left:18px;">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('orders.update', $order->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label class="form-label">Customer</label>
      <select name="customer_id" class="form-input select2-enable" required>
        @foreach($customers as $c)
          <option value="{{ $c->id }}" {{ $order->customer_id == $c->id ? 'selected' : '' }}>
            {{ $c->customer_name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Product</label>
      <select name="product_id" class="form-input" required>
        @foreach($products as $p)
          <option value="{{ $p->id }}" {{ $order->product_id == $p->id ? 'selected' : '' }}>
            {{ $p->name }} — Rp {{ number_format($p->price, 0, ',', '.') }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Quantity</label>
      <input
        type="number"
        name="quantity"
        class="form-input"
        value="{{ old('quantity', $order->quantity) }}"
        min="1"
        required
      >
    </div>

    <div class="form-group">
      <label class="form-label">Status</label>
      <select name="status" class="form-input">
        <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
        <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>✅ Completed</option>
      </select>
    </div>

    <div style="display:flex;gap:10px;margin-top:8px;">
      <button type="submit" class="btn-primary-custom">💾 Update Order</button>
      <a href="{{ route('orders.index') }}" class="btn-secondary-custom">Batal</a>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-enable').select2({
        width: '100%'
    });
});
</script>
@endsection
