@extends('layouts.admin')
@section('title', 'Edit OLT')

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
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
                    <div class="mb-3" id="hsgq-key-field" style="{{ $olt->model == 'HSGQ' ? '' : 'display:none' }}">
                        <label class="form-label fw-semibold">HSGQ Key</label>
                        <div class="input-group">
                            <input type="text" name="hsgq_key" id="hsgq_key_input" class="form-control font-monospace" value="{{ $olt->hsgq_key }}" placeholder="contoh: 1761d487ba0cde5f285059b5cca9a22c">
                            <button type="button" class="btn btn-outline-primary" id="btn-fetch-key" onclick="fetchHsgqKey()">
                                <i class="fas fa-key"></i> Fetch Key Otomatis
                            </button>
                        </div>
                        <div id="fetch-key-result" class="mt-1"></div>
                        <div class="mt-2 p-2 bg-light border rounded small">
                            <b><i class="fas fa-info-circle text-primary"></i> Cara ambil HSGQ Key manual:</b>
                            <ol class="mb-1 mt-1 ps-3">
                                <li>Buka browser, akses IP OLT: <code>http://<b>IP_LOKAL_OLT</b></code> (tanyakan ke teknisi jika tidak tahu IP lokalnya)</li>
                                <li>Tekan <kbd>F12</kbd> → pilih tab <b>Network</b></li>
                                <li>Login ke OLT seperti biasa</li>
                                <li>Cari request <code>userlogin?form=login</code> (method POST)</li>
                                <li>Klik request tersebut → tab <b>Request</b> → lihat nilai <code>key</code></li>
                                <li>Copy nilai <code>key</code>, paste ke kolom HSGQ Key di atas, klik Update</li>
                            </ol>
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
<script>
var oltLat = {{ $olt->lat ?? -8.207014 }};
var oltLng = {{ $olt->lng ?? 112.019986 }};
var gmap, gmarker;

function initMapPicker() {
    var center = { lat: oltLat, lng: oltLng };
    gmap = new google.maps.Map(document.getElementById('map-picker'), {
        center: center,
        zoom: 15,
        mapTypeId: 'hybrid',
        gestureHandling: 'greedy',
        fullscreenControl: true,
        streetViewControl: false,
        mapTypeControl: true,
    });
    gmarker = new google.maps.Marker({
        position: center,
        map: gmap,
        title: '{{ addslashes($olt->name) }}',
        draggable: true,
        icon: { url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', scaledSize: new google.maps.Size(44,44) },
    });
    gmarker.addListener('dragend', function() {
        var pos = gmarker.getPosition();
        document.getElementById('lat-input').value = pos.lat().toFixed(8);
        document.getElementById('lng-input').value = pos.lng().toFixed(8);
        document.getElementById('lat-display').textContent = pos.lat().toFixed(6);
        document.getElementById('lng-display').textContent = pos.lng().toFixed(6);
    });
    gmap.addListener('click', function(e) {
        var pos = e.latLng;
        gmarker.setPosition(pos);
        document.getElementById('lat-input').value = pos.lat().toFixed(8);
        document.getElementById('lng-input').value = pos.lng().toFixed(8);
        document.getElementById('lat-display').textContent = pos.lat().toFixed(6);
        document.getElementById('lng-display').textContent = pos.lng().toFixed(6);
    });
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check);
            initMapPicker();
        }
    }, 100);
});

function fetchHsgqKey() {
    const btn = document.getElementById('btn-fetch-key');
    const result = document.getElementById('fetch-key-result');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Fetching...';
    result.innerHTML = '';
    fetch('/admin/topologi/olt/{{ $olt->id }}/fetch-hsgq-key', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({})
    }).then(r => r.json()).then(data => {
        if (data.success) {
            document.getElementById('hsgq_key_input').value = data.key;
            result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
        } else {
            result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + data.error + '</span>';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-key"></i> Fetch Key Otomatis';
    }).catch(e => {
        result.innerHTML = '<span class="text-danger">Error: ' + e.message + '</span>';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-key"></i> Fetch Key Otomatis';
    });
}
function hapusOlt() {
    if (confirm('Yakin hapus OLT {{ $olt->name }}? Semua ONU akan ikut terhapus!')) {
        document.getElementById('form-hapus').submit();
    }
}
</script>
@endpush
