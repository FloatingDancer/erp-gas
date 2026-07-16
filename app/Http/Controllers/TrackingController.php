<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
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
        $deliveries = Delivery::with(['order.customer', 'driver'])
            ->where('status', 'On Delivery')
            ->get();

        $data = $deliveries->map(function ($del) {
            return [
                'id'            => $del->id,
                'order_id'      => $del->order_id,
                'driver_name'   => $del->driver_name,
                'phone'         => $del->driver->phone ?? '-',
                'vehicle'       => $del->driver->vehicle_number ?? '-',
                'customer_name' => $del->order->customer->customer_name ?? 'Customer',
                'address'       => $del->order->customer->address ?? '-',
                'latitude'      => $del->latitude,
                'longitude'     => $del->longitude,
            ];
        });

        return response()->json($data);
    }

    public function updateLocation(Request $request, Delivery $delivery)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $delivery->update([
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Lokasi driver berhasil diperbarui.']);
    }
}
