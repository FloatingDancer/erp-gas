<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice.order.customer'])->get();
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        // Hanya tampilkan invoice yang statusnya Unpaid
        $invoices = Invoice::where('status', 'Unpaid')->get();
        return view('payments.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount'     => 'required|numeric|min:1',
            'method'     => 'required|in:Cash,Transfer,E-Wallet',
        ]);

        $payment = Payment::create([
            'invoice_id' => $request->invoice_id,
            'amount'     => $request->amount,
            'method'     => $request->method,
        ]);

        // Otomatis ubah status Invoice menjadi Paid & sinkronkan Order menjadi Paid
        $invoice = Invoice::find($request->invoice_id);
        if ($invoice) {
            $invoice->update(['status' => 'Paid']);
            if ($invoice->order) {
                $invoice->order->update(['status' => 'Paid']);
            }
        }

        ActivityLog::log('Create', 'Mencatat pembayaran baru untuk Invoice #' . $payment->invoice_id . ' sebesar Rp ' . number_format($payment->amount, 0, ',', '.') . ' via ' . $payment->method);

        return redirect()->route('payments.index')
            ->with('success', 'Payment berhasil ditambahkan!');
    }

    public function edit(Payment $payment)
    {
        // Tampilkan invoice yang statusnya Unpaid, atau yang sedang digunakan oleh pembayaran ini sendiri
        $invoices = Invoice::where('status', 'Unpaid')
            ->orWhere('id', $payment->invoice_id)
            ->get();
        return view('payments.edit', compact('payment', 'invoices'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount'     => 'required|numeric|min:1',
            'method'     => 'required|in:Cash,Transfer,E-Wallet',
        ]);

        $payment->update([
            'invoice_id' => $request->invoice_id,
            'amount'     => $request->amount,
            'method'     => $request->method,
        ]);

        ActivityLog::log('Update', 'Memperbarui data pembayaran #' . $payment->id);

        return redirect()->route('payments.index')
            ->with('success', 'Payment berhasil diperbarui!');
    }

    public function destroy(Payment $payment)
    {
        $id = $payment->id;
        $invoiceId = $payment->invoice_id;
        $payment->delete();

        // Cek apakah masih ada payment lain untuk invoice ini
        $remainingPayments = Payment::where('invoice_id', $invoiceId)->count();
        if ($remainingPayments === 0) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $invoice->update(['status' => 'Unpaid']);
                if ($invoice->order) {
                    $invoice->order->update(['status' => 'Pending']);
                }
            }
        }

        ActivityLog::log('Delete', 'Menghapus pembayaran #' . $id);

        return redirect()->route('payments.index')
            ->with('success', 'Payment berhasil dihapus!');
    }
}