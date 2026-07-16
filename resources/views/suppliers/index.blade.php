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
.action-edit { display:inline-flex; align-items:center; gap:4px; background:#fef9c3; color:#854d0e; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.action-edit:hover { background:#fef08a; color:#713f12; }
.action-delete { display:inline-flex; align-items:center; gap:4px; background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; transition:background 0.15s; }
.action-delete:hover { background:#fecaca; color:#991b1b; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:48px; margin-bottom:12px; }
.empty-state p { font-size:14px; margin:0; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="store" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Suppliers</h1>
        <p class="page-subtitle">Kelola mitra supplier pengadaan gas</p>
    </div>
    @if(auth()->user() && auth()->user()->isAdmin())
        <a href="{{ route('suppliers.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:14px;height:14px;margin-right:2px;"></i> Add Supplier</a>
    @endif
</div>

@if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Supplier</th>
                    <th>No. Telepon</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            @forelse($suppliers as $s)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">{{ $s->id }}</td>
                    <td><span style="font-weight:600;color:#0f172a;">{{ $s->name }}</span></td>
                    <td>{{ $s->phone }}</td>
                    <td style="color:#64748b;">{{ $s->email ?? '-' }}</td>
                    <td style="color:#64748b;max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s->address }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('suppliers.edit', $s->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;vertical-align:middle;margin-top:-2px;"></i> Edit</a>
                                <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST" id="del-{{ $s->id }}" style="display:inline;">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="button" class="action-delete" onclick="confirmDelete({{ $s->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;vertical-align:middle;margin-top:-2px;"></i> Delete</button>
                                </form>
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon"><i data-lucide="store" style="width:48px;height:48px;margin:0 auto;color:#94a3b8;"></i></div>
                            <p>Belum ada data supplier</p>
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
function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus supplier?',
        text: 'Menghapus supplier juga akan menghapus data pembelian terkait!',
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
