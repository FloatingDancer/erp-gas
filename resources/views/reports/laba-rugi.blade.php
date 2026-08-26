@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
.btn-secondary-custom { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#374151; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-secondary-custom:hover { background:#e2e8f0; color:#111827; }
.btn-success-custom { display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-success-custom:hover { background:#15803d; color:white; }
.card-clean { background:white; border-radius:16px; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:24px; margin-bottom:24px; }
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:16px; margin-bottom:24px; }
.kpi-card { background:white; border-radius:14px; padding:18px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #f1f5f9; display:flex; align-items:center; gap:14px; }
.kpi-icon { width:46px; height:46px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.kpi-title { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; margin:0; letter-spacing:0.5px; }
.kpi-value { font-size:18px; font-weight:700; color:#0f172a; margin:3px 0 0 0; }
.kpi-sub { font-size:11.5px; color:#64748b; margin-top:2px; }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; white-space:nowrap; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.12s; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:12px 16px; font-size:13px; color:#374151; vertical-align:middle; }
.grid-3col { display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:20px; }
.form-input { padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:13.5px; color:#1e293b; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; }
.section-title { font-size:15px; font-weight:700; color:#1e293b; margin:0 0 14px 0; display:flex; align-items:center; gap:8px; }
.statement-table { width:100%; border-collapse:collapse; margin-top:10px; }
.statement-table td { padding:10px 14px; font-size:13.5px; border-bottom:1px solid #f1f5f9; }
.statement-table .header-row td { background:#f8fafc; font-weight:700; color:#0f172a; font-size:12.5px; text-transform:uppercase; letter-spacing:0.5px; border-top:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; }
.statement-table .subtotal-row td { font-weight:700; color:#0f172a; background:#fafafa; border-top:1px solid #cbd5e1; border-bottom:1px solid #cbd5e1; }
.statement-table .final-row td { font-weight:800; font-size:15px; background:#f0fdf4; border-top:2px double #0f172a; border-bottom:2px double #0f172a; }
.badge-pill { display:inline-block; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-red { background:#fee2e2; color:#b91c1c; }
.badge-blue { background:#dbeafe; color:#1d4ed8; }
.badge-orange { background:#ffedd5; color:#c2410c; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="trending-up" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Profit & Loss (Laporan Laba Rugi)</h1>
        <p class="page-subtitle">Modul Akuntansi & Keuangan — Laporan Kinerja Finansial, HPP, Retur Penjualan & Profitabilitas</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('reports.laba-rugi.export-csv', ['month' => $month, 'year' => $year]) }}" class="btn-success-custom">
            <i data-lucide="download" style="width:14px;height:14px;vertical-align:middle;"></i> Ekspor Excel / CSV
        </a>
        <a href="{{ route('reports.laba-rugi.print', ['month' => $month, 'year' => $year]) }}" target="_blank" class="btn-secondary-custom">
            <i data-lucide="printer" style="width:14px;height:14px;vertical-align:middle;"></i> Cetak Laporan
        </a>
    </div>
</div>

{{-- Filter Card --}}
<div class="card-clean" style="padding: 16px 24px;">
    <form method="GET" action="{{ route('reports.laba-rugi') }}" style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:13px; font-weight:600; color:#475569;">Bulan:</label>
            <select name="month" class="form-input" required>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:13px; font-weight:600; color:#475569;">Tahun:</label>
            <select name="year" class="form-input" required>
                @for ($y = now()->year - 3; $y <= now()->year + 2; $y++)
                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn-primary-custom" style="padding: 8px 16px;">
            <i data-lucide="filter" style="width:14px;height:14px;"></i> Tampilkan Laporan
        </button>
    </form>
</div>

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
            <i data-lucide="dollar-sign" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <p class="kpi-title">Penjualan Bersih (Net Sales)</p>
            <p class="kpi-value" style="color:#15803d;">Rp {{ number_format($totalNetRevenue, 0, ',', '.') }}</p>
            <p class="kpi-sub">{{ $totalUnitsSold }} tabung terjual @if($totalRefundAmount > 0)· <span style="color:#b91c1c;">-Rp {{ number_format($totalRefundAmount, 0, ',', '.') }} Retur</span>@endif</p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f1f5f9; color:#475569;">
            <i data-lucide="boxes" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <p class="kpi-title">HPP (Modal Tabung Terjual)</p>
            <p class="kpi-value">Rp {{ number_format($totalCOGS, 0, ',', '.') }}</p>
            <p class="kpi-sub">Cost of Goods Sold (COGS)</p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dbeafe; color:#1d4ed8;">
            <i data-lucide="pie-chart" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <p class="kpi-title">Laba Kotor (Gross Profit)</p>
            <p class="kpi-value" style="color:#1d4ed8;">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
            <p class="kpi-sub">Profit Margin: <strong>{{ $grossProfitMargin }}%</strong></p>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fee2e2; color:#b91c1c;">
            <i data-lucide="shopping-cart" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <p class="kpi-title">Pengadaan Stok (Purchases)</p>
            <p class="kpi-value" style="color:#b91c1c;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            <p class="kpi-sub">{{ $totalUnitsPurchased }} tabung masuk</p>
        </div>
    </div>

    @php
        $isProfit = $netProfit >= 0;
        $profitBg = $isProfit ? '#dcfce7' : '#fee2e2';
        $profitColor = $isProfit ? '#15803d' : '#b91c1c';
    @endphp
    <div class="kpi-card" style="border: 2px solid {{ $profitColor }};">
        <div class="kpi-icon" style="background: {{ $profitBg }}; color: {{ $profitColor }};">
            <i data-lucide="{{ $isProfit ? 'award' : 'trending-down' }}" style="width:22px;height:22px;"></i>
        </div>
        <div>
            <p class="kpi-title">Arus Kas Bersih (Net Cash Flow)</p>
            <p class="kpi-value" style="color: {{ $profitColor }};">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
            <p class="kpi-sub">{{ $isProfit ? 'Surplus Kas' : 'Defisit (Restock Investasi)' }}</p>
        </div>
    </div>
</div>

{{-- Financial Trend Chart Card --}}
<div class="card-clean">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
        <div>
            <h2 class="section-title" style="margin:0;"><i data-lucide="bar-chart-3" style="width:18px;height:18px;color:#2563eb;"></i> Tren Keuangan Bulanan (6 Bulan Terakhir)</h2>
            <p style="font-size:12.5px;color:#64748b;margin:2px 0 0;">Perbandingan Pendapatan Bersih, Biaya Pengadaan Stok, dan Arus Kas Bersih Toko</p>
        </div>
        <span class="badge-pill badge-blue">Periode: {{ $trendLabels[0] ?? '' }} - {{ end($trendLabels) }}</span>
    </div>
    <div style="height:280px;width:100%;">
        <canvas id="financialChart"></canvas>
    </div>
</div>

{{-- Formal Financial Statement (Format Standar Laporan Laba Rugi) --}}
<div class="card-clean">
    <h2 class="section-title" style="color:#0f172a;">
        <i data-lucide="file-text" style="width:18px;height:18px;color:#2563eb;"></i> Format Standar Laporan Laba Rugi (Income Statement)
    </h2>
    <p style="font-size:12.5px;color:#64748b;margin:-6px 0 16px;">Dokumentasi pembukuan laba rugi resmi periode {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</p>

    <div class="tbl-wrap">
        <table class="statement-table">
            <tbody>
                {{-- Section I: Revenues --}}
                <tr class="header-row">
                    <td>I. PENDAPATAN USAHA (REVENUE)</td>
                    <td style="text-align:center;width:120px;">Volume</td>
                    <td style="text-align:right;width:200px;">Jumlah (Rp)</td>
                </tr>
                <tr>
                    <td style="padding-left:24px;">Penjualan Kotor Gas (Gross Sales)</td>
                    <td style="text-align:center;">{{ $totalUnitsSold }} Tabung</td>
                    <td style="text-align:right;font-weight:600;">Rp {{ number_format($totalGrossRevenue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="padding-left:24px;color:#b91c1c;">Dikurangi: Retur Penjualan & Pengembalian Dana (Sales Returns & Refund)</td>
                    <td style="text-align:center;color:#b91c1c;">{{ $totalUnitsReturned }} Tabung</td>
                    <td style="text-align:right;color:#b91c1c;font-weight:600;">(Rp {{ number_format($totalRefundAmount, 0, ',', '.') }})</td>
                </tr>
                <tr class="subtotal-row" style="color:#15803d;">
                    <td style="padding-left:24px;">TOTAL PENDAPATAN BERSIH (NET REVENUE)</td>
                    <td style="text-align:center;">{{ $totalUnitsSold - $totalUnitsReturned }} Tabung</td>
                    <td style="text-align:right;">Rp {{ number_format($totalNetRevenue, 0, ',', '.') }}</td>
                </tr>

                {{-- Spacer --}}
                <tr><td colspan="3" style="height:8px;border:none;"></td></tr>

                {{-- Section II: COGS --}}
                <tr class="header-row">
                    <td>II. HARGA POKOK PENJUALAN (HPP / COGS)</td>
                    <td></td>
                    <td style="text-align:right;">Jumlah (Rp)</td>
                </tr>
                <tr>
                    <td style="padding-left:24px;">Harga Pokok Penjualan (Biaya Modal Tabung Terjual)</td>
                    <td style="text-align:center;">{{ $totalUnitsSold }} Tabung</td>
                    <td style="text-align:right;font-weight:600;">(Rp {{ number_format($totalCOGS, 0, ',', '.') }})</td>
                </tr>
                <tr class="subtotal-row" style="color:#1d4ed8;">
                    <td style="padding-left:24px;">LABA KOTOR DARI PENJUALAN (GROSS PROFIT)</td>
                    <td style="text-align:center;"><span class="badge-pill badge-blue">Margin: {{ $grossProfitMargin }}%</span></td>
                    <td style="text-align:right;">Rp {{ number_format($grossProfit, 0, ',', '.') }}</td>
                </tr>

                {{-- Spacer --}}
                <tr><td colspan="3" style="height:8px;border:none;"></td></tr>

                {{-- Section III: Purchases --}}
                <tr class="header-row">
                    <td>III. PENGELUARAN PENGADAAN STOK (INVENTORY PURCHASES)</td>
                    <td style="text-align:center;">Volume Masuk</td>
                    <td style="text-align:right;">Jumlah (Rp)</td>
                </tr>
                @forelse($expenseDetails as $pName => $det)
                    <tr>
                        <td style="padding-left:24px;">Pembelian {{ $pName }}</td>
                        <td style="text-align:center;">{{ $det['qty'] }} Tabung</td>
                        <td style="text-align:right;">Rp {{ number_format($det['amount'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="padding-left:24px;color:#94a3b8;font-style:italic;">Tidak ada pengadaan stok pada periode ini</td></tr>
                @endforelse
                <tr class="subtotal-row" style="color:#b91c1c;">
                    <td style="padding-left:24px;">TOTAL PENGELUARAN PENGADAAN STOK</td>
                    <td style="text-align:center;">{{ $totalUnitsPurchased }} Tabung</td>
                    <td style="text-align:right;">(Rp {{ number_format($totalExpense, 0, ',', '.') }})</td>
                </tr>

                {{-- Spacer --}}
                <tr><td colspan="3" style="height:12px;border:none;"></td></tr>

                {{-- Section IV: Final Result --}}
                <tr class="final-row" style="color: {{ $profitColor }}; background: {{ $profitBg }};">
                    <td>HASIL AKHIR: ARUS KAS BERSIH (NET CASH FLOW)</td>
                    <td style="text-align:center;"><span class="badge-pill {{ $isProfit ? 'badge-green' : 'badge-red' }}">{{ $isProfit ? 'SURPLUS' : 'DEFISIT' }}</span></td>
                    <td style="text-align:right;">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Breakdown Detail Tables (3 Columns) --}}
<div class="grid-3col">
    {{-- Revenues Breakdown --}}
    <div class="card-clean">
        <h2 class="section-title" style="color:#15803d;">
            <i data-lucide="trending-up" style="width:16px;height:16px;"></i> Rincian Penjualan Produk
        </h2>
        <div class="tbl-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Produk Gas</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenueDetails as $prodName => $det)
                        <tr>
                            <td style="font-weight:600;color:#0f172a;">{{ $prodName }}</td>
                            <td style="text-align: center;font-weight:600;">{{ $det['qty'] }}</td>
                            <td style="text-align: right;font-weight:700;color:#15803d;">Rp {{ number_format($det['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#64748b;padding:16px 0;">Tidak ada transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Returns & Damage Breakdown --}}
    <div class="card-clean">
        <h2 class="section-title" style="color:#c2410c;">
            <i data-lucide="rotate-ccw" style="width:16px;height:16px;"></i> Rincian Retur & Refund
        </h2>
        <div class="tbl-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Produk Gas</th>
                        <th style="text-align: center;">Retur / Rusak</th>
                        <th style="text-align: right;">Refund (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnDetails as $prodName => $det)
                        <tr>
                            <td style="font-weight:600;color:#0f172a;">{{ $prodName }}</td>
                            <td style="text-align: center;font-weight:600;">
                                {{ $det['qty'] }} Tabung
                                @if($det['damaged_qty'] > 0)
                                    <br><span style="font-size:10.5px;color:#b91c1c;">({{ $det['damaged_qty'] }} Rusak)</span>
                                @endif
                            </td>
                            <td style="text-align: right;font-weight:700;color:#c2410c;">Rp {{ number_format($det['refund_amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#64748b;padding:16px 0;">Tidak ada retur pada periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Purchases Breakdown --}}
    <div class="card-clean">
        <h2 class="section-title" style="color:#b91c1c;">
            <i data-lucide="trending-down" style="width:16px;height:16px;"></i> Rincian Pengadaan Stok
        </h2>
        <div class="tbl-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Produk Gas</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Total Beli</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseDetails as $prodName => $det)
                        <tr>
                            <td style="font-weight:600;color:#0f172a;">{{ $prodName }}</td>
                            <td style="text-align: center;font-weight:600;">{{ $det['qty'] }}</td>
                            <td style="text-align: right;font-weight:700;color:#b91c1c;">Rp {{ number_format($det['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:#64748b;padding:16px 0;">Tidak ada pembelian</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = @json($trendLabels);
    const revData = @json($trendRevenues);
    const expData = @json($trendExpenses);
    const pftData = @json($trendProfits);

    const ctx = document.getElementById('financialChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pendapatan Bersih (Rp)',
                    data: revData,
                    backgroundColor: 'rgba(22, 163, 74, 0.75)',
                    borderColor: '#16a34a',
                    borderWidth: 1.5,
                    borderRadius: 6
                },
                {
                    label: 'Biaya Pengadaan Stok (Rp)',
                    data: expData,
                    backgroundColor: 'rgba(220, 38, 38, 0.75)',
                    borderColor: '#dc2626',
                    borderWidth: 1.5,
                    borderRadius: 6
                },
                {
                    type: 'line',
                    label: 'Arus Kas Bersih (Rp)',
                    data: pftData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.25,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: { font: { family: 'Inter', size: 12, weight: 600 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                        }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
