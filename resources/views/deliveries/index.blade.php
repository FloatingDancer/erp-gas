@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.page-title{font-size:22px;font-weight:700;color:#0f172a;margin:0;}
.page-subtitle{font-size:13px;color:#64748b;margin:2px 0 0;}
.btn-primary-custom{display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.btn-primary-custom:hover{background:#1d4ed8;color:white;}
.btn-secondary-custom{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#374151;border:none;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.btn-secondary-custom:hover{background:#e2e8f0;color:#111827;}
.card-clean{background:white;border-radius:16px;border:none;box-shadow:0 1px 4px rgba(0,0,0,0.06);}
.tbl-wrap{overflow-x:auto;}
table.modern-table{width:100%;border-collapse:collapse;}
table.modern-table thead tr{background:#f8fafc;border-bottom:2px solid #e2e8f0;}
table.modern-table thead th{padding:12px 16px;font-size:11.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:#64748b;white-space:nowrap;}
table.modern-table tbody tr{border-bottom:1px solid #f1f5f9;transition:background 0.12s;}
table.modern-table tbody tr:hover{background:#f8fafc;}
table.modern-table tbody td{padding:13px 16px;font-size:13.5px;color:#374151;vertical-align:middle;}
.badge-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11.5px;font-weight:600;}
.badge-yellow{background:#fef9c3;color:#854d0e;}
.badge-blue{background:#dbeafe;color:#1d4ed8;}
.badge-green{background:#dcfce7;color:#15803d;}
.badge-gray{background:#f1f5f9;color:#475569;}
.action-edit{display:inline-flex;align-items:center;gap:4px;background:#fef9c3;color:#854d0e;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;}
.action-edit:hover{background:#fef08a;}
.action-delete{display:inline-flex;align-items:center;gap:4px;background:#fee2e2;color:#b91c1c;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;cursor:pointer;}
.action-delete:hover{background:#fecaca;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:12px 16px;border-radius:10px;margin-bottom:20px;}
.empty-state{text-align:center;padding:60px 20px;color:#94a3b8;}
.empty-state .empty-icon{font-size:48px;margin-bottom:12px;}
.empty-state p{font-size:14px;margin:0;}
.action-confirm{display:inline-flex;align-items:center;gap:4px;background:#dcfce7;color:#15803d;border:none;padding:6px 12px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;cursor:pointer;transition:background 0.15s;}
.action-confirm:hover{background:#bbf7d0;}
.delivery-mobile-cards{display:none;}
.delivery-card{background:white;border-radius:16px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:18px;margin-bottom:16px;border:1px solid #f1f5f9;}
.delivery-card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;border-bottom:1px solid #f1f5f9;padding-bottom:10px;}
.delivery-card-title{font-size:14.5px;font-weight:700;color:#0f172a;margin:0;}
.delivery-card-body{font-size:13px;color:#475569;}
.delivery-card-item{margin-bottom:8px;display:flex;flex-direction:column;gap:2px;}
.delivery-card-label{font-size:11px;color:#94a3b8;text-transform:uppercase;font-weight:600;letter-spacing:0.4px;}
.delivery-card-value{font-weight:500;color:#1e293b;}
.btn-confirm-mobile{width:100%;margin-top:12px;padding:10px;font-size:13.5px;font-weight:700;text-align:center;justify-content:center;text-decoration:none;}

@media (max-width: 768px) {
    .card-clean { display:none !important; }
    .delivery-mobile-cards { display:block; }
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i data-lucide="{{ ($isLiveOrderPage ?? false) ? 'compass' : 'truck' }}" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> 
            {{ ($isLiveOrderPage ?? false) ? 'Live Order' : 'Deliveries' }}
        </h1>
        <p class="page-subtitle">{{ ($isLiveOrderPage ?? false) ? 'Daftar pengantaran aktif/berlangsung' : 'Kelola data pengiriman' }}</p>
    </div>
    @if(!auth()->user()->isDriver())
        <a href="{{ route('deliveries.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:15px;height:15px;margin-right:2px;"></i> Add Delivery</a>
    @elseif(auth()->user()->isDriver())
        @php
            $activeDelivery = \App\Models\Delivery::where('driver_id', auth()->user()->driver_id)
                ->where('status', 'On Delivery')
                ->first();
            $activeDeliveryId = $activeDelivery ? $activeDelivery->id : 0;
        @endphp
        <button type="button" class="btn-primary-custom" id="btn-real-header" onclick="toggleRealGPSTracking({{ $activeDeliveryId }}, true)" style="background:#3b82f6; border:none; font-weight:700;">
            <i data-lucide="locate" style="width:14px;height:14px;margin-right:4px;"></i> Aktifkan GPS Asli
        </button>
    @endif
</div>

@if(auth()->user()->isDriver())
    <div id="standby-map-container" style="display:none; height:320px; width:100%; border-radius:16px; margin-bottom:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; position: relative; background: #f8fafc; overflow:hidden;"></div>
    <!-- Floating debug badge -->
    <div id="gps-debug-badge" style="position: fixed; bottom: 8px; left: 8px; z-index: 9999; background: rgba(15, 23, 42, 0.9); color: #10b981; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-family: monospace; border: 1px solid #1e293b; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); pointer-events: none;">
        GPS: Siaga | Sent: <span id="debug-sent">0</span> | Ok: <span id="debug-ok">0</span> | Err: <span id="debug-err">0</span> | Last: <span id="debug-coords">-</span>
    </div>
@endif

@if(session('success'))
    <div class="alert-success"><i data-lucide="check-circle-2" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> {{ session('success') }}</div>
@endif

<div class="card-clean">
    <div class="tbl-wrap">
        <table class="modern-table" id="deliveriesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Driver</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($deliveries as $d)
                <tr>
                    <td style="color:#94a3b8;font-size:12px;">{{ $d->id }}</td>
                    <td><span style="font-weight:600;color:#0f172a;">Order #{{ $d->order->id }}</span></td>
                    <td>
                        @if($d->driver)
                            <span style="font-weight:600;color:#0f172a;">{{ $d->driver->name }}</span>
                            <div style="font-size:11px;color:#64748b;">{{ $d->driver->license_plate }}</div>
                        @else
                            {{ $d->driver_name }}
                        @endif
                    </td>
                    <td style="color:#64748b;">{{ \Carbon\Carbon::parse($d->delivery_date)->format('d M Y') }}</td>
                    <td>
                        @if($d->status === 'Scheduled')
                            <span class="badge-pill badge-yellow"><i data-lucide="calendar" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Scheduled</span>
                        @elseif($d->status === 'On Delivery')
                            <span class="badge-pill badge-blue"><i data-lucide="truck" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> On Delivery</span>
                        @elseif($d->status === 'Delivered')
                            <span class="badge-pill badge-green"><i data-lucide="check" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Delivered</span>
                        @else
                            <span class="badge-pill badge-gray">{{ $d->status }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center;">
                            @if(auth()->user()->isDriver() && $d->status === 'On Delivery')
                                <div style="display:flex; flex-direction:column; gap:4px; width:100%;">
                                    <form action="{{ route('deliveries.confirm-arrival', $d->id) }}" method="POST" id="confirm-arrival-{{ $d->id }}" style="display:inline;">
                                        @csrf
                                        <button type="button" class="action-confirm" style="width:100%; justify-content:center;" onclick="confirmArrival({{ $d->id }}, 'Order #{{ $d->order->id }}')"><i data-lucide="check" style="width:13px;height:13px;margin-right:2px;"></i> Sampai</button>
                                    </form>
                                    <button type="button" class="btn-primary-custom" onclick="startDriverSimulation({{ $d->id }}, {{ $d->order->customer->latitude ?? 'null' }}, {{ $d->order->customer->longitude ?? 'null' }}, false)" id="btn-sim-desktop-{{ $d->id }}" style="background:#10b981; padding:6px 12px; font-size:12px; border-radius:8px; width:100%; justify-content:center; gap:2px;">
                                        <i data-lucide="play" style="width:12px;height:12px;"></i> Simulasi
                                    </button>
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $d->order->customer->latitude && $d->order->customer->longitude ? $d->order->customer->latitude . ',' . $d->order->customer->longitude : urlencode($d->order->customer->address) }}" target="_blank" class="btn-primary-custom" style="background:#3b82f6; padding:6px 12px; font-size:12px; border-radius:8px; width:100%; justify-content:center; gap:2px; margin-top:4px; text-decoration:none;">
                                        <i data-lucide="map" style="width:12px;height:12px;"></i> Buka Maps
                                    </a>
                                </div>
                            @endif

                            @if(!auth()->user()->isDriver())
                                <a href="{{ route('deliveries.edit', $d->id) }}" class="action-edit"><i data-lucide="edit" style="width:13px;height:13px;margin-right:2px;"></i> Edit</a>
                                <form action="{{ route('deliveries.destroy', $d->id) }}" method="POST" id="del-{{ $d->id }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="action-delete" onclick="confirmDelete({{ $d->id }})"><i data-lucide="trash-2" style="width:13px;height:13px;margin-right:2px;"></i> Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="truck" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                            <p>Belum ada data pengiriman</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="delivery-mobile-cards">
    @forelse($deliveries as $d)
        <div class="delivery-card">
            <div class="delivery-card-header">
                <span class="delivery-card-title">Order #{{ $d->order->id }}</span>
                <div>
                    @if($d->status === 'Scheduled')
                        <span class="badge-pill badge-yellow"><i data-lucide="calendar" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Scheduled</span>
                    @elseif($d->status === 'On Delivery')
                        <span class="badge-pill badge-blue"><i data-lucide="truck" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> On Delivery</span>
                    @elseif($d->status === 'Delivered')
                        <span class="badge-pill badge-green"><i data-lucide="check" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> Delivered</span>
                    @else
                        <span class="badge-pill badge-gray">{{ $d->status }}</span>
                    @endif
                </div>
            </div>
            <div class="delivery-card-body">
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Pelanggan</span>
                    <span class="delivery-card-value">{{ $d->order->customer->customer_name }}</span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">No. Telp Pelanggan</span>
                    <span class="delivery-card-value" style="color: #2563eb; font-weight:600;">
                        <a href="tel:{{ $d->order->customer->phone }}" style="text-decoration: none; color:inherit;"><i data-lucide="phone" style="width:12px;height:12px;margin-right:3px;vertical-align:middle;margin-top:-2px;"></i> {{ $d->order->customer->phone }}</a>
                    </span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Alamat Pengiriman</span>
                    <span class="delivery-card-value" style="color:#0f172a; font-weight: 600;">
                        @if($d->order->customer->latitude && $d->order->customer->longitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $d->order->customer->latitude }},{{ $d->order->customer->longitude }}" target="_blank" style="color: #2563eb; text-decoration: none;" title="Buka Rute di Google Maps">
                                <i data-lucide="map-pin" style="width:12px;height:12px;vertical-align:middle;margin-top:-2px;margin-right:2px;color:#ef4444;"></i>{{ $d->order->customer->address }}
                            </a>
                        @else
                            {{ $d->order->customer->address }}
                        @endif
                    </span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Driver & Nopol</span>
                    <span class="delivery-card-value">
                        @if($d->driver)
                            {{ $d->driver->name }} ({{ $d->driver->license_plate }})
                        @else
                            {{ $d->driver_name }}
                        @endif
                    </span>
                </div>
                <div class="delivery-card-item">
                    <span class="delivery-card-label">Tanggal Pengiriman</span>
                    <span class="delivery-card-value">{{ \Carbon\Carbon::parse($d->delivery_date)->format('d M Y') }}</span>
                </div>

                @if(auth()->user()->isDriver() && $d->status === 'On Delivery')
                    <!-- Map & Simulator for Driver -->
                    <div style="margin-top: 14px; border-top: 1px solid #f1f5f9; padding-top: 12px;">
                        <span class="delivery-card-label">Live GPS Simulator</span>
                        <div id="driver-map-{{ $d->id }}" style="height: 180px; width: 100%; border-radius: 10px; border: 1.5px solid #cbd5e1; margin-top: 6px; z-index: 1;"></div>
                        <button type="button" class="btn-primary-custom btn-confirm-mobile" id="btn-sim-{{ $d->id }}" onclick="startDriverSimulation({{ $d->id }}, {{ $d->order->customer->latitude ?? 'null' }}, {{ $d->order->customer->longitude ?? 'null' }}, true)" style="background:#10b981; border:none; margin-top:10px; font-weight:700; width:100%; justify-content:center;">
                            <i data-lucide="play" style="width:14px;height:14px;margin-right:4px;"></i> Mulai Simulasi Perjalanan
                        </button>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $d->order->customer->latitude && $d->order->customer->longitude ? $d->order->customer->latitude . ',' . $d->order->customer->longitude : urlencode($d->order->customer->address) }}" target="_blank" class="btn-primary-custom btn-confirm-mobile" style="background:#3b82f6; border:none; margin-top:8px; font-weight:700; width:100%; justify-content:center; text-decoration:none;">
                            <i data-lucide="map" style="width:14px;height:14px;margin-right:4px;"></i> Buka Google Maps (Rute Pelanggan)
                        </a>
                    </div>

                    <form action="{{ route('deliveries.confirm-arrival', $d->id) }}" method="POST" id="confirm-arrival-mob-{{ $d->id }}" style="display:block; margin-top:10px;">
                        @csrf
                        <button type="button" class="btn-primary-custom action-confirm btn-confirm-mobile" onclick="confirmArrival({{ $d->id }}, 'Order #{{ $d->order->id }}', true)">
                            <i data-lucide="check" style="width:14px;height:14px;margin-right:4px;"></i> Konfirmasi Sampai
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="card-clean" style="display:block; padding: 24px; text-align:center;">
            <div class="empty-state">
                <div class="empty-icon" style="display:flex;justify-content:center;margin-bottom:12px;"><i data-lucide="truck" style="width:48px;height:48px;stroke-width:1.5;color:#94a3b8;"></i></div>
                <p>Belum ada data pengiriman</p>
            </div>
        </div>
    @endforelse
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
<script>
Swal.fire({icon:'success',title:'Berhasil!',text:'{{ session('success') }}',timer:2000,showConfirmButton:false});
</script>
@endif
<script>
function confirmDelete(id) {
    Swal.fire({
        title:'Hapus pengiriman?', text:'Data akan dihapus permanen!', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#dc2626', cancelButtonColor:'#6b7280',
        confirmButtonText:'Ya, hapus!', cancelButtonText:'Batal'
    }).then(r => { if(r.isConfirmed) document.getElementById('del-'+id).submit(); });
}

function confirmArrival(id, orderLabel, isMobile = false) {
    Swal.fire({
        title: 'Konfirmasi Pengiriman?',
        text: 'Apakah ' + orderLabel + ' sudah benar-benar sampai di tempat pelanggan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Sampai!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if(r.isConfirmed) {
            if (isMobile) {
                document.getElementById('confirm-arrival-mob-' + id).submit();
            } else {
                document.getElementById('confirm-arrival-' + id).submit();
            }
        }
    });
}

$(document).ready(function() {
    $('#deliveriesTable').DataTable({
        "order": [[0, "desc"]],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ baris",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Lanjut",
                "previous": "Kembali"
            },
            "zeroRecords": "Tidak ada data ditemukan",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "infoFiltered": "(disaring dari _MAX_ total data)"
        }
    });
});
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const storeLatLng = [-6.3825657, 107.0871247];

    let activeMaps = {};
    let activeMarkers = {};
    let activeRoutes = {};
    let simulationIntervals = {};
    let gpsWatchIds = {};

    function destroyActiveMap(deliveryId) {
        if (simulationIntervals[deliveryId]) {
            clearInterval(simulationIntervals[deliveryId]);
            delete simulationIntervals[deliveryId];
        }
        if (activeMaps[deliveryId]) {
            try {
                activeMaps[deliveryId].remove();
            } catch (e) {
                console.error("Error removing map:", e);
            }
            delete activeMaps[deliveryId];
        }
        if (activeMarkers[deliveryId]) {
            delete activeMarkers[deliveryId];
        }
        if (activeRoutes[deliveryId]) {
            delete activeRoutes[deliveryId];
        }
        
        // Also clear any DOM residual leaflet IDs
        const mapContainer = document.getElementById('driver-map-' + deliveryId);
        if (mapContainer && mapContainer._leaflet_id) {
            delete mapContainer._leaflet_id;
            mapContainer.innerHTML = ''; // reset DOM content
        }
        const desktopContainer = document.getElementById('desktop-map-container');
        if (desktopContainer && desktopContainer._leaflet_id) {
            delete desktopContainer._leaflet_id;
            desktopContainer.innerHTML = ''; // reset DOM content
        }
        const standbyContainer = document.getElementById('standby-map-container');
        if (standbyContainer && standbyContainer._leaflet_id) {
            delete standbyContainer._leaflet_id;
            standbyContainer.innerHTML = ''; // reset DOM content
        }
    }

    function startDriverSimulation(deliveryId, lat, lng, isMobile = true) {
        destroyActiveMap(deliveryId);

        let customerLatLng;
        if (lat && lng) {
            customerLatLng = [lat, lng];
        } else {
            const customerLat = storeLatLng[0] + (Math.sin(deliveryId) * 0.012);
            const customerLng = storeLatLng[1] + (Math.cos(deliveryId) * 0.012);
            customerLatLng = [customerLat, customerLng];
        }

        let mapElementId = isMobile ? 'driver-map-' + deliveryId : 'desktop-map-container';
        
        const btnId = isMobile ? 'btn-sim-' + deliveryId : 'btn-sim-desktop-' + deliveryId;
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Simulasi Berjalan...';
            btn.style.background = '#64748b';
        }

        if (!isMobile) {
            Swal.fire({
                title: 'Simulasi Perjalanan Driver',
                html: '<div id="desktop-map-container" style="height: 300px; width: 100%; border-radius: 8px;"></div><p style="margin-top: 10px; font-size:12px; color:#64748b;">Mensimulasikan pengantaran ke lokasi pelanggan...</p>',
                width: 500,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    runMapSimulation('desktop-map-container', customerLatLng, deliveryId, btn);
                }
            });
        } else {
            runMapSimulation(mapElementId, customerLatLng, deliveryId, btn);
        }
    }

    function runMapSimulation(containerId, customerLatLng, deliveryId, btnElement) {
        const mapObj = L.map(containerId).setView(storeLatLng, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapObj);
        
        const storeIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3670/3670910.png',
            iconSize: [24, 24],
            iconAnchor: [12, 24]
        });
        const driverIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3120/3120014.png',
            iconSize: [28, 28],
            iconAnchor: [14, 28]
        });
        const customerIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/2776/2776067.png',
            iconSize: [28, 28],
            iconAnchor: [14, 28]
        });

        L.marker(storeLatLng, {icon: storeIcon}).addTo(mapObj).bindPopup('Gudang');
        L.marker(customerLatLng, {icon: customerIcon}).addTo(mapObj).bindPopup('Pelanggan');

        const driverMarker = L.marker([...storeLatLng], {icon: driverIcon}).addTo(mapObj);
        L.polyline([storeLatLng, customerLatLng], {color: '#3b82f6', dashArray: '5, 5'}).addTo(mapObj);

        // Tombol Pusatkan Peta Simulasi (Recenter Control)
        const centerControl = L.control({ position: 'topleft' });
        centerControl.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = `
                <a href="#" title="Pusatkan Peta Simulasi" role="button" aria-label="Pusatkan Peta Simulasi" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; background:white; color:#0f172a; text-decoration:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="22" y1="12" x2="18" y2="12"/><line x1="6" y1="12" x2="2" y2="12"/><line x1="12" y1="6" x2="12" y2="2"/><line x1="12" y1="22" x2="12" y2="18"/></svg>
                </a>
            `;
            div.onclick = function(e) {
                e.preventDefault();
                if (driverMarker) {
                    mapObj.setView(driverMarker.getLatLng(), 14);
                }
            };
            return div;
        };
        centerControl.addTo(mapObj);

        activeMaps[deliveryId] = mapObj;
        activeMarkers[deliveryId] = driverMarker;

        let currentStep = 0;
        const totalSteps = 10;
        
        const interval = setInterval(() => {
            currentStep++;
            
            const ratio = currentStep / totalSteps;
            const curLat = storeLatLng[0] + (customerLatLng[0] - storeLatLng[0]) * ratio;
            const curLng = storeLatLng[1] + (customerLatLng[1] - storeLatLng[1]) * ratio;
            
            driverMarker.setLatLng([curLat, curLng]);
            mapObj.panTo([curLat, curLng]);

            $.post(`/api/deliveries/${deliveryId}/location`, {
                _token: '{{ csrf_token() }}',
                latitude: curLat,
                longitude: curLng
            }).catch(err => console.error("Error updating location:", err));

            if (currentStep >= totalSteps) {
                clearInterval(interval);
                delete simulationIntervals[deliveryId];
                setTimeout(() => {
                    if (containerId === 'desktop-map-container') {
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Simulasi Selesai!',
                            text: 'Driver telah sampai di lokasi pelanggan.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sampai!',
                            text: 'Simulasi perjalanan selesai.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    if (btnElement) {
                        btnElement.disabled = false;
                        btnElement.innerHTML = '<i data-lucide="play" style="width:14px;height:14px;margin-right:4px;"></i> Mulai Simulasi Perjalanan';
                        btnElement.style.background = '#10b981';
                    }
                }, 1000);
            }
        }, 2000);

        simulationIntervals[deliveryId] = interval;
    }

    let gpsIntervalIds = {};

    function showGpsDebugError(errorObj) {
        let errorMsg = "Gagal mengirim data lokasi ke server.";
        if (errorObj) {
            errorMsg += " Status: " + errorObj.status + " (" + errorObj.statusText + ")";
            if (errorObj.responseText) {
                try {
                    const parsed = JSON.parse(errorObj.responseText);
                    if (parsed.message) errorMsg += " - " + parsed.message;
                } catch(e) {}
            }
        }
        
        const toast = Swal.mixin({
            toast: true,
            position: 'bottom',
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true
        });
        toast.fire({
            icon: 'warning',
            title: 'GPS Sync Error',
            text: errorMsg
        });
    }

    function toggleRealGPSTracking(deliveryId, isMobile = true) {
        const btnId = 'btn-real-header';
        const btn = document.getElementById(btnId);
        
        if (gpsWatchIds[deliveryId]) {
            navigator.geolocation.clearWatch(gpsWatchIds[deliveryId]);
            delete gpsWatchIds[deliveryId];
            
            if (gpsIntervalIds[deliveryId]) {
                clearInterval(gpsIntervalIds[deliveryId]);
                delete gpsIntervalIds[deliveryId];
            }
            
            // Clear coordinates in database on deactivate
            const url = deliveryId == 0 ? '/api/driver/location' : `/api/deliveries/${deliveryId}/location`;
            $.post(url, {
                _token: '{{ csrf_token() }}',
                latitude: null,
                longitude: null
            }).catch(err => {
                console.error("Error clearing location:", err);
                showGpsDebugError(err);
            });
            
            destroyActiveMap(deliveryId);
            
            if (deliveryId == 0) {
                const standbyMap = document.getElementById('standby-map-container');
                const desktopList = document.querySelector('.card-clean');
                const mobileList = document.querySelector('.delivery-mobile-cards');
                if (standbyMap) standbyMap.style.setProperty('display', 'none', 'important');
                if (window.innerWidth > 768) {
                    if (desktopList) desktopList.style.setProperty('display', 'block', 'important');
                } else {
                    if (mobileList) mobileList.style.setProperty('display', 'block', 'important');
                }
            }
            
            if (btn) {
                btn.innerHTML = '<i data-lucide="locate" style="width:14px;height:14px;margin-right:4px;"></i> Aktifkan GPS Asli';
                btn.style.background = '#3b82f6';
            }
            
            Swal.fire({
                icon: 'info',
                title: 'Pelacakan Dinonaktifkan',
                text: 'Pelacakan GPS fisik telah dihentikan.',
                timer: 1500,
                showConfirmButton: false
            });
            return;
        }

        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'error',
                title: 'Tidak Didukung',
                text: 'Browser Anda tidak mendukung fitur Geolocation GPS.',
            });
            return;
        }

        destroyActiveMap(deliveryId);

        if (btn) {
            btn.innerHTML = 'Menghubungkan GPS...';
            btn.style.background = '#64748b';
        }

        startWatching(deliveryId, isMobile, true);
    }

    let debugSentCount = 0;
    let debugOkCount = 0;
    let debugErrCount = 0;

    function updateGpsDebug(lat, lng, status = null, errorMsg = null) {
        const sentEl = document.getElementById('debug-sent');
        const okEl = document.getElementById('debug-ok');
        const errEl = document.getElementById('debug-err');
        const coordsEl = document.getElementById('debug-coords');
        
        if (lat !== null && lng !== null) {
            if (coordsEl) coordsEl.innerText = lat.toFixed(5) + "," + lng.toFixed(5);
        }
        if (status === 'sent') {
            debugSentCount++;
            if (sentEl) sentEl.innerText = debugSentCount;
        } else if (status === 'ok') {
            debugOkCount++;
            if (okEl) okEl.innerText = debugOkCount;
        } else if (status === 'err') {
            debugErrCount++;
            if (errEl) errEl.innerText = debugErrCount;
            if (errorMsg && coordsEl) {
                coordsEl.innerText = "Err: " + errorMsg;
            }
        }
    }

    function startWatching(deliveryId, isMobile, highAccuracy = true) {
        const btnId = 'btn-real-header';
        const btn = document.getElementById(btnId);
        let latestCoords = null;
        let hasShownSuccess = false;
        let lastPostTime = 0;

        // Fetch initial position immediately
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                latestCoords = { lat: lat, lng: lng };
                
                if (btn) {
                    btn.innerHTML = '<i data-lucide="stop-circle" style="width:14px;height:14px;margin-right:4px;"></i> Matikan GPS Asli';
                    btn.style.background = '#ef4444';
                }

                // Send immediate post to database
                const url = deliveryId == 0 ? '/api/driver/location' : `/api/deliveries/${deliveryId}/location`;
                updateGpsDebug(lat, lng, 'sent');
                
                $.post(url, {
                    _token: '{{ csrf_token() }}',
                    latitude: lat,
                    longitude: lng
                }).done(function() {
                    updateGpsDebug(null, null, 'ok');
                }).catch(err => {
                    console.error("Error sending initial position:", err);
                    updateGpsDebug(null, null, 'err', err.status + " " + err.statusText);
                    showGpsDebugError(err);
                });

                if (!hasShownSuccess) {
                    hasShownSuccess = true;
                    Swal.fire({
                        icon: 'success',
                        title: deliveryId == 0 ? 'GPS Dinas Aktif' : 'GPS Pengiriman Aktif',
                        text: deliveryId == 0 
                            ? 'Status siaga aktif. Posisi Anda dipantau oleh Manager.' 
                            : 'Pelacakan pengiriman aktif.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }

                if (deliveryId == 0) {
                    const standbyMap = document.getElementById('standby-map-container');
                    const desktopList = document.querySelector('.card-clean');
                    const mobileList = document.querySelector('.delivery-mobile-cards');
                    if (standbyMap) {
                        standbyMap.style.setProperty('display', 'block', 'important');
                        if (desktopList) desktopList.style.setProperty('display', 'none', 'important');
                        if (mobileList) mobileList.style.setProperty('display', 'none', 'important');
                        setupRealMap('standby-map-container', lat, lng, 0);
                    }
                } else {
                    let mapElementId = isMobile ? 'driver-map-' + deliveryId : 'desktop-map-container';
                    setupRealMap(mapElementId, lat, lng, deliveryId);
                }
            },
            function(error) {
                console.warn("Initial getCurrentPosition failed:", error);
            },
            {
                enableHighAccuracy: highAccuracy,
                timeout: 5000,
                maximumAge: 0
            }
        );

        // Keep watching for continuous position changes
        const watchId = navigator.geolocation.watchPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                latestCoords = { lat: lat, lng: lng };
                
                if (btn) {
                    btn.innerHTML = '<i data-lucide="stop-circle" style="width:14px;height:14px;margin-right:4px;"></i> Matikan GPS Asli';
                    btn.style.background = '#ef4444';
                }

                if (!hasShownSuccess) {
                    hasShownSuccess = true;
                    Swal.fire({
                        icon: 'success',
                        title: deliveryId == 0 ? 'GPS Dinas Aktif' : 'GPS Pengiriman Aktif',
                        text: deliveryId == 0 
                            ? 'Status siaga aktif. Posisi Anda dipantau oleh Manager.' 
                            : 'Pelacakan pengiriman aktif.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }

                // Send coordinates directly from watchPosition callback (throttled to once every 10 seconds)
                const now = Date.now();
                if (now - lastPostTime >= 10000 && !simulationIntervals[deliveryId]) {
                    lastPostTime = now;
                    const url = deliveryId == 0 ? '/api/driver/location' : `/api/deliveries/${deliveryId}/location`;
                    updateGpsDebug(lat, lng, 'sent');
                    
                    $.post(url, {
                        _token: '{{ csrf_token() }}',
                        latitude: lat,
                        longitude: lng
                    }).done(function() {
                        updateGpsDebug(null, null, 'ok');
                    }).catch(err => {
                        console.error("Error sending watch update:", err);
                        updateGpsDebug(null, null, 'err', err.status + " " + err.statusText);
                        showGpsDebugError(err);
                    });
                }

                // If simulation is running for this delivery, do not let real GPS override the simulation map view
                if (simulationIntervals[deliveryId]) {
                    return;
                }

                if (deliveryId == 0) {
                    const standbyMap = document.getElementById('standby-map-container');
                    const desktopList = document.querySelector('.card-clean');
                    const mobileList = document.querySelector('.delivery-mobile-cards');
                    if (standbyMap) {
                        standbyMap.style.setProperty('display', 'block', 'important');
                        if (desktopList) desktopList.style.setProperty('display', 'none', 'important');
                        if (mobileList) mobileList.style.setProperty('display', 'none', 'important');
                        setupRealMap('standby-map-container', lat, lng, 0);
                    }
                } else {
                    let mapElementId = isMobile ? 'driver-map-' + deliveryId : 'desktop-map-container';
                    
                    if (!isMobile && !document.getElementById('desktop-map-container')) {
                        Swal.fire({
                            title: 'Pelacakan GPS Fisik Driver',
                            html: '<div id="desktop-map-container" style="height: 300px; width: 100%; border-radius: 8px;"></div><p style="margin-top: 10px; font-size:12px; color:#10b981; font-weight:600;">Pelacakan GPS fisik sedang aktif...</p>',
                            width: 500,
                            showConfirmButton: true,
                            confirmButtonText: 'Tutup Peta (Pelacakan Tetap Berjalan)',
                            didOpen: () => {
                                setupRealMap(mapElementId, lat, lng, deliveryId);
                            }
                        });
                    } else {
                        setupRealMap(mapElementId, lat, lng, deliveryId);
                    }
                }
            },
            function(error) {
                console.error("GPS watch error:", error);
                
                if (highAccuracy) {
                    console.log("Retrying with low accuracy fallback...");
                    navigator.geolocation.clearWatch(watchId);
                    startWatching(deliveryId, isMobile, false);
                    return;
                }

                let msg = 'Gagal mengakses sensor GPS HP Anda. Pastikan layanan lokasi/GPS di HP Anda sudah aktif.';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Perizinan lokasi ditolak oleh browser/perangkat Anda. Mohon izinkan akses lokasi di pengaturan browser.';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    msg = 'Sinyal lokasi tidak tersedia. Silakan aktifkan GPS HP Anda dan pastikan berada di area dengan sinyal GPS yang baik.';
                } else if (error.code === error.TIMEOUT) {
                    msg = 'Waktu permintaan lokasi habis (timeout). Silakan coba lagi.';
                }
                
                if (btn) {
                    btn.innerHTML = '<i data-lucide="locate" style="width:14px;height:14px;margin-right:4px;"></i> Aktifkan GPS Asli';
                    btn.style.background = '#3b82f6';
                }
                
                if (gpsWatchIds[deliveryId]) {
                    navigator.geolocation.clearWatch(gpsWatchIds[deliveryId]);
                    delete gpsWatchIds[deliveryId];
                }
                if (gpsIntervalIds[deliveryId]) {
                    clearInterval(gpsIntervalIds[deliveryId]);
                    delete gpsIntervalIds[deliveryId];
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'GPS Gagal',
                    text: msg
                });
            },
            {
                enableHighAccuracy: highAccuracy,
                maximumAge: 0,
                timeout: 10000
            }
        );

        gpsWatchIds[deliveryId] = watchId;

        // Post coordinates to database exactly every 10 seconds (10000ms)
        const intervalId = setInterval(function() {
            if (latestCoords && !simulationIntervals[deliveryId]) {
                const url = deliveryId == 0 ? '/api/driver/location' : `/api/deliveries/${deliveryId}/location`;
                updateGpsDebug(latestCoords.lat, latestCoords.lng, 'sent');
                
                $.post(url, {
                    _token: '{{ csrf_token() }}',
                    latitude: latestCoords.lat,
                    longitude: latestCoords.lng
                }).done(function() {
                    updateGpsDebug(null, null, 'ok');
                }).catch(err => {
                    console.error("Error updating location via real GPS:", err);
                    updateGpsDebug(null, null, 'err', err.status + " " + err.statusText);
                    showGpsDebugError(err);
                });
            }
        }, 10000);

        gpsIntervalIds[deliveryId] = intervalId;
    }

    function setupRealMap(containerId, lat, lng, deliveryId) {
        if (simulationIntervals[deliveryId]) {
            return; // Skip setup/update if simulation is currently active
        }
        if (activeMaps[deliveryId]) {
            const latLng = [lat, lng];
            if (activeMarkers[deliveryId]) {
                activeMarkers[deliveryId].setLatLng(latLng);
            }
            activeMaps[deliveryId].setView(latLng, 15);
            activeMaps[deliveryId].invalidateSize();
            return;
        }

        const mapObj = L.map(containerId).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapObj);
        
        const driverIcon = L.icon({
            iconUrl: 'https://cdn-icons-png.flaticon.com/512/3120/3120014.png',
            iconSize: [28, 28],
            iconAnchor: [14, 28]
        });

        activeMarkers[deliveryId] = L.marker([lat, lng], {icon: driverIcon}).addTo(mapObj).bindPopup('Lokasi GPS Fisik Anda');
        activeMaps[deliveryId] = mapObj;

        // Tombol Pusatkan Lokasi Saya (Recenter Control)
        const centerControl = L.control({ position: 'topleft' });
        centerControl.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = `
                <a href="#" title="Pusatkan ke Lokasi Saya" role="button" aria-label="Pusatkan ke Lokasi Saya" style="display:flex; align-items:center; justify-content:center; width:34px; height:34px; background:white; color:#0f172a; text-decoration:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="22" y1="12" x2="18" y2="12"/><line x1="6" y1="12" x2="2" y2="12"/><line x1="12" y1="6" x2="12" y2="2"/><line x1="12" y1="22" x2="12" y2="18"/></svg>
                </a>
            `;
            div.onclick = function(e) {
                e.preventDefault();
                if (activeMarkers[deliveryId]) {
                    const currentPos = activeMarkers[deliveryId].getLatLng();
                    mapObj.setView(currentPos, 15);
                    activeMarkers[deliveryId].openPopup();
                } else {
                    mapObj.setView([lat, lng], 15);
                }
            };
            return div;
        };
        centerControl.addTo(mapObj);
        
        setTimeout(() => {
            mapObj.invalidateSize();
        }, 200);
    }
</script>
@endsection