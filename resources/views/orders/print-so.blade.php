<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order #SO-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            padding: 30px;
            font-size: 13.5px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .company-info h2 {
            font-size: 20px;
            color: #1e3a8a;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .company-info p {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
            margin-top: 4px;
        }
        .doc-info {
            text-align: right;
        }
        .doc-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-number {
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
            margin-top: 2px;
        }
        .doc-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            margin-top: 6px;
            text-transform: uppercase;
        }
        .badge-pending { background: #ffedd5; color: #c2410c; }
        .badge-completed { background: #dcfce7; color: #15803d; }
        
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            background: #f8fafc;
            padding: 16px 20px;
            border-radius: 10px;
            border: 1px solid #f1f5f9;
            margin-bottom: 24px;
        }
        .meta-group h4 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .meta-group p {
            font-size: 13.5px;
            color: #0f172a;
            line-height: 1.4;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.items-table th {
            background: #f1f5f9;
            padding: 10px 14px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        table.items-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .summary-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .summary-box {
            width: 320px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: #475569;
        }
        .summary-row.total {
            border-top: 2px solid #0f172a;
            padding-top: 10px;
            margin-top: 6px;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .terms-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 36px;
            font-size: 12px;
            color: #1e40af;
            line-height: 1.5;
        }
        .terms-box strong {
            display: block;
            margin-bottom: 4px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 10px;
        }
        .sig-block {
            text-align: center;
            width: 220px;
        }
        .sig-space {
            height: 70px;
        }
        .sig-line {
            border-top: 1.5px solid #334155;
            padding-top: 6px;
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
        }
        .sig-role {
            font-size: 11.5px;
            color: #64748b;
        }

        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back {
            background: #e2e8f0;
            color: #334155;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media print {
            body { background: white; padding: 0; }
            .container { box-shadow: none; padding: 0; }
            .no-print-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="{{ route('orders.index') }}" class="btn-back"><i data-lucide="arrow-left" style="width:15px;height:15px;"></i> Kembali ke Daftar Order</a>
        <button class="btn-print" onclick="window.print()"><i data-lucide="printer" style="width:15px;height:15px;"></i> Cetak Sales Order</button>
    </div>

    <div class="container">
        {{-- Header --}}
        <div class="header">
            <div class="company-info">
                <h2>{{ config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA' }}</h2>
                <p>Distributor Resmi Gas LPG & Bright Gas</p>
                <p>Perumahan Mutiara Sampurna Blok R4/19, Kec. Serang Baru, Kab. Bekasi 17330</p>
                <p>Telepon: 0812-3456-789 | Email: sales@nagasaktijaya.my.id</p>
            </div>
            <div class="doc-info">
                <div class="doc-title">SALES ORDER</div>
                <div class="doc-number">#SO-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div>
                    @if($order->status === 'Completed')
                        <span class="doc-badge badge-completed">Status: Selesai (Completed)</span>
                    @else
                        <span class="doc-badge badge-pending">Status: Diproses (Pending)</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Meta Details --}}
        <div class="meta-grid">
            <div class="meta-group">
                <h4>Data Pelanggan (Pemesan):</h4>
                <p style="font-weight:700;color:#0f172a;">{{ $order->customer->customer_name ?? 'Pelanggan Umum' }}</p>
                <p>{{ $order->customer->address ?? 'Alamat tidak tersedia' }}</p>
                <p style="color:#64748b;">No. Telp / WA: {{ $order->customer->phone ?? '-' }}</p>
            </div>
            <div class="meta-group">
                <h4>Informasi Surat Pesanan:</h4>
                <p><strong>Tanggal Order:</strong> {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d F Y') : \Carbon\Carbon::parse($order->created_at)->format('d F Y') }}</p>
                <p><strong>Waktu Pencatatan:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }} WIB</p>
                <p><strong>Metode Penagihan:</strong> Faktur Penjualan (Sales Invoice)</p>
            </div>
        </div>

        {{-- Order Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">No</th>
                    <th>Nama Produk / Tabung Gas</th>
                    <th style="text-align: center; width: 100px;">Kategori</th>
                    <th style="text-align: center; width: 90px;">Jumlah</th>
                    <th style="text-align: right; width: 140px;">Harga Satuan</th>
                    <th style="text-align: right; width: 150px;">Total (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center; font-weight: 600;">1</td>
                    <td style="font-weight: 700; color: #0f172a;">
                        {{ $order->product->name ?? 'Produk Gas' }}
                    </td>
                    <td style="text-align: center;">
                        <span style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:11px;font-weight:600;">{{ $order->product->category ?? 'Gas' }}</span>
                    </td>
                    <td style="text-align: center; font-weight: 700; font-size: 14px;">
                        {{ $order->quantity }} Tabung
                    </td>
                    <td style="text-align: right;">
                        Rp {{ number_format($order->product->price ?? ($order->total_amount / max(1, $order->quantity)), 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #0f172a;">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Summary --}}
        <div class="summary-wrap">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal Pemesanan:</span>
                    <span style="font-weight:600;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Biaya Pengiriman (Ongkir):</span>
                    <span style="font-weight:600;color:#15803d;">Rp 0 (Gratis)</span>
                </div>
                <div class="summary-row">
                    <span>Pajak (PPN):</span>
                    <span style="font-weight:600;">Rp 0</span>
                </div>
                <div class="summary-row total">
                    <span>TOTAL SALES ORDER:</span>
                    <span style="color:#2563eb;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Terms --}}
        <div class="terms-box">
            <strong><i data-lucide="info" style="width:13px;height:13px;vertical-align:middle;margin-right:2px;"></i> Catatan & Ketentuan Sales Order:</strong>
            <ul style="margin: 4px 0 0; padding-left: 16px;">
                <li>Dokumen ini sah sebagai permohonan pemesanan penjualan (*Sales Order*) resmi dari pelanggan.</li>
                <li>Pengiriman fisik tabung gas diproses menggunakan <strong>Surat Jalan Pengiriman (Sales Delivery Order)</strong>.</li>
                <li>Bukti pembayaran dan penagihan resmi dicatat melalui <strong>Faktur Penjualan (Sales Invoice)</strong>.</li>
            </ul>
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="sig-block">
                <p class="sig-role">Pelanggan / Pemesan,</p>
                <div class="sig-space"></div>
                <div class="sig-line">{{ $order->customer->customer_name ?? 'Pelanggan' }}</div>
            </div>
            <div class="sig-block">
                <p class="sig-role">Hormat Kami (Admin Sales),</p>
                <div class="sig-space"></div>
                <div class="sig-line">TK. NAGA SAKTI JAYA</div>
            </div>
        </div>
    </div>

    <script>
        if (typeof lucide !== 'undefined') lucide.createIcons();
    </script>
</body>
</html>
