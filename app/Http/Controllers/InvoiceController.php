<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['order.customer', 'order.product'])->get();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        // Tampilkan order yang belum Completed dan belum memiliki invoice
        $orders = Order::where('status', '!=', 'Completed')
            ->whereDoesntHave('invoice')
            ->get();
        return view('invoices.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id'     => 'required|exists:orders,id',
            'total_amount' => 'required|numeric|min:0',
            'status'       => 'required|in:Unpaid,Paid',
        ]);

        $invoice = Invoice::create([
            'order_id'     => $request->order_id,
            'total_amount' => $request->total_amount,
            'status'       => $request->status,
        ]);

        ActivityLog::log('Create', 'Membuat invoice baru #' . $invoice->id . ' untuk Order #' . $invoice->order_id . ' (Total: Rp ' . number_format($invoice->total_amount, 0, ',', '.') . ')');

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil ditambahkan!');
    }

    public function edit(Invoice $invoice)
    {
        // Tampilkan order yang belum Completed dan belum memiliki invoice, atau yang sedang digunakan oleh invoice ini sendiri
        $orders = Order::where(function($q) use ($invoice) {
            $q->where('status', '!=', 'Completed')
              ->whereDoesntHave('invoice');
        })->orWhere('id', $invoice->order_id)
        ->get();

        return view('invoices.edit', compact('invoice', 'orders'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'order_id'     => 'required|exists:orders,id',
            'total_amount' => 'required|numeric|min:0',
            'status'       => 'required|in:Unpaid,Paid',
        ]);

        $invoice->update([
            'order_id'     => $request->order_id,
            'total_amount' => $request->total_amount,
            'status'       => $request->status,
        ]);

        ActivityLog::log('Update', 'Memperbarui invoice #' . $invoice->id . ' status: ' . $invoice->status);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil diperbarui!');
    }

    public function destroy(Invoice $invoice)
    {
        $id = $invoice->id;
        $invoice->delete();

        ActivityLog::log('Delete', 'Menghapus invoice #' . $id);

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil dihapus!');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['order.customer', 'order.product']);
        return view('invoices.print', compact('invoice'));
    }

    public function printPublic($id)
    {
        try {
            $invoice = Invoice::with(['order.customer', 'order.product'])->findOrFail($id);
            return view('invoices.print', compact('invoice'));
        } catch (\Throwable $e) {
            return response("Error: " . $e->getMessage() . "\n" . $e->getTraceAsString(), 500)
                ->header('Content-Type', 'text/plain');
        }
    }
}