@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
.btn-secondary-custom { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#374151; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-secondary-custom:hover { background:#e2e8f0; color:#111827; }
.card-clean { background:white; border-radius:16px; border:none; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:24px; margin-bottom:24px; }
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin-bottom:24px; }
.kpi-card { background:white; border-radius:16px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06); display:flex; align-items:center; gap:16px; }
.kpi-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; }
.kpi-title { font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; margin:0; }
.kpi-value { font-size:20px; font-weight:700; color:#0f172a; margin:4px 0 0 0; }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; white-space:nowrap; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.12s; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:13px 16px; font-size:13.5px; color:#374151; vertical-align:middle; }
.grid-2col { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
@media (max-width: 768px) {
    .grid-2col { grid-template-columns: 1fr; }
}
.form-input { padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; }
.section-title { font-size:16px; font-weight:700; color:#1e293b; margin:0 0 16px 0; display:flex; align-items:center; gap:8px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="trending-up" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Profit & Loss</h1>
        <p class="page-subtitle">Modul Keuangan & Akuntansi — Laporan Profitabilitas Periode Bulanan</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('reports.laba-rugi.print', ['month' => $month, 'year' => $year]) }}" target="_blank" class="btn-secondary-custom">
            <i data-lucide="printer" style="width:14px;height:14px;vertical-align:middle;"></i> Cetak Laporan
        </a>
    </div>
</div>

{{-- Filter Card --}}
<div class="card-clean" style="padding: 16px 24px;">
    <form method="GET" action="{{ route('reports.laba-rugi') }}" style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:13px; font-weight:600; color:#475569;">Bulan</label>
            <select name="month" class="form-input" required>
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <label style="font-size:13px; font-weight:600; color:#475569;">Tahun</label>
            <select name="year" class="form-input" required>
                @for ($y = now()->year - 3; $y <= now()->year + 2; $y++)
                    <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn-primary-custom" style="padding: 8px 16px;">
            <i data-lucide="filter" style="width:14px;height:14px;"></i> Filter
        </button>
    </form>
</div>

{{-- KPI Summary Cards --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#dcfce7; color:#15803d;">
            <i data-lucide="arrow-up-right" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <p class="kpi-title">Total Pendapatan (Revenues)</p>
            <p class="kpi-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fee2e2; color:#b91c1c;">
            <i data-lucide="arrow-down-left" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <p class="kpi-title">Total Pengeluaran (Expenses)</p>
            <p class="kpi-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
    </div>
    @php
        $isProfit = $netProfit >= 0;
        $profitBg = $isProfit ? '#dcfce7' : '#fee2e2';
        $profitColor = $isProfit ? '#15803d' : '#b91c1c';
    @endphp
    <div class="kpi-card" style="border: 2px solid {{ $profitColor }};">
        <div class="kpi-icon" style="background: {{ $profitBg }}; color: {{ $profitColor }};">
            <i data-lucide="{{ $isProfit ? 'award' : 'trending-down' }}" style="width:24px;height:24px;"></i>
        </div>
        <div>
            <p class="kpi-title">Laba/Rugi Bersih (Net Profit)</p>
            <p class="kpi-value" style="color: {{ $profitColor }};">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
        </div>
    </div>
</div>

<div class="grid-2col">
    {{-- Revenues Breakdown --}}
    <div class="card-clean">
        <h2 class="section-title" style="color:#15803d;">
            <i data-lucide="trending-up" style="width:18px;height:18px;"></i> Pendapatan dari Penjualan
        </h2>
        <div class="tbl-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Produk Gas</th>
                        <th style="text-align: center;">Qty Terjual</th>
                        <th style="text-align: right;">Total Pendapatan</th>
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
                        <tr>
                            <td colspan="3" style="text-align:center;color:#64748b;padding:24px 0;">Tidak ada catatan transaksi pendapatan untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Expenses Breakdown --}}
    <div class="card-clean">
        <h2 class="section-title" style="color:#b91c1c;">
            <i data-lucide="trending-down" style="width:18px;height:18px;"></i> Pengeluaran Pengadaan Stok
        </h2>
        <div class="tbl-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Produk Gas</th>
                        <th style="text-align: center;">Qty Dibeli</th>
                        <th style="text-align: right;">Total Biaya Beli</th>
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
                        <tr>
                            <td colspan="3" style="text-align:center;color:#64748b;padding:24px 0;">Tidak ada catatan transaksi pembelian untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
