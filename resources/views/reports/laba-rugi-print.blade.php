<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }
        .report-box {
            max-width: 800px;
            margin: auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            color: #0f172a;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #475569;
            font-size: 11px;
        }
        .title-section {
            text-align: center;
            margin-bottom: 24px;
        }
        .title-section h3 {
            margin: 0 0 4px 0;
            font-size: 15px;
            color: #1e293b;
        }
        .title-section p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
            font-style: italic;
        }
        table.financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.financial-table th {
            border-bottom: 2px solid #000;
            border-top: 1px solid #000;
            padding: 8px 12px;
            font-weight: 700;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        table.financial-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .section-header-row {
            background-color: #f8fafc;
            font-weight: 700;
        }
        .section-header-row td {
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            color: #0f172a;
        }
        .total-row {
            font-weight: 700;
        }
        .total-row td {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
        .net-profit-row {
            font-weight: 700;
            font-size: 14px;
        }
        .net-profit-row td {
            border-top: 2px double #000;
            border-bottom: 2px double #000;
            padding: 10px 12px;
        }
        .footer {
            margin-top: 48px;
            display: flex;
            justify-content: space-between;
        }
        .signature-block {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .no-print-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
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
    <div class="report-box">
        <button class="no-print-btn" onclick="window.print()" style="display:inline-flex;align-items:center;"><i data-lucide="printer" style="width:15px;height:15px;margin-right:6px;"></i> Cetak Laporan</button>

        <div class="header">
            <h2>{{ config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA' }}</h2>
            <p>Perumahan Mutiara Sampurna Blok R4/19, Jayasampurna, Kec. Serang Baru, Kabupaten Bekasi, Jawa Barat 17330</p>
            <p>Telepon: 08123456789 | Email: contact@nagasaktijaya.my.id</p>
        </div>

        <div class="title-section">
            <h3>LAPORAN LABA RUGI</h3>
            <p>Untuk Periode: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</p>
        </div>

        <table class="financial-table">
            <thead>
                <tr>
                    <th>Keterangan / Deskripsi Produk</th>
                    <th style="text-align: center; width: 120px;">Volume (Qty)</th>
                    <th style="text-align: right; width: 200px;">Jumlah Rupiah</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. REVENUES --}}
                <tr class="section-header-row">
                    <td colspan="3">PENDAPATAN USAHA (REVENUES)</td>
                </tr>
                @forelse($revenueDetails as $prodName => $det)
                    <tr>
                        <td style="padding-left: 24px;">Penjualan {{ $prodName }}</td>
                        <td style="text-align: center;">{{ $det['qty'] }} tabung</td>
                        <td style="text-align: right;">Rp {{ number_format($det['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding-left: 24px; color: #64748b; font-style: italic;">Tidak ada transaksi pendapatan penjualan</td>
                    </tr>
                @endforelse
                <tr class="total-row" style="color: #15803d;">
                    <td style="padding-left: 24px;">TOTAL PENDAPATAN</td>
                    <td></td>
                    <td style="text-align: right;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>

                {{-- Empty Spacer Row --}}
                <tr><td colspan="3" style="height: 12px; border: none;"></td></tr>

                {{-- 2. EXPENSES --}}
                <tr class="section-header-row">
                    <td colspan="3">Beban Usaha / Pengadaan Stok (EXPENSES)</td>
                </tr>
                @forelse($expenseDetails as $prodName => $det)
                    <tr>
                        <td style="padding-left: 24px;">Pembelian {{ $prodName }}</td>
                        <td style="text-align: center;">{{ $det['qty'] }} tabung</td>
                        <td style="text-align: right;">Rp {{ number_format($det['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding-left: 24px; color: #64748b; font-style: italic;">Tidak ada transaksi pengadaan barang</td>
                    </tr>
                @endforelse
                <tr class="total-row" style="color: #b91c1c;">
                    <td style="padding-left: 24px;">TOTAL PENGELUARAN</td>
                    <td></td>
                    <td style="text-align: right;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>

                {{-- Empty Spacer Row --}}
                <tr><td colspan="3" style="height: 24px; border: none;"></td></tr>

                {{-- 3. NET PROFIT --}}
                @php
                    $isProfit = $netProfit >= 0;
                    $profitLabel = $isProfit ? 'LABA BERSIH (NET PROFIT)' : 'RUGI BERSIH (NET LOSS)';
                    $profitColor = $isProfit ? '#15803d' : '#b91c1c';
                @endphp
                <tr class="net-profit-row" style="color: {{ $profitColor }}; background-color: #f8fafc;">
                    <td>{{ $profitLabel }}</td>
                    <td></td>
                    <td style="text-align: right; border-bottom: 2px double {{ $profitColor }};">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <div class="signature-block">
                <p>Dilaporkan Oleh,</p>
                <div class="signature-line">Administrasi Keuangan</div>
            </div>
            <div class="signature-block">
                <p>Mengetahui,</p>
                <div class="signature-line">Manager Toko</div>
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
