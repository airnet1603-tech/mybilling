@extends('layouts.admin')
@section('title', 'Tambah OLT')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
#map-picker { height: 350px; border-radius: 10px; cursor: crosshair; }
.coord-box { background:#f8f9fa; border-radius:8px; padding:10px 14px; font-size:0.85rem; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h4 class="mb-0 fw-bold">Tambah OLT</h4>
        <small class="text-muted">Daftarkan perangkat OLT baru</small>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="/admin/topologi/olt/store" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama OLT</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: OLT-Utama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">IP Address</label>
                        <input type="text" name="ip_address" class="form-control" placeholder="192.168.10.88" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Model</label>
                        <input type="text" name="model" class="form-control" placeholder="4P1GM" value="4P1GM">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" value="admin" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" value="admin" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi OLT <small class="text-muted">(klik peta)</small></label>
                        <div class="coord-box mb-2 d-flex gap-3">
                            <span>📍 Lat: <b id="lat-display">-</b></span>
                            <span>Lng: <b id="lng-display">-</b></span>
                        </div>
                        <input type="hidden" name="lat" id="lat-input">
                        <input type="hidden" name="lng" id="lng-input">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Simpan OLT
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-map-marker-alt text-danger"></i> Klik peta untuk set lokasi OLT
            </div>
            <div class="card-body p-2">
                <div id="map-picker"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map-picker').setView([-7.5, 111.9], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marker = null;
map.on('click', function(e) {
    const { lat, lng } = e.latlng;
    document.getElementById('lat-input').value = lat.toFixed(8);
    document.getElementById('lng-input').value = lng.toFixed(8);
    document.getElementById('lat-display').textContent = lat.toFixed(6);
    document.getElementById('lng-display').textContent = lng.toFixed(6);
    if (marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map).bindPopup('Lokasi OLT').openPopup();
});
</script>
@endpush
