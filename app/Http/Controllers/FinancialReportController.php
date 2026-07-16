<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->isDriver()) {
            abort(403, 'Unauthorized action.');
        }

        $month = (int)$request->input('month', now()->month);
        $year = (int)$request->input('year', now()->year);

        // Fetch payments (revenue) for that month
        $payments = Payment::with('invoice.order.product')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $totalRevenue = $payments->sum('amount');

        // Group revenue by product
        $revenueDetails = [];
        foreach ($payments as $pay) {
            $prodName = $pay->invoice->order->product->name ?? 'Lain-lain';
            if (!isset($revenueDetails[$prodName])) {
                $revenueDetails[$prodName] = [
                    'qty' => 0,
                    'amount' => 0
                ];
            }
            $revenueDetails[$prodName]['qty'] += $pay->invoice->order->quantity ?? 0;
            $revenueDetails[$prodName]['amount'] += $pay->amount;
        }

        // Fetch purchases (expense) for that month
        $purchases = Purchase::with(['product', 'supplier'])
            ->whereMonth('purchase_date', $month)
            ->whereYear('purchase_date', $year)
            ->get();

        $totalExpense = $purchases->sum('total_amount');

        // Group expense by product
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

        $netProfit = $totalRevenue - $totalExpense;

        return view('reports.laba-rugi', compact(
            'month',
            'year',
            'totalRevenue',
            'revenueDetails',
            'totalExpense',
            'expenseDetails',
            'netProfit'
        ));
    }

    public function print(Request $request)
    {
        if (auth()->user()->isDriver()) {
            abort(403, 'Unauthorized action.');
        }

        $month = (int)$request->input('month', now()->month);
        $year = (int)$request->input('year', now()->year);

        // Fetch payments (revenue) for that month
        $payments = Payment::with('invoice.order.product')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $totalRevenue = $payments->sum('amount');

        // Group revenue by product
        $revenueDetails = [];
        foreach ($payments as $pay) {
            $prodName = $pay->invoice->order->product->name ?? 'Lain-lain';
            if (!isset($revenueDetails[$prodName])) {
                $revenueDetails[$prodName] = [
                    'qty' => 0,
                    'amount' => 0
                ];
            }
            $revenueDetails[$prodName]['qty'] += $pay->invoice->order->quantity ?? 0;
            $revenueDetails[$prodName]['amount'] += $pay->amount;
        }

        // Fetch purchases (expense) for that month
        $purchases = Purchase::with(['product', 'supplier'])
            ->whereMonth('purchase_date', $month)
            ->whereYear('purchase_date', $year)
            ->get();

        $totalExpense = $purchases->sum('total_amount');

        // Group expense by product
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

        $netProfit = $totalRevenue - $totalExpense;

        return view('reports.laba-rugi-print', compact(
            'month',
            'year',
            'totalRevenue',
            'revenueDetails',
            'totalExpense',
            'expenseDetails',
            'netProfit'
        ));
    }
}
