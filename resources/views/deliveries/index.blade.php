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
.card-clean{background:white;border-radius:16px;border:none;box-shadow:0 1px 4px rgba(0,0,0,0.06);}
.tbl-wrap{overflow-x:auto;}
table.modern-table{width:100%;border-collapse:collapse;}
table.modern-table thead tr{background:#f8fafc;border-bottom:2px solid #e2e8f0;}
table.modern-table thead th{padding:12px 16px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;white-space:nowrap;}
table.modern-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background 0.12s;}
table.modern-table tbody tr:hover{background:#f8fafc;}
table.modern-table tbody td{padding:13px 16px;font-size:13.5px;color:#374151;vertical-align:middle;}
.badge-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;}
.badge-yellow{background:#fef9c3;color:#854d0e;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-green{background:#dcfce7;color:#15803d;}
.badge-gray{background:#f1f5f9;color:#475569;}
.action-edit{display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.action-edit:hover{background:#fef08a;}
.action-delete{display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#b91c1c;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;cursor:pointer;}
.action-delete:hover{background:#fecaca;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:20px;}
.empty-state{text-align:center;padding:60px 20px;color:#94a3b8;}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px;}
.empty-state p{font-size:14px;margin:0;}
.action-confirm{display:inline-flex;align-items:center;gap:4px;background:#dcfce7;color:#15803d;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.action-confirm:hover{background:#bbf7d0;}
.delivery-mobile-cards{display:none;}
.delivery-card{background:white;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:18px;margin-bottom:16px;border:1px solid #f1f5f9;}
.delivery-card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;}
.delivery-card-title{font-size:14.5px;font-weight:700;color:#0f172a;margin:0;}
.delivery-card-body{font-size:13px;color:#475569;}
.delivery-card-item{margin-bottom:8px;display:flex;flex-direction:column;gap:2px;}
.delivery-card-label{font-size:11px;color:#94a3b8;text-transform:uppercase;font-weight:600;letter-spacing:0.4px;}
.delivery-card-value{font-weight:500;color:#1e293b;}
.btn-confirm-mobile{width:100%;margin-top:12px;padding:10px;font-size:13.5px;font-weight:700;text-align:center;justify-content:center;text-decoration:none;}

@media (max-width: 768px) {
    .card-clean { display:none !important; }
    .delivery-mobile-cards { display:block; }
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="truck" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Deliveries</h1>
        <p class="page-subtitle">Kelola data pengiriman</p>
    </div>
    @if(!auth()->user()->isDriver())
        <a href="{{ route('deliveries.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Add Delivery</a>
    @endif
</div>

@if(session('success'))
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table" id="deliveriesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Driver</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($deliveries as $d)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">{{ $d->id }}</td>
                    <td><span style="font-weight:600;color:#0f172a;">Order #{{ $d->order->id }}</span></td>
                    <td>
                        @if($d->driver)
                            <span style="font-weight:600;color:#0f172a;">{{ $d->driver->name }}</span>
                            <div style="font-size:11px;color:#64748b;">{{ $d->driver->license_plate }}</div>
                        @else
                            {{ $d->driver_name }}
                        @endif
                    </td>
                    <td style="color:#64748b;">{{ \Carbon\Carbon::parse($d->delivery_date)->format('d M Y') }}</td>
                    <td>
                        @if($d->status === 'Scheduled')
                            <span class="badge-pill badge-yellow"><i data-lucide="calendar" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Scheduled</span>
                        @elseif($d->status === 'On Delivery')
                            <span class="badge-pill badge-blue"><i data-lucide="truck" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> On Delivery</span>
                        @elseif($d->status === 'Delivered')
                            <span class="badge-pill badge-green"><i data-lucide="check" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Delivered</span>
                        @else
                            <span class="badge-pill badge-gray">{{ $d->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if(auth()->user()->isDriver() && $d->status === 'On Delivery')
                                <form action="{{ route('deliveries.confirm-arrival', $d->id) }}" method="POST" id="confirm-arrival-{{ $d->id }}" style="display:inline;">
                                    @csrf
                                    <button type="button" class="action-confirm" onclick="confirmArrival({{ $d->id }}, 'Order #{{ $d->order->id }}')"><i data-lucide="check" style="width:13px;height:13px;margin-right:2px;"></i> Sampai</button>
                                </form>
                            @endif

                            @if(!auth()->user()->isDriver())
                                <a href="{{ route('deliveries.edit', $d->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;margin-right:2px;"></i> Edit</a>
                                <form action="{{ route('deliveries.destroy', $d->id) }}" method="POST" id="del-{{ $d->id }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="action-delete" onclick="confirmDelete({{ $d->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;margin-right:2px;"></i> Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="truck" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                            <p>Belum ada data pengiriman</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="delivery-mobile-cards">
    @forelse($deliveries as $d)
        <div class="delivery-card">
            <div class="delivery-card-header">
                <span class="delivery-card-title">Order #{{ $d->order->id }}</span>
                <div>
                    @if($d->status === 'Scheduled')
                        <span class="badge-pill badge-yellow"><i data-lucide="calendar" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Scheduled</span>
                    @elseif($d->status === 'On Delivery')
                        <span class="badge-pill badge-blue"><i data-lucide="truck" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> On Delivery</span>
                    @elseif($d->status === 'Delivered')
                        <span class="badge-pill badge-green"><i data-lucide="check" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Delivered</span>
                    @else
                        <span class="badge-pill badge-gray">{{ $d->status }}</span>
                    @endif
                </div>
            </div>
            <div class="delivery-card-body">
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Pelanggan</span>
                    <span class="delivery-card-value">{{ $d->order->customer->customer_name }}</span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">No. Telp Pelanggan</span>
                    <span class="delivery-card-value" style="color: #2563eb; font-weight:600;">
                        <a href="tel:{{ $d->order->customer->phone }}" style="text-decoration: none; color:inherit;"><i data-lucide="phone" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> {{ $d->order->customer->phone }}</a>
                    </span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Alamat Pengiriman</span>
                    <span class="delivery-card-value" style="color:#0f172a; font-weight: 600;">{{ $d->order->customer->address }}</span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Driver & Nopol</span>
                    <span class="delivery-card-value">
                        @if($d->driver)
                            {{ $d->driver->name }} ({{ $d->driver->license_plate }})
                        @else
                            {{ $d->driver_name }}
                        @endif
                    </span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Tanggal Pengiriman</span>
                    <span class="delivery-card-value">{{ \Carbon\Carbon::parse($d->delivery_date)->format('d M Y') }}</span>
                </div>

                @if(auth()->user()->isDriver() && $d->status === 'On Delivery')
                    <form action="{{ route('deliveries.confirm-arrival', $d->id) }}" method="POST" id="confirm-arrival-mob-{{ $d->id }}" style="display:block;">
                        @csrf
                        <button type="button" class="btn-primary-custom action-confirm btn-confirm-mobile" onclick="confirmArrival({{ $d->id }}, 'Order #{{ $d->order->id }}', true)">
                            <i data-lucide="check" style="width:14px;height:14px;margin-right:4px;"></i> Konfirmasi Sampai
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="card-clean" style="display:block; padding: 24px; text-align:center;">
            <div class="empty-state">
                <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="truck" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                <p>Belum ada data pengiriman</p>
            </div>
        </div>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
Swal.fire({icon:'success',title:'Berhasil!',text:'{{ session('success') }}',timer:2000,showConfirmButton:false});
</script>
@endif
<script>
function confirmDelete(id) {
    Swal.fire({
        title:'Hapus pengiriman?', text:'Data akan dihapus permanen!', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal'
    }).then(r => { if(r.isConfirmed) document.getElementById('del-'+id).submit(); });
}

function confirmArrival(id, orderLabel, isMobile = false) {
    Swal.fire({
        title: 'Konfirmasi Pengiriman?',
        text: 'Apakah ' + orderLabel + ' sudah benar-benar sampai di tempat pelanggan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Sampai!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(r.isConfirmed) {
            if (isMobile) {
                document.getElementById('confirm-arrival-mob-' + id).submit();
            } else {
                document.getElementById('confirm-arrival-' + id).submit();
            }
        }
    });
}

$(document).ready(function() {
    $('#deliveriesTable').DataTable({
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