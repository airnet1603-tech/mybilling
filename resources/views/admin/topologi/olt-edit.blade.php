@extends('layouts.admin')
@section('title', 'Edit OLT')

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
        <h4 class="mb-0 fw-bold">Edit OLT</h4>
        <small class="text-muted">{{ $olt->name }}</small>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <form action="/admin/topologi/olt/{{ $olt->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama OLT</label>
                        <input type="text" name="name" class="form-control" value="{{ $olt->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">IP Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ $olt->ip_address }}" required>
                        <small class="text-muted">Contoh: 157.15.67.51:8088</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Model / Tipe OLT</label>
                        <select name="model" class="form-select" required>
                            <option value="">-- Pilih Model --</option>
                            @foreach([
                                'HisFocus 4P1GM'  => 'HisFocus 4P1GM',
                                'HisFocus 8P2GM'  => 'HisFocus 8P2GM',
                                'HiOSO'           => 'HiOSO',
                                'HSGQ'            => 'HSGQ',
                                'ZTE C300'        => 'ZTE C300',
                                'ZTE C320'        => 'ZTE C320',
                                'Huawei MA5608T'  => 'Huawei MA5608T',
                                'Huawei MA5800'   => 'Huawei MA5800',
                                'FiberHome'       => 'FiberHome',
                                'Nokia'           => 'Nokia',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ $olt->model == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ $olt->username }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="text" name="password" class="form-control" value="{{ $olt->password }}" required>
                        </div>
                    </div>

                    <!-- Settings Tambahan -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SNMP Community</label>
                            <input type="text" name="snmp_community" class="form-control" value="{{ $olt->snmp_community ?? 'public' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">API Endpoint</label>
                            <input type="text" name="api_endpoint" class="form-control" value="{{ $olt->api_endpoint ?? '/onuAllPonOnuList.asp' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sync Interval (menit)</label>
                            <input type="number" name="sync_interval" class="form-control" value="{{ $olt->sync_interval ?? 60 }}" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi <small class="text-muted">(klik peta)</small></label>
                        <div class="coord-box mb-2 d-flex gap-3">
                            <span>📍 Lat: <b id="lat-display">{{ $olt->lat ?? '-' }}</b></span>
                            <span>Lng: <b id="lng-display">{{ $olt->lng ?? '-' }}</b></span>
                        </div>
                        <input type="hidden" name="lat" id="lat-input" value="{{ $olt->lat }}">
                        <input type="hidden" name="lng" id="lng-input" value="{{ $olt->lng }}">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <button type="button" class="btn btn-danger" onclick="hapusOlt()">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </form>

                {{-- Form hapus --}}
                <form id="form-hapus" action="/admin/topologi/olt/{{ $olt->id }}" method="POST" style="display:none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-map-marker-alt text-danger"></i> Klik peta untuk ubah lokasi
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
const lat = {{ $olt->lat ?? -7.5 }};
const lng = {{ $olt->lng ?? 111.9 }};
const map = L.map('map-picker').setView([lat, lng], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

let marker = L.marker([lat, lng]).addTo(map).bindPopup('{{ $olt->name }}').openPopup();

map.on('click', function(e) {
    const { lat, lng } = e.latlng;
    document.getElementById('lat-input').value = lat.toFixed(8);
    document.getElementById('lng-input').value = lng.toFixed(8);
    document.getElementById('lat-display').textContent = lat.toFixed(6);
    document.getElementById('lng-display').textContent = lng.toFixed(6);
    marker.setLatLng([lat, lng]);
});

function hapusOlt() {
    if (confirm('Yakin hapus OLT {{ $olt->name }}? Semua ONU akan ikut terhapus!')) {
        document.getElementById('form-hapus').submit();
    }
}
</script>
@endpush
