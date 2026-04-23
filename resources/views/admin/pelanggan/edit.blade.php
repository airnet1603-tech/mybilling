@extends('layouts.admin')

@section('title', 'Edit Pelanggan - ISP Billing')

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places&callback=initMap" async defer></script>
@endpush

@push('styles')
<style>
    .section-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6c757d;
        margin-bottom: 14px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f0f2f5;
    }
    .paket-option {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .paket-option:hover { border-color: #adb5bd; }
    .paket-option.selected { border-color: #0d6efd; background: #f0f5ff; }
    .paket-option input[type="radio"] { display: none; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Edit Pelanggan</h5>
        <small class="text-muted">{{ $pelanggan->id_pelanggan }} &mdash; {{ $pelanggan->nama }}</small>
    </div>
    <a href="/admin/pelanggan/{{ $pelanggan->id }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0 small">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="/admin/pelanggan/{{ $pelanggan->id }}">
    @csrf @method('PUT')
    <div class="row g-3">

        {{-- KOLOM KIRI --}}
        <div class="col-md-8">

            {{-- DATA PRIBADI --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-user me-1"></i> Data Pribadi</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control form-control-sm @error('nama') is-invalid @enderror"
                                   value="{{ old('nama', $pelanggan->nama) }}" required>
                            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">No. HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control form-control-sm @error('no_hp') is-invalid @enderror"
                                   value="{{ old('no_hp', $pelanggan->no_hp) }}" required>
                            @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm"
                                   value="{{ old('email', $pelanggan->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Wilayah</label>
                            <input type="text" name="wilayah" class="form-control form-control-sm"
                                   value="{{ old('wilayah', $pelanggan->wilayah) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                            <div class="mt-2">
                                <label class="form-label small fw-semibold">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>Lokasi di Peta
                                    <span class="text-muted fw-normal">(opsional - klik peta atau GPS)</span>
                                </label>
                                <div class="d-flex gap-2 mb-2">
                                    <input type="number" step="any" name="latitude" id="lat_input"
                                           class="form-control form-control-sm" placeholder="Latitude"
                                           value="{{ old('latitude', $pelanggan->latitude) }}">
                                    <input type="number" step="any" name="longitude" id="lng_input"
                                           class="form-control form-control-sm" placeholder="Longitude"
                                           value="{{ old('longitude', $pelanggan->longitude) }}">
                                    <button type="button" class="btn btn-sm btn-outline-primary text-nowrap" onclick="getGPS()">
                                        <i class="fas fa-crosshairs me-1"></i>GPS
                                    </button>
                                </div>
                                <div class="mb-1">
                                    <input type="text" name="maps" class="form-control form-control-sm"
                                           placeholder="URL Google Maps (https://maps.app.goo.gl/...)"
                                           value="{{ old('maps', $pelanggan->maps) }}">
                                </div>
                                <div class="d-flex gap-1 mb-1">
                                    <div class="input-group input-group-sm flex-grow-1">
                                        <input type="text" id="mapSearch" class="form-control form-control-sm"
                                               placeholder="Cari lokasi... (contoh: Jl. Merdeka Malang)">
                                        <button type="button" class="btn btn-outline-secondary" onclick="searchLocation()">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="toggleFullscreen()" title="Fullscreen">
                                        <i class="fas fa-expand" id="fsIcon"></i>
                                    </button>
                                </div>
                                <div id="mapWrapper" style="width:100%;overflow:hidden;border-radius:8px 0px 8px 0px;">
                                    <div id="mapContainer" style="height:280px;width:100%;border-radius:8px 0px 8px 0px;border:1px solid #dee2e6;position:relative;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DATA KONEKSI --}}
            <div class="card">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-network-wired me-1"></i> Data Koneksi</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username PPPoE <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control form-control-sm @error('username') is-invalid @enderror"
                                   value="{{ old('username', $pelanggan->username) }}" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Password Baru</label>
                            <input type="text" name="password" class="form-control form-control-sm"
                                   placeholder="Kosongkan jika tidak diubah">
                            <div class="form-text">Isi hanya jika ingin mengubah password.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">IP Address</label>
                            <input type="text" name="ip_address" class="form-control form-control-sm"
                                   value="{{ old('ip_address', $pelanggan->ip_address) }}"
                                   placeholder="Kosongkan jika dinamis">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Router</label>
                            <select name="router_id" class="form-select form-select-sm">
                                <option value="">-- Pilih Router --</option>
                                @foreach($routers as $router)
                                    <option value="{{ $router->id }}" {{ (old('router_id', $pelanggan->router_id) == $router->id) ? 'selected' : '' }}>
                                        {{ $router->nama }} ({{ $router->ip_address }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Jenis Layanan</label>
                            <select name="jenis_layanan" class="form-select form-select-sm">
                                <option value="pppoe"   {{ old('jenis_layanan', $pelanggan->jenis_layanan)=='pppoe'   ? 'selected':'' }}>PPPoE</option>
                                <option value="hotspot" {{ old('jenis_layanan', $pelanggan->jenis_layanan)=='hotspot' ? 'selected':'' }}>Hotspot</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tanggal Expired</label>
                            @if(auth()->user()->role === 'admin')
                            <input type="date" name="tgl_expired" class="form-control form-control-sm"
                                   value="{{ old('tgl_expired', $pelanggan->tgl_expired?->format('Y-m-d')) }}">
                            @else
                            <input type="date" class="form-control form-control-sm"
                                   value="{{ $pelanggan->tgl_expired?->format('Y-m-d') }}" disabled>
                            <input type="hidden" name="tgl_expired" value="{{ $pelanggan->tgl_expired?->format('Y-m-d') }}">
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-md-4">

            {{-- PAKET INTERNET --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-box me-1"></i> Paket Internet</div>
                    @foreach($pakets as $paket)
                    <label class="paket-option d-block {{ old('paket_id', $pelanggan->paket_id) == $paket->id ? 'selected' : '' }}"
                           for="paket{{ $paket->id }}">
                        <input type="radio" name="paket_id" id="paket{{ $paket->id }}"
                               value="{{ $paket->id }}"
                               {{ old('paket_id', $pelanggan->paket_id) == $paket->id ? 'checked' : '' }}>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-bold small">{{ $paket->nama_paket }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-arrow-down fa-xs"></i> {{ $paket->kecepatan_download }} Mbps &nbsp;
                                    <i class="fas fa-arrow-up fa-xs"></i> {{ $paket->kecepatan_upload }} Mbps
                                </small>
                            </div>
                            <div class="text-success fw-bold small text-end">
                                Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                <div class="text-muted fw-normal" style="font-size:0.7rem;">/bulan</div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- TOMBOL --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
                <a href="/admin/pelanggan/{{ $pelanggan->id }}" class="btn btn-outline-secondary btn-sm">Batal</a>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.paket-option').forEach(label => {
    label.addEventListener('click', function () {
        document.querySelectorAll('.paket-option').forEach(l => l.classList.remove('selected'));
        this.classList.add('selected');
    });
});

var gmap = null, gmarker = null;

function initMap() {
    var lat = parseFloat(document.getElementById('lat_input').value) || -8.188492;
    var lng = parseFloat(document.getElementById('lng_input').value) || 112.018204;
    var zoom = parseFloat(document.getElementById('lat_input').value) ? 17 : 13;
    gmap = new google.maps.Map(document.getElementById('mapContainer'), {
        center: { lat: lat, lng: lng }, zoom: zoom,
        mapTypeId: 'hybrid', gestureHandling: 'greedy',
        mapTypeControl: true, streetViewControl: true, fullscreenControl: false,
    });
    if (parseFloat(document.getElementById('lat_input').value)) setPin(lat, lng);
    gmap.addListener('click', function(e) { setPin(e.latLng.lat(), e.latLng.lng()); });
    var ac = new google.maps.places.Autocomplete(document.getElementById('mapSearch'), { componentRestrictions: { country: 'id' } });
    ac.addListener('place_changed', function() {
        var place = ac.getPlace();
        if (!place.geometry) return;
        setPin(place.geometry.location.lat(), place.geometry.location.lng());
        gmap.setZoom(17);
    });
}

function setPin(lat, lng) {
    autoFillMapsUrl(lat, lng);
    if (gmarker) gmarker.setMap(null);
    gmarker = new google.maps.Marker({ position: { lat: parseFloat(lat), lng: parseFloat(lng) }, map: gmap, draggable: true, animation: google.maps.Animation.DROP });
    gmarker.addListener('dragend', function(e) {
        document.getElementById('lat_input').value = e.latLng.lat().toFixed(8);
        document.getElementById('lng_input').value = e.latLng.lng().toFixed(8);
    });
    document.getElementById('lat_input').value = parseFloat(lat).toFixed(8);
    document.getElementById('lng_input').value = parseFloat(lng).toFixed(8);
    gmap.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
}

function autoFillMapsUrl(lat, lng) {
    var mapsInput = document.querySelector('input[name="maps"]');

    // Auto extract koordinat dari URL Google Maps
    if (mapsInput) {
        mapsInput.addEventListener('change', function() {
            var url = this.value.trim();
            if (!url) return;
            var extracted = extractCoordsFromUrl(url);
            if (extracted) {
                document.querySelector('input[name="latitude"]').value = extracted.lat;
                document.querySelector('input[name="longitude"]').value = extracted.lng;
                setPin(extracted.lat, extracted.lng);
            } else if (url.indexOf('goo.gl') !== -1 || url.indexOf('maps.app') !== -1) {
                // Short URL - resolve via backend
                var inp = this;
                inp.placeholder = 'Memproses URL...';
                fetch('/admin/utils/resolve-maps-url?url=' + encodeURIComponent(url))
                    .then(function(r){ return r.json(); })
                    .then(function(data){
                        inp.placeholder = '';
                        if (data.lat && data.lng) {
                            document.querySelector('input[name="latitude"]').value = data.lat;
                            document.querySelector('input[name="longitude"]').value = data.lng;
                            if (data.url) inp.value = data.url;
                            setPin(parseFloat(data.lat), parseFloat(data.lng));
                        } else {
                            alert('Koordinat tidak ditemukan. Gunakan URL panjang dari Google Maps.');
                        }
                    })
                    .catch(function(){ inp.placeholder = ''; alert('Gagal resolve URL.'); });
            }
        });
    }

    function extractCoordsFromUrl(url) {
        var patterns = [
            /[?&]q=(-?\d+\.?\d*),(-?\d+\.?\d*)/,
            /@(-?\d+\.?\d*),(-?\d+\.?\d*)/,
            /maps\/place\/[^/]+\/@(-?\d+\.?\d*),(-?\d+\.?\d*)/,
            /ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/,
        ];
        for (var i = 0; i < patterns.length; i++) {
            var m = url.match(patterns[i]);
            if (m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
        }
        return null;
    }
    if (mapsInput && !mapsInput.value && lat && lng && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng))) mapsInput.value = 'https://www.google.com/maps?q=' + lat + ',' + lng;
}

function getGPS() {
    if (!navigator.geolocation) { alert('Browser tidak support GPS'); return; }
    navigator.geolocation.getCurrentPosition(
        function(pos) { setPin(pos.coords.latitude, pos.coords.longitude); gmap.setZoom(18); },
        function() { alert('Gagal ambil lokasi GPS'); }
    );
}

function searchLocation() {
    var q = document.getElementById('mapSearch').value.trim();
    if (!q) return;
    new google.maps.Geocoder().geocode({ address: q, region: 'id' }, function(r, s) {
        if (s === 'OK') { setPin(r[0].geometry.location.lat(), r[0].geometry.location.lng()); gmap.setZoom(17); }
        else alert('Lokasi tidak ditemukan.');
    });
}

document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && document.activeElement.id === 'mapSearch') { e.preventDefault(); searchLocation(); }
});

document.getElementById('lat_input').addEventListener('change', function() {
    var la = parseFloat(this.value), ln = parseFloat(document.getElementById('lng_input').value);
    if (la && ln && gmap) setPin(la, ln);
    autoFillMapsUrl(la, ln);
});
document.getElementById('lng_input').addEventListener('change', function() {
    var la = parseFloat(document.getElementById('lat_input').value), ln = parseFloat(this.value);
    if (la && ln && gmap) setPin(la, ln);
    autoFillMapsUrl(la, ln);
});

window.addEventListener('load', function() {
    var lat = parseFloat(document.getElementById('lat_input').value);
    var lng = parseFloat(document.getElementById('lng_input').value);
    autoFillMapsUrl(lat, lng);
});

var isFullscreen = false;
function toggleFullscreen() {
    var el = document.getElementById('mapContainer');
    var icon = document.getElementById('fsIcon');
    if (!isFullscreen) {
        el.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;border-radius:0;';
        icon.className = 'fas fa-compress';
        isFullscreen = true;
        var fsBox = document.createElement('div');
        fsBox.id = 'fsSearchBox';
        fsBox.style.cssText = 'position:fixed;top:12px;left:50%;transform:translateX(-50%);z-index:10001;width:420px;max-width:85vw;display:flex;gap:6px;';
        fsBox.innerHTML = '<input id="fsSearchInput" type="text" placeholder="Cari lokasi..." style="flex:1;padding:10px 16px;border-radius:24px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.4);font-size:14px;outline:none;">'
            + '<button onclick="fsSearch()" style="padding:10px 16px;border-radius:24px;border:none;background:#1a73e8;color:white;cursor:pointer;"><i class="fas fa-search"></i></button>'
            + '<button onclick="toggleFullscreen()" style="padding:10px 14px;border-radius:24px;border:none;background:rgba(0,0,0,0.6);color:white;cursor:pointer;"><i class="fas fa-compress"></i></button>';
        document.body.appendChild(fsBox);
        var style = document.createElement('style');
        style.id = 'pacStyle';
        style.innerHTML = '.pac-container { z-index: 10002 !important; }';
        document.head.appendChild(style);
        var fsAc = new google.maps.places.Autocomplete(document.getElementById('fsSearchInput'), { componentRestrictions: { country: 'id' } });
        fsAc.addListener('place_changed', function() {
            var place = fsAc.getPlace();
            if (!place.geometry) return;
            setPin(place.geometry.location.lat(), place.geometry.location.lng());
            gmap.setZoom(18);
        });
        document.getElementById('fsSearchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); fsSearch(); }
        });
    } else {
        icon.className = 'fas fa-expand';
        isFullscreen = false;
        var fsBox = document.getElementById('fsSearchBox');
        if (fsBox) fsBox.remove();
        var pacStyle = document.getElementById('pacStyle');
        if (pacStyle) pacStyle.remove();
        el.style.cssText = 'height:280px;width:100%;border-radius:8px 0px 8px 0px;border:1px solid #dee2e6;position:relative;';
    }
    setTimeout(function() {
        google.maps.event.trigger(gmap, 'resize');
        setTimeout(function() {
            google.maps.event.trigger(gmap, 'resize');
            if (gmarker) gmap.setCenter(gmarker.getPosition());
            else gmap.setCenter({ lat: -8.188492, lng: 112.018204 });
        }, 200);
    }, 150);
}

document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && isFullscreen) toggleFullscreen(); });

function fsSearch() {
    var q = document.getElementById('fsSearchInput') ? document.getElementById('fsSearchInput').value.trim() : '';
    if (!q) return;
    new google.maps.Geocoder().geocode({ address: q, region: 'id' }, function(r, s) {
        if (s === 'OK') { setPin(r[0].geometry.location.lat(), r[0].geometry.location.lng()); gmap.setZoom(18); }
        else alert('Lokasi tidak ditemukan.');
    });
}
</script>
@endpush
