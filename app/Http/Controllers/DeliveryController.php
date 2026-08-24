<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\Driver;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user && $user->isDriver()) {
            $deliveries = Delivery::with(['order', 'driver'])
                ->where('driver_id', $user->driver_id)
                ->where('status', 'Delivered')
                ->get();
        } else {
            $deliveries = Delivery::with(['order', 'driver'])->get();
        }
        $isLiveOrderPage = false;
        return view('deliveries.index', compact('deliveries', 'isLiveOrderPage'));
    }

    public function liveOrders()
    {
        $user = auth()->user();
        if (!$user || !$user->isDriver()) {
            abort(403);
        }
        $deliveries = Delivery::with(['order.customer', 'order.product', 'driver'])
            ->where('driver_id', $user->driver_id)
            ->where('status', '!=', 'Delivered')
            ->get();
        $isLiveOrderPage = true;
        return view('deliveries.index', compact('deliveries', 'isLiveOrderPage'));
    }

    public function create()
    {
        // Tampilkan order yang belum memiliki data pengiriman (delivery)
        $orders = Order::whereDoesntHave('delivery')->get();
        $drivers = Driver::where('status', 'Active')->get();
        return view('deliveries.create', compact('orders', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:drivers,id',
            'delivery_date' => 'required|date',
            'status' => 'required',
        ]);

        $driver = Driver::find($request->driver_id);
        $data = $request->all();
        $data['driver_name'] = $driver->name;

        $delivery = Delivery::create($data);

        // Jika status delivery adalah 'Delivered', otomatis set status order menjadi 'Completed'
        if ($delivery->status === 'Delivered') {
            $delivery->order->update(['status' => 'Completed']);
        }

        ActivityLog::log('Create', 'Menjadwalkan pengiriman untuk Order #' . $request->order_id . ' dengan driver ' . $driver->name);

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery berhasil ditambahkan!');
    }

    public function edit(Delivery $delivery)
    {
        // Tampilkan order yang belum memiliki data pengiriman, atau yang sedang digunakan oleh pengiriman ini sendiri
        $orders = Order::whereDoesntHave('delivery')
            ->orWhere('id', $delivery->order_id)
            ->get();
        $drivers = Driver::all();
        return view('deliveries.edit', compact('delivery', 'orders', 'drivers'));
    }

    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:drivers,id',
            'delivery_date' => 'required|date',
            'status' => 'required',
        ]);

        $driver = Driver::find($request->driver_id);
        $data = $request->all();
        $data['driver_name'] = $driver->name;

        $delivery->update($data);

        // Jika status delivery adalah 'Delivered', otomatis set status order menjadi 'Completed'
        if ($delivery->status === 'Delivered') {
            if ($delivery->order) {
                $delivery->order->update(['status' => 'Completed']);
            }
        } else {
            // Jika status pengiriman diubah dari Delivered ke status lain (misal kembali ke Scheduled/On Delivery), 
            // kembalikan status order menjadi 'Pending'
            if ($delivery->order && $delivery->order->status === 'Completed') {
                $delivery->order->update(['status' => 'Pending']);
            }
        }

        ActivityLog::log('Update', 'Memperbarui pengiriman #' . $delivery->id . ' status: ' . $delivery->status);

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery berhasil diperbarui!');
    }

    public function destroy(Delivery $delivery)
    {
        $id = $delivery->id;
        $delivery->delete();

        ActivityLog::log('Delete', 'Menghapus pengiriman #' . $id);

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery berhasil dihapus!');
    }

    public function confirmArrival(Delivery $delivery)
    {
        $delivery->update([
            'status'    => 'Delivered',
            'latitude'  => null,
            'longitude' => null,
        ]);
        
        if ($delivery->driver) {
            $delivery->driver->update([
                'latitude'  => null,
                'longitude' => null,
            ]);
        }

        if ($delivery->order) {
            $delivery->order->update(['status' => 'Completed']);
        }

        $delivery->load(['order.customer', 'order.product', 'driver']);
        $customer = $delivery->order->customer ?? null;
        $custName = $customer->customer_name ?? 'Pelanggan';

        if ($customer && $customer->phone) {
            $phone = preg_replace('/[^0-9]/', '', $customer->phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $prodName = $delivery->order->product->name ?? 'Gas Elpiji';
            $driverName = $delivery->driver->name ?? $delivery->driver_name ?? 'Driver Kami';
            
            $waMsg = "Halo *{$custName}*,\n\nDriver kami (*{$driverName}*) telah mengonfirmasi bahwa pesanan tabung gas Anda telah *SAMPAI & DITERIMA* dengan baik di alamat Anda.\n\n"
                   . "*Detail Pengiriman:*\n"
                   . "- No. DO: #DO-" . str_pad($delivery->id, 5, '0', STR_PAD_LEFT) . "\n"
                   . "- Produk: {$prodName} ({$delivery->order->quantity} Tabung)\n"
                   . "- Waktu Sampai: " . now()->format('d M Y H:i') . " WIB\n\n"
                   . "Terima kasih telah berbelanja di *TK. NAGA SAKTI JAYA*!";
                   
            $waUrl = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . rawurlencode($waMsg);
            session()->flash('wa_url', $waUrl);
        }

        ActivityLog::log('Update', 'Mengonfirmasi pengiriman #' . $delivery->id . ' telah sampai di tempat pelanggan: ' . $custName);

        return redirect()->back()->with('success', 'Pengiriman berhasil dikonfirmasi sampai di lokasi pelanggan!');
    }

    public function printDO(Delivery $delivery)
    {
        $delivery->load(['order.customer', 'order.product', 'driver']);
        return view('deliveries.print-do', compact('delivery'));
    }

    public function printDOPublic($id)
    {
        try {
            $delivery = Delivery::with(['order.customer', 'order.product', 'driver'])->findOrFail($id);
            return view('deliveries.print-do', compact('delivery'));
        } catch (\Throwable $e) {
            return response("Error: " . $e->getMessage(), 500)->header('Content-Type', 'text/plain');
        }
    }
}