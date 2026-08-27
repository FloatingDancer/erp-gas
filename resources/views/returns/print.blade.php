<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Retur Barang #{{ $return->return_number }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0 0 10px 0;
            color: #0f172a;
        }
        .company-info {
            text-align: right;
        }
        .details-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .billing-info, .meta-info {
            width: 48%;
        }
        .billing-info h4, .meta-info h4 {
            margin: 0 0 10px 0;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 5px;
            color: #475569;
        }
        table.invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.invoice-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            padding: 12px;
            border-bottom: 2px solid #eaeaea;
            text-align: left;
        }
        table.invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #eaeaea;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }
        .totals-box {
            width: 320px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .totals-row.grand-total {
            border-top: 2px solid #eaeaea;
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
        }
        .no-print-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 20px;
            display: inline-block;
        }
        @media print {
            .no-print-btn {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <button class="no-print-btn" onclick="window.print()" style="display:inline-flex;align-items:center;">
            <i data-lucide="printer" style="width:16px;height:16px;margin-right:6px;"></i> Cetak Nota Retur
        </button>

        <div class="header">
            <div>
                <h2>{{ config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA' }}</h2>
                <p style="margin: 0; color: #475569; font-size: 12px; max-width: 400px; line-height: 1.4;">
                    Perumahan Mutiara Sampurna Blok R4/19, Jayasampurna, Kec. Serang Baru, Kabupaten Bekasi, Jawa Barat 17330
                </p>
            </div>
            <div class="company-info">
                <h3 style="margin: 0; color: #0f172a;">NOTA RETUR BARANG</h3>
                <p style="margin: 5px 0 0 0; color: #64748b;">No: {{ $return->return_number }}</p>
            </div>
        </div>

        <div class="details-container">
            <div class="billing-info">
                @if(($return->return_category ?? 'Customer') === 'Supplier')
                    <h4>Data Supplier (Pihak Tujuan Retur):</h4>
                    <strong>{{ $return->supplier->name ?? 'Supplier' }}</strong><br>
                    Telepon: {{ $return->supplier->phone ?? '-' }}<br>
                    Alamat: {{ $return->supplier->address ?? '-' }}
                @else
                    <h4>Data Pelanggan:</h4>
                    <strong>{{ $return->customer->customer_name ?? 'Pelanggan' }}</strong><br>
                    Telepon: {{ $return->customer->phone ?? '-' }}<br>
                    Alamat: {{ $return->customer->address ?? '-' }}
                @endif
            </div>
            <div class="meta-info">
                <h4>Detail Dokumen Retur:</h4>
                Kategori Retur: <strong>{{ ($return->return_category ?? 'Customer') === 'Supplier' ? 'Retur ke Supplier (Purchase Return)' : 'Retur dari Pelanggan (Sales Return)' }}</strong><br>
                Tanggal Retur: {{ \Carbon\Carbon::parse($return->return_date)->format('d M Y') }}<br>
                @if($return->purchase_id)
                    Ref Pembelian: PO #{{ $return->purchase_id }}<br>
                @elseif($return->order_id)
                    Ref Order: Order #{{ $return->order_id }}<br>
                @endif
                @if($return->delivery_id)
                    Ref Delivery: Pengiriman #{{ $return->delivery_id }}<br>
                @endif
                Status Retur: 
                <span style="font-weight: 700; color: {{ $return->status === 'Approved' ? '#15803d' : '#c2410c' }}">
                    {{ $return->status === 'Approved' ? 'DISETUJUI (Approved)' : 'PENDING' }}
                </span>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th style="text-align: center;">Jumlah (Qty)</th>
                    <th style="text-align: center;">Kondisi Barang</th>
                    <th>Jenis Penyelesaian</th>
                    <th style="text-align: right;">Nominal Refund</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $return->product->name ?? 'Produk Gas' }}</strong> ({{ $return->product->category ?? '-' }})
                        @if($return->reason)
                            <div style="font-size:12px;color:#64748b;margin-top:4px;"><em>Catatan/Kerusakan: {{ $return->reason }}</em></div>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight:700;">{{ $return->quantity }} tabung</td>
                    <td style="text-align: center;">
                        <span style="font-weight:600; color: {{ $return->condition === 'Good' ? '#15803d' : '#b91c1c' }};">
                            {{ $return->condition === 'Good' ? 'Bagus / Utuh' : 'Rusak / Cacat / Bocor' }}
                        </span>
                    </td>
                    <td>
                        <strong>
                            @if($return->return_type === 'Exchange')
                                Ganti Barang Baru (Exchange)
                            @elseif($return->return_type === 'Refund')
                                Pengembalian Dana (Refund)
                            @else
                                Potong Nota / Tagihan (Credit)
                            @endif
                        </strong>
                    </td>
                    <td style="text-align: right; font-weight:700; color:#0f172a;">
                        Rp {{ number_format($return->refund_amount ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span style="color:#64748b;">Jumlah Unit Retur:</span>
                    <strong>{{ $return->quantity }} Tabung</strong>
                </div>
                <div class="totals-row grand-total">
                    <span>Total Klaim / Refund:</span>
                    <span style="color:#2563eb;">Rp {{ number_format($return->refund_amount ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p style="margin-bottom: 0;">{{ ($return->return_category ?? 'Customer') === 'Supplier' ? 'Pihak Supplier,' : 'Pelanggan / Penerima,' }}</p>
                <div class="signature-line">
                    ( {{ ($return->return_category ?? 'Customer') === 'Supplier' ? ($return->supplier->name ?? 'Supplier') : ($return->customer->customer_name ?? 'Pelanggan') }} )
                </div>
            </div>
            <div class="signature-block">
                <p style="margin-bottom: 0;">Bagian Gudang / Kasir,</p>
                <div class="signature-line">
                    ( {{ auth()->user()->name ?? 'Petugas Toko' }} )
                </div>
            </div>
            <div class="signature-block">
                <p style="margin-bottom: 0;">Manager / Pemilik,</p>
                <div class="signature-line">
                    ( ........................................... )
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
