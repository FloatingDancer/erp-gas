<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</title>
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
        <button class="no-print-btn" onclick="window.print()">🖨 Cetak Halaman Ini</button>

        <div class="header">
            <div>
                <h2>⛽ NAGA SAKTI JAYA</h2>
                <p style="margin: 0; color: #475569; font-size: 12px; max-width: 400px; line-height: 1.4;">
                    Perumahan Mutiara Sampurna Blok R4/19, Jayasampurna, Kec. Serang Baru, Kabupaten Bekasi, Jawa Barat 17330
                </p>
            </div>
            <div class="company-info">
                <h3 style="margin: 0; color: #0f172a;">FAKTUR PENJUALAN</h3>
                <p style="margin: 5px 0 0 0; color: #64748b;">No: #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="details-container">
            <div class="billing-info">
                <h4>Ditagihkan Kepada:</h4>
                <strong>{{ $invoice->order->customer->customer_name }}</strong><br>
                Telepon: {{ $invoice->order->customer->phone }}<br>
                Alamat: {{ $invoice->order->customer->address }}
            </div>
            <div class="meta-info">
                <h4>Detail Faktur:</h4>
                Tanggal Faktur: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}<br>
                No. Order: #ORD-{{ str_pad($invoice->order_id, 5, '0', STR_PAD_LEFT) }}<br>
                Status Pembayaran: 
                <span style="font-weight: 700; color: {{ $invoice->status === 'Paid' ? '#15803d' : '#b91c1c' }}">
                    {{ $invoice->status === 'Paid' ? 'LUNAS (Paid)' : 'BELUM DIBAYAR (Unpaid)' }}
                </span>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Deskripsi Produk</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: center;">Jumlah (Qty)</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->order->product->name }} ({{ $invoice->order->product->category }})</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->order->product->price, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $invoice->order->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($invoice->order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="totals-row">
                    <span>Pajak (0%)</span>
                    <span>Rp 0</span>
                </div>
                <div class="totals-row grand-total">
                    <span>Total Tagihan</span>
                    <span>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p>Penerima,</p>
                <div class="signature-line">Pelanggan</div>
            </div>
            <div class="signature-block">
                <p>Hormat Kami,</p>
                <div class="signature-line">TK. Naga Sakti Jaya</div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            // Otomatis cetak saat halaman terbuka
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
