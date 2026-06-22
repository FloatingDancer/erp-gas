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
.badge-orange{background:#ffedd5;color:#c2410c;}
.badge-green{background:#dcfce7;color:#15803d;}
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
        <h1 class="page-title">🧾 Invoices</h1>
        <p class="page-subtitle">Kelola data faktur</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="btn-primary-custom">+ Add Invoice</a>
</div>

@if(session('success'))
    <div class="alert-success">✅ {{ session('success') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table" id="invoicesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice ID</th>
                    <th>Order</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">{{ $inv->id }}</td>
                    <td>
                        <span style="font-family:monospace;font-weight:700;color:#0f172a;font-size:13px;">
                            INV-{{ str_pad($inv->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td style="color:#64748b;">Order #{{ $inv->order->id }}</td>
                    <td style="font-weight:700;color:#0f172a;">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($inv->status === 'Unpaid')
                            <span class="badge-pill badge-orange">⏳ Unpaid</span>
                        @elseif($inv->status === 'Paid')
                            <span class="badge-pill badge-green">✅ Paid</span>
                        @else
                            <span class="badge-pill" style="background:#f1f5f9;color:#475569;">{{ $inv->status }}</span>
                        @endif
                    </td>
                    <td style="color:#64748b;">{{ \Carbon\Carbon::parse($inv->created_at)->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('invoices.edit', $inv->id) }}" class="action-edit">✏️ Edit</a>
                            <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;background:#f1f5f9;color:#374151;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;" title="Cetak Faktur">🖨 Print</a>
                            
                            @php
                                $customerName = $inv->order->customer->customer_name ?? 'Pelanggan';
                                $customerPhone = $inv->order->customer->phone ?? '';
                                $customerEmail = $inv->order->customer->email ?? '';
                                $productName = $inv->order->product->name ?? 'Produk Gas';
                                $qty = $inv->order->quantity ?? 0;
                                $total = number_format($inv->total_amount, 0, ',', '.');
                                $statusText = $inv->status === 'Paid' ? 'LUNAS (Paid)' : 'BELUM DIBAYAR (Unpaid)';
                                
                                $cleanPhone = preg_replace('/[^0-9]/', '', $customerPhone);
                                if (str_starts_with($cleanPhone, '0')) {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                                
                                $message = "Halo {$customerName},\n\nPerkenalkan, kami dari TK. NAGA SAKTI JAYA. Berikut adalah tagihan Invoice Anda dengan nomor #INV-" . str_pad($inv->id, 5, '0', STR_PAD_LEFT) . " untuk pembelian:\n- Produk: {$productName}\n- Qty: {$qty}\n- Total: Rp {$total}\n- Status: {$statusText}\n\nUnduh/cetak faktur PDF Anda melalui tautan berikut:\n" . route('invoices.print-public', $inv->id) . "\n\nSilakan lakukan pembayaran jika status masih Unpaid. Terima kasih!\n\nSalam hormat,\nTK. NAGA SAKTI JAYA";
                                $subject = "Faktur Tagihan #INV-" . str_pad($inv->id, 5, '0', STR_PAD_LEFT);
                            @endphp
                            
                            @if($cleanPhone)
                                <a href="https://api.whatsapp.com/send?phone={{ $cleanPhone }}&text={{ rawurlencode($message) }}" target="_blank" style="display:inline-flex;align-items:center;background:#dcfce7;color:#15803d;padding:6px 10px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;" title="Kirim via WhatsApp">💬 WA</a>
                            @endif

                            @if($customerEmail)
                                <button type="button" onclick="sendEmail('{{ $customerEmail }}', '{{ rawurlencode($subject) }}', '{{ rawurlencode($message) }}')" style="display:inline-flex;align-items:center;background:#dbeafe;color:#1d4ed8;border:none;padding:6px 10px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;" title="Kirim via Email">✉️ Email</button>
                             @endif

                            <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" id="del-{{ $inv->id }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="button" class="action-delete" onclick="confirmDelete({{ $inv->id }})">🗑 Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">🧾</div>
                            <p>Belum ada invoice</p>
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
        title:'Hapus invoice?', text:'Data akan dihapus permanen!', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal'
    }).then(r => { if(r.isConfirmed) document.getElementById('del-'+id).submit(); });
}

function sendEmail(email, rawSubject, rawBody) {
    const subject = decodeURIComponent(rawSubject);
    const body = decodeURIComponent(rawBody);

    Swal.fire({
        title: 'Kirim Invoice via Email',
        text: 'Pilih metode pengiriman untuk email ke ' + email + ':',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: '🌐 Gmail (Web)',
        denyButtonText: '✉️ Aplikasi Default (Mailto)',
        cancelButtonText: '📋 Salin Teks Invoice',
        confirmButtonColor: '#2563eb',
        denyButtonColor: '#475569',
        cancelButtonColor: '#10b981',
    }).then((result) => {
        if (result.isConfirmed) {
            // Gmail Web
            const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(email)}&su=${rawSubject}&body=${rawBody}`;
            window.open(gmailUrl, '_blank');
        } else if (result.isDenied) {
            // Mailto
            const mailtoUrl = `mailto:${email}?subject=${rawSubject}&body=${rawBody}`;
            window.location.href = mailtoUrl;
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Salin Teks
            navigator.clipboard.writeText(body).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Disalin!',
                    text: 'Teks invoice berhasil disalin ke clipboard.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Gagal menyalin teks. Silakan salin secara manual.'
                });
            });
        }
    });
}

$(document).ready(function() {
    $('#invoicesTable').DataTable({
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