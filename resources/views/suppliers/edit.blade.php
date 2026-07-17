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
.form-card { background:white; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); padding:32px; max-width:680px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-input { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:14px; color:#1e293b; transition:border-color 0.15s; outline:none; font-family:inherit; }
.form-input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.12); }
.form-group { margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title"><i data-lucide="edit" style="width:22px;height:22px;vertical-align:middle;margin-top:-4px;margin-right:4px;"></i> Edit Supplier</h1>
        <p class="page-subtitle">Perbarui data supplier</p>
    </div>
    <a href="{{ route('suppliers.index') }}" class="btn-secondary-custom"><i data-lucide="arrow-left" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Kembali</a>
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

    <form method="POST" action="{{ route('suppliers.update', $supplier->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label class="form-label">Nama Supplier</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $supplier->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone', $supplier->phone) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email (Opsional)</label>
            <input type="email" name="email" class="form-input" value="{{ old('email', $supplier->email) }}">
        </div>
        <div class="form-group">
            <label class="form-label">Alamat Lengkap</label>
            <textarea name="address" class="form-input" rows="3" required>{{ old('address', $supplier->address) }}</textarea>
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
                <input type="text" name="latitude" id="latitude" class="form-input" value="{{ old('latitude', $supplier->latitude) }}" placeholder="Pilih dari peta atau isi manual">
            </div>
            <div>
                <label class="form-label">Longitude</label>
                <input type="text" name="longitude" id="longitude" class="form-input" value="{{ old('longitude', $supplier->longitude) }}" placeholder="Pilih dari peta atau isi manual">
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn-primary-custom"><i data-lucide="save" style="width:14px;height:14px;vertical-align:middle;margin-top:-2px;"></i> Update Supplier</button>
            <a href="{{ route('suppliers.index') }}" class="btn-secondary-custom">Batal</a>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const storeLatLng = [-6.3825657, 107.0871247];
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
