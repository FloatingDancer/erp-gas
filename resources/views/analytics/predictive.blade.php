@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.card-clean { background:white; border-radius:16px; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:24px; margin-bottom:24px; }
.stats-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px; }
.stat-card-custom { background:white; border-radius:14px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #f1f5f9; position:relative; overflow:hidden; }
.stat-card-custom .stat-title { font-size:11.5px; font-weight:700; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:6px; }
.stat-card-custom .stat-val { font-size:26px; font-weight:700; color:#0f172a; line-height:1.1; }
.stat-card-custom .stat-desc { font-size:12px; color:#64748b; margin-top:4px; }
.badge-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-blue { background:#dbeafe; color:#1d4ed8; }
.badge-purple { background:#ede9fe; color:#6d28d9; }
.badge-orange { background:#ffedd5; color:#c2410c; }
.badge-best { background:linear-gradient(135deg, #10b981, #059669); color:white; font-size:11px; padding:3px 8px; border-radius:6px; font-weight:700; text-transform:uppercase; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
.btn-success-custom { display:inline-flex; align-items:center; gap:6px; background:#16a34a; color:white; border:none; padding:10px 20px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; cursor:pointer; }
.btn-success-custom:hover { background:#15803d; color:white; }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:13px 16px; font-size:13.5px; color:#374151; vertical-align:middle; }
.formula-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px; font-family:'Courier New', monospace; font-size:13px; color:#0f172a; margin-bottom:16px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="trending-up" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Predictive Analytics (Peramalan Permintaan Gas)</h1>
        <p class="page-subtitle">Prediksi kebutuhan stok tabung gas periode mendatang berbasis rata-rata kuantitas produk yang dipesan</p>
    </div>
    
    {{-- Product Filter --}}
    <form method="GET" action="{{ route('analytics.predictive') }}" style="display:flex;align-items:center;gap:8px;">
        <label style="font-size:13px;font-weight:600;color:#475569;">Pilih Produk:</label>
        <select name="product_id" onchange="this.form.submit()" style="padding:8px 14px; border:1.5px solid #cbd5e1; border-radius:10px; font-size:13.5px; font-weight:600; color:#0f172a; outline:none; background:white;">
            @foreach($products as $p)
                <option value="{{ $p->id }}" {{ $selectedProduct && $selectedProduct->id == $p->id ? 'selected' : '' }}>
                    {{ $p->name }} (Stok: {{ $p->stock }})
                </option>
            @endforeach
        </select>
    </form>
</div>

{{-- Top Cards Summary --}}
<div class="stats-grid">
    <div class="stat-card-custom" style="border-left: 4px solid #2563eb;">
        <div class="stat-title">Prediksi Permintaan ({{ $nextPeriodLabel }})</div>
        <div class="stat-val" style="color:#2563eb;">{{ $bestModel['forecast_next'] }} <span style="font-size:14px;font-weight:500;color:#64748b;">Tabung</span></div>
        <div class="stat-desc">Berdasarkan model terbaik ({{ $bestModel['code'] }})</div>
    </div>

    <div class="stat-card-custom" style="border-left: 4px solid #10b981;">
        <div class="stat-title">Tingkat Akurasi Model (MAPE)</div>
        <div class="stat-val" style="color:#10b981;">{{ 100 - $bestModel['mape'] }}%</div>
        <div class="stat-desc">Error MAPE: <strong>{{ $bestModel['mape'] }}%</strong> (Akurasi Sangat Baik)</div>
    </div>

    <div class="stat-card-custom" style="border-left: 4px solid #f59e0b;">
        <div class="stat-title">Stok Gudang Saat Ini</div>
        <div class="stat-val" style="color:#0f172a;">{{ $currentStock }} <span style="font-size:14px;font-weight:500;color:#64748b;">Tabung</span></div>
        <div class="stat-desc">Safety Stock Disarankan: <strong>+{{ $safetyStock }} Tabung</strong></div>
    </div>

    <div class="stat-card-custom" style="border-left: 4px solid #8b5cf6;">
        <div class="stat-title">Rekomendasi Restock (PO)</div>
        <div class="stat-val" style="color:#7c3aed;">{{ $recommendedPO }} <span style="font-size:14px;font-weight:500;color:#64748b;">Tabung</span></div>
        <div class="stat-desc">Jumlah order ke Supplier mitra</div>
    </div>
</div>

{{-- Chart Card --}}
<div class="card-clean">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
        <div>
            <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;">
                <i data-lucide="line-chart" style="width:18px;height:18px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> 
                Grafik Tren Penjualan Aktual vs Hasil Prediksi Permintaan ({{ $selectedProduct->name ?? 'Produk' }})
            </h3>
            <p style="font-size:12.5px;color:#64748b;margin:2px 0 0;">Perbandingan kuantitas tabung gas riil yang dipesan dengan model peramalan Moving Average dan Exponential Smoothing</p>
        </div>
        <span class="badge-pill badge-green"><i data-lucide="check-circle" style="width:12px;height:12px;vertical-align:middle;margin-right:2px;"></i> Real Demand (Unit Tabung)</span>
    </div>

    <div style="height:350px;width:100%;">
        <canvas id="predictiveChart"></canvas>
    </div>
</div>

{{-- Academic Algorithm Comparison Table --}}
<div class="card-clean">
    <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0 0 8px;">
        <i data-lucide="calculator" style="width:18px;height:18px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> 
        Evaluasi & Perbandingan Algoritma Peramalan (Model Accuracy Evaluation)
    </h3>
    <p style="font-size:13px;color:#64748b;margin:0 0 20px;">
        Sistem menghitung metrik kesalahan <strong>MAD (Mean Absolute Deviation)</strong> dan <strong>MAPE (Mean Absolute Percentage Error)</strong> untuk memilih model dengan deviasi terkecil sebagai rekomendasi pengadaan.
    </p>

    <div class="tbl-wrap">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Metode / Algoritma</th>
                    <th>Formula Matematika</th>
                    <th style="text-align:center;">Prediksi Bulan Depan</th>
                    <th style="text-align:center;">MAD (Deviasi Rata-rata)</th>
                    <th style="text-align:center;">MAPE (% Error)</th>
                    <th style="text-align:center;">Evaluasi Model</th>
                </tr>
            </thead>
            <tbody>
                @foreach($models as $m)
                <tr style="{{ $m['code'] === $bestModel['code'] ? 'background:#f0fdf4;' : '' }}">
                    <td>
                        <strong>{{ $m['name'] }}</strong>
                        @if($m['code'] === $bestModel['code'])
                            <span class="badge-best" style="margin-left:6px;"><i data-lucide="award" style="width:11px;height:11px;vertical-align:middle;"></i> Best Model</span>
                        @endif
                    </td>
                    <td><code style="background:#f1f5f9;padding:3px 6px;border-radius:4px;color:#0f172a;font-size:12px;">{{ $m['formula'] }}</code></td>
                    <td style="text-align:center;font-weight:700;font-size:15px;color:#0f172a;">{{ $m['forecast_next'] }} Tabung</td>
                    <td style="text-align:center;">{{ $m['mad'] }}</td>
                    <td style="text-align:center;font-weight:600;color:{{ $m['mape'] < 10 ? '#15803d' : '#c2410c' }};">{{ $m['mape'] }}%</td>
                    <td style="text-align:center;">
                        @if($m['mape'] < 10)
                            <span class="badge-pill badge-green">Sangat Akurat (&lt;10%)</span>
                        @elseif($m['mape'] <= 20)
                            <span class="badge-pill badge-blue">Akurasi Baik (10-20%)</span>
                        @else
                            <span class="badge-pill badge-orange">Cukup (20-50%)</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Restock Action Card --}}
    <div style="margin-top:24px;background:linear-gradient(135deg, #eff6ff, #f8fafc);border:1.5px solid #bfdbfe;border-radius:14px;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
        <div>
            <h4 style="font-size:15px;font-weight:700;color:#1e3a8a;margin:0 0 4px;">
                <i data-lucide="shopping-bag" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i>
                Tindak Lanjut Pengadaan Stok (Purchase Order)
            </h4>
            <p style="font-size:13px;color:#475569;margin:0;">
                Berdasarkan prediksi permintaan sebesar <strong>{{ $bestModel['forecast_next'] }} tabung</strong> dan stok tersedia <strong>{{ $currentStock }} tabung</strong>, disarankan membuat PO sebesar <strong>{{ $recommendedPO }} tabung</strong> ke Supplier.
            </p>
        </div>
        <div>
            <a href="{{ route('purchases.create') }}?product_id={{ $selectedProduct->id ?? '' }}&quantity={{ $recommendedPO }}" class="btn-success-custom">
                <i data-lucide="plus-circle" style="width:16px;height:16px;"></i> Buat PO Berdasarkan Prediksi ({{ $recommendedPO }} Tabung)
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = @json($labels);
    const actualData = @json($actualQuantities);
    const sesData = @json($bestModel['series']);
    const nextLabel = @json($nextPeriodLabel);
    const nextForecastVal = @json($bestModel['forecast_next']);

    // Extended labels including next period
    const chartLabels = [...labels, nextLabel];
    
    // Extended actual data (null for next period)
    const chartActual = [...actualData, null];
    
    // Extended forecast data including the next period forecast
    const chartForecast = [...sesData, nextForecastVal];

    const ctx = document.getElementById('predictiveChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Penjualan Aktual (Tabung)',
                    data: chartActual,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    tension: 0.2,
                    fill: false,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 5
                },
                {
                    label: 'Prediksi Exponential Smoothing (SES)',
                    data: chartForecast,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    borderDash: [5, 5],
                    tension: 0.2,
                    fill: false,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: 'Inter', size: 12, weight: 600 },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let val = context.parsed.y;
                            if (val === null) return '';
                            return ' ' + context.dataset.label + ': ' + val + ' Tabung';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Kuantitas Tabung Gas (Unit)',
                        font: { family: 'Inter', weight: 600 }
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
