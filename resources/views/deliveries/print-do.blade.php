<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan #DO-{{ str_pad($delivery->id, 5, '0', STR_PAD_LEFT) }}</title>
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
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            text-align: center;
            width: 190px;
        }
        .signature-line {
            margin-top: 65px;
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            font-size: 13px;
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
        .notes-box {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            color: #475569;
            margin-bottom: 30px;
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
            <i data-lucide="printer" style="width:16px;height:16px;margin-right:6px;"></i> Cetak Surat Jalan (DO)
        </button>

        <div class="header">
            <div>
                <h2>{{ config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA' }}</h2>
                <p style="margin: 0; color: #475569; font-size: 12px; max-width: 400px; line-height: 1.4;">
                    Perumahan Mutiara Sampurna Blok R4/19, Jayasampurna, Kec. Serang Baru, Kabupaten Bekasi, Jawa Barat 17330<br>
                    Distributor & Agen Resmi Gas Elpiji
                </p>
            </div>
            <div class="company-info">
                <h3 style="margin: 0; color: #0f172a;">SURAT JALAN PENGIRIMAN</h3>
                <p style="margin: 2px 0 0 0; font-size:12px; color: #64748b;">(SALES DELIVERY ORDER)</p>
                <p style="margin: 5px 0 0 0; color: #0f172a; font-weight:700;">No. DO: #DO-{{ str_pad($delivery->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="details-container">
            <div class="billing-info">
                <h4>Tujuan Pengiriman (Penerima):</h4>
                <strong>{{ $delivery->order->customer->customer_name ?? 'Pelanggan' }}</strong><br>
                Telepon: {{ $delivery->order->customer->phone ?? '-' }}<br>
                Alamat Tujuan: {{ $delivery->order->customer->address ?? '-' }}
            </div>
            <div class="meta-info">
                <h4>Detail Pengiriman & Armada:</h4>
                No. Order: #ORD-{{ str_pad($delivery->order_id, 5, '0', STR_PAD_LEFT) }}<br>
                Tanggal Pengiriman: {{ \Carbon\Carbon::parse($delivery->delivery_date)->format('d M Y') }}<br>
                Driver Petugas: <strong>{{ $delivery->driver->name ?? $delivery->driver_name }}</strong><br>
                No. Polisi / Kendaraan: <strong>{{ $delivery->driver->license_plate ?? '-' }}</strong><br>
                Status Pengiriman: 
                <span style="font-weight: 700; color: {{ $delivery->status === 'Delivered' ? '#15803d' : '#2563eb' }}">
                    {{ $delivery->status === 'Delivered' ? 'TELAH DITERIMA (Delivered)' : 'DALAM PENGIRIMAN (On Delivery)' }}
                </span>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 50px;">No.</th>
                    <th>Nama & Deskripsi Barang Gas</th>
                    <th style="text-align: center;">Kategori</th>
                    <th style="text-align: center;">Kuantitas (Qty)</th>
                    <th>Keterangan / Kondisi Tabung</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>{{ $delivery->order->product->name ?? 'Produk Gas' }}</strong>
                    </td>
                    <td style="text-align: center;">{{ $delivery->order->product->category ?? 'Gas Elpiji' }}</td>
                    <td style="text-align: center; font-weight:700; font-size:15px;">{{ $delivery->order->quantity }} Tabung</td>
                    <td>Segel utuh, kondisi tabung baik & siap pakai</td>
                </tr>
            </tbody>
        </table>

        <div class="notes-box">
            <strong>Catatan Pengiriman:</strong>
            <ol style="margin: 4px 0 0 0; padding-left: 18px;">
                <li>Harap periksa segel dan kondisi fisik tabung gas sebelum menandatangani Surat Jalan ini.</li>
                <li>Barang yang telah diterima dengan tanda tangan basah/stempel dianggap telah diterima dalam keadaan baik dan lengkap.</li>
            </ol>
        </div>

        <div class="footer">
            <div class="signature-block">
                <p>Pengirim / Petugas Gudang,</p>
                <div class="signature-line">{{ config('app.demo') ? 'PT XYZ' : 'TK. Naga Sakti Jaya' }}</div>
            </div>
            <div class="signature-block">
                <p>Driver / Pengantar,</p>
                <div class="signature-line">{{ $delivery->driver->name ?? $delivery->driver_name }}</div>
            </div>
            <div class="signature-block">
                <p>Penerima Barang,</p>
                <div class="signature-line">{{ $delivery->order->customer->customer_name ?? 'Pelanggan' }}</div>
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
