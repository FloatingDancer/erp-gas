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
.badge-green{background:#dcfce7;color:#15803d;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-purple{background:#ede9fe;color:#6d28d9;}
.badge-gray{background:#f1f5f9;color:#475569;}
.action-edit{display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.action-edit:hover{background:#fef08a;}
.action-delete{display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#b91c1c;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;cursor:pointer;}
.action-delete:hover{background:#fecaca;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:20px;}
.empty-state{text-align:center;padding:60px 20px;color:#94a3b8;}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px;}
.empty-state p{font-size:14px;margin:0;}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="credit-card" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Payments</h1>
        <p class="page-subtitle">Kelola data pembayaran</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Add Payment</a>
</div>

@if(session('success'))
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table" id="paymentsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment ID</th>
                    <th>Invoice</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payments as $pay)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">{{ $pay->id }}</td>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:#0f172a;font-size:13px;">
                            PAY-{{ str_pad($pay->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td style="color:#64748b;">INV-{{ str_pad($pay->invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight:700;color:#0f172a;">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($pay->method === 'Cash')
                            <span class="badge-pill badge-green"><i data-lucide="banknote" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Cash</span>
                        @elseif($pay->method === 'Transfer')
                            <span class="badge-pill badge-blue"><i data-lucide="landmark" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Transfer</span>
                        @elseif($pay->method === 'E-Wallet')
                            <span class="badge-pill badge-purple"><i data-lucide="smartphone" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> E-Wallet</span>
                        @else
                            <span class="badge-pill badge-gray">{{ $pay->method }}</span>
                        @endif
                    </td>
                    <td style="color:#64748b;">{{ \Carbon\Carbon::parse($pay->created_at)->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('payments.edit', $pay->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;margin-right:2px;"></i> Edit</a>
                            <form action="{{ route('payments.destroy', $pay->id) }}" method="POST" id="del-{{ $pay->id }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="button" class="action-delete" onclick="confirmDelete({{ $pay->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;margin-right:2px;"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="credit-card" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                            <p>Belum ada data pembayaran</p>
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
Swal.fire({icon:'success',title:'Berhasil!',text:'{{ session('success') }}',timer:2000,showConfirmButton:false});
</script>
@endif
<script>
function confirmDelete(id) {
    Swal.fire({
        title:'Hapus payment?', text:'Data akan dihapus permanen!', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal'
    }).then(r => { if(r.isConfirmed) document.getElementById('del-'+id).submit(); });
}

$(document).ready(function() {
    $('#paymentsTable').DataTable({
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