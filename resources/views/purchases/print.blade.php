<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order #PO-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</title>
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
            width: 300px;
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
        <button class="no-print-btn" onclick="window.print()" style="display:inline-flex;align-items:center;"><i data-lucide="printer" style="width:16px;height:16px;margin-right:6px;"></i> Cetak Halaman Ini</button>

        <div class="header">
            <div>
                <h2>{{ config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA' }}</h2>
                <p style="margin: 0; color: #475569; font-size: 12px; max-width: 400px; line-height: 1.4;">
                    Perumahan Mutiara Sampurna Blok R4/19, Jayasampurna, Kec. Serang Baru, Kabupaten Bekasi, Jawa Barat 17330
                </p>
            </div>
            <div class="company-info">
                <h3 style="margin: 0; color: #0f172a;">PURCHASE ORDER</h3>
                <p style="margin: 5px 0 0 0; color: #64748b;">No: #PO-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="details-container">
            <div class="billing-info">
                <h4>Supplier Mitra:</h4>
                <strong>{{ $purchase->supplier->name ?? 'Supplier' }}</strong><br>
                Telepon: {{ $purchase->supplier->phone ?? '-' }}<br>
                Email: {{ $purchase->supplier->email ?? '-' }}<br>
                Alamat: {{ $purchase->supplier->address ?? '-' }}
            </div>
            <div class="meta-info">
                <h4>Detail PO / Faktur Pembelian:</h4>
                Tanggal PO: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}<br>
                Status Transaksi: 
                <span style="font-weight: 700; color: {{ $purchase->status === 'Received' ? '#15803d' : '#c2410c' }}">
                    {{ $purchase->status === 'Received' ? 'DITERIMA (Received)' : 'PENDING' }}
                </span><br>
                Tujuan Pengiriman: TK. NAGA SAKTI JAYA Gudang
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th style="text-align: right;">Harga Satuan Beli</th>
                    <th style="text-align: center;">Jumlah (Qty)</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $purchase->product->name ?? 'Produk Gas' }} ({{ $purchase->product->category ?? '-' }})</td>
                    <td style="text-align: right;">Rp {{ number_format($purchase->purchase_price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $purchase->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="totals-row">
                    <span>Pajak (0%)</span>
                    <span>Rp 0</span>
                </div>
                <div class="totals-row grand-total">
                    <span>Total Pembelian</span>
                    <span>Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p>Hormat Kami (Manager),</p>
                <div class="signature-line">{{ config('app.demo') ? 'PT XYZ' : 'TK. Naga Sakti Jaya' }}</div>
            </div>
            <div class="signature-block">
                <p>Mitra Supplier,</p>
                <div class="signature-line">{{ $purchase->supplier->name ?? 'Supplier' }}</div>
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
