@extends('layouts.admin')

@push('styles')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
<style>
    #petaMap { height:calc(100vh - 280px); border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
    .stat-card { border:none; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.07); }
    .map-label { background:rgba(0,0,0,0.65); padding:2px 6px; border-radius:4px; margin-top:4px; white-space:nowrap; text-shadow:none; }
    @media (max-width:768px) { #petaMap { height:calc(100vh - 320px); } }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0"><i class="fas fa-map-marked-alt me-2 text-danger"></i>Peta Pelanggan</h5>
        <small class="text-muted">Lokasi pelanggan terdaftar</small>
    </div>
    <a href="/admin/pelanggan" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card card">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-users text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5" id="counterTotal">{{ $total }}</div>
                    <div class="small text-muted">Total Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card card">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;background:#d4edda;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-map-marker-alt text-success"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5" id="counterPeta">{{ $totalPeta }}</div>
                    <div class="small text-muted">Ada di Peta</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card card">
            <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;background:#f8d7da;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-map-marker text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5" id="counterTanpa">{{ $tanpaPeta }}</div>
                    <div class="small text-muted">Tanpa Koordinat</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card mb-3" style="border:none;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.07);">
    <div class="card-body py-2 px-3 d-flex gap-2 flex-wrap align-items-center">
        <input type="text" id="searchPelanggan" class="form-control form-control-sm" style="width:200px;" placeholder="Cari nama/username...">
        <select id="filterStatus" class="form-select form-select-sm" style="width:150px;">
            <option value="">Semua Status</option>
            <option value="aktif">Aktif</option>
            <option value="isolir">Isolir</option>
            <option value="suspend">Suspend</option>
            <option value="nonaktif">Nonaktif</option>
        </select>
        <select id="filterPaket" class="form-select form-select-sm" style="width:150px;">
            <option value="">Semua Paket</option>
            @foreach(\App\Models\Paket::all() as $paket)
            <option value="{{ $paket->nama_paket }}">{{ $paket->nama_paket }}</option>
            @endforeach
        </select>
        <select id="filterRouter" class="form-select form-select-sm" style="width:150px;">
            <option value="">Semua Router</option>
            @foreach(\App\Models\Router::where('is_active', true)->get() as $router)
            <option value="{{ $router->nama }}">{{ $router->nama }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-outline-secondary" onclick="resetFilter()">
            <i class="fas fa-times me-1"></i>Reset
        </button>
        <span class="ms-auto small text-muted" id="pinCount">{{ $totalPeta }} pin ditampilkan</span>
    </div>
</div>

{{-- PETA --}}
<div id="petaMap"></div>

@endsection

@push('scripts')
<script>
var allMarkers = [];
var infoWindow = null;
var petaMap    = null;
var pelangganData = {!! $mapDataJson !!};

function initMap() {
    petaMap = new google.maps.Map(document.getElementById('petaMap'), {
        center            : { lat: -7.9, lng: 112.6 },
        zoom              : 12,
        mapTypeId         : 'hybrid',
        gestureHandling   : 'greedy',
        fullscreenControl : true,
        streetViewControl : true,
        mapTypeControl    : true,
    });

    infoWindow = new google.maps.InfoWindow();

    var pinColors = {
        'aktif'    : 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
        'isolir'   : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
        'suspend'  : 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
        'nonaktif' : 'http://maps.google.com/mapfiles/ms/icons/grey-dot.png',
    };

    pelangganData.forEach(function(p) {
        var marker = new google.maps.Marker({
            position : { lat: p.lat, lng: p.lng },
            map      : petaMap,
            title    : p.nama,
            icon     : pinColors[p.status] || pinColors['aktif'],
            data     : p,
            label    : {
                text      : p.nama,
                color     : '#ffffff',
                fontSize  : '11px',
                fontWeight: 'bold',
                className : 'map-label',
            },
        });

        marker.addListener('click', function() {
            var statusColor = { aktif:'#28a745', isolir:'#dc3545', suspend:'#ffc107', nonaktif:'#6c757d' };
            var color = statusColor[p.status] || '#6c757d';
            var googleMapsUrl = (p.maps && p.maps.trim() !== '')
                ? p.maps
                : (p.lat && p.lng && !isNaN(p.lat) && !isNaN(p.lng))
                    ? 'https://www.google.com/maps?q=' + p.lat + ',' + p.lng
                    : null;
            var openStreetMapUrl = 'https://www.openstreetmap.org/?mlat=' + p.lat + '&mlon=' + p.lng + '&zoom=17';

            var onuHtml = '';
            if (p.onus && p.onus.length > 0) {
                onuHtml += '<div style="margin-top:8px;border-top:1px solid #eee;padding-top:6px;">';
                onuHtml += '<div style="font-size:11px;font-weight:600;color:#555;margin-bottom:4px;">&#128225; ONU Terhubung:</div>';
                p.onus.forEach(function(o) {
                    var sColor = o.status === 'Up' ? '#28a745' : '#dc3545';
                    onuHtml += '<div style="font-size:11px;margin-bottom:2px;">'
                        + '<span style="color:'+sColor+';font-weight:600;">&#9679; </span>'
                        + (o.name || o.onu_id)
                        + (o.odp ? ' <span style="color:#888;">&rarr; '+o.odp+'</span>' : '')
                        + '</div>';
                });
                onuHtml += '</div>';
            } else {
                onuHtml = '<div style="margin-top:6px;font-size:11px;color:#aaa;"><i>Tidak ada ONU terhubung</i></div>';
            }
            infoWindow.setContent(
                '<div style="min-width:220px;font-family:Segoe UI,sans-serif;">' +
                '<div style="font-weight:700;font-size:14px;margin-bottom:6px;">' + p.nama + '</div>' +
                '<div style="font-size:12px;color:#666;margin-bottom:4px;"><i class="fas fa-user me-1"></i>' + p.username + '</div>' +
                '<div style="font-size:12px;margin-bottom:4px;"><span style="background:' + color + ';color:white;padding:2px 8px;border-radius:10px;font-size:11px;">' + p.status.toUpperCase() + '</span></div>' +
                '<div style="font-size:12px;color:#666;margin-bottom:2px;"><i class="fas fa-box me-1"></i>' + p.paket + '</div>' +
                '<div style="font-size:12px;color:#666;margin-bottom:4px;"><i class="fas fa-calendar me-1"></i>Expired: ' + p.expired + '</div>' +
                onuHtml +
                '<a href="' + p.url + '" style="display:block;text-align:center;background:#1a73e8;color:white;padding:6px;border-radius:6px;text-decoration:none;font-size:12px;margin-top:8px;margin-bottom:5px;"><i class="fas fa-eye me-1"></i>Lihat Detail</a>' +
                (googleMapsUrl ? '<a href="' + googleMapsUrl + '" target="_blank" style="display:block;text-align:center;background:#34a853;color:white;padding:6px;border-radius:6px;text-decoration:none;font-size:12px;margin-bottom:5px;"><i class="fas fa-map-marked-alt me-1"></i>Buka di Google Maps</a>' : '') +
                '<a href="' + openStreetMapUrl + '" target="_blank" style="display:block;text-align:center;background:#e67e22;color:white;padding:6px;border-radius:6px;text-decoration:none;font-size:12px;"><i class="fas fa-map me-1"></i>Buka di OpenStreetMap</a>' +
                '</div>'
            );
            infoWindow.open(petaMap, marker);
        });

        allMarkers.push(marker);
    });

    if (allMarkers.length > 0) {
        var bounds = new google.maps.LatLngBounds();
        allMarkers.forEach(function(m) { bounds.extend(m.getPosition()); });
        petaMap.fitBounds(bounds);
    }

    document.getElementById('searchPelanggan').addEventListener('input', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    document.getElementById('filterPaket').addEventListener('change', applyFilter);
    document.getElementById('filterRouter').addEventListener('change', applyFilter);
}

function applyFilter() {
    var search = document.getElementById('searchPelanggan').value.toLowerCase();
    var status = document.getElementById('filterStatus').value;
    var paket  = document.getElementById('filterPaket').value;
    var router = document.getElementById('filterRouter').value;
    var count  = 0;
    allMarkers.forEach(function(m) {
        var p    = m.data;
        var show = true;
        if (search && !p.nama.toLowerCase().includes(search) && !p.username.toLowerCase().includes(search)) show = false;
        if (status && p.status !== status) show = false;
        if (paket  && p.paket  !== paket)  show = false;
        if (router && p.router !== router) show = false;
        m.setVisible(show);
        if (show) count++;
    });
    document.getElementById('pinCount').textContent = count + ' pin ditampilkan';

    // Update counter cards
    var router = document.getElementById('filterRouter').value;
    if (router) {
        var total = allMarkers.filter(m => m.data.router === router).length;
        document.getElementById('counterTotal').textContent = total;
        document.getElementById('counterPeta').textContent  = count;
        document.getElementById('counterTanpa').textContent = total - count;
    } else {
        document.getElementById('counterTotal').textContent = {{ $total }};
        document.getElementById('counterPeta').textContent  = count;
        document.getElementById('counterTanpa').textContent = {{ $tanpaPeta }};
    }
}

function resetFilter() {
    document.getElementById('searchPelanggan').value = '';
    document.getElementById('filterStatus').value    = '';
    document.getElementById('filterPaket').value     = '';
    document.getElementById('filterRouter').value    = '';
    applyFilter();
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check);
            initMap();
        }
    }, 100);
});
</script>
@endpush
