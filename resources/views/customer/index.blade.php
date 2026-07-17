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
.badge-blue { background:#dbeafe; color:#1d4ed8; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-orange { background:#ffedd5; color:#c2410c; }
.badge-red { background:#fee2e2; color:#b91c1c; }
.badge-gray { background:#f1f5f9; color:#475569; }
.action-edit { display:inline-flex; align-items:center; gap:4px; background:#fef9c3; color:#854d0e; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.action-edit:hover { background:#fef08a; color:#713f12; }
.action-delete { display:inline-flex; align-items:center; gap:4px; background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; transition:background 0.15s; }
.action-delete:hover { background:#fecaca; color:#991b1b; }
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:680px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:48px; margin-bottom:12px; }
.empty-state p { font-size:14px; margin:0; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="users" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Customers</h1>
        <p class="page-subtitle">Kelola data pelanggan</p>
    </div>
    <a href="{{ route('customers.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Add Customer</a>
</div>

@if(session('success'))
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table">
            <thead><tr>
                <th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Action</th>
            </tr></thead>
            <tbody>
            @forelse($customers as $c)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">{{ $c->id }}</td>
                    <td><span style="font-weight:600;color:#0f172a;">{{ $c->customer_name }}</span></td>
                    <td style="color:#64748b;">{{ $c->email }}</td>
                    <td>{{ $c->phone }}</td>
                    <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        @if($c->latitude && $c->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $c->latitude }},{{ $c->longitude }}" target="_blank" style="color: #2563eb; text-decoration: none; font-weight: 500;" title="Buka di Google Maps">
                                <i data-lucide="map-pin" style="width:13px;height:13px;vertical-align:middle;margin-top:-2px;margin-right:2px;color:#ef4444;"></i>{{ $c->address }}
                            </a>
                        @else
                            <span style="color:#64748b;">{{ $c->address }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('customers.edit', $c->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;margin-right:2px;"></i> Edit</a>
                            <form action="{{ route('customers.destroy', $c->id) }}" method="POST" id="del-{{ $c->id }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="button" class="action-delete" onclick="confirmDelete({{ $c->id }},'customer')"><i data-lucide="trash-2" style="width:13px;height:13px;margin-right:2px;"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="users" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div><p>Belum ada customer</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 2000,
        showConfirmButton: false
    });
@endif

function confirmDelete(id, type) {
    Swal.fire({ title:'Hapus data?', text:'Data akan dihapus permanen!', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal'
    }).then(r => { if(r.isConfirmed) document.getElementById('del-'+id).submit(); });
}
</script>
@endsection