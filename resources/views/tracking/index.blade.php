@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.tracking-wrapper { display:flex; gap:20px; height:calc(100vh - 200px); min-height:500px; }
.map-container { flex:1; background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.05); overflow:hidden; position:relative; border:1px solid #e2e8f0; }
#map { width:100%; height:100%; z-index:1; }
.driver-list-card { width:300px; background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.05); border:1px solid #e2e8f0; display:flex; flex-direction:column; padding:20px; }
.driver-list-title { font-size:15px; font-weight:700; color:#1e293b; margin-bottom:14px; display:flex; align-items:center; gap:6px; }
.driver-items { flex:1; overflow-y:auto; display:flex; flex-direction:column; gap:10px; }
.driver-item { padding:12px; border-radius:10px; border:1.5px solid #f1f5f9; cursor:pointer; transition:all 0.15s; background:#f8fafc; }
.driver-item:hover { border-color:#3b82f6; background:#f0f7ff; }
.driver-item.active { border-color:#2563eb; background:#eff6ff; }
.driver-name { font-size:13.5px; font-weight:600; color:#0f172a; margin-bottom:4px; }
.driver-meta { font-size:11.5px; color:#64748b; display:flex; flex-direction:column; gap:2px; }
.badge-live { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:700; color:#ef4444; background:#fee2e2; padding:2px 8px; border-radius:20px; animation: pulse-live 1.5s infinite; }
@keyframes pulse-live {
    0% { opacity: 0.6; }
    50% { opacity: 1; }
    100% { opacity: 0.6; }
}
.empty-drivers { text-align:center; padding:40px 10px; color:#94a3b8; font-size:13px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="map-pin" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Live Driver Tracking</h1>
        <p class="page-subtitle">Pantau pergerakan pengiriman driver secara real-time</p>
    </div>
    <div>
        <span class="badge-live"><span style="width:6px;height:6px;background:#ef4444;border-radius:50%;display:inline-block;"></span> LIVE MONITORING</span>
    </div>
</div>

<div class="tracking-wrapper">
    <div class="map-container">
        <div id="map"></div>
    </div>
    
    <div class="driver-list-card">
        <div class="driver-list-title">
            <i data-lucide="truck" style="width:16px;height:16px;color:#2563eb;"></i>
            Driver Aktif (<span id="active-count">0</span>)
        </div>
        <div class="driver-items" id="driver-list-container">
            <div class="empty-drivers">
                <i data-lucide="info" style="width:24px;height:24px;margin:0 auto 8px;color:#cbd5e1;"></i>
                <p>Tidak ada pengiriman aktif saat ini</p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const storeLatLng = [-6.353809, 107.114757];
    
    const map = L.map('map').setView(storeLatLng, 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    
    const storeIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3670/3670910.png',
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -32]
    });
    L.marker(storeLatLng, {icon: storeIcon}).addTo(map).bindPopup('<strong>TK. NAGA SAKTI JAYA (Gudang)</strong><br>Titik Awal Pengiriman.').openPopup();

    let markers = {};
    let routes = {};
    let selectedDriverId = null;

    const driverIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3120/3120014.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -28]
    });

    function updateDriverLocations() {
        fetch('/api/driver-coordinates')
            .then(res => res.json())
            .then(data => {
                document.getElementById('active-count').innerText = data.length;
                const container = document.getElementById('driver-list-container');
                
                if (data.length === 0) {
                    container.innerHTML = `
                        <div class="empty-drivers">
                            <i data-lucide="info" style="width:24px;height:24px;margin:0 auto 8px;color:#cbd5e1;"></i>
                            <p>Tidak ada pengiriman aktif saat ini</p>
                        </div>
                    `;
                    for (let id in markers) {
                        map.removeLayer(markers[id]);
                        if (routes[id]) map.removeLayer(routes[id]);
                    }
                    markers = {};
                    routes = {};
                    lucide.createIcons();
                    return;
                }
                
                let listHtml = '';
                let currentIds = data.map(d => d.id);
                
                for (let id in markers) {
                    if (!currentIds.includes(parseInt(id))) {
                        map.removeLayer(markers[id]);
                        if (routes[id]) map.removeLayer(routes[id]);
                        delete markers[id];
                        delete routes[id];
                    }
                }
                
                data.forEach(d => {
                    const isActive = selectedDriverId === d.id ? 'active' : '';
                    listHtml += `
                        <div class="driver-item ${isActive}" onclick="selectDriver(${d.id}, ${d.latitude}, ${d.longitude})">
                            <div class="driver-name">${d.driver_name}</div>
                            <div class="driver-meta">
                                <span><i data-lucide="shopping-bag" style="width:11px;height:11px;vertical-align:middle;margin-right:2px;"></i> Order #${d.order_id} - ${d.customer_name}</span>
                                <span><i data-lucide="map-pin" style="width:11px;height:11px;vertical-align:middle;margin-right:2px;"></i> ${d.address}</span>
                                <span><i data-lucide="phone" style="width:11px;height:11px;vertical-align:middle;margin-right:2px;"></i> Telp: ${d.phone}</span>
                            </div>
                        </div>
                    `;
                    
                    if (d.latitude && d.longitude) {
                        const latLng = [parseFloat(d.latitude), parseFloat(d.longitude)];
                        
                        if (markers[d.id]) {
                            markers[d.id].setLatLng(latLng);
                        } else {
                            markers[d.id] = L.marker(latLng, {icon: driverIcon})
                                .addTo(map)
                                .bindPopup(`
                                    <strong>Driver: ${d.driver_name}</strong><br>
                                    Nopol: ${d.vehicle}<br>
                                    Order: #${d.order_id} ke ${d.customer_name}
                                `);
                        }
                    }
                });
                
                container.innerHTML = listHtml;
                lucide.createIcons();
                
                if (selectedDriverId && markers[selectedDriverId]) {
                    map.panTo(markers[selectedDriverId].getLatLng());
                }
            })
            .catch(err => console.error("Error fetching coordinates:", err));
    }

    function selectDriver(id, lat, lng) {
        selectedDriverId = id;
        
        document.querySelectorAll('.driver-item').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        if (lat && lng) {
            map.setView([lat, lng], 15);
            if (markers[id]) markers[id].openPopup();
        }
    }

    updateDriverLocations();
    setInterval(updateDriverLocations, 8000);
</script>
@endsection
