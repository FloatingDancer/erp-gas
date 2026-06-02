@extends('layouts.app')

@section('content')

<style>
    /* ===== STAT CARDS ===== */
    .stat-card {
        border-radius: 16px;
        border: none;
        padding: 20px 24px;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 120px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.18) !important;
    }
    .stat-card .stat-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 52px;
        opacity: 0.2;
    }
    .stat-card .stat-label {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        opacity: 0.85;
        margin-bottom: 6px;
    }
    .stat-card .stat-value {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-card .stat-sub {
        font-size: 12px;
        opacity: 0.75;
    }
    .bg-grad-blue   { background: linear-gradient(135deg, #2563eb, #4f9cf9); }
    .bg-grad-green  { background: linear-gradient(135deg, #059669, #34d399); }
    .bg-grad-orange { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .bg-grad-red    { background: linear-gradient(135deg, #dc2626, #f87171); }
    .bg-grad-purple { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
    .bg-grad-teal   { background: linear-gradient(135deg, #0891b2, #22d3ee); }
    .bg-grad-pink   { background: linear-gradient(135deg, #db2777, #f472b6); }
    .bg-grad-gray   { background: linear-gradient(135deg, #374151, #6b7280); }

    /* ===== CHART CARDS ===== */
    .chart-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 24px;
        background: white;
    }
    .chart-card h5 {
        font-size: 15px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
    }

    /* ===== QUICK ACTIONS ===== */
    .action-btn {
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        padding: 10px 18px;
        text-decoration: none;
        display: inline-block;
        transition: opacity 0.15s, transform 0.15s;
    }
    .action-btn:hover { opacity: 0.88; transform: translateY(-1px); }

    /* ===== SECTION TITLE ===== */
    .section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6b7280;
        margin-bottom: 14px;
    }
</style>

{{-- Page Header --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:28px;">
    <div>
        <h1 style="font-size:26px; font-weight:700; margin:0; color:#111827;">ERP Dashboard</h1>
        <p style="margin:4px 0 0; color:#6b7280; font-size:14px;">
            {{ now()->format('l, d F Y') }}
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items: center; flex-wrap: wrap;">
        <form action="{{ route('reports.export-csv') }}" method="GET" style="display:flex; gap:6px; align-items:center; background:white; padding:6px 12px; border-radius:10px; border:1px solid #e2e8f0; font-size:13px; font-weight:600;">
            <span style="color:#6b7280;">Ekspor Laporan:</span>
            <select name="month" style="border:none; outline:none; font-weight:600; color:#374151; background:transparent;">
                @for ($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
            <select name="year" style="border:none; outline:none; font-weight:600; color:#374151; background:transparent;">
                @for ($y=now()->year-3; $y<=now()->year; $y++)
                    <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" style="background:none; border:none; color:#2563eb; cursor:pointer; font-size:15px; padding:0 4px;" title="Unduh CSV">📥</button>
        </form>
        <a href="/orders/create" class="action-btn" style="background:#2563eb; color:white;">+ New Order</a>
        <a href="/customers/create" class="action-btn" style="background:#d97706; color:white;">+ New Customer</a>
    </div>
</div>

{{-- ===== PREDICTIVE ANALYTICS SECTION ===== --}}
<p class="section-title">🔮 Predictive Analytics</p>
<div class="row g-3 mb-4">

    {{-- Left Column: Demand Forecasting Card --}}
    <div class="col-12 col-md-5">
        <div class="chart-card" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h5 style="margin-bottom: 8px;">📊 Peramalan Permintaan (Next Month)</h5>
                <p style="font-size:12.5px; color:#64748b; margin: 0 0 20px;">Estimasi penjualan dan pendapatan bulan depan menggunakan algoritma <strong>Simple Moving Average (SMA)</strong> 3 bulan terakhir.</p>
                
                <div style="background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 16px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 14px;">
                    <div style="font-size: 32px; background: #dbeafe; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">📦</div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Prediksi Jumlah Order</div>
                        <div style="font-size: 24px; font-weight: 700; color: #1e293b; line-height: 1.2; margin-top: 2px;">{{ $forecastOrders }} <span style="font-size: 14px; font-weight: 500; color:#64748b;">Order</span></div>
                    </div>
                </div>

                <div style="background: #f8fafc; border-radius: 12px; padding: 16px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 14px;">
                    <div style="font-size: 32px; background: #dcfce7; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">💵</div>
                    <div>
                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Prediksi Pendapatan (Revenue)</div>
                        <div style="font-size: 20px; font-weight: 700; color: #1e293b; line-height: 1.2; margin-top: 2px;">Rp {{ number_format($forecastRevenue, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; padding-top: 12px; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                <span>ℹ️</span> 
                @if($hasEnoughData)
                    <span>Data riwayat transaksi cukup. Akurasi peramalan sedang (berdasarkan 3 bulan penjualan).</span>
                @else
                    <span>Data transaksi masih terbatas. Angka dihitung menggunakan rata-rata data yang tersedia saat ini.</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Customer Gas Run-out Predictions Table --}}
    <div class="col-12 col-md-7">
        <div class="chart-card" style="height: 100%;">
            <h5 style="margin-bottom: 8px;">🔥 Prediksi Tabung Gas Pelanggan Habis</h5>
            <p style="font-size:12.5px; color:#64748b; margin: 0 0 20px;">Daftar pelanggan yang diprediksi akan kehabisan tabung gas berdasarkan rata-rata interval pembelian sebelumnya.</p>
            
            <div class="table-responsive" style="margin: 0 -24px -24px; border-top: 1px solid #f1f5f9;">
                <table class="table table-hover align-middle" style="margin-bottom: 0; font-size: 13px;">
                    <thead>
                        <tr class="table-light">
                            <th style="padding: 12px 24px; font-weight: 600; color:#475569; font-size:11px; text-transform:uppercase;">Pelanggan</th>
                            <th style="padding: 12px 12px; font-weight: 600; color:#475569; font-size:11px; text-transform:uppercase;">Produk Terakhir</th>
                            <th style="padding: 12px 12px; font-weight: 600; color:#475569; font-size:11px; text-transform:uppercase; text-align:center;">Sisa Hari</th>
                            <th style="padding: 12px 24px; font-weight: 600; color:#475569; font-size:11px; text-transform:uppercase; text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($predictions as $pred)
                            @php
                                $days = $pred['days_remaining'];
                                $badgeColor = '';
                                $badgeText = '';
                                if ($days < 0) {
                                    $badgeColor = 'background: #fee2e2; color: #ef4444;';
                                    $badgeText = abs($days) . ' hari lalu';
                                } elseif ($days == 0) {
                                    $badgeColor = 'background: #fef3c7; color: #d97706;';
                                    $badgeText = 'Hari ini';
                                } elseif ($days <= 3) {
                                    $badgeColor = 'background: #fef3c7; color: #d97706;';
                                    $badgeText = $days . ' hari lagi';
                                } else {
                                    $badgeColor = 'background: #dcfce7; color: #16a34a;';
                                    $badgeText = $days . ' hari lagi';
                                }

                                // Compose WA message
                                $waPhone = preg_replace('/[^0-9]/', '', $pred['customer_phone']);
                                if (str_starts_with($waPhone, '0')) {
                                    $waPhone = '62' . substr($waPhone, 1);
                                }
                                $custName = $pred['customer_name'];
                                $prodName = $pred['last_product'];
                                $predDate = $pred['predicted_date'];
                                $waMessage = "Halo {$custName},\n\nPerkenalkan, kami dari NAGA SAKTI JAYA. Menurut catatan estimasi sistem kami, tabung {$prodName} Anda diprediksi akan habis sekitar tanggal {$predDate}.\n\nApakah Anda ingin melakukan pemesanan ulang untuk menjamin ketersediaan stok?\n\nSalam hormat,\nNAGA SAKTI JAYA";
                            @endphp
                            <tr>
                                <td style="padding: 12px 24px;">
                                    <div style="font-weight: 600; color: #1e293b;">{{ $pred['customer_name'] }}</div>
                                    <div style="font-size: 11px; color: #94a3b8;">Terakhir: {{ $pred['last_purchase'] }}</div>
                                </td>
                                <td style="padding: 12px 12px; color: #475569;">{{ $pred['last_product'] }}</td>
                                <td style="padding: 12px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 10.5px; font-weight: 700; {!! $badgeColor !!}">
                                        {{ $badgeText }}
                                    </span>
                                </td>
                                <td style="padding: 12px 24px; text-align: right;">
                                    <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}&text={{ rawurlencode($waMessage) }}" target="_blank" class="btn btn-sm btn-success" style="font-size: 11.5px; font-weight: 600; border-radius: 8px; padding: 4px 10px; background: #25d366; border-color: #25d366; display: inline-flex; align-items: center; gap: 4px; color: white; text-decoration: none;">
                                        💬 Follow Up
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center" style="padding: 30px; color: #94a3b8;">
                                    Tidak ada data prediksi pelanggan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ===== STAT CARDS ===== --}}
<p class="section-title">Overview</p>
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-blue shadow-sm">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Customers</div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-sub">Total terdaftar</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-teal shadow-sm">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Products</div>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-sub">{{ $lowStockProducts }} stok rendah · {{ $outOfStock }} habis</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-purple shadow-sm">
            <div class="stat-icon">🛒</div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-sub">{{ $pendingOrders }} pending</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-green shadow-sm">
            <div class="stat-icon">💰</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" style="font-size:22px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-sub">{{ $totalPayments }} transaksi</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-orange shadow-sm">
            <div class="stat-icon">⏳</div>
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value">{{ $pendingOrders }}</div>
            <div class="stat-sub">Menunggu diproses</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-pink shadow-sm">
            <div class="stat-icon">🧾</div>
            <div class="stat-label">Unpaid Invoices</div>
            <div class="stat-value">{{ $unpaidInvoices }}</div>
            <div class="stat-sub">{{ $paidInvoices }} sudah dibayar</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-gray shadow-sm">
            <div class="stat-icon">🚚</div>
            <div class="stat-label">Deliveries</div>
            <div class="stat-value">{{ $totalDeliveries }}</div>
            <div class="stat-sub">{{ $completedDeliveries }} selesai · {{ $todayDeliveries }} hari ini</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-red shadow-sm">
            <div class="stat-icon">⚠️</div>
            <div class="stat-label">Low Stock</div>
            <div class="stat-value">{{ $lowStockProducts }}</div>
            <div class="stat-sub">{{ $outOfStock }} produk habis</div>
        </div>
    </div>

</div>

{{-- ===== CHARTS ===== --}}
<p class="section-title">Analitik</p>
<div class="row g-3 mb-4">

    {{-- Bar Chart: Orders --}}
    <div class="col-12 col-md-8">
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h5 id="ordersChartTitle" style="margin:0; font-size:15px; font-weight:700; color:#1f2937;">📊 Order Bulanan (6 Bulan Terakhir)</h5>
                <div style="display:flex; background:#f1f5f9; padding:4px; border-radius:8px; gap:4px;">
                    <button type="button" onclick="updateOrdersChart('daily')" id="btn-ord-daily" style="border:none; background:transparent; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#475569; cursor:pointer; outline:none; transition: all 0.15s;">Harian</button>
                    <button type="button" onclick="updateOrdersChart('weekly')" id="btn-ord-weekly" style="border:none; background:transparent; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#475569; cursor:pointer; outline:none; transition: all 0.15s;">Mingguan</button>
                    <button type="button" onclick="updateOrdersChart('monthly')" id="btn-ord-monthly" style="border:none; background:#ffffff; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#2563eb; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,0.08); outline:none; transition: all 0.15s;">Bulanan</button>
                    <button type="button" onclick="updateOrdersChart('yearly')" id="btn-ord-yearly" style="border:none; background:transparent; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#475569; cursor:pointer; outline:none; transition: all 0.15s;">Tahunan</button>
                </div>
            </div>
            <canvas id="ordersChart" height="110"></canvas>
        </div>
    </div>

    {{-- Donut Chart: Status Delivery --}}
    <div class="col-12 col-md-4">
        <div class="chart-card">
            <h5>🚚 Status Pengiriman</h5>
            <canvas id="deliveryChart" height="180"></canvas>
        </div>
    </div>

    {{-- Bar Chart: Revenue --}}
    <div class="col-12 col-md-8">
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h5 id="revenueChartTitle" style="margin:0; font-size:15px; font-weight:700; color:#1f2937;">💵 Revenue (6 Bulan Terakhir)</h5>
                <div style="display:flex; background:#f1f5f9; padding:4px; border-radius:8px; gap:4px;">
                    <button type="button" onclick="updateRevenueChart('daily')" id="btn-rev-daily" style="border:none; background:transparent; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#475569; cursor:pointer; outline:none; transition: all 0.15s;">Harian</button>
                    <button type="button" onclick="updateRevenueChart('weekly')" id="btn-rev-weekly" style="border:none; background:transparent; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#475569; cursor:pointer; outline:none; transition: all 0.15s;">Mingguan</button>
                    <button type="button" onclick="updateRevenueChart('monthly')" id="btn-rev-monthly" style="border:none; background:#ffffff; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#2563eb; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,0.08); outline:none; transition: all 0.15s;">Bulanan</button>
                    <button type="button" onclick="updateRevenueChart('yearly')" id="btn-rev-yearly" style="border:none; background:transparent; padding:6px 12px; border-radius:6px; font-size:12px; font-weight:600; color:#475569; cursor:pointer; outline:none; transition: all 0.15s;">Tahunan</button>
                </div>
            </div>
            <canvas id="revenueChart" height="110"></canvas>
        </div>
    </div>

    {{-- Recent Activity Log --}}
    <div class="col-12 col-md-4">
        <div class="chart-card" style="height: 100%; min-height: 290px; overflow-y: auto;">
            <h5>📋 Riwayat Aktivitas Terbaru</h5>
            <div style="display:flex; flex-direction:column; gap:12px;">
                @forelse($activityLogs as $log)
                    <div style="display:flex; gap:10px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                        <div style="font-size:18px; padding-top:2px;">
                            @if($log->action === 'Create')
                                🟢
                            @elseif($log->action === 'Update')
                                🟡
                            @elseif($log->action === 'Delete')
                                🔴
                            @else
                                🔵
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px; font-weight:600; color:#374151;">{{ $log->description }}</div>
                            <div style="font-size:11px; color:#94a3b8; display:flex; justify-content:space-between; margin-top:2px;">
                                <span>Oleh: {{ $log->user->name ?? 'System' }}</span>
                                <span style="text-align:right;">
                                    {{ $log->created_at->format('H:i · d M Y') }}<br>
                                    <small style="color:#94a3b8;font-size:9.5px;">({{ $log->created_at->diffForHumans() }})</small>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:13.5px; text-align:center; margin-top:30px;">Belum ada aktivitas tercatat</p>
                @endforelse
            </div>
            
            @if($activityLogs->isNotEmpty())
                <div style="margin-top:16px; text-align:center; border-top:1px solid #f1f5f9; padding-top:12px;">
                    <a href="{{ route('activity-logs.index') }}" style="font-size:12.5px; font-weight:600; color:#2563eb; text-decoration:none;">See More →</a>
                </div>
            @endif
        </div>
    </div>

</div>



{{-- ===== CHART.JS ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ---- Data dari Laravel ----
    const orderDailyLabels = @json($orderDailyLabels);
    const orderDailyData   = @json($orderDailyData);
    const orderWeeklyLabels = @json($orderWeeklyLabels);
    const orderWeeklyData   = @json($orderWeeklyData);
    const orderMonthlyLabels = @json($orderMonthlyLabels->values());
    const orderMonthlyData   = @json($orderMonthlyData->values());
    const orderYearlyLabels = @json($orderYearlyLabels);
    const orderYearlyData   = @json($orderYearlyData);
    
    const revenueDailyLabels = @json($revenueDailyLabels);
    const revenueDailyData   = @json($revenueDailyData);
    const revenueWeeklyLabels = @json($revenueWeekLabels);
    const revenueWeeklyData   = @json($revenueWeekData);
    const revenueMonthlyLabels = @json($revenueLabels->values());
    const revenueMonthlyData   = @json($revenueData->values());
    const revenueYearlyLabels = @json($revenueYearLabels);
    const revenueYearlyData   = @json($revenueYearData);
    const deliveryData  = @json($deliveryStatusData);

    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.color       = '#6b7280';

    // ---- Bar Chart: Orders ----
    let ordersChartInstance = new Chart(document.getElementById('ordersChart'), {
        type: 'bar',
        data: {
            labels: orderMonthlyLabels.length ? orderMonthlyLabels : ['Belum ada data'],
            datasets: [{
                label: 'Jumlah Order',
                data: orderMonthlyData.length ? orderMonthlyData : [0],
                backgroundColor: 'rgba(79, 156, 249, 0.8)',
                borderColor: '#2563eb',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} order`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: '#f3f4f6' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    function updateOrdersChart(period) {
        let labels, data, titleText;
        const btnDaily = document.getElementById('btn-ord-daily');
        const btnWeekly = document.getElementById('btn-ord-weekly');
        const btnMonthly = document.getElementById('btn-ord-monthly');
        const btnYearly = document.getElementById('btn-ord-yearly');

        // Reset button styles
        [btnDaily, btnWeekly, btnMonthly, btnYearly].forEach(btn => {
            btn.style.background = 'transparent';
            btn.style.color = '#475569';
            btn.style.boxShadow = 'none';
        });

        const activeBtn = document.getElementById('btn-ord-' + period);
        activeBtn.style.background = '#ffffff';
        activeBtn.style.color = '#2563eb';
        activeBtn.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';

        if (period === 'daily') {
            labels = orderDailyLabels;
            data = orderDailyData;
            titleText = '📊 Order Harian (7 Hari Terakhir)';
        } else if (period === 'weekly') {
            labels = orderWeeklyLabels;
            data = orderWeeklyData;
            titleText = '📊 Order Mingguan (12 Minggu Terakhir)';
        } else if (period === 'yearly') {
            labels = orderYearlyLabels;
            data = orderYearlyData;
            titleText = '📊 Order Tahunan (5 Tahun Terakhir)';
        } else {
            labels = orderMonthlyLabels;
            data = orderMonthlyData;
            titleText = '📊 Order Bulanan (6 Bulan Terakhir)';
        }

        document.getElementById('ordersChartTitle').innerText = titleText;

        ordersChartInstance.data.labels = labels.length ? labels : ['Belum ada data'];
        ordersChartInstance.data.datasets[0].data = data.length ? data : [0];
        ordersChartInstance.update();
    }

    // ---- Bar Chart: Revenue ----
    let revenueChartInstance = new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revenueMonthlyLabels.length ? revenueMonthlyLabels : ['Belum ada data'],
            datasets: [{
                label: 'Revenue (Rp)',
                data: revenueMonthlyData.length ? revenueMonthlyData : [0],
                backgroundColor: 'rgba(5, 150, 105, 0.75)',
                borderColor: '#059669',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` Rp ${Number(ctx.parsed.y).toLocaleString('id-ID')}`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: val => 'Rp ' + Number(val).toLocaleString('id-ID')
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });

    function updateRevenueChart(period) {
        let labels, data, titleText;
        const btnDaily = document.getElementById('btn-rev-daily');
        const btnWeekly = document.getElementById('btn-rev-weekly');
        const btnMonthly = document.getElementById('btn-rev-monthly');
        const btnYearly = document.getElementById('btn-rev-yearly');

        // Reset button styles
        [btnDaily, btnWeekly, btnMonthly, btnYearly].forEach(btn => {
            btn.style.background = 'transparent';
            btn.style.color = '#475569';
            btn.style.boxShadow = 'none';
        });

        const activeBtn = document.getElementById('btn-rev-' + period);
        activeBtn.style.background = '#ffffff';
        activeBtn.style.color = '#2563eb';
        activeBtn.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';

        if (period === 'daily') {
            labels = revenueDailyLabels;
            data = revenueDailyData;
            titleText = '💵 Revenue Harian (7 Hari Terakhir)';
        } else if (period === 'weekly') {
            labels = revenueWeeklyLabels;
            data = revenueWeeklyData;
            titleText = '💵 Revenue Mingguan (12 Minggu Terakhir)';
        } else if (period === 'yearly') {
            labels = revenueYearlyLabels;
            data = revenueYearlyData;
            titleText = '💵 Revenue Tahunan (5 Tahun Terakhir)';
        } else {
            labels = revenueMonthlyLabels;
            data = revenueMonthlyData;
            titleText = '💵 Revenue Bulanan (6 Bulan Terakhir)';
        }

        document.getElementById('revenueChartTitle').innerText = titleText;

        revenueChartInstance.data.labels = labels.length ? labels : ['Belum ada data'];
        revenueChartInstance.data.datasets[0].data = data.length ? data : [0];
        revenueChartInstance.update();
    }

    // ---- Donut Chart: Status Delivery ----
    new Chart(document.getElementById('deliveryChart'), {
        type: 'doughnut',
        data: {
            labels: ['Scheduled', 'On Delivery', 'Delivered'],
            datasets: [{
                data: deliveryData,
                backgroundColor: ['#fbbf24', '#60a5fa', '#34d399'],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} pengiriman`
                    }
                }
            }
        }
    });
</script>

@endsection