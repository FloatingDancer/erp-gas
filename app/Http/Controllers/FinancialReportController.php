<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isDriver()) {
            abort(403, 'Unauthorized action.');
        }

        $month = (int)$request->input('month', now()->month);
        $year = (int)$request->input('year', now()->year);

        $reportData = $this->calculateFinancialData($month, $year);

        // 6-Month Trend Data for Chart.js
        $trendLabels = [];
        $trendRevenues = [];
        $trendExpenses = [];
        $trendProfits = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->setYear($year)->setMonth($month)->subMonths($i);
            $tMonth = $dt->month;
            $tYear = $dt->year;
            $trendLabels[] = $dt->format('M Y');

            $mRev = Payment::whereMonth('created_at', $tMonth)->whereYear('created_at', $tYear)->sum('amount');
            $mRet = ProductReturn::whereMonth('return_date', $tMonth)->whereYear('return_date', $tYear)->where('status', 'Approved')->sum('refund_amount');
            $mNetRev = max(0, $mRev - $mRet);

            $mExp = Purchase::whereMonth('purchase_date', $tMonth)->whereYear('purchase_date', $tYear)->sum('total_amount');
            
            $trendRevenues[] = (float)$mNetRev;
            $trendExpenses[] = (float)$mExp;
            $trendProfits[] = (float)($mNetRev - $mExp);
        }

        return view('reports.laba-rugi', array_merge($reportData, compact(
            'month',
            'year',
            'trendLabels',
            'trendRevenues',
            'trendExpenses',
            'trendProfits'
        )));
    }

    public function print(Request $request)
    {
        if (auth()->user()->isDriver()) {
            abort(403, 'Unauthorized action.');
        }

        $month = (int)$request->input('month', now()->month);
        $year = (int)$request->input('year', now()->year);

        $reportData = $this->calculateFinancialData($month, $year);

        return view('reports.laba-rugi-print', array_merge($reportData, compact('month', 'year')));
    }

    public function exportCSV(Request $request)
    {
        if (auth()->user()->isDriver()) {
            abort(403, 'Unauthorized action.');
        }

        $month = (int)$request->input('month', now()->month);
        $year = (int)$request->input('year', now()->year);
        $data = $this->calculateFinancialData($month, $year);

        $monthName = \Carbon\Carbon::create()->month($month)->translatedFormat('F');
        $fileName = "Laporan_Laba_Rugi_{$monthName}_{$year}.csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data, $monthName, $year) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['LAPORAN LABA RUGI - TK. NAGA SAKTI JAYA']);
            fputcsv($file, ["Periode: {$monthName} {$year}"]);
            fputcsv($file, []);

            fputcsv($file, ['I. PENDAPATAN USAHA (REVENUES)']);
            fputcsv($file, ['Keterangan', 'Volume (Qty)', 'Jumlah (Rp)']);
            foreach ($data['revenueDetails'] as $name => $det) {
                fputcsv($file, ["Penjualan {$name}", $det['qty'], $det['amount']]);
            }
            fputcsv($file, ['Total Pendapatan Kotor (Gross Sales)', $data['totalUnitsSold'], $data['totalGrossRevenue']]);
            fputcsv($file, ['Dikurangi: Retur Penjualan & Refund', $data['totalUnitsReturned'], -$data['totalRefundAmount']]);
            fputcsv($file, ['TOTAL PENDAPATAN BERSIH (NET REVENUE)', $data['totalUnitsSold'] - $data['totalUnitsReturned'], $data['totalNetRevenue']]);
            fputcsv($file, []);

            fputcsv($file, ['II. HARGA POKOK PENJUALAN (HPP / COGS)']);
            fputcsv($file, ['Harga Pokok Penjualan (Modal Barang Terjual)', '', $data['totalCOGS']]);
            fputcsv($file, ['LABA KOTOR (GROSS PROFIT)', "Margin: {$data['grossProfitMargin']}%", $data['grossProfit']]);
            fputcsv($file, []);

            fputcsv($file, ['III. PENGELUARAN PENGADAAN STOK (EXPENSES)']);
            foreach ($data['expenseDetails'] as $name => $det) {
                fputcsv($file, ["Pembelian {$name}", $det['qty'], $det['amount']]);
            }
            fputcsv($file, ['TOTAL PENGELUARAN PENGADAAN STOK', $data['totalUnitsPurchased'], $data['totalExpense']]);
            fputcsv($file, []);

            fputcsv($file, ['IV. HASIL AKHIR KEUANGAN']);
            fputcsv($file, ['LABA / (RUGI) BERSIH OPERASIONAL', '', $data['netProfit']]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateFinancialData(int $month, int $year): array
    {
        // 1. Fetch payments (Gross Revenue)
        $payments = Payment::with('invoice.order.product')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $totalGrossRevenue = $payments->sum('amount');
        $totalUnitsSold = 0;
        $revenueDetails = [];
        $totalCOGS = 0;

        foreach ($payments as $pay) {
            $product = $pay->invoice->order->product ?? null;
            $prodName = $product->name ?? 'Lain-lain';
            $qty = $pay->invoice->order->quantity ?? 0;
            $totalUnitsSold += $qty;

            if (!isset($revenueDetails[$prodName])) {
                $revenueDetails[$prodName] = [
                    'qty' => 0,
                    'amount' => 0,
                    'unit_price' => $product->price ?? 0
                ];
            }
            $revenueDetails[$prodName]['qty'] += $qty;
            $revenueDetails[$prodName]['amount'] += $pay->amount;

            // Estimasi HPP / Modal: harga jual dikurangi margin atau rata-rata purchase price
            $costPerUnit = $product ? max(0, $product->price - 3000) : 0;
            $totalCOGS += ($qty * $costPerUnit);
        }

        // 2. Fetch Returns & Refunds
        $returns = ProductReturn::with('product')
            ->whereMonth('return_date', $month)
            ->whereYear('return_date', $year)
            ->where('status', 'Approved')
            ->get();

        $totalRefundAmount = $returns->sum('refund_amount');
        $totalUnitsReturned = $returns->sum('quantity');
        $returnDetails = [];

        foreach ($returns as $ret) {
            $prodName = $ret->product->name ?? 'Lain-lain';
            if (!isset($returnDetails[$prodName])) {
                $returnDetails[$prodName] = [
                    'qty' => 0,
                    'damaged_qty' => 0,
                    'refund_amount' => 0,
                ];
            }
            $returnDetails[$prodName]['qty'] += $ret->quantity;
            if ($ret->condition === 'Damaged') {
                $returnDetails[$prodName]['damaged_qty'] += $ret->quantity;
            }
            $returnDetails[$prodName]['refund_amount'] += $ret->refund_amount;
        }

        // 3. Net Revenue & Gross Profit
        $totalNetRevenue = max(0, $totalGrossRevenue - $totalRefundAmount);
        $grossProfit = $totalNetRevenue - $totalCOGS;
        $grossProfitMargin = $totalNetRevenue > 0 ? round(($grossProfit / $totalNetRevenue) * 100, 1) : 0;

        // 4. Fetch Purchases (Expenses)
        $purchases = Purchase::with(['product', 'supplier'])
            ->whereMonth('purchase_date', $month)
            ->whereYear('purchase_date', $year)
            ->get();

        $totalExpense = $purchases->sum('total_amount');
        $totalUnitsPurchased = $purchases->sum('quantity');
        $expenseDetails = [];

        foreach ($purchases as $pur) {
            $prodName = $pur->product->name ?? 'Lain-lain';
            if (!isset($expenseDetails[$prodName])) {
                $expenseDetails[$prodName] = [
                    'qty' => 0,
                    'amount' => 0
                ];
            }
            $expenseDetails[$prodName]['qty'] += $pur->quantity;
            $expenseDetails[$prodName]['amount'] += $pur->total_amount;
        }

        // 5. Net Cash Flow / Net Profit
        $netProfit = $totalNetRevenue - $totalExpense;

        return compact(
            'totalGrossRevenue',
            'totalRefundAmount',
            'totalUnitsReturned',
            'returnDetails',
            'totalNetRevenue',
            'totalCOGS',
            'grossProfit',
            'grossProfitMargin',
            'totalUnitsSold',
            'revenueDetails',
            'totalExpense',
            'totalUnitsPurchased',
            'expenseDetails',
            'netProfit'
        );
    }
}
