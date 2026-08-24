@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
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
.badge-red { background:#fee2e2; color:#b91c1c; }
.badge-yellow { background:#fef9c3; color:#854d0e; }
.badge-blue { background:#dbeafe; color:#1d4ed8; }
.action-approve { display:inline-flex; align-items:center; gap:4px; background:#dcfce7; color:#15803d; border:none; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; }
.action-approve:hover { background:#bbf7d0; }
.action-print { display:inline-flex; align-items:center; gap:4px; background:#f1f5f9; color:#334155; border:none; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; }
.action-print:hover { background:#e2e8f0; color:#0f172a; }
.action-delete { display:inline-flex; align-items:center; gap:4px; background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; }
.action-delete:hover { background:#fecaca; }
.stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px; }
.stat-card { background:white; border-radius:14px; padding:18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); display:flex; align-items:center; gap:14px; border:1px solid #f1f5f9; }
.stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.stat-val { font-size:20px; font-weight:700; color:#0f172a; line-height:1.2; }
.stat-label { font-size:12px; color:#64748b; margin-top:2px; font-weight:500; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="rotate-ccw" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Retur Barang & Damage Product</h1>
        <p class="page-subtitle">Kelola pengembalian produk pelanggan, barang rusak, dan penyesuaian stok</p>
    </div>
    @if(auth()->user() && !auth()->user()->isDriver())
        <a href="{{ route('returns.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Buat Retur Baru</a>
    @endif
</div>

@if(session('success'))
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

{{-- Summary Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">
            <i data-lucide="rotate-ccw" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $returns->count() }}</div>
            <div class="stat-label">Total Transaksi Retur</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7; color:#15803d;">
            <i data-lucide="check-circle" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $totalGood }} <span style="font-size:13px;font-weight:500;color:#64748b;">tabung</span></div>
            <div class="stat-label">Retur Bagus (Kembali ke Stok)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2; color:#b91c1c;">
            <i data-lucide="alert-triangle" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $totalDamaged }} <span style="font-size:13px;font-weight:500;color:#64748b;">tabung</span></div>
            <div class="stat-label">Produk Rusak/Bocor (Damaged)</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3; color:#854d0e;">
            <i data-lucide="banknote" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <div class="stat-val">Rp {{ number_format($totalRefund, 0, ',', '.') }}</div>
            <div class="stat-label">Total Dana Refund / Potong Nota</div>
        </div>
    </div>
</div>

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table" id="returnsTable">
            <thead>
                <tr>
                    <th>No. Retur</th>
                    <th>Pelanggan</th>
                    <th>Ref Order / Delivery</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Kondisi Barang</th>
                    <th>Jenis Retur</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($returns as $r)
                <tr>
                    <td>
                        <span style="font-weight:700;color:#0f172a;">{{ $r->return_number }}</span>
                        <div style="font-size:11px;color:#64748b;">{{ \Carbon\Carbon::parse($r->return_date)->format('d M Y') }}</div>
                    </td>
                    <td>
                        <span style="font-weight:600;color:#0f172a;">{{ $r->customer->customer_name ?? '-' }}</span>
                        <div style="font-size:11.5px;color:#64748b;">{{ $r->customer->phone ?? '-' }}</div>
                    </td>
                    <td>
                        @if($r->order_id)
                            <span style="font-weight:600;color:#2563eb;">Order #{{ $r->order_id }}</span>
                        @endif
                        @if($r->delivery_id)
                            <div style="font-size:11.5px;color:#64748b;">Pengiriman #{{ $r->delivery_id }}</div>
                        @endif
                        @if(!$r->order_id && !$r->delivery_id)
                            <span style="color:#94a3b8;font-size:12px;">Direct Return</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-weight:600;color:#0f172a;">{{ $r->product->name ?? '-' }}</span>
                        @if($r->reason)
                            <div style="font-size:11px;color:#64748b;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $r->reason }}">Ket: {{ $r->reason }}</div>
                        @endif
                    </td>
                    <td><span style="font-weight:700;font-size:14px;color:#0f172a;">{{ $r->quantity }}</span> tabung</td>
                    <td>
                        @if($r->condition === 'Good')
                            <span class="badge-pill badge-green"><i data-lucide="check" style="width:11px;height:11px;vertical-align:middle;margin-right:2px;"></i> Bagus / Layak</span>
                        @else
                            <span class="badge-pill badge-red"><i data-lucide="alert-octagon" style="width:11px;height:11px;vertical-align:middle;margin-right:2px;"></i> Rusak / Bocor</span>
                        @endif
                    </td>
                    <td>
                        @if($r->return_type === 'Exchange')
                            <span class="badge-pill badge-blue">Ganti Barang</span>
                        @elseif($r->return_type === 'Refund')
                            <span class="badge-pill badge-yellow">Refund Dana</span>
                        @else
                            <span class="badge-pill badge-blue">Potong Nota</span>
                        @endif
                    </td>
                    <td>
                        @if($r->status === 'Approved')
                            <span class="badge-pill badge-green"><i data-lucide="check-circle-2" style="width:12px;height:12px;vertical-align:middle;margin-right:2px;"></i> Disetujui</span>
                        @elseif($r->status === 'Pending')
                            <span class="badge-pill badge-yellow"><i data-lucide="clock" style="width:12px;height:12px;vertical-align:middle;margin-right:2px;"></i> Menunggu</span>
                        @else
                            <span class="badge-pill badge-red"><i data-lucide="x-circle" style="width:12px;height:12px;vertical-align:middle;margin-right:2px;"></i> Ditolak</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if($r->status === 'Pending')
                                <form action="{{ route('returns.approve', $r->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="action-approve" onclick="return confirm('Setujui retur barang ini? Stok inventaris akan otomatis disesuaikan.')">
                                        <i data-lucide="check" style="width:13px;height:13px;"></i> Setujui
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('returns.print', $r->id) }}" target="_blank" class="action-print" title="Cetak Nota Retur">
                                <i data-lucide="printer" style="width:13px;height:13px;"></i> Nota
                            </a>

                            <form action="{{ route('returns.destroy', $r->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus data retur ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-delete" title="Hapus Retur">
                                    <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
                        <i data-lucide="rotate-ccw" style="width:40px;height:40px;margin-bottom:8px;stroke-width:1.5;"></i>
                        <p style="margin:0;">Belum ada data retur barang atau produk rusak</p>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#returnsTable').DataTable({
        "order": [[0, "desc"]],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "next": "Lanjut",
                "previous": "Kembali"
            }
        }
    });
});
</script>
@endsection
