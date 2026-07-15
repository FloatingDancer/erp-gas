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
</style>

<div class="page-header">
  <div>
    <h1 class="page-title"><i data-lucide="shopping-cart" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Orders</h1>
    <p class="page-subtitle">Kelola data pesanan</p>
  </div>
  <a href="{{ route('orders.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Add Order</a>
</div>

@if(session('error'))
  <div class="alert-error"><i data-lucide="alert-triangle" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('error') }}</div>
@endif
@if(session('success'))
  <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

<div class="card-clean">
  <div class="tbl-wrap">
    <table class="modern-table" id="ordersTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Product</th>
          <th>Qty</th>
          <th>Total</th>
          <th>Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td style="color:#94a3b8;font-size:12px;">{{ $order->id }}</td>
            <td><span style="font-weight:600;color:#0f172a;">{{ $order->customer->customer_name }}</span></td>
            <td>{{ $order->product->name }}</td>
            <td><span class="badge-pill badge-blue">{{ $order->quantity }}</span></td>
            <td style="font-weight:600;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td style="color:#64748b;">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
            <td>
              @if($order->status == 'Pending')
                <span class="badge-pill badge-orange"><i data-lucide="clock" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Pending</span>
              @elseif($order->status == 'Completed')
                <span class="badge-pill badge-green"><i data-lucide="check" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Completed</span>
              @else
                <span class="badge-pill badge-gray">{{ $order->status }}</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="{{ route('orders.edit', $order->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;margin-right:2px;"></i> Edit</a>
                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" id="del-{{ $order->id }}" style="display:inline;">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="action-delete" onclick="confirmDelete({{ $order->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;margin-right:2px;"></i> Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8">
              <div class="empty-state">
                <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="shopping-cart" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                <p>Belum ada order</p>
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
  @if(session('show_navigation_popup'))
    Swal.fire({
      icon: 'success',
      title: 'Order Berhasil Dibuat!',
      html: `
        <p style="font-size: 14px; color: #64748b; margin-top: 10px; margin-bottom: 24px;">Pilih modul tujuan Anda selanjutnya:</p>
        <div style="display: flex; flex-direction: column; gap: 10px; max-width: 320px; margin: 0 auto;">
          <a href="{{ route('invoices.edit', session('invoice_id')) }}" 
             style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; color: white; background: #2563eb; transition: background 0.15s; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(37,99,235,0.18);"
             onmouseover="this.style.background='#1d4ed8'" 
             onmouseout="this.style.background='#2563eb'">
            📄 Cek Invoice & Cetak
          </a>
          <a href="{{ route('payments.create') }}?invoice_id={{ session('invoice_id') }}" 
             style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; color: white; background: #10b981; transition: background 0.15s; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(16,185,129,0.18);"
             onmouseover="this.style.background='#059669'" 
             onmouseout="this.style.background='#10b981'">
            💸 Buat Pembayaran
          </a>
          <button onclick="Swal.close()" 
             style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 18px; border-radius: 10px; font-size: 14px; font-weight: 600; border: 1.5px solid #e2e8f0; color: #475569; background: #f8fafc; transition: all 0.15s; cursor: pointer;"
             onmouseover="this.style.background='#f1f5f9'; this.style.color='#1e293b'" 
             onmouseout="this.style.background='#f8fafc'; this.style.color='#475569'">
            ❌ Batal / Tetap di Sini
          </button>
        </div>
      `,
      showConfirmButton: false,
      showDenyButton: false,
      showCancelButton: false
    });
  @else
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: '{{ session('success') }}',
      timer: 2000,
      showConfirmButton: false
    });
  @endif
</script>
@endif

<script>
function confirmDelete(id) {
  Swal.fire({
    title: 'Hapus order?',
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

$(document).ready(function() {
  $('#ordersTable').DataTable({
    "order": [[0, "desc"]],
    "pageLength": 10,
    "lengthMenu": [10, 25, 50, 100],
    "language": {
      "search": "Cari:",
      "lengthMenu": "Tampilkan _MENU_ baris",
      "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
      "paginate": {
        "first": "Pertama",
        "last": "Terakhir",
        "next": "Lanjut",
        "previous": "Kembali"
      },
      "zeroRecords": "Tidak ada data ditemukan",
      "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
      "infoFiltered": "(disaring dari _MAX_ total data)"
    }
  });
});
</script>
@endsection