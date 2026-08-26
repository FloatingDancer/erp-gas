@extends('layouts.app')
@section('content')
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.card-clean { background:white; border-radius:16px; border:1px solid #f1f5f9; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:24px; margin-bottom:24px; position:relative; }
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
.btn-info-icon { display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:50%; background:#eff6ff; color:#2563eb; border:1.5px solid #bfdbfe; cursor:pointer; transition:all 0.15s; }
.btn-info-icon:hover { background:#dbeafe; color:#1d4ed8; transform:scale(1.05); }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:13px 16px; font-size:13.5px; color:#374151; vertical-align:middle; }

/* Modal Styles */
.algo-modal-backdrop { position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(15,23,42,0.65); backdrop-filter:blur(4px); display:flex; align-items:center; justify-content:center; z-index:9999; padding:20px; }
.algo-modal-box { background:white; border-radius:18px; max-width:760px; width:100%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); animation: modalIn 0.2s ease-out; }
@keyframes modalIn { from { opacity:0; transform:scale(0.96); } to { opacity:1; transform:scale(1); } }
.algo-modal-header { padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
.algo-modal-body { padding:20px 24px; overflow-y:auto; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="trending-up" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Predictive Analytics (Peramalan Permintaan Gas)</h1>
        <p class="page-subtitle">Prediksi kebutuhan stok tabung gas periode mendatang berbasis rata-rata kuantitas produk yang dipesan</p>
    </div>
    
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        {{-- Exclamation in circle button --}}
        <button type="button" onclick="openAlgoModal()" style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;color:#2563eb;border:1.5px solid #bfdbfe;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.15s;" title="Klik untuk melihat penjelasan rumus dan simbol formula">
            <i data-lucide="alert-circle" style="width:16px;height:16px;color:#2563eb;"></i> Penjelasan Algoritma
        </button>

        {{-- Product Filter --}}
        <form method="GET" action="{{ route('analytics.predictive') }}" style="display:flex;align-items:center;gap:8px;">
            <label style="font-size:13px;font-weight:600;color:#475569;">Produk:</label>
            <select name="product_id" onchange="this.form.submit()" style="padding:8px 14px; border:1.5px solid #cbd5e1; border-radius:10px; font-size:13.5px; font-weight:600; color:#0f172a; outline:none; background:white;">
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ $selectedProduct && $selectedProduct->id == $p->id ? 'selected' : '' }}>
                        {{ $p->name }} (Stok: {{ $p->stock }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>
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
                <i data-lucide="line-chart" style="width:18px;height:18px;vertical-align:middle;margin-top:-3px;margin-right:4px;color:#2563eb;"></i> 
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
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
        <h3 style="font-size:16px;font-weight:700;color:#0f172a;margin:0;">
            <i data-lucide="calculator" style="width:18px;height:18px;vertical-align:middle;margin-top:-3px;margin-right:4px;color:#2563eb;"></i> 
            Evaluasi & Perbandingan Algoritma Peramalan (Model Accuracy Evaluation)
        </h3>
        {{-- Circular Info/Exclamation Button in Card Corner --}}
        <button type="button" onclick="openAlgoModal()" class="btn-info-icon" title="Lihat Penjelasan Rumus & Simbol Algoritma">
            <i data-lucide="alert-circle" style="width:18px;height:18px;"></i>
        </button>
    </div>
    
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

{{-- MODAL PENJELASAN ALGORITMA & SIMBOL FORMULA --}}
<div id="algoModal" class="algo-modal-backdrop" style="display:none;" onclick="closeAlgoModal(event)">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function openAlgoModal() {
    document.getElementById('algoModal').style.display = 'flex';
}

function closeAlgoModal(e) {
    if (!e || e.target === document.getElementById('algoModal') || e.target.tagName === 'BUTTON') {
        document.getElementById('algoModal').style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const labels = @json($labels);
    const actualData = @json($actualQuantities);
    const sesData = @json($bestModel['series']);
    const nextLabel = @json($nextPeriodLabel);
    const nextForecastVal = @json($bestModel['forecast_next']);

    const chartLabels = [...labels, nextLabel];
    const chartActual = [...actualData, null];
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
