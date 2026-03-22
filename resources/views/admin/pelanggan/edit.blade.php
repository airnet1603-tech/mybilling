<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan – ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 230px;
            --sidebar-bg-start: #1a1a2e;
            --sidebar-bg-end: #0f3460;
            --accent: #e94560;
        }

        * { box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            min-height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            top: 0; left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-brand { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .brand-icon { width: 70px; height: 40px; background: rgba(233,69,96,0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; }
        .sidebar-brand .brand-title { color: #fff; font-weight: 700; font-size: 0.9rem; display: block; }
        .sidebar-brand .brand-sub { color: rgba(255,255,255,0.45); font-size: 0.7rem; }
        .sidebar-nav { padding: 8px 0; flex: 1; }
        .sidebar-nav .nav-link { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; transition: background 0.2s, color 0.2s; white-space: nowrap; }
        .sidebar-nav .nav-link i { width: 16px; font-size: 0.82rem; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.25); color: #fff; }
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.35); }
        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 14px; }
        .sidebar-nav .logout-btn { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; background: none; border: none; width: calc(100% - 16px); text-align: left; cursor: pointer; transition: background 0.2s, color 0.2s; }
        .sidebar-nav .logout-btn:hover { background: rgba(233,69,96,0.25); color: #fff; }

        /* ===== TOPBAR MOBILE ===== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 54px;
            background: linear-gradient(90deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            z-index: 1040;
            align-items: center;
            padding: 0 14px;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .mobile-topbar .hamburger-btn { background: none; border: none; color: #fff; font-size: 1.3rem; cursor: pointer; padding: 4px 8px; border-radius: 6px; }
        .mobile-topbar .hamburger-btn:hover { background: rgba(255,255,255,0.15); }
        .mobile-topbar .brand-title { color: #fff; font-weight: 700; font-size: 0.95rem; }

        /* ===== OVERLAY ===== */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1045; }
        .sidebar-overlay.show { display: block; }

        /* ===== MAIN ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

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

        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places&callback=initMap" async defer></script>
</head>
<body>

<!-- Topbar Mobile (hamburger) -->
<div class="mobile-topbar">
    <button class="hamburger-btn" id="hamburgerBtn">
        <i class="fas fa-bars"></i>
    </button>
    <span class="brand-title">ISP Billing</span>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><img src="https://airnetps.my.id/app/icon/icon_airnet.png" style="height:38px;object-fit:contain;background:#ffffff;padding:2px 4px;border-radius:8px 0px 8px 0px;"></div>
        <div class="brand-text">
            <span class="brand-title">ISP Billing</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">
            <li class="nav-item"><a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a href="/admin/pelanggan" class="nav-link active"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li class="nav-item"><a href="/admin/paket" class="nav-link"><i class="fas fa-box"></i> Paket Internet</a></li>
            <li class="nav-item"><a href="/admin/tagihan" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Tagihan</a></li>
            <li class="nav-item"><a href="/admin/pembayaran" class="nav-link"><i class="fas fa-money-bill-wave"></i> Pembayaran</a></li>
            <li class="nav-item"><a href="/admin/laporan" class="nav-link"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li class="nav-item"><a href="/admin/mikrotik" class="nav-link"><i class="fas fa-network-wired"></i> Mikrotik</a></li>
        </ul>
        <div class="sidebar-divider"></div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/setting" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="width:16px;font-size:0.82rem;"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Edit Pelanggan</h5>
            <small class="text-muted">{{ $pelanggan->id_pelanggan }} · {{ $pelanggan->nama }}</small>
        </div>
        <a href="/admin/pelanggan/{{ $pelanggan->id }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ERRORS --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0 small">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="/admin/pelanggan/{{ $pelanggan->id }}">
        @csrf @method('PUT')

        <div class="row g-3">

            {{-- ===== KOLOM KIRI ===== --}}
            <div class="col-md-8">

                {{-- DATA PRIBADI --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-user me-1"></i> Data Pribadi
                        </div>
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
                        <div class="section-title">
                            <i class="fas fa-network-wired me-1"></i> Data Koneksi
                        </div>
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
                                <input type="date" name="tgl_expired" class="form-control form-control-sm"
                                       value="{{ old('tgl_expired', $pelanggan->tgl_expired?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== KOLOM KANAN ===== --}}
            <div class="col-md-4">

                {{-- PAKET INTERNET --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-box me-1"></i> Paket Internet
                        </div>
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

                {{-- TOMBOL AKSI --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                    <a href="/admin/pelanggan/{{ $pelanggan->id }}" class="btn btn-outline-secondary btn-sm">
                        Batal
                    </a>
                </div>

            </div>
        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== HAMBURGER MENU (sama seperti peta.blade) =====
var hamburgerBtn   = document.getElementById('hamburgerBtn');
var sidebar        = document.getElementById('sidebar');
var sidebarOverlay = document.getElementById('sidebarOverlay');

hamburgerBtn.addEventListener('click', function () {
    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('show');
});
sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});

// ===== PAKET OPTION =====
document.querySelectorAll('.paket-option').forEach(label => {
    label.addEventListener('click', function () {
        document.querySelectorAll('.paket-option').forEach(l => l.classList.remove('selected'));
        this.classList.add('selected');
    });
});
</script>

<script>
var gmap = null;
var gmarker = null;

function initMap() {
    var lat = parseFloat(document.getElementById('lat_input').value) || -8.188492;
    var lng = parseFloat(document.getElementById('lng_input').value) || 112.018204;
    var zoom = (parseFloat(document.getElementById('lat_input').value)) ? 17 : 13;

    gmap = new google.maps.Map(document.getElementById('mapContainer'), {
        center: { lat: lat, lng: lng },
        zoom: zoom,
        mapTypeId: 'hybrid',
        gestureHandling: 'greedy',
        scrollwheel: true,
        draggable: true,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: false,
    });

    if (parseFloat(document.getElementById('lat_input').value)) {
        setPin(lat, lng);
    }

    gmap.addListener('click', function(e) {
        setPin(e.latLng.lat(), e.latLng.lng());
    });

    var input = document.getElementById('mapSearch');
    if (input) {
        var autocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: 'id' }
        });
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) return;
            var la = place.geometry.location.lat();
            var ln = place.geometry.location.lng();
            setPin(la, ln);
            gmap.setCenter({ lat: la, lng: ln });
            gmap.setZoom(17);
        });
    }
}

function setPin(lat, lng) {
    if (gmarker) gmarker.setMap(null);
    gmarker = new google.maps.Marker({
        position: { lat: parseFloat(lat), lng: parseFloat(lng) },
        map: gmap,
        draggable: true,
        animation: google.maps.Animation.DROP,
    });
    gmarker.addListener('dragend', function(e) {
        document.getElementById('lat_input').value = e.latLng.lat().toFixed(8);
        document.getElementById('lng_input').value = e.latLng.lng().toFixed(8);
    });
    document.getElementById('lat_input').value = parseFloat(lat).toFixed(8);
    document.getElementById('lng_input').value = parseFloat(lng).toFixed(8);
    gmap.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
}

function getGPS() {
    if (!navigator.geolocation) { alert('Browser tidak support GPS'); return; }
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            setPin(pos.coords.latitude, pos.coords.longitude);
            gmap.setZoom(18);
        },
        function() { alert('Gagal ambil lokasi GPS'); }
    );
}

function searchLocation() {
    var q = document.getElementById('mapSearch').value.trim();
    if (!q) return;
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: q, region: 'id' }, function(results, status) {
        if (status === 'OK') {
            var loc = results[0].geometry.location;
            setPin(loc.lat(), loc.lng());
            gmap.setZoom(17);
        } else {
            alert('Lokasi tidak ditemukan.');
        }
    });
}

document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && document.activeElement.id === 'mapSearch') {
        e.preventDefault();
        searchLocation();
    }
});

document.getElementById('lat_input').addEventListener('change', function() {
    var la = parseFloat(this.value), ln = parseFloat(document.getElementById('lng_input').value);
    if (la && ln && gmap) setPin(la, ln);
});
document.getElementById('lng_input').addEventListener('change', function() {
    var la = parseFloat(document.getElementById('lat_input').value), ln = parseFloat(this.value);
    if (la && ln && gmap) setPin(la, ln);
});

var isFullscreen = false;
function toggleFullscreen() {
    var el = document.getElementById('mapContainer');
    var icon = document.getElementById('fsIcon');
    if (!isFullscreen) {
        el.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;border-radius:0;';
        icon.className = 'fas fa-compress';
        isFullscreen = true;
        gmap.setOptions({ streetViewControl: true, mapTypeControl: true, scaleControl: true, zoomControl: true });

        var fsBox = document.createElement('div');
        fsBox.id = 'fsSearchBox';
        fsBox.style.cssText = 'position:fixed;top:12px;left:50%;transform:translateX(-50%);z-index:10001;width:420px;max-width:85vw;display:flex;gap:6px;';
        fsBox.innerHTML = '<input id="fsSearchInput" type="text" placeholder="Cari lokasi, nama jalan, tempat..." '
            + 'style="flex:1;padding:10px 16px;border-radius:24px;border:none;box-shadow:0 2px 12px rgba(0,0,0,0.4);font-size:14px;outline:none;">'
            + '<button onclick="fsSearch()" style="padding:10px 16px;border-radius:24px;border:none;background:#1a73e8;color:white;cursor:pointer;box-shadow:0 2px 12px rgba(0,0,0,0.4);">'
            + '<i class=\'fas fa-search\'></i></button>'
            + '<button onclick="toggleFullscreen()" style="padding:10px 14px;border-radius:24px;border:none;background:rgba(0,0,0,0.6);color:white;cursor:pointer;box-shadow:0 2px 12px rgba(0,0,0,0.4);">'
            + '<i class=\'fas fa-compress\'></i></button>';
        document.body.appendChild(fsBox);

        var fsAc = new google.maps.places.Autocomplete(
            document.getElementById('fsSearchInput'),
            { componentRestrictions: { country: 'id' } }
        );
        var style = document.createElement('style');
        style.id = 'pacStyle';
        style.innerHTML = '.pac-container { z-index: 10002 !important; }';
        document.head.appendChild(style);
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
            if (gmarker) {
                gmap.setCenter(gmarker.getPosition());
            } else {
                gmap.setCenter({ lat: -8.188492, lng: 112.018204 });
            }
        }, 200);
    }, 150);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && isFullscreen) toggleFullscreen();
});

function fsSearch() {
    var q = document.getElementById('fsSearchInput') ? document.getElementById('fsSearchInput').value.trim() : '';
    if (!q) return;
    new google.maps.Geocoder().geocode({ address: q, region: 'id' }, function(results, status) {
        if (status === 'OK') {
            setPin(results[0].geometry.location.lat(), results[0].geometry.location.lng());
            gmap.setZoom(18);
        } else {
            alert('Lokasi tidak ditemukan.');
        }
    });
}
</script>

</body>
</html>