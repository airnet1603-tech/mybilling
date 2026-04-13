<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah ODP - ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
    <style>
        :root { --sidebar-width:230px; --sidebar-bg-start:#1a1a2e; --sidebar-bg-end:#0f3460; --accent:#e94560; }
        body { background:#f0f2f5; font-family:'Segoe UI',sans-serif; }
        .sidebar { background:linear-gradient(180deg,var(--sidebar-bg-start),var(--sidebar-bg-end)); min-height:100vh; width:var(--sidebar-width); position:fixed; top:0; left:0; z-index:1050; display:flex; flex-direction:column; }
        .sidebar-brand { padding:14px 16px; border-bottom:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; gap:10px; }
        .sidebar-brand .brand-icon { width:70px; height:40px; background:rgba(233,69,96,0.25); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--accent); font-size:1rem; flex-shrink:0; }
        .sidebar-brand .brand-title { color:#fff; font-weight:700; font-size:0.9rem; display:block; }
        .sidebar-brand .brand-sub { color:rgba(255,255,255,0.45); font-size:0.7rem; }
        .sidebar-nav { padding:8px 0; flex:1; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; transition:background 0.2s,color 0.2s; text-decoration:none; }
        .sidebar-nav .nav-link i { width:16px; font-size:0.82rem; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background:rgba(233,69,96,0.25); color:#fff; }
        .sidebar-divider { border-top:1px solid rgba(255,255,255,0.08); margin:6px 14px; }
        .main-content { margin-left:var(--sidebar-width); padding:24px; }
        #pickMap { height:500px; border-radius:12px; border:2px solid #dee2e6; }
        .coord-box { background:#f8f9fa; border-radius:10px; padding:12px 16px; border:1px solid #dee2e6; }
        .pin-hint { background:#fff3cd; color:#856404; border-radius:8px; padding:10px 14px; font-size:0.85rem; }
    </style>
</head>
<body>
@include('admin.partials.sidebar')

<div class="main-content">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
        <h5 class="fw-bold mb-0"><i class="fas fa-project-diagram me-2 text-warning"></i>Tambah ODP Baru</h5>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-body p-4">
                    <form method="POST" action="/admin/topologi/odp/store">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama ODP <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: ODP-A01" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">OLT Induk <span class="text-danger">*</span></label>
                            <select name="olt_id" class="form-select" required>
                                <option value="">-- Pilih OLT --</option>
                                @foreach($olts as $olt)
                                <option value="{{ $olt->id }}" {{ old('olt_id') == $olt->id ? 'selected' : '' }}>
                                    {{ $olt->name }} ({{ $olt->ip_address }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">ODC Induk <small class="text-muted">(opsional)</small></label>
                            <select name="odc_id" class="form-select">
                                <option value="">-- Langsung ke OLT --</option>
                                @foreach($odcs as $odc)
                                <option value="{{ $odc->id }}" {{ old('odc_id') == $odc->id ? 'selected' : '' }}>
                                    {{ $odc->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">ODP Induk <small class="text-muted">(opsional)</small></label>
                            <select name="parent_odp_id" id="parent_odp_id" class="form-select">
                                <option value="">-- Tidak ada --</option>
                                @foreach($odps as $odp)
                                <option value="{{ $odp->id }}" {{ old('parent_odp_id') == $odp->id ? 'selected' : '' }}>
                                    {{ $odp->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kapasitas Port</label>
                            <input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', 8) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}">
                        </div>

                        <div class="coord-box mb-4">
                            <div class="fw-semibold small mb-2"><i class="fas fa-map-pin me-1 text-warning"></i>Koordinat Lokasi</div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1">Latitude</label>
                                    <input type="text" name="lat" id="inputLat" class="form-control form-control-sm" value="{{ old('lat') }}" required readonly placeholder="Klik peta">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1">Longitude</label>
                                    <input type="text" name="lng" id="inputLng" class="form-control form-control-sm" value="{{ old('lng') }}" required readonly placeholder="Klik peta">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning flex-fill text-white">
                                <i class="fas fa-save me-1"></i> Simpan ODP
                            </button>
                            <a href="/admin/topologi" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="pin-hint mb-2">
                <i class="fas fa-hand-pointer me-1"></i>
                <strong>Klik pada peta</strong> untuk menentukan lokasi ODP. Geser titik oranye jika perlu.
            </div>
            <div id="pickMap"></div>
            <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Bisa juga ketik nama lokasi di kotak pencarian di peta.</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var pickMarker = null;

function initPickMap() {
    var map = new google.maps.Map(document.getElementById('pickMap'), {
        center: { lat: -8.207019, lng: 112.019980 },
        zoom: 13, mapTypeId: 'hybrid', gestureHandling: 'greedy',
    });

    var input = document.createElement('input');
    input.type = 'text';
    input.placeholder = '🔍 Cari lokasi di sini...';
    input.style.cssText = 'margin:10px;padding:8px 14px;width:320px;border-radius:8px;border:2px solid #fd7e14;font-size:13px;box-shadow:0 2px 8px rgba(0,0,0,0.2);outline:none;';
    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
    var searchBox = new google.maps.places.SearchBox(input);
    searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();
        if (!places.length) return;
        var loc = places[0].geometry.location;
        map.setCenter(loc);
        map.setZoom(16);
        placeMarker(map, loc.lat(), loc.lng());
    });

    map.addListener('click', function(e) {
        placeMarker(map, e.latLng.lat(), e.latLng.lng());
    });
}

function placeMarker(map, lat, lng) {
    if (pickMarker) pickMarker.setMap(null);
    pickMarker = new google.maps.Marker({
        position: { lat: lat, lng: lng }, map: map,
        title: 'Lokasi ODP', draggable: true,
        icon: { url: 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png', scaledSize: new google.maps.Size(40,40) },
        animation: google.maps.Animation.DROP,
    });
    document.getElementById('inputLat').value = lat.toFixed(8);
    document.getElementById('inputLng').value = lng.toFixed(8);
    pickMarker.addListener('dragend', function(e) {
        document.getElementById('inputLat').value = e.latLng.lat().toFixed(8);
        document.getElementById('inputLng').value = e.latLng.lng().toFixed(8);
    });
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check); initPickMap();
        }
    }, 100);
});
</script>
</body>
</html>
