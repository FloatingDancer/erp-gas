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
.card-clean { background:white; border-radius:16px; border:none; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; white-space:nowrap; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.12s; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:13px 16px; font-size:13.5px; color:#374151; vertical-align:middle; }
.badge-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-orange { background:#ffedd5; color:#c2410c; }
.badge-red { background:#fee2e2; color:#b91c1c; }
.badge-gray { background:#f1f5f9; color:#475569; }
.action-edit { display:inline-flex; align-items:center; gap:4px; background:#fef9c3; color:#854d0e; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.action-edit:hover { background:#fef08a; }
.action-delete { display:inline-flex; align-items:center; gap:4px; background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; transition:background 0.15s; }
.action-delete:hover { background:#fecaca; }
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:680px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:48px; margin-bottom:12px; }
</style>

<div class="page-header">
  <div>
    <h1 class="page-title"><i data-lucide="package" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Products</h1>
    <p class="page-subtitle">Kelola data produk gas</p>
  </div>
  <a href="{{ route('products.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Add Product</a>
</div>

@if(session('success'))
  <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

<div class="card-clean">
  <div class="tbl-wrap">
    <table class="modern-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          <tr>
            <td style="color:#94a3b8;font-size:12px;">{{ $p->id }}</td>
            <td><span style="font-weight:600;color:#0f172a;">{{ $p->name }}</span></td>
            <td><span class="badge-pill badge-gray">{{ $p->category }}</span></td>
            <td style="font-weight:600;color:#0f172a;">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
            <td>
              @if($p->stock <= 0)
                <span class="badge-pill badge-red">Habis</span>
              @elseif($p->stock <= 10)
                <span class="badge-pill badge-orange">{{ $p->stock }} — Rendah</span>
              @else
                <span class="badge-pill badge-green">{{ $p->stock }}</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('products.edit', $p->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;margin-right:2px;"></i> Edit</a>
                <form action="{{ route('products.destroy', $p->id) }}" method="POST" id="del-{{ $p->id }}" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="action-delete" onclick="confirmDelete({{ $p->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;margin-right:2px;"></i> Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6">
              <div class="empty-state">
                <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="package" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                <p>Belum ada produk</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '{{ session('success') }}',
    timer: 2000,
    showConfirmButton: false
  });
</script>
@endif
<script>
function confirmDelete(id) {
  Swal.fire({
    title: 'Hapus produk?',
    text: 'Data akan dihapus permanen!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then(r => {
    if (r.isConfirmed) document.getElementById('del-' + id).submit();
  });
}
</script>
@endsection