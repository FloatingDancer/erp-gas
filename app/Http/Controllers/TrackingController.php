<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Driver;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        if (!auth()->user() || auth()->user()->role !== 'manager') {
            abort(403, 'Akses ditolak. Hanya Manager yang dapat memantau lokasi driver.');
        }

        return view('tracking.index');
    }

    public function getCoordinates()
    {
        // 1. Dapatkan pengiriman aktif yang sedang 'On Delivery'
        $deliveries = Delivery::with(['order.customer', 'driver'])
            ->where('status', 'On Delivery')
            ->get();

        $deliveryDriverIds = $deliveries->pluck('driver_id')->filter()->toArray();

        $data = $deliveries->map(function ($del) {
            return [
                'id'                 => 'del-' . $del->id,
                'db_id'              => $del->id,
                'is_active_delivery' => true,
                'order_id'           => $del->order_id,
                'driver_name'        => $del->driver_name,
                'phone'              => $del->driver->phone ?? '-',
                'vehicle'            => $del->driver->license_plate ?? $del->driver->vehicle_number ?? '-',
                'customer_name'      => $del->order->customer->customer_name ?? 'Customer',
                'address'            => $del->order->customer->address ?? '-',
                'latitude'           => $del->latitude ?: ($del->driver->latitude ?? null),
                'longitude'          => $del->longitude ?: ($del->driver->longitude ?? null),
                'customer_lat'       => $del->order->customer->latitude ?? null,
                'customer_lng'       => $del->order->customer->longitude ?? null,
            ];
        })->toArray();

        // 2. Dapatkan driver aktif yang TIDAK sedang mengirim barang tapi GPS-nya aktif
        $idleDrivers = Driver::where('status', 'Active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotIn('id', $deliveryDriverIds)
            ->get();

        foreach ($idleDrivers as $drv) {
            $data[] = [
                'id'                 => 'drv-' . $drv->id,
                'db_id'              => $drv->id,
                'is_active_delivery' => false,
                'order_id'           => 'Standby',
                'driver_name'        => $drv->name,
                'phone'              => $drv->phone ?? '-',
                'vehicle'            => $drv->license_plate ?? '-',
                'customer_name'      => 'Siaga / Standby',
                'address'            => 'Menunggu Orderan Baru',
                'latitude'           => $drv->latitude,
                'longitude'          => $drv->longitude,
                'customer_lat'       => null,
                'customer_lng'       => null,
            ];
        }

        return response()->json($data);
    }

    public function updateLocation(Request $request, Delivery $delivery)
    {
        $request->validate([
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $delivery->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        if ($delivery->driver_id) {
            Driver::where('id', $delivery->driver_id)->update([
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Lokasi driver berhasil diperbarui.']);
    }

    public function updateLocationGlobal(Request $request)
    {
        $request->validate([
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = auth()->user();
        if (!$user || !$user->driver_id) {
            return response()->json(['status' => 'error', 'message' => 'User bukan merupakan driver.'], 403);
        }

        Driver::where('id', $user->driver_id)->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Cari jika driver ini memiliki pengiriman yang berstatus 'On Delivery'
        $activeDelivery = Delivery::where('driver_id', $user->driver_id)
            ->where('status', 'On Delivery')
            ->first();

        if ($activeDelivery) {
            $activeDelivery->update([
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Lokasi driver berhasil diperbarui secara global.']);
    }
}
