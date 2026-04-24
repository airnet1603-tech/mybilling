@extends('layouts.admin')
@section('title', 'Tambah OLT')

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places&callback=initMapPicker" defer></script>
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
                        <label class="form-label fw-semibold">Model OLT</label>
                        <select name="model" id="olt-model" class="form-select" required onchange="onModelChange(this.value)">
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
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div id="model-info" class="mt-2" style="display:none">
                            <span id="model-badge" class="badge fs-6 px-3 py-1"></span>
                            <small id="model-note" class="text-muted ms-2"></small>
                        </div>
                    </div>
                    <div class="mb-3" id="hsgq-key-field" style="display:none">
                        <label class="form-label fw-semibold">HSGQ Key</label>
                        <div class="input-group">
                            <input type="text" name="hsgq_key" class="form-control font-monospace" placeholder="Kosongkan dulu, fetch setelah OLT disimpan">
                        </div>
                        <small class="text-muted">💡 Key bisa di-fetch otomatis setelah OLT disimpan</small>
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

                    <!-- Settings Tambahan -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SNMP Community</label>
                            <input type="text" name="snmp_community" class="form-control" placeholder="public" value="public">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">API Endpoint</label>
                            <input type="text" name="api_endpoint" class="form-control" id="api-endpoint" placeholder="/onuAllPonOnuList.asp" value="/onuAllPonOnuList.asp">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sync Interval (menit)</label>
                            <input type="number" name="sync_interval" class="form-control" placeholder="60" value="60" min="1">
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
<script>
var gmap, gmarker;
function initMapPicker() {
    var defaultPos = { lat: -8.207014, lng: 112.019986 };
    gmap = new google.maps.Map(document.getElementById('map-picker'), {
        center: defaultPos, zoom: 15, mapTypeId: 'hybrid',
        gestureHandling: 'greedy', fullscreenControl: true,
        streetViewControl: false, mapTypeControl: true,
    });
    gmarker = new google.maps.Marker({
        position: defaultPos, map: gmap,
        title: 'Lokasi OLT Baru', draggable: true,
    });
    gmap.addListener('click', function(e) { moveMarker(e.latLng); });
    gmarker.addListener('dragend', function(e) { moveMarker(e.latLng); });
}
function moveMarker(latLng) {
    gmarker.setPosition(latLng);
    gmap.panTo(latLng);
    document.getElementById('lat-input').value  = latLng.lat().toFixed(8);
    document.getElementById('lng-input').value  = latLng.lng().toFixed(8);
    document.getElementById('lat-display').textContent = latLng.lat().toFixed(6);
    document.getElementById('lng-display').textContent = latLng.lng().toFixed(6);
}
window.initMapPicker = initMapPicker;

const modelConfig = {
    'HiOSO':          { endpoint: '/onuConfigPonList.asp', sync: true,  color: 'success', note: 'Auto-sync didukung ✅ (HTTP Basic Auth)' },
    'HSGQ':           { endpoint: '',                      sync: true,  color: 'success', note: 'Auto-sync didukung ✅ (Token Auth)' },
    'HisFocus 4P1GM': { endpoint: '/onuAllPonOnuList.asp', sync: true,  color: 'success', note: 'Auto-sync didukung ✅ (HTTP Basic Auth)' },
    'HisFocus 8P2GM': { endpoint: '/onuAllPonOnuList.asp', sync: true,  color: 'success', note: 'Auto-sync didukung ✅ (HTTP Basic Auth)' },
    'ZTE C300':       { endpoint: '',                      sync: false, color: 'warning', note: 'Sync belum didukung ⚠️' },
    'ZTE C320':       { endpoint: '',                      sync: false, color: 'warning', note: 'Sync belum didukung ⚠️' },
    'Huawei MA5608T': { endpoint: '',                      sync: false, color: 'warning', note: 'Sync belum didukung ⚠️' },
    'Huawei MA5800':  { endpoint: '',                      sync: false, color: 'warning', note: 'Sync belum didukung ⚠️' },
    'FiberHome':      { endpoint: '',                      sync: false, color: 'warning', note: 'Sync belum didukung ⚠️' },
    'Nokia':          { endpoint: '',                      sync: false, color: 'warning', note: 'Sync belum didukung ⚠️' },
};

function onModelChange(val) {
    const cfg = modelConfig[val];
    const infoDiv = document.getElementById('model-info');
    const badge   = document.getElementById('model-badge');
    const note    = document.getElementById('model-note');
    const epInput = document.getElementById('api-endpoint');
    const hsgqDiv = document.getElementById('hsgq-key-field');

    if (!cfg) { infoDiv.style.display = 'none'; return; }

    infoDiv.style.display = '';
    badge.className = 'badge fs-6 px-3 py-1 bg-' + cfg.color;
    badge.textContent = val;
    note.textContent  = cfg.note;

    if (epInput && cfg.endpoint) epInput.value = cfg.endpoint;
    if (hsgqDiv) hsgqDiv.style.display = (val === 'HSGQ') ? '' : 'none';
}
</script>
@endpush
