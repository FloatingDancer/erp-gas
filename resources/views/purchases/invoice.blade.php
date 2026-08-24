<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Pembelian #PINV-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</title>
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
            <i data-lucide="printer" style="width:16px;height:16px;margin-right:6px;"></i> Cetak Faktur Pembelian
        </button>

        <div class="header">
            <div>
                <h2>{{ $purchase->supplier->name ?? 'MITRA SUPPLIER' }}</h2>
                <p style="margin: 0; color: #475569; font-size: 12px; max-width: 400px; line-height: 1.4;">
                    {{ $purchase->supplier->address ?? 'Alamat Supplier Mitra' }}<br>
                    Telepon: {{ $purchase->supplier->phone ?? '-' }} | Email: {{ $purchase->supplier->email ?? '-' }}
                </p>
            </div>
            <div class="company-info">
                <h3 style="margin: 0; color: #0f172a;">FAKTUR PEMBELIAN</h3>
                <p style="margin: 2px 0 0 0; font-size:12px; color: #64748b;">(PURCHASE INVOICE)</p>
                <p style="margin: 5px 0 0 0; color: #0f172a; font-weight:700;">No: #PINV-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="details-container">
            <div class="billing-info">
                <h4>Ditagihkan Kepada Pembeli:</h4>
                <strong>{{ config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA' }}</strong><br>
                Perumahan Mutiara Sampurna Blok R4/19, Jayasampurna<br>
                Kec. Serang Baru, Kabupaten Bekasi, Jawa Barat 17330<br>
                Kategori Bisnis: Pangkalan & Distributor Gas Elpiji
            </div>
            <div class="meta-info">
                <h4>Detail Faktur & Pengadaan:</h4>
                No. Purchase Order: #PO-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}<br>
                Tanggal Faktur: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}<br>
                Status Barang: 
                <span style="font-weight: 700; color: {{ $purchase->status === 'Received' ? '#15803d' : '#c2410c' }}">
                    {{ $purchase->status === 'Received' ? 'DITERIMA DI GUDANG (Received)' : 'PENDING PENGIRIMAN' }}
                </span><br>
                Status Pembayaran: <span style="font-weight: 700; color: #15803d;">LUNAS / TERCATAT</span>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item & Spesifikasi Produk Gas</th>
                    <th style="text-align: right;">Harga Beli Satuan</th>
                    <th style="text-align: center;">Jumlah (Qty)</th>
                    <th style="text-align: right;">Total Nominal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $purchase->product->name ?? 'Produk Gas' }}</strong>
                        <div style="font-size:12px;color:#64748b;">Kategori: {{ $purchase->product->category ?? 'Gas Tabung' }}</div>
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($purchase->purchase_price, 0, ',', '.') }}</td>
                    <td style="text-align: center; font-weight:700;">{{ $purchase->quantity }} Tabung</td>
                    <td style="text-align: right; font-weight:700; color:#0f172a;">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal Pembelian</span>
                    <span>Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="totals-row">
                    <span>PPN / Pajak (0%)</span>
                    <span>Rp 0</span>
                </div>
                <div class="totals-row grand-total">
                    <span>Total Tagihan Pembelian</span>
                    <span>Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p>Mitra Supplier (Penerbit),</p>
                <div class="signature-line">{{ $purchase->supplier->name ?? 'Supplier' }}</div>
            </div>
            <div class="signature-block">
                <p>Diterima Oleh (Manager/Admin),</p>
                <div class="signature-line">{{ config('app.demo') ? 'PT XYZ' : 'TK. Naga Sakti Jaya' }}</div>
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
