<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Models\Invoice;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer', 'product'])->get();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();

        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        // cek stok
        if ($request->quantity > $product->stock) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stock tidak mencukupi! Stock tersedia: ' . $product->stock);
        }

        $total = $product->price * $request->quantity;

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_amount' => $total,
            'order_date' => now(),
            'status' => 'Pending',
        ]);

        $product->decrement('stock', $request->quantity);

        // Otomatis buat Invoice
        Invoice::create([
            'order_id'     => $order->id,
            'total_amount' => $order->total_amount,
            'status'       => 'Unpaid',
        ]);

        ActivityLog::log('Create', 'Membuat order baru #' . $order->id . ' untuk ' . $order->customer->customer_name . ' (Qty: ' . $order->quantity . ')');

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dibuat!');
    }

    public function edit(Order $order)
    {
        $customers = Customer::all();
        $products = Product::all();

        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id'  => 'required|exists:products,id',
            'quantity'    => 'required|integer|min:1',
            'status'      => 'required',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Kembalikan stok lama, lalu cek stok dengan qty baru
        $oldQty    = $order->quantity;
        $stockBack = $product->stock + $oldQty;

        if ($request->quantity > $stockBack) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stock tidak mencukupi! Stock tersedia: ' . $stockBack);
        }

        $total = $product->price * $request->quantity;

        // Sesuaikan stok: kembalikan lama, kurangi baru
        $product->increment('stock', $oldQty);
        $product->decrement('stock', $request->quantity);

        $order->update([
            'customer_id'  => $request->customer_id,
            'product_id'   => $request->product_id,
            'quantity'     => $request->quantity,
            'total_amount' => $total,
            'status'       => $request->status,
        ]);

        // Otomatis sinkronisasi nominal Invoice
        $invoice = Invoice::where('order_id', $order->id)->first();
        if ($invoice) {
            $invoice->update([
                'total_amount' => $total
            ]);
        }

        ActivityLog::log('Update', 'Memperbarui order #' . $order->id . ' status: ' . $order->status);

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil diperbarui!');
    }

    public function destroy(Order $order)
    {
        $id = $order->id;
        $order->delete();

        ActivityLog::log('Delete', 'Menghapus order #' . $id);

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dihapus!');
    }
}