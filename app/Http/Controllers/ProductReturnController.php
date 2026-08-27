<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Delivery;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductReturnController extends Controller
{
    private function ensureSchema()
    {
        try {
            if (!Schema::hasColumn('product_returns', 'supplier_id')) {
                Schema::table('product_returns', function ($table) {
                    $table->string('return_category')->default('Customer')->nullable();
                    $table->unsignedBigInteger('supplier_id')->nullable();
                    $table->unsignedBigInteger('purchase_id')->nullable();
                });
            }
        } catch (\Throwable $e) {
            try {
                DB::statement("ALTER TABLE `product_returns` ADD `return_category` VARCHAR(50) DEFAULT 'Customer' NULL");
            } catch (\Throwable $ex) {}
            try {
                DB::statement("ALTER TABLE `product_returns` ADD `supplier_id` BIGINT UNSIGNED NULL");
            } catch (\Throwable $ex) {}
            try {
                DB::statement("ALTER TABLE `product_returns` ADD `purchase_id` BIGINT UNSIGNED NULL");
            } catch (\Throwable $ex) {}
        }
    }

    public function index()
    {
        $this->ensureSchema();

        $relations = ['customer', 'product', 'order', 'delivery'];
        if (Schema::hasColumn('product_returns', 'supplier_id')) {
            $relations[] = 'supplier';
        }
        if (Schema::hasColumn('product_returns', 'purchase_id')) {
            $relations[] = 'purchase';
        }

        $returns = ProductReturn::with($relations)
            ->latest()
            ->get();

        $totalGood = $returns->where('condition', 'Good')->where('status', 'Approved')->sum('quantity');
        $totalDamaged = $returns->where('condition', 'Damaged')->where('status', 'Approved')->sum('quantity');
        $totalRefund = $returns->where('status', 'Approved')->sum('refund_amount');

        return view('returns.index', compact('returns', 'totalGood', 'totalDamaged', 'totalRefund'));
    }

    public function create(Request $request)
    {
        $this->ensureSchema();

        $customers = Customer::orderBy('customer_name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        $selectedOrderId = $request->get('order_id');
        $selectedDeliveryId = $request->get('delivery_id');
        $selectedPurchaseId = $request->get('purchase_id');
        
        $selectedOrder = $selectedOrderId ? Order::with(['customer', 'product', 'delivery'])->find($selectedOrderId) : null;
        $selectedDelivery = $selectedDeliveryId ? Delivery::with(['order.customer', 'order.product'])->find($selectedDeliveryId) : null;
        $selectedPurchase = $selectedPurchaseId ? Purchase::with(['supplier', 'product'])->find($selectedPurchaseId) : null;

        $orders = Order::with(['customer', 'product'])->latest()->take(50)->get();
        $deliveries = Delivery::with(['order.customer', 'order.product'])->latest()->take(50)->get();
        $purchases = Purchase::with(['supplier', 'product'])->latest()->take(50)->get();

        return view('returns.create', compact('customers', 'suppliers', 'products', 'orders', 'purchases', 'deliveries', 'selectedOrder', 'selectedDelivery', 'selectedPurchase'));
    }

    public function store(Request $request)
    {
        $this->ensureSchema();

        $request->validate([
            'return_category' => 'required|in:Customer,Supplier',
            'customer_id'     => 'nullable|required_if:return_category,Customer|exists:customers,id',
            'supplier_id'     => 'nullable|required_if:return_category,Supplier|exists:suppliers,id',
            'product_id'      => 'required|exists:products,id',
            'order_id'        => 'nullable|exists:orders,id',
            'purchase_id'     => 'nullable|exists:purchases,id',
            'delivery_id'     => 'nullable|exists:deliveries,id',
            'quantity'        => 'required|integer|min:1',
            'condition'       => 'required|in:Good,Damaged',
            'return_type'     => 'required|in:Exchange,Refund,Credit',
            'refund_amount'   => 'nullable|numeric|min:0',
            'reason'          => 'nullable|string',
            'status'          => 'required|in:Pending,Approved,Rejected',
            'return_date'     => 'required|date',
        ]);

        $prefix = $request->return_category === 'Supplier' ? 'RETSUP-' : 'RET-';
        $returnNumber = $prefix . date('Ymd') . '-' . str_pad((ProductReturn::count() + 1), 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($request, $returnNumber) {
            $data = [
                'return_number'   => $returnNumber,
                'customer_id'     => $request->return_category === 'Customer' ? $request->customer_id : null,
                'product_id'      => $request->product_id,
                'order_id'        => $request->order_id,
                'delivery_id'     => $request->delivery_id,
                'quantity'        => $request->quantity,
                'condition'       => $request->condition,
                'return_type'     => $request->return_type,
                'refund_amount'   => $request->refund_amount ?? 0,
                'reason'          => $request->reason,
                'status'          => $request->status,
                'return_date'     => $request->return_date,
            ];

            if (Schema::hasColumn('product_returns', 'return_category')) {
                $data['return_category'] = $request->return_category;
            }
            if (Schema::hasColumn('product_returns', 'supplier_id')) {
                $data['supplier_id'] = $request->return_category === 'Supplier' ? $request->supplier_id : null;
            }
            if (Schema::hasColumn('product_returns', 'purchase_id')) {
                $data['purchase_id'] = $request->purchase_id;
            }

            $productReturn = ProductReturn::create($data);

            $product = Product::find($request->product_id);

            // Jika status disetujui (Approved), otomatis sesuaikan inventaris
            if ($productReturn->status === 'Approved' && $product) {
                if (($productReturn->return_category ?? 'Customer') === 'Supplier') {
                    // Retur ke Supplier: keluarkan tabung rusak dari stok karantina gudang
                    $product->decrement('damaged_stock', min($productReturn->quantity, $product->damaged_stock));
                    
                    // Jika ditukar tabung baru oleh supplier, tambah ke stok siap jual
                    if ($productReturn->return_type === 'Exchange') {
                        $product->increment('stock', $productReturn->quantity);
                    }
                    
                    $logDesc = "Retur ke Supplier #{$returnNumber} disetujui. {$productReturn->quantity} tabung rusak dikembalikan ke Supplier ({$product->name}).";
                } else {
                    // Retur dari Pelanggan
                    if ($productReturn->condition === 'Good') {
                        $product->increment('stock', $productReturn->quantity);
                        $logDesc = "Retur #{$returnNumber} disetujui. {$productReturn->quantity} tabung kondisi Bagus dikembalikan ke Stok Siap Jual ({$product->name}).";
                    } else {
                        $product->increment('damaged_stock', $productReturn->quantity);
                        $logDesc = "Retur #{$returnNumber} disetujui. {$productReturn->quantity} tabung kondisi Rusak/Bocor dimasukkan ke Stok Rusak ({$product->name}).";
                    }
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
                if (($return->return_category ?? 'Customer') === 'Supplier') {
                    $product->decrement('damaged_stock', min($return->quantity, $product->damaged_stock));
                    if ($return->return_type === 'Exchange') {
                        $product->increment('stock', $return->quantity);
                    }
                    $logDesc = "Menyetujui Retur ke Supplier #{$return->return_number}. {$return->quantity} tabung rusak dikembalikan ke Supplier.";
                } else {
                    if ($return->condition === 'Good') {
                        $product->increment('stock', $return->quantity);
                        $logDesc = "Menyetujui Retur #{$return->return_number}. {$return->quantity} tabung kondisi Bagus dimasukkan kembali ke Stok Siap Jual.";
                    } else {
                        $product->increment('damaged_stock', $return->quantity);
                        $logDesc = "Menyetujui Retur #{$return->return_number}. {$return->quantity} tabung kondisi Rusak/Bocor dimasukkan ke Stok Rusak.";
                    }
                }
                ActivityLog::log('Update', $logDesc);
            }
        });

        return redirect()->route('returns.index')->with('success', 'Retur barang berhasil disetujui dan stok inventaris telah otomatis diperbarui!');
    }

    public function reject(Request $request, ProductReturn $return)
    {
        if ($return->status === 'Approved') {
            return redirect()->back()->with('error', 'Retur yang sudah disetujui tidak dapat ditolak.');
        }

        $return->update(['status' => 'Rejected']);
        ActivityLog::log('Update', "Menolak permohonan retur #{$return->return_number}");

        return redirect()->route('returns.index')->with('success', 'Permohonan retur barang telah ditolak.');
    }

    public function destroy(ProductReturn $return)
    {
        DB::transaction(function () use ($return) {
            // Jika retur sudah Approved dan dihapus, kembalikan stok
            if ($return->status === 'Approved' && $return->product) {
                if (($return->return_category ?? 'Customer') === 'Supplier') {
                    $return->product->increment('damaged_stock', $return->quantity);
                    if ($return->return_type === 'Exchange') {
                        $return->product->decrement('stock', min($return->quantity, $return->product->stock));
                    }
                } else {
                    if ($return->condition === 'Good') {
                        $return->product->decrement('stock', min($return->quantity, $return->product->stock));
                    } else {
                        $return->product->decrement('damaged_stock', min($return->quantity, $return->product->damaged_stock));
                    }
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
        $this->ensureSchema();
        $relations = ['customer', 'product', 'order', 'delivery.driver'];
        if (Schema::hasColumn('product_returns', 'supplier_id')) {
            $relations[] = 'supplier';
        }
        if (Schema::hasColumn('product_returns', 'purchase_id')) {
            $relations[] = 'purchase';
        }
        $return->load($relations);
        return view('returns.print', compact('return'));
    }

    public function printPublic($id)
    {
        try {
            $this->ensureSchema();
            $relations = ['customer', 'product', 'order', 'delivery.driver'];
            if (Schema::hasColumn('product_returns', 'supplier_id')) {
                $relations[] = 'supplier';
            }
            if (Schema::hasColumn('product_returns', 'purchase_id')) {
                $relations[] = 'purchase';
            }
            $return = ProductReturn::with($relations)->findOrFail($id);
            return view('returns.print', compact('return'));
        } catch (\Throwable $e) {
            return response("Error: " . $e->getMessage(), 500)->header('Content-Type', 'text/plain');
        }
    }
}
