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
.action-receive { display:inline-flex; align-items:center; gap:4px; background:#dcfce7; color:#15803d; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.action-receive:hover { background:#bbf7d0; }
.action-delete { display:inline-flex; align-items:center; gap:4px; background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; transition:background 0.15s; }
.action-delete:hover { background:#fecaca; color:#991b1b; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:48px; margin-bottom:12px; }
.empty-state p { font-size:14px; margin:0; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="shopping-bag" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Purchases</h1>
        <p class="page-subtitle">Kelola pembelian stok gas dari supplier</p>
    </div>
    @if(auth()->user() && auth()->user()->isAdmin())
        <a href="{{ route('purchases.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:14px;height:14px;margin-right:2px;"></i> New PO</a>
    @endif
</div>

@if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-error">⚠️ {{ session('error') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>PO ID</th>
                    <th>Supplier</th>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga Beli</th>
                    <th>Total</th>
                    <th>Tanggal PO</th>
                    <th>Status</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @forelse($purchases as $p)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;font-weight:600;">#PO-{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td><span style="font-weight:600;color:#0f172a;">{{ $p->supplier->name ?? '-' }}</span></td>
                    <td>{{ $p->product->name ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $p->quantity }}</td>
                    <td>Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                    <td style="font-weight:600;color:#0f172a;">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                    <td style="color:#64748b;">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</td>
                    <td>
                        @if($p->status === 'Received')
                            <span class="badge-pill badge-green"><i data-lucide="check-circle" style="width:12px;height:12px;vertical-align:middle;margin-top:-2px;margin-right:2px;"></i> Received</span>
                        @else
                            <span class="badge-pill badge-orange"><i data-lucide="clock" style="width:12px;height:12px;vertical-align:middle;margin-top:-2px;margin-right:2px;"></i> Pending</span>
                        @endif
                    </td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td>
                            <div style="display:flex;gap:6px;">
                                @if($p->status === 'Pending')
                                    <form action="{{ route('purchases.receive', $p->id) }}" method="POST" id="rec-{{ $p->id }}" style="display:inline;">
                                        @csrf
                                        <button type="button" class="action-receive" onclick="confirmReceive({{ $p->id }}, '{{ $p->product->name ?? 'Gas' }}', {{ $p->quantity }})"><i data-lucide="download" style="width:13px;height:13px;vertical-align:middle;margin-top:-2px;"></i> Terima Barang</button>
                                    </form>
                                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" id="del-{{ $p->id }}" style="display:inline;">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="button" class="action-delete" onclick="confirmDelete({{ $p->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;vertical-align:middle;margin-top:-2px;"></i> Delete</button>
                                    </form>
                                @else
                                    <span style="color:#94a3b8;font-size:12px;font-style:italic;">No action</span>
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-icon"><i data-lucide="shopping-bag" style="width:48px;height:48px;margin:0 auto;color:#94a3b8;"></i></div>
                            <p>Belum ada data transaksi pembelian</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmReceive(id, prodName, qty) {
    Swal.fire({
        title: 'Konfirmasi Penerimaan',
        text: 'Apakah Anda yakin barang ini sudah diterima? Tindakan ini akan menambah stok ' + prodName + ' sebanyak +' + qty + ' tabung di inventaris gudang Anda!',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Terima!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(r.isConfirmed) document.getElementById('rec-'+id).submit();
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Purchase Order?',
        text: 'Data PO akan dihapus permanen dari sistem!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(r.isConfirmed) document.getElementById('del-'+id).submit();
    });
}
</script>
@endsection
