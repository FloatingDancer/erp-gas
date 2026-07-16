<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'product'])->get();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'product_id'     => 'required|exists:products,id',
            'quantity'       => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        $totalAmount = $request->quantity * $request->purchase_price;

        $purchase = Purchase::create([
            'supplier_id'    => $request->supplier_id,
            'product_id'     => $request->product_id,
            'quantity'       => $request->quantity,
            'purchase_price' => $request->purchase_price,
            'total_amount'   => $totalAmount,
            'purchase_date'  => now(),
            'status'         => 'Pending',
        ]);

        ActivityLog::log('Create', 'Membuat PO baru #' . $purchase->id . ' ke Supplier: ' . $purchase->supplier->name);

        return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    public function receive(Purchase $purchase)
    {
        if ($purchase->status === 'Received') {
            return redirect()->back()->with('error', 'Item pembelian ini sudah diterima sebelumnya.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($purchase) {
            $purchase->update(['status' => 'Received']);
            
            $product = $purchase->product;
            $product->increment('stock', $purchase->quantity);
            
            ActivityLog::log('Update', 'Menerima stok PO #' . $purchase->id . ' dari ' . $purchase->supplier->name . '. Stok ' . $product->name . ' bertambah +' . $purchase->quantity);
        });

        return redirect()->route('purchases.index')->with('success', 'Barang berhasil diterima! Stok produk telah bertambah.');
    }

    public function destroy(Purchase $purchase)
    {
        if ($purchase->status === 'Received') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Purchase Order yang sudah berstatus diterima.');
        }

        $id = $purchase->id;
        $purchase->delete();

        ActivityLog::log('Delete', 'Menghapus PO #' . $id);

        return redirect()->route('purchases.index')->with('success', 'Purchase Order berhasil dihapus!');
    }
}
