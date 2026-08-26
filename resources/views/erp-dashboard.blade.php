@extends('layouts.app')

@section('content')

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px;">
        <i data-lucide="check-circle-2" style="width:16px;height:16px;color:#10b981;flex-shrink:0;"></i>
        {{ session('success') }}
    </div>
@endif

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
        opacity: 0.2;
        color: white;
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

    /* ===== RESPONSIVE PAGE HEADER ===== */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        gap: 16px;
    }
    .dashboard-title {
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        color: #111827;
    }
    .dashboard-subtitle {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    .dashboard-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .export-form {
        display: flex;
        gap: 6px;
        align-items: center;
        background: white;
        padding: 6px 12px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        font-weight: 600;
    }
    .action-btn-group {
        display: flex;
        gap: 10px;
    }
    
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }
        .dashboard-actions {
            flex-direction: column;
            align-items: stretch;
            width: 100%;
            gap: 12px;
        }
        .export-form {
            justify-content: space-between;
            width: 100%;
            padding: 10px 14px;
        }
        .action-btn-group {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .action-btn-group .action-btn {
            text-align: center;
            width: 100%;
            padding: 12px;
        }
    }
</style>

{{-- Page Header --}}
<div class="dashboard-header">
    <div>
        <h1 class="dashboard-title">ERP Dashboard</h1>
        <p class="dashboard-subtitle">
            {{ now()->format('l, d F Y') }}
        </p>
    </div>
    <div class="dashboard-actions">
        <form action="{{ route('reports.export-csv') }}" method="GET" class="export-form">
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
            <button type="submit" style="background:none; border:none; color:#2563eb; cursor:pointer; font-size:15px; padding:0 4px; display:inline-flex; align-items:center; justify-content:center;" title="Unduh CSV"><i data-lucide="download" style="width:16px;height:16px;"></i></button>
        </form>
        <div class="action-btn-group">
            <a href="/orders/create" class="action-btn" style="background:#2563eb; color:white;"><i data-lucide="plus" style="width:14px;height:14px;margin-right:2px;vertical-align:middle;margin-top:-2px;"></i> New Order</a>
            <a href="/customers/create" class="action-btn" style="background:#d97706; color:white;"><i data-lucide="user-plus" style="width:14px;height:14px;margin-right:2px;vertical-align:middle;margin-top:-2px;"></i> New Customer</a>
        </div>
    </div>
</div>

{{-- ===== PREDICTIVE ANALYTICS SECTION ===== --}}
<p class="section-title"><i data-lucide="sparkles" style="width:14px;height:14px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Predictive Analytics</p>
<div class="row g-3 mb-4">

    {{-- Left Column: Demand Forecasting Card --}}
    <div class="col-12 col-md-5">
        <div class="chart-card" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <h5 style="margin: 0;"><i data-lucide="trending-up" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;color:#2563eb;"></i> Peramalan Permintaan Gas (Next Month)</h5>
                    <button type="button" onclick="openAlgoModal()" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:#eff6ff;color:#2563eb;border:1.5px solid #bfdbfe;cursor:pointer;transition:transform 0.15s;" title="Penjelasan Detail Algoritma & Simbol Formula">
                        <i data-lucide="alert-circle" style="width:16px;height:16px;"></i>
                    </button>
                </div>
                <p style="font-size:12px; color:#64748b; margin: 0 0 16px;">Estimasi kebutuhan tabung gas bulan depan berbasis rata-rata kuantitas unit produk yang dipesan.</p>
                
                <div style="background: #f8fafc; border-radius: 12px; padding: 14px 16px; margin-bottom: 12px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 14px;">
                    <div style="background: #dbeafe; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink:0;"><i data-lucide="package" style="width:24px;height:24px;color:#2563eb;"></i></div>
                    <div>
                        <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Prediksi Permintaan Tabung</div>
                        <div style="font-size: 22px; font-weight: 700; color: #1e293b; line-height: 1.2; margin-top: 2px;">
                            {{ $forecastQuantity > 0 ? $forecastQuantity : max(15, $forecastOrders * 12) }} <span style="font-size: 13px; font-weight: 600; color:#2563eb;">Tabung Gas</span>
                        </div>
                    </div>
                </div>
 
                <div style="background: #f8fafc; border-radius: 12px; padding: 14px 16px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 14px;">
                    <div style="background: #dcfce7; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink:0;"><i data-lucide="dollar-sign" style="width:24px;height:24px;color:#059669;"></i></div>
                    <div>
                        <div style="font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Estimasi Pendapatan (Revenue)</div>
                        <div style="font-size: 18px; font-weight: 700; color: #1e293b; line-height: 1.2; margin-top: 2px;">Rp {{ number_format($forecastRevenue, 0, ',', '.') }}</div>
                    </div>
                </div>

                <a href="{{ route('analytics.predictive') }}" style="display:inline-flex; align-items:center; justify-content:center; gap:6px; background:#eff6ff; color:#2563eb; font-weight:600; font-size:12.5px; padding:9px 14px; border-radius:10px; text-decoration:none; margin-top:12px; border:1px solid #bfdbfe; width:100%; transition:background 0.15s;">
                    <i data-lucide="line-chart" style="width:14px;height:14px;"></i> Lihat Grafik & Evaluasi Akurasi (SES / SMA) →
                </a>
            </div>

            <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid #f1f5f9; font-size: 11px; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                <i data-lucide="info" style="width:13px;height:13px;flex-shrink:0;"></i>
                @if($hasEnoughData)
                    <span>Data historis memadai untuk peramalan per kuantitas unit produk.</span>
                @else
                    <span>Dihitung dari rata-rata kuantitas produk yang dipesan.</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Customer Gas Run-out Predictions Table --}}
    <div class="col-12 col-md-7">
        <div class="chart-card" style="height: 100%;">
            <h5 style="margin-bottom: 8px;"><i data-lucide="flame" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Prediksi Tabung Gas Pelanggan Habis</h5>
            <p style="font-size:12.5px; color:#64748b; margin: 0 0 20px;">Daftar pelanggan yang diprediksi akan kehabisan tabung gas berdasarkan rata-rata interval pembelian sebelumnya.</p>
            
            <div class="table-responsive" style="margin: 0 -24px -24px; border-top: 1px solid #f1f5f9;">
                <table class="table table-hover align-middle" style="margin-bottom: 0; font-size: 13px;">
                    <thead>
                        <tr class="table-light">
                            <th style="padding: 12px 24px; font-weight: 600; color:#475569; font-size:11px; text-transform:uppercase;">Pelanggan</th>
                            <th class="d-none d-md-table-cell" style="padding: 12px 12px; font-weight: 600; color:#475569; font-size:11px; text-transform:uppercase;">Produk Terakhir</th>
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
                                $waMessage = "Halo {$custName},\n\nPerkenalkan, kami dari " . (config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA') . ". Menurut catatan estimasi sistem kami, tabung {$prodName} Anda diprediksi akan habis sekitar tanggal {$predDate}.\n\nApakah Anda ingin melakukan pemesanan ulang untuk menjamin ketersediaan stok?\n\nSalam hormat,\n" . (config('app.demo') ? 'PT XYZ' : 'TK. NAGA SAKTI JAYA');
                            @endphp
                            <tr>
                                <td style="padding: 12px 24px;">
                                    <div style="font-weight: 600; color: #1e293b;">{{ $pred['customer_name'] }}</div>
                                    <div style="font-size: 11px; color: #94a3b8;">Terakhir: {{ $pred['last_purchase'] }}</div>
                                    <div class="d-md-none" style="font-size: 11px; color: #475569; margin-top: 3px; font-weight: 500;">
                                        <i data-lucide="package" style="width:12px;height:12px;margin-right:2px;vertical-align:middle;margin-top:-2px;"></i> {{ $pred['last_product'] }}
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell" style="padding: 12px 12px; color: #475569;">{{ $pred['last_product'] }}</td>
                                <td style="padding: 12px 12px; text-align: center;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 10.5px; font-weight: 700; {!! $badgeColor !!}">
                                        {{ $badgeText }}
                                    </span>
                                </td>
                                <td style="padding: 12px 24px; text-align: right;">
                                    <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}&text={{ rawurlencode($waMessage) }}" target="_blank" class="btn btn-sm btn-success" style="font-size: 11.5px; font-weight: 600; border-radius: 8px; padding: 4px 10px; background: #25d366; border-color: #25d366; display: inline-flex; align-items: center; gap: 4px; color: white; text-decoration: none;">
                                        <i data-lucide="message-square" style="width:12px;height:12px;"></i> Follow Up
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
            <div class="stat-icon"><i data-lucide="users" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Customers</div>
            <div class="stat-value">{{ $totalCustomers }}</div>
            <div class="stat-sub">Total terdaftar</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-teal shadow-sm">
            <div class="stat-icon"><i data-lucide="package" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Products</div>
            <div class="stat-value">{{ $totalProducts }}</div>
            <div class="stat-sub">{{ $lowStockProducts }} stok rendah · {{ $outOfStock }} habis</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-purple shadow-sm">
            <div class="stat-icon"><i data-lucide="shopping-cart" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-sub">{{ $pendingOrders }} pending</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-green shadow-sm">
            <div class="stat-icon"><i data-lucide="dollar-sign" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value" style="font-size:22px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-sub">{{ $totalPayments }} transaksi</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-orange shadow-sm">
            <div class="stat-icon"><i data-lucide="clock" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value">{{ $pendingOrders }}</div>
            <div class="stat-sub">Menunggu diproses</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-pink shadow-sm">
            <div class="stat-icon"><i data-lucide="file-text" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Unpaid Invoices</div>
            <div class="stat-value">{{ $unpaidInvoices }}</div>
            <div class="stat-sub">{{ $paidInvoices }} sudah dibayar</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-gray shadow-sm">
            <div class="stat-icon"><i data-lucide="truck" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
            <div class="stat-label">Deliveries</div>
            <div class="stat-value">{{ $totalDeliveries }}</div>
            <div class="stat-sub">{{ $completedDeliveries }} selesai · {{ $todayDeliveries }} hari ini</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card bg-grad-red shadow-sm">
            <div class="stat-icon"><i data-lucide="alert-triangle" style="width:48px;height:48px;stroke-width:1.5;"></i></div>
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
                <h5 id="ordersChartTitle" style="margin:0; font-size:15px; font-weight:700; color:#1f2937;"><i data-lucide="bar-chart-3" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Order Bulanan (6 Bulan Terakhir)</h5>
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
            <h5><i data-lucide="truck" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Status Pengiriman</h5>
            <canvas id="deliveryChart" height="180"></canvas>
        </div>
    </div>

    {{-- Bar Chart: Revenue --}}
    <div class="col-12 col-md-8">
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
                <h5 id="revenueChartTitle" style="margin:0; font-size:15px; font-weight:700; color:#1f2937;"><i data-lucide="dollar-sign" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Revenue (6 Bulan Terakhir)</h5>
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
            <h5><i data-lucide="clipboard-list" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> Riwayat Aktivitas Terbaru</h5>
            <div style="display:flex; flex-direction:column; gap:12px;">
                @forelse($activityLogs as $log)
                    <div style="display:flex; gap:10px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
                        <div style="padding-top:2px;">
                            @if($log->action === 'Create')
                                <i data-lucide="plus-circle" style="width:16px;height:16px;color:#10b981;vertical-align:middle;"></i>
                            @elseif($log->action === 'Update')
                                <i data-lucide="edit-3" style="width:16px;height:16px;color:#f59e0b;vertical-align:middle;"></i>
                            @elseif($log->action === 'Delete')
                                <i data-lucide="minus-circle" style="width:16px;height:16px;color:#ef4444;vertical-align:middle;"></i>
                            @else
                                <i data-lucide="info" style="width:16px;height:16px;color:#3b82f6;vertical-align:middle;"></i>
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px; font-weight:600; color:#374151; word-break: break-word;">
                                @if(strlen($log->description) > 120)
                                    <span id="desc-short-{{ $log->id }}">{{ Str::limit($log->description, 120, '...') }}</span>
                                    <span id="desc-full-{{ $log->id }}" style="display: none;">{{ $log->description }}</span>
                                    <button type="button" onclick="toggleDesc({{ $log->id }})" id="btn-toggle-{{ $log->id }}" style="background: none; border: none; color: #2563eb; font-size: 11.5px; font-weight: 600; padding: 0 4px; cursor: pointer; display: inline-block;">See More</button>
                                @else
                                    {{ $log->description }}
                                @endif
                            </div>
                            <div style="font-size:11px; color:#94a3b8; display:flex; justify-content:space-between; margin-top:4px;">
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
            titleText = 'Order Harian (7 Hari Terakhir)';
        } else if (period === 'weekly') {
            labels = orderWeeklyLabels;
            data = orderWeeklyData;
            titleText = 'Order Mingguan (12 Minggu Terakhir)';
        } else if (period === 'yearly') {
            labels = orderYearlyLabels;
            data = orderYearlyData;
            titleText = 'Order Tahunan (5 Tahun Terakhir)';
        } else {
            labels = orderMonthlyLabels;
            data = orderMonthlyData;
            titleText = 'Order Bulanan (6 Bulan Terakhir)';
        }

        document.getElementById('ordersChartTitle').innerHTML = '<i data-lucide="bar-chart-3" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> ' + titleText;
        if (typeof lucide !== 'undefined') lucide.createIcons();

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
            titleText = 'Revenue Harian (7 Hari Terakhir)';
        } else if (period === 'weekly') {
            labels = revenueWeeklyLabels;
            data = revenueWeeklyData;
            titleText = 'Revenue Mingguan (12 Minggu Terakhir)';
        } else if (period === 'yearly') {
            labels = revenueYearlyLabels;
            data = revenueYearlyData;
            titleText = 'Revenue Tahunan (5 Tahun Terakhir)';
        } else {
            labels = revenueMonthlyLabels;
            data = revenueMonthlyData;
            titleText = 'Revenue Bulanan (6 Bulan Terakhir)';
        }

        document.getElementById('revenueChartTitle').innerHTML = '<i data-lucide="dollar-sign" style="width:16px;height:16px;vertical-align:middle;margin-top:-3px;margin-right:4px;"></i> ' + titleText;
        if (typeof lucide !== 'undefined') lucide.createIcons();

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

<script>
    function toggleDesc(id) {
        const shortSpan = document.getElementById('desc-short-' + id);
        const fullSpan = document.getElementById('desc-full-' + id);
        const btn = document.getElementById('btn-toggle-' + id);
        
        if (fullSpan.style.display === 'none') {
            fullSpan.style.display = 'inline';
            shortSpan.style.display = 'none';
            btn.innerText = 'Show Less';
        } else {
            fullSpan.style.display = 'none';
            shortSpan.style.display = 'inline';
            btn.innerText = 'See More';
        }
    }

    function openAlgoModal() {
        document.getElementById('algoModalDashboard').style.display = 'flex';
    }

    function closeAlgoModal(e) {
        if (!e || e.target === document.getElementById('algoModalDashboard') || e.target.tagName === 'BUTTON') {
            document.getElementById('algoModalDashboard').style.display = 'none';
        }
    }
</script>

{{-- MODAL PENJELASAN ALGORITMA & SIMBOL FORMULA (DASHBOARD) --}}
<style>
.algo-modal-backdrop { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; z-index:9999; padding:20px; }
.algo-modal-box { background:white; border-radius:18px; max-width:760px; width:100%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation: modalIn 0.2s ease-out; }
@keyframes modalIn { from { opacity:0; transform:scale(0.96); } to { opacity:1; transform:scale(1); } }
.algo-modal-header { padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
.algo-modal-body { padding:20px 24px; overflow-y:auto; }
</style>

<div id="algoModalDashboard" class="algo-modal-backdrop" style="display:none;" onclick="closeAlgoModal(event)">
    <div class="algo-modal-box" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="algo-modal-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">
                    <i data-lucide="alert-circle" style="width:22px;height:22px;"></i>
                </div>
                <div>
                    <h3 style="margin:0;font-size:16.5px;font-weight:800;color:#0f172a;">Rincian & Panduan Algoritma Peramalan (Demand Forecasting)</h3>
                    <p style="margin:2px 0 0;font-size:12.5px;color:#64748b;">Penjelasan rumus matematika, simbol variabel, dan cara evaluasi akurasi</p>
                </div>
            </div>
            <button type="button" onclick="closeAlgoModal()" style="border:none;background:#f1f5f9;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>

        <!-- Body -->
        <div class="algo-modal-body">
            
            <!-- Konsep Qty vs Order -->
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:14px 18px;margin-bottom:18px;">
                <h4 style="margin:0 0 6px;font-size:13.5px;font-weight:700;color:#1e40af;">
                    <i data-lucide="help-circle" style="width:15px;height:15px;vertical-align:middle;margin-right:2px;"></i> Mengapa Peramalan Berbasis Kuantitas Tabung (Qty), Bukan Jumlah Transaksi?
                </h4>
                <p style="margin:0;font-size:12.5px;color:#1e3a8a;line-height:1.6;">
                    Satu kali transaksi pemesanan dapat berisi <strong>1 tabung</strong> atau <strong>100 tabung</strong>. Oleh karena itu, peramalan berbasis <strong>Kuantitas Unit Tabung Riil ($Qty$)</strong> yang terjual memberikan estimasi kebutuhan stok gudang yang jauh lebih akurat dan mencegah terjadinya kehabisan stok (*stockout*).
                </p>
            </div>

            <!-- 1. SMA -->
            <div style="border:1.5px solid #dbeafe;border-radius:12px;padding:16px;margin-bottom:18px;background:#fbfdff;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <h4 style="margin:0;font-size:14px;font-weight:800;color:#1e40af;">1. Simple Moving Average (SMA - 3 Bulan)</h4>
                    <span style="background:#dbeafe;color:#1e40af;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">Rata-rata Bergerak</span>
                </div>
                <p style="font-size:12.5px;color:#475569;margin-bottom:10px;">
                    Menghitung nilai ramalan permintaan periode berikutnya dengan mengambil rata-rata aritmatika dari 3 bulan penjualan terakhir.
                </p>
                
                <div style="background:#0f172a;color:#38bdf8;padding:12px 16px;border-radius:8px;font-family:'Courier New', monospace;font-size:13.5px;margin-bottom:12px;">
                    Formula: F(t+1) = [ Q(t) + Q(t-1) + Q(t-2) ] / 3
                </div>

                <h5 style="font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;margin:0 0 6px;">Keterangan Simbol Formula:</h5>
                <ul style="margin:0;padding-left:18px;font-size:12.5px;color:#334155;line-height:1.6;">
                    <li><strong>F(t+1)</strong> : Nilai ramalan (*Forecast*) kuantitas permintaan untuk bulan berikutnya.</li>
                    <li><strong>Q(t)</strong> : Kuantitas aktual tabung gas yang dipesan pada bulan berjalan (bulan saat ini).</li>
                    <li><strong>Q(t-1)</strong> : Kuantitas aktual tabung gas yang dipesan pada 1 bulan yang lalu.</li>
                    <li><strong>Q(t-2)</strong> : Kuantitas aktual tabung gas yang dipesan pada 2 bulan yang lalu.</li>
                    <li><strong>3</strong> : Jumlah periode waktu ($n = 3$ bulan) yang dirata-ratakan.</li>
                </ul>
            </div>

            <!-- 2. SES -->
            <div style="border:1.5px solid #d1fae5;border-radius:12px;padding:16px;margin-bottom:18px;background:#fbfefc;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <h4 style="margin:0;font-size:14px;font-weight:800;color:#065f46;">2. Single Exponential Smoothing (SES)</h4>
                    <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;">Pemulusan Eksponensial</span>
                </div>
                <p style="font-size:12.5px;color:#475569;margin-bottom:10px;">
                    Memberikan pembobotan eksponensial lebih tinggi pada data transaksi terbaru dan pembobotan yang menurun bertahap pada data historis masa lalu.
                </p>

                <div style="background:#0f172a;color:#4ade80;padding:12px 16px;border-radius:8px;font-family:'Courier New', monospace;font-size:13.5px;margin-bottom:12px;">
                    Formula: F(t+1) = α · Q(t) + (1 - α) · F(t)
                </div>

                <h5 style="font-size:12px;font-weight:700;text-transform:uppercase;color:#64748b;margin:0 0 6px;">Keterangan Simbol Formula:</h5>
                <ul style="margin:0;padding-left:18px;font-size:12.5px;color:#334155;line-height:1.6;">
                    <li><strong>F(t+1)</strong> : Nilai ramalan permintaan untuk periode berikutnya.</li>
                    <li><strong>α (Alpha)</strong> : Konstanta pemulusan (*smoothing constant*, rentang $0 < α \le 1$). Sistem menguji bobot <strong>α = 0.3</strong> (lebih stabil) dan <strong>α = 0.5</strong> (lebih responsif terhadap perubahan tren).</li>
                    <li><strong>Q(t)</strong> : Kuantitas tabung gas riil/aktual yang terjual pada periode berjalan.</li>
                    <li><strong>F(t)</strong> : Nilai hasil peramalan pada periode berjalan saat ini.</li>
                    <li><strong>(1 - α)</strong> : Bobot sisa yang diberikan kepada tren peramalan sebelumnya.</li>
                </ul>
            </div>

            <!-- 3. Error Metrics -->
            <div style="border:1.5px solid #fed7aa;border-radius:12px;padding:16px;margin-bottom:18px;background:#fffdfa;">
                <h4 style="margin:0 0 8px;font-size:14px;font-weight:800;color:#9a3412;">3. Evaluasi Akurasi & Pemilihan Model Terbaik (Best Model)</h4>
                
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:12px;margin-bottom:12px;">
                    <div style="background:white;border:1px solid #fed7aa;border-radius:8px;padding:12px;">
                        <div style="font-weight:700;font-size:12px;color:#9a3412;">MAD (Mean Absolute Deviation)</div>
                        <div style="font-family:'Courier New',monospace;font-size:12px;margin:4px 0;background:#fef3c7;padding:4px 8px;border-radius:4px;">MAD = Σ |Q(t) - F(t)| / N</div>
                        <p style="font-size:11.5px;color:#64748b;margin:0;">Mengukur rata-rata besaran selisih absolut antara permintaan riil dengan hasil ramalan (dalam unit tabung).</p>
                    </div>
                    <div style="background:white;border:1px solid #fed7aa;border-radius:8px;padding:12px;">
                        <div style="font-weight:700;font-size:12px;color:#9a3412;">MAPE (Mean Absolute % Error)</div>
                        <div style="font-family:'Courier New',monospace;font-size:12px;margin:4px 0;background:#fef3c7;padding:4px 8px;border-radius:4px;">MAPE = (Σ |(Q-F)/Q| / N) × 100%</div>
                        <p style="font-size:11.5px;color:#64748b;margin:0;">Persentase rata-rata kesalahan prediksi. Model dengan <strong>MAPE terkecil</strong> otomatis dinobatkan sebagai <strong>Best Model</strong>.</p>
                    </div>
                </div>

                <div style="font-size:12px;background:#fff7ed;padding:8px 12px;border-radius:6px;color:#c2410c;">
                    <strong>Standar Akurasi MAPE:</strong> &lt;10% (*Sangat Akurat*) | 10%–20% (*Akurasi Baik*) | 20%–50% (*Cukup/Wajar*).
                </div>
            </div>

            <!-- 4. PO Restock Formula -->
            <div style="border:1.5px solid #e9d5ff;border-radius:12px;padding:16px;background:#fdfcfe;">
                <h4 style="margin:0 0 6px;font-size:14px;font-weight:800;color:#6b21a8;">4. Rumus Rekomendasi Pengadaan Stok (Purchase Order - PO)</h4>
                <div style="background:#0f172a;color:#c084fc;padding:10px 14px;border-radius:8px;font-family:'Courier New', monospace;font-size:12.5px;margin-bottom:8px;">
                    Rekomendasi PO = MAX(0, [ Prediksi Permintaan + Safety Stock ] - Stok Gudang Saat Ini)
                </div>
                <p style="font-size:12px;color:#64748b;margin:0;">
                    Di mana <strong>Safety Stock</strong> disetel sebesar <strong>20%</strong> dari hasil prediksi untuk mengantisipasi lonjakan konsumsi gas tak terduga oleh pelanggan.
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div style="padding:14px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;">
            <button type="button" onclick="closeAlgoModal()" style="background:#2563eb;color:white;border:none;padding:8px 18px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;">Tutup Panduan</button>
        </div>
    </div>
</div>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

@endsection