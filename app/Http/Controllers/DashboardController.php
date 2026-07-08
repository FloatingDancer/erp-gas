<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $data = \Illuminate\Support\Facades\Cache::remember('dashboard_data', 1800, function () {
            $totalCustomers = Customer::count();
            $totalProducts  = Product::count();
            $totalOrders    = Order::count();
            $pendingOrders  = Order::where('status', 'Pending')->count();

            $totalDeliveries    = Delivery::count();
            $completedDeliveries = Delivery::where('status', 'Delivered')->count();
            $todayDeliveries    = Delivery::whereDate('delivery_date', now())->count();

            $unpaidInvoices = Invoice::where('status', 'Unpaid')->count();
            $paidInvoices   = Invoice::where('status', 'Paid')->count();

            $totalPayments = Payment::count();
            $totalRevenue  = Payment::sum('amount');

            $lowStockProducts = Product::whereBetween('stock', [1, 10])->count();
            $outOfStock       = Product::where('stock', '<=', 0)->count();

            // --- Data untuk Grafik ---

            // --- Orders per Hari (7 hari terakhir) ---
            $ordersLast7Days = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $orderDailyLabels = $ordersLast7Days->map(fn($r) =>
                \Carbon\Carbon::parse($r->date)->format('d M')
            );
            $orderDailyData = $ordersLast7Days->pluck('total');

            // --- Orders per Minggu (12 minggu terakhir) ---
            $ordersPerWeek = Order::select(
                    DB::raw('WEEK(created_at) as week'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subWeeks(12))
                ->groupBy('year', 'week')
                ->orderBy('year')
                ->orderBy('week')
                ->get();

            $orderWeeklyLabels = $ordersPerWeek->map(fn($r) =>
                'Wk ' . $r->week . ' (' . $r->year . ')'
            );
            $orderWeeklyData = $ordersPerWeek->pluck('total');

            // --- Orders per Bulan (6 bulan terakhir) ---
            $ordersPerMonth = Order::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $orderMonthlyLabels = $ordersPerMonth->map(fn($r) =>
                \Carbon\Carbon::createFromDate($r->year, $r->month, 1)->format('M Y')
            );
            $orderMonthlyData = $ordersPerMonth->pluck('total');

            // --- Orders per Tahun (5 tahun terakhir) ---
            $ordersPerYear = Order::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('created_at', '>=', now()->subYears(5))
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            $orderYearlyLabels = $ordersPerYear->map(fn($r) => (string)$r->year);
            $orderYearlyData = $ordersPerYear->pluck('total');

            // Revenue per bulan (6 bulan terakhir)
            $revenuePerMonth = Payment::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $revenueLabels = $revenuePerMonth->map(fn($r) =>
                \Carbon\Carbon::createFromDate($r->year, $r->month, 1)->format('M Y')
            );
            $revenueData = $revenuePerMonth->pluck('total');

            // Revenue 7 hari terakhir (Harian)
            $revenueLast7Days = Payment::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $revenueDailyLabels = $revenueLast7Days->map(fn($r) =>
                \Carbon\Carbon::parse($r->date)->format('d M')
            );
            $revenueDailyData = $revenueLast7Days->pluck('total');

            // Revenue per minggu (12 minggu terakhir)
            $revenuePerWeek = Payment::select(
                    DB::raw('WEEK(created_at) as week'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('created_at', '>=', now()->subWeeks(12))
                ->groupBy('year', 'week')
                ->orderBy('year')
                ->orderBy('week')
                ->get();

            $revenueWeekLabels = $revenuePerWeek->map(fn($r) =>
                'Wk ' . $r->week . ' (' . $r->year . ')'
            );
            $revenueWeekData = $revenuePerWeek->pluck('total');

            // Revenue per tahun (5 tahun terakhir)
            $revenuePerYear = Payment::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('created_at', '>=', now()->subYears(5))
                ->groupBy('year')
                ->orderBy('year')
                ->get();

            $revenueYearLabels = $revenuePerYear->map(fn($r) => (string)$r->year);
            $revenueYearData = $revenuePerYear->pluck('total');

            // Distribusi status order
            $orderStatusData = [
                Order::where('status', 'Pending')->count(),
                Order::where('status', 'Completed')->count(),
                Order::whereNotIn('status', ['Pending', 'Completed'])->count(),
            ];

            // Distribusi status delivery
            $deliveryStatusData = [
                Delivery::where('status', 'Scheduled')->count(),
                Delivery::where('status', 'On Delivery')->count(),
                Delivery::where('status', 'Delivered')->count(),
            ];

            // =============================================
            // PREDICATIVE ANALYTICS CALCULATIONS
            // =============================================
            
            // 1. Demand Forecasting (Simple Moving Average - SMA)
            // Ambil data total order bulanan selama 3 bulan terakhir
            $monthlySales = Order::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as total_orders'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(3)
                ->get();

            $forecastOrders = 0;
            $forecastRevenue = 0;
            $hasEnoughData = $monthlySales->count() >= 2;

            if ($monthlySales->isNotEmpty()) {
                $forecastOrders = round($monthlySales->avg('total_orders'));
                $forecastRevenue = $monthlySales->avg('total_revenue');
            }

            // 2. Customer Gas Run-out Prediction
            $customersList = Customer::all();
            $predictions = [];

            foreach ($customersList as $cust) {
                $custOrders = Order::with('product')
                    ->where('customer_id', $cust->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($custOrders->isEmpty()) {
                    continue;
                }

                $lastOrd = $custOrders->last();
                $lastPurchase = \Carbon\Carbon::parse($lastOrd->created_at);
                
                // Tentukan estimasi masa pakai gas berdasarkan jenis produk terakhir
                $avgInterval = 30; // default jika produk tidak terdeteksi
                if ($lastOrd->product) {
                    $pName = strtolower($lastOrd->product->name);
                    if (str_contains($pName, '3 kg') || str_contains($pName, '3kg') || str_contains($pName, 'hijau')) {
                        $avgInterval = 25;
                    } elseif (str_contains($pName, '5,5 kg') || str_contains($pName, '5.5kg') || str_contains($pName, '5,5kg')) {
                        $avgInterval = 45;
                    } elseif (str_contains($pName, '12 kg') || str_contains($pName, '12kg')) {
                        $avgInterval = 60;
                    }
                }

                $predDate = $lastPurchase->copy()->addDays($avgInterval);
                $remaining = round(now()->diffInDays($predDate, false));

                $predictions[] = [
                    'customer_name' => $cust->customer_name,
                    'customer_phone' => $cust->phone,
                    'last_product' => $lastOrd->product->name ?? 'Gas Cylinder',
                    'last_purchase' => $lastPurchase->format('d M Y'),
                    'predicted_date' => $predDate->format('d M Y'),
                    'raw_predicted_date' => $predDate,
                    'days_remaining' => $remaining,
                    'interval' => $avgInterval,
                    'is_calculated' => true
                ];
            }

            // Urutkan prediksi berdasarkan sisa hari tersedikit (tanggal habis terdekat)
            usort($predictions, function($a, $b) {
                return $a['raw_predicted_date'] <=> $b['raw_predicted_date'];
            });

            $predictions = array_slice($predictions, 0, 5);

            // Riwayat Aktivitas Terbaru
            $activityLogs = ActivityLog::with('user')->latest()->take(5)->get();

            return compact(
                'totalCustomers',
                'totalProducts',
                'totalOrders',
                'pendingOrders',
                'totalDeliveries',
                'completedDeliveries',
                'todayDeliveries',
                'unpaidInvoices',
                'paidInvoices',
                'totalPayments',
                'totalRevenue',
                'lowStockProducts',
                'outOfStock',
                'orderDailyLabels',
                'orderDailyData',
                'orderWeeklyLabels',
                'orderWeeklyData',
                'orderMonthlyLabels',
                'orderMonthlyData',
                'orderYearlyLabels',
                'orderYearlyData',
                'revenueLabels',
                'revenueData',
                'revenueDailyLabels',
                'revenueDailyData',
                'revenueWeekLabels',
                'revenueWeekData',
                'revenueYearLabels',
                'revenueYearData',
                'orderStatusData',
                'deliveryStatusData',
                'activityLogs',
                'forecastOrders',
                'forecastRevenue',
                'hasEnoughData',
                'predictions'
            );
        });

        return view('erp-dashboard', $data);
    }

    public function exportCSV(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $invoices = Invoice::with(['order.customer', 'order.product'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $fileName = 'Laporan_NAGA_SAKTI_JAYA_' . $month . '_' . $year . '.xls';

        $headers = array(
            "Content-type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($invoices) {
            $output = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
                <style>
                    table { border-collapse: collapse; }
                    th { background-color: #2563eb; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: left; padding: 10px; font-family: sans-serif; font-size: 11pt; }
                    td { border: 1px solid #000000; text-align: left; padding: 8px; font-family: sans-serif; font-size: 10pt; }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                </style>
            </head>
            <body>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 120px;">Invoice ID</th>
                            <th style="width: 120px;">Order ID</th>
                            <th style="width: 180px;">Customer</th>
                            <th style="width: 180px;">Product</th>
                            <th style="width: 80px;" class="text-center">Qty</th>
                            <th style="width: 130px;" class="text-right">Total Amount</th>
                            <th style="width: 100px;" class="text-center">Status</th>
                            <th style="width: 150px;">Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($invoices as $inv) {
                $invId = 'INV-' . str_pad($inv->id, 5, '0', STR_PAD_LEFT);
                $ordId = 'ORD-' . str_pad($inv->order_id, 5, '0', STR_PAD_LEFT);
                $cust = $inv->order->customer->customer_name ?? '-';
                $prod = $inv->order->product->name ?? '-';
                $qty = $inv->order->quantity ?? 0;
                $amount = (int)$inv->total_amount;
                $status = $inv->status;
                $date = $inv->created_at->format('d/m/Y H:i');

                $html .= "
                        <tr>
                            <td>{$invId}</td>
                            <td>{$ordId}</td>
                            <td>{$cust}</td>
                            <td>{$prod}</td>
                            <td class=\"text-center\">{$qty}</td>
                            <td class=\"text-right\">Rp " . number_format($amount, 0, ',', '.') . "</td>
                            <td class=\"text-center\">{$status}</td>
                            <td>{$date}</td>
                        </tr>";
            }

            $html .= '
                    </tbody>
                </table>
            </body>
            </html>';

            fwrite($output, $html);
            fclose($output);
        };

        ActivityLog::log('Export', 'Mengekspor laporan bulanan XLS untuk ' . $month . '/' . $year);

        return response()->stream($callback, 200, $headers);
    }

    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('activity-logs.index', compact('logs'));
    }

    public function getNotifications()
    {
        $user = auth()->user();
        $notifications = [];

        // Jika user adalah driver, hanya tampilkan notifikasi jika ada tugasnya yang berstatus 'On Delivery'
        if ($user && $user->isDriver()) {
            $driverDeliveries = Delivery::with(['order.customer'])
                ->where('driver_id', $user->driver_id)
                ->where('status', 'On Delivery')
                ->get();

            foreach ($driverDeliveries as $del) {
                $notifications[] = [
                    'type' => 'success',
                    'title' => 'Pengiriman On Delivery',
                    'message' => 'Pengiriman untuk Order #' . $del->order_id . ' ke ' . ($del->order->customer->customer_name ?? 'Pelanggan') . ' sedang dalam perjalanan!',
                    'time' => 'Sedang dikirim'
                ];
            }

            return response()->json($notifications);
        }

        // Jika administrator (admin/manager), tampilkan semua notifikasi sistem:
        // 1. Cek stok produk rendah (di bawah atau sama dengan 10)
        $lowStockProducts = Product::where('stock', '<=', 10)->get();
        foreach ($lowStockProducts as $product) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Stok Rendah',
                'message' => 'Stok produk ' . $product->name . ' sisa ' . $product->stock . ' tabung!',
                'time' => 'Segera restock'
            ];
        }

        // 2. Cek order pending
        $pendingOrders = Order::with('customer')->where('status', 'Pending')->get();
        foreach ($pendingOrders as $order) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Order Pending',
                'message' => 'Order #' . $order->id . ' oleh ' . ($order->customer->customer_name ?? 'Pelanggan') . ' menunggu konfirmasi.',
                'time' => $order->created_at->diffForHumans()
            ];
        }

        // 3. Cek invoice unpaid
        $unpaidInvoices = Invoice::with('order.customer')->where('status', 'Unpaid')->get();
        foreach ($unpaidInvoices as $inv) {
            $notifications[] = [
                'type' => 'danger',
                'title' => 'Invoice Belum Lunas',
                'message' => 'Invoice #INV-' . str_pad($inv->id, 4, '0', STR_PAD_LEFT) . ' untuk ' . ($inv->order->customer->customer_name ?? 'Pelanggan') . ' belum dibayar.',
                'time' => $inv->created_at->diffForHumans()
            ];
        }

        // 4. Cek pengiriman terjadwal hari ini
        $todayDeliveries = Delivery::with(['driver', 'order.customer'])->whereDate('delivery_date', today())->get();
        foreach ($todayDeliveries as $del) {
            $notifications[] = [
                'type' => 'success',
                'title' => 'Pengiriman Hari Ini',
                'message' => 'Kirim order #' . $del->order_id . ' oleh ' . ($del->driver->name ?? 'Driver') . ' dijadwalkan hari ini.',
                'time' => 'Hari ini'
            ];
        }

        // 5. Cek notifikasi pengiriman email (sukses/gagal) dalam 12 jam terakhir
        $emailLogs = ActivityLog::whereIn('action', ['Email Sent', 'Email Failed'])
            ->where('created_at', '>=', now()->subHours(12))
            ->orderBy('id', 'desc')
            ->get();
            
        foreach ($emailLogs as $log) {
            $isSuccess = $log->action === 'Email Sent';
            $notifications[] = [
                'type' => $isSuccess ? 'success' : 'danger',
                'title' => $isSuccess ? 'Email Terkirim' : 'Gagal Kirim Email',
                'message' => $log->description,
                'time' => $log->created_at->diffForHumans()
            ];
        }

        return response()->json($notifications);
    }
}