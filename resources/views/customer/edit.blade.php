@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
.page-title { font-size:22px; font-weight:700; color:#0f172a; margin:0; }
.page-subtitle { font-size:13px; color:#64748b; margin:2px 0 0; }
.btn-primary-custom { display:inline-flex; align-items:center; gap:6px; background:#2563eb; color:white; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-primary-custom:hover { background:#1d4ed8; color:white; }
.btn-secondary-custom { display:inline-flex; align-items:center; gap:6px; background:#f1f5f9; color:#374151; border:none; padding:9px 18px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.btn-secondary-custom:hover { background:#e2e8f0; color:#111827; }
.card-clean { background:white; border-radius:16px; border:none; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
.tbl-wrap { overflow-x:auto; }
table.modern-table { width:100%; border-collapse:collapse; }
table.modern-table thead tr { background:#f8fafc; border-bottom:2px solid #e2e8f0; }
table.modern-table thead th { padding:12px 16px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#64748b; white-space:nowrap; }
table.modern-table tbody tr { border-bottom:1px solid #f1f5f9; transition:background 0.12s; }
table.modern-table tbody tr:hover { background:#f8fafc; }
table.modern-table tbody td { padding:13px 16px; font-size:13.5px; color:#374151; vertical-align:middle; }
.badge-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11.5px; font-weight:600; }
.badge-blue { background:#dbeafe; color:#1d4ed8; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-orange { background:#ffedd5; color:#c2410c; }
.badge-red { background:#fee2e2; color:#b91c1c; }
.badge-gray { background:#f1f5f9; color:#475569; }
.action-edit { display:inline-flex; align-items:center; gap:4px; background:#fef9c3; color:#854d0e; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; text-decoration:none; cursor:pointer; transition:background 0.15s; }
.action-edit:hover { background:#fef08a; color:#713f12; }
.action-delete { display:inline-flex; align-items:center; gap:4px; background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:8px; font-size:12.5px; font-weight:600; cursor:pointer; transition:background 0.15s; }
.action-delete:hover { background:#fecaca; color:#991b1b; }
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:680px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
.empty-state { text-align:center; padding:60px 20px; color:#94a3b8; }
.empty-state .empty-icon { font-size:48px; margin-bottom:12px; }
.empty-state p { font-size:14px; margin:0; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="edit" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Edit Customer</h1>
        <p class="page-subtitle">Perbarui data pelanggan</p>
    </div>
    <a href="{{ route('customers.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:15px;height:15px;margin-right:2px;"></i> Kembali</a>
</div>

<div class="form-card">
    @if($errors->any())
        <div class="alert-error">
            <strong><i data-lucide="alert-triangle" style="width:16px;height:16px;vertical-align:middle;margin-top:-2px;margin-right:4px;"></i> Ada kesalahan:</strong>
            <ul style="margin:6px 0 0;padding-left:18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="/customers/{{ $customer->id }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Nama Pelanggan</label>
            <input type="text" name="customer_name" class="form-input" value="{{ old('customer_name', $customer->customer_name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email', $customer->email) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone', $customer->phone) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-input" rows="3" required>{{ old('address', $customer->address) }}</textarea>
        </div>
        <div style="margin-bottom: 20px;">
            <label class="form-label" style="margin-bottom: 8px;">Pilih Lokasi di Peta</label>
            <div style="font-size:12px; color:#64748b; margin-bottom:8px;">
                <i data-lucide="info" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;margin-right:2px;color:#3b82f6;"></i>
                Klik pada peta atau geser pin merah untuk mendapatkan koordinat lokasi secara otomatis.
            </div>
            <div id="map-picker" style="height: 260px; width: 100%; border-radius: 12px; border: 1.5px solid #cbd5e1; z-index: 1;"></div>
        </div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom: 20px;">
            <div>
                <label class="form-label">Latitude</label>
                <input type="text" name="latitude" id="latitude" class="form-input" value="{{ old('latitude', $customer->latitude) }}" placeholder="Pilih dari peta atau isi manual">
            </div>
            <div>
                <label class="form-label">Longitude</label>
                <input type="text" name="longitude" id="longitude" class="form-input" value="{{ old('longitude', $customer->longitude) }}" placeholder="Pilih dari peta atau isi manual">
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:15px;height:15px;margin-right:4px;"></i> Update Customer</button>
            <a href="{{ route('customers.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const storeLatLng = [-6.353809, 107.114757];
    let latInput = document.getElementById('latitude');
    let lngInput = document.getElementById('longitude');
    
    let initialLat = latInput.value ? parseFloat(latInput.value) : null;
    let initialLng = lngInput.value ? parseFloat(lngInput.value) : null;
    
    let startPoint = (initialLat && initialLng) ? [initialLat, initialLng] : storeLatLng;
    
    let mapPicker = L.map('map-picker').setView(startPoint, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapPicker);
    
    let marker;
    if (initialLat && initialLng) {
        marker = L.marker(startPoint, {draggable: true}).addTo(mapPicker);
    }
    
    function updateInputs(lat, lng) {
        latInput.value = parseFloat(lat).toFixed(6);
        lngInput.value = parseFloat(lng).toFixed(6);
    }
    
    mapPicker.on('click', function(e) {
        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, {draggable: true}).addTo(mapPicker);
        }
        updateInputs(lat, lng);
        
        marker.on('dragend', function(event) {
            let position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });
    });
    
    if (marker) {
        marker.on('dragend', function(event) {
            let position = marker.getLatLng();
            updateInputs(position.lat, position.lng);
        });
    }
    
    function onInputChange() {
        let lat = parseFloat(latInput.value);
        let lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            let newLatLng = [lat, lng];
            if (marker) {
                marker.setLatLng(newLatLng);
            } else {
                marker = L.marker(newLatLng, {draggable: true}).addTo(mapPicker);
            }
            mapPicker.setView(newLatLng, 15);
        }
    }
    latInput.addEventListener('change', onInputChange);
    lngInput.addEventListener('change', onInputChange);
});
</script>
@endsection