<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReturnController extends Controller
{
    public function index()
    {
        $returns = ProductReturn::with(['customer', 'product', 'order', 'delivery'])
            ->latest()
            ->get();

        $totalGood = $returns->where('condition', 'Good')->where('status', 'Approved')->sum('quantity');
        $totalDamaged = $returns->where('condition', 'Damaged')->where('status', 'Approved')->sum('quantity');
        $totalRefund = $returns->where('status', 'Approved')->sum('refund_amount');

        return view('returns.index', compact('returns', 'totalGood', 'totalDamaged', 'totalRefund'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::orderBy('name')->get();
        
        $selectedOrderId = $request->get('order_id');
        $selectedDeliveryId = $request->get('delivery_id');
        
        $selectedOrder = $selectedOrderId ? Order::with(['customer', 'product', 'delivery'])->find($selectedOrderId) : null;
        $selectedDelivery = $selectedDeliveryId ? Delivery::with(['order.customer', 'order.product'])->find($selectedDeliveryId) : null;

        $orders = Order::with(['customer', 'product'])->latest()->take(50)->get();
        $deliveries = Delivery::with(['order.customer', 'order.product'])->latest()->take(50)->get();

        return view('returns.create', compact('customers', 'products', 'orders', 'deliveries', 'selectedOrder', 'selectedDelivery'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'product_id'    => 'required|exists:products,id',
            'order_id'      => 'nullable|exists:orders,id',
            'delivery_id'   => 'nullable|exists:deliveries,id',
            'quantity'      => 'required|integer|min:1',
            'condition'     => 'required|in:Good,Damaged',
            'return_type'   => 'required|in:Exchange,Refund,Credit',
            'refund_amount' => 'nullable|numeric|min:0',
            'reason'        => 'nullable|string',
            'status'        => 'required|in:Pending,Approved,Rejected',
            'return_date'   => 'required|date',
        ]);

        $returnNumber = 'RET-' . date('Ymd') . '-' . str_pad((ProductReturn::count() + 1), 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $returnNumber) {
            $productReturn = ProductReturn::create([
                'return_number' => $returnNumber,
                'customer_id'   => $request->customer_id,
                'product_id'    => $request->product_id,
                'order_id'      => $request->order_id,
                'delivery_id'   => $request->delivery_id,
                'quantity'      => $request->quantity,
                'condition'     => $request->condition,
                'return_type'   => $request->return_type,
                'refund_amount' => $request->refund_amount ?? 0,
                'reason'        => $request->reason,
                'status'        => $request->status,
                'return_date'   => $request->return_date,
            ]);

            $product = Product::find($request->product_id);

            // Jika status disetujui (Approved), otomatis sesuaikan inventaris
            if ($productReturn->status === 'Approved' && $product) {
                if ($productReturn->condition === 'Good') {
                    $product->increment('stock', $productReturn->quantity);
                    $logDesc = "Retur #{$returnNumber} disetujui. {$productReturn->quantity} tabung kondisi Bagus dikembalikan ke Stok Siap Jual ({$product->name}).";
                } else {
                    $product->increment('damaged_stock', $productReturn->quantity);
                    $logDesc = "Retur #{$returnNumber} disetujui. {$productReturn->quantity} tabung kondisi Rusak/Bocor dimasukkan ke Stok Rusak ({$product->name}).";
                }
                ActivityLog::log('Create', $logDesc);
            } else {
                ActivityLog::log('Create', "Mencatat permohonan retur baru #{$returnNumber} (Status: {$productReturn->status})");
            }
        });

        return redirect()->route('returns.index')->with('success', 'Data retur barang berhasil disimpan!');
    }

    public function approve(ProductReturn $return)
    {
        if ($return->status === 'Approved') {
            return redirect()->back()->with('error', 'Retur ini sudah disetujui sebelumnya.');
        }

        DB::transaction(function () use ($return) {
            $return->update(['status' => 'Approved']);
            $product = $return->product;

            if ($product) {
                if ($return->condition === 'Good') {
                    $product->increment('stock', $return->quantity);
                    $logDesc = "Menyetujui Retur #{$return->return_number}. {$return->quantity} tabung kondisi Bagus dimasukkan kembali ke Stok Siap Jual.";
                } else {
                    $product->increment('damaged_stock', $return->quantity);
                    $logDesc = "Menyetujui Retur #{$return->return_number}. {$return->quantity} tabung kondisi Rusak/Bocor dimasukkan ke Stok Rusak.";
                }
                ActivityLog::log('Update', $logDesc);
            }
        });

        return redirect()->route('returns.index')->with('success', 'Retur barang berhasil disetujui dan stok inventaris telah otomatis diperbarui!');
    }

    public function destroy(ProductReturn $return)
    {
        DB::transaction(function () use ($return) {
            // Jika retur sudah Approved dan dihapus, kembalikan stok
            if ($return->status === 'Approved' && $return->product) {
                if ($return->condition === 'Good') {
                    $return->product->decrement('stock', min($return->quantity, $return->product->stock));
                } else {
                    $return->product->decrement('damaged_stock', min($return->quantity, $return->product->damaged_stock));
                }
            }

            $num = $return->return_number;
            $return->delete();
            ActivityLog::log('Delete', "Menghapus data retur #{$num}");
        });

        return redirect()->route('returns.index')->with('success', 'Data retur barang berhasil dihapus!');
    }

    public function print(ProductReturn $return)
    {
        $return->load(['customer', 'product', 'order', 'delivery.driver']);
        return view('returns.print', compact('return'));
    }

    public function printPublic($id)
    {
        try {
            $return = ProductReturn::with(['customer', 'product', 'order', 'delivery.driver'])->findOrFail($id);
            return view('returns.print', compact('return'));
        } catch (\Throwable $e) {
            return response("Error: " . $e->getMessage(), 500)->header('Content-Type', 'text/plain');
        }
    }
}
