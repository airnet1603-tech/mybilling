@extends('layouts.admin')
@section('title', 'Topologi OLT')

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
<style>
#map { height: calc(100vh - 140px); border-radius: 12px; }
.olt-card { cursor: pointer; transition: 0.2s; border-left: 4px solid #e94560; }
.olt-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.badge-up   { background:#d4edda; color:#155724; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.badge-down { background:#f8d7da; color:#721c24; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.dot { width:12px; height:12px; border-radius:50%; display:inline-block; margin-right:6px; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0 fw-bold">🗺️ Topologi OLT</h4>
        <small class="text-muted">Peta jaringan fiber optik</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/topologi/peta" class="btn btn-warning btn-sm">
            <i class="fas fa-map-marked-alt"></i> Peta Topologi
        </a>
        <button class="btn btn-success btn-sm" onclick="syncAllOnu()">
            <i class="fas fa-sync"></i> Sync ONU
        </button>
        <a href="/admin/topologi/olt/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah OLT
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Kartu OLT --}}
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0">
                <i class="fas fa-server text-danger"></i> Daftar OLT
            </div>
            <div class="card-body p-2" id="olt-list">
                @forelse($olts as $olt)
                <div class="card olt-card mb-2 p-2" onclick="focusOlt({{ $olt->lat }}, {{ $olt->lng }}, '{{ $olt->name }}')">
                    <div class="fw-semibold">{{ $olt->name }}</div>
                    <small class="text-muted">{{ $olt->ip_address }}</small><br>
                    <small>
                        🔵 ODP: {{ $olt->odps_count }} &nbsp;
                        📡 ONU: {{ $olt->onus_count }}
                    </small>
                    <div class="mt-1">
                        <a href="/admin/topologi/olt/{{ $olt->id }}" class="btn btn-xs btn-outline-primary" style="font-size:0.7rem;padding:1px 8px;">Detail</a>
                        <a href="/admin/topologi/olt/{{ $olt->id }}/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:1px 8px;">Edit</a>
                        <button onclick="syncOnu({{ $olt->id }}, event)" class="btn btn-xs btn-outline-success" style="font-size:0.7rem;padding:1px 8px;">Sync</button>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-server fa-2x mb-2"></i><br>
                    Belum ada OLT.<br>
                    <a href="/admin/topologi/olt/create">Tambah OLT</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Peta --}}
    <div class="col-md-9 position-relative">
        <div class="card">
            <div class="card-body p-2">
                <div id="map"></div>
                <div style="position:absolute;bottom:40px;right:20px;z-index:999;background:#fff;padding:10px 14px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.15);font-size:0.8rem;">
                    <div><span class="dot" style="background:#e94560"></span> OLT</div>
                    <div><span class="dot" style="background:#f59e0b"></span> ODP</div>
                    <div><span class="dot" style="background:#10b981"></span> ONU Online</div>
                    <div><span class="dot" style="background:#ef4444"></span> ONU Offline</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast notifikasi --}}
<div id="toast" style="display:none;position:fixed;bottom:20px;right:20px;z-index:9999;background:#333;color:#fff;padding:10px 18px;border-radius:10px;font-size:0.85rem;"></div>
@endsection

@push('scripts')
<script>
var gmap = null;
var infoWindow = null;
var nodeMap = {};
var allPolylines = [];

function initMap() {
    gmap = new google.maps.Map(document.getElementById('map'), {
        center          : { lat: -7.5, lng: 111.9 },
        zoom            : 13,
        mapTypeId       : 'hybrid',
        gestureHandling : 'greedy',
        fullscreenControl: true,
        streetViewControl: true,
        mapTypeControl  : true,
    });
    infoWindow = new google.maps.InfoWindow();
    loadNodes();
}

function loadNodes() {
    fetch('/admin/topologi/api/nodes')
    .then(r => r.json())
    .then(data => {
        // Clear polylines
        allPolylines.forEach(p => p.setMap(null));
        allPolylines = [];

        // OLT markers
        data.olts.forEach(o => {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = { lat: parseFloat(o.lat), lng: parseFloat(o.lng) };
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : gmap,
                title    : o.name,
                icon     : { url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', scaledSize: new google.maps.Size(40,40) },
                zIndex   : 10,
            });
            marker.addListener('click', function() {
                infoWindow.setContent('<div style="font-family:Segoe UI,sans-serif;min-width:160px;"><b>🔴 OLT: ' + o.name + '</b><br><small>IP: ' + o.ip + '</small></div>');
                infoWindow.open(gmap, marker);
            });
        });

        // ODP markers + garis ke OLT
        data.odps.forEach(o => {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = { lat: parseFloat(o.lat), lng: parseFloat(o.lng) };
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : gmap,
                title    : o.name,
                icon     : { url: 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png', scaledSize: new google.maps.Size(32,32) },
                zIndex   : 5,
            });
            marker.addListener('click', function() {
                infoWindow.setContent('<div style="font-family:Segoe UI,sans-serif;min-width:160px;"><b>🟡 ' + o.type + ': ' + o.name + '</b></div>');
                infoWindow.open(gmap, marker);
            });
            if (nodeMap[o.olt_id]) {
                var line = new google.maps.Polyline({
                    path          : [nodeMap[o.olt_id], { lat: parseFloat(o.lat), lng: parseFloat(o.lng) }],
                    strokeColor   : '#f59e0b',
                    strokeWeight  : 2,
                    strokeOpacity : 0.8,
                    map           : gmap,
                });
                allPolylines.push(line);
            }
        });

        // ONU markers + garis ke ODP
        data.onus.forEach(o => {
            if (!o.lat || !o.lng) return;
            var isUp  = o.status === 'Up';
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : gmap,
                title    : o.name,
                icon     : { url: isUp ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', scaledSize: new google.maps.Size(24,24) },
                zIndex   : 1,
            });
            marker.addListener('click', function() {
                infoWindow.setContent('<div style="font-family:Segoe UI,sans-serif;min-width:180px;"><b>📡 ONU: ' + o.name + '</b><br><small>MAC: ' + o.mac + '<br>Status: ' + o.status + '<br>Pelanggan: ' + (o.pelanggan || '-') + '</small></div>');
                infoWindow.open(gmap, marker);
            });
            if (o.odp_id && nodeMap[o.odp_id]) {
                var line = new google.maps.Polyline({
                    path          : [nodeMap[o.odp_id], { lat: parseFloat(o.lat), lng: parseFloat(o.lng) }],
                    strokeColor   : isUp ? '#10b981' : '#ef4444',
                    strokeWeight  : 1.5,
                    strokeOpacity : 0.6,
                    map           : gmap,
                });
                allPolylines.push(line);
            }
        });
    });
}

function focusOlt(lat, lng, name) {
    if (!lat || !lng) { toast('OLT belum punya koordinat!'); return; }
    gmap.panTo({ lat: parseFloat(lat), lng: parseFloat(lng) });
    gmap.setZoom(16);
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check);
            initMap();
        }
    }, 100);
});

function syncOnu(olt_id, e) {
    e.stopPropagation();
    toast('Sync ONU...');
    fetch(`/admin/topologi/sync-onu/${olt_id}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(d => {
        toast(d.success ? `✅ Sync berhasil: ${d.synced} ONU` : `❌ ${d.error}`);
        loadNodes();
    });
}

function syncAllOnu() {
    toast('Sync semua ONU...');
    document.querySelectorAll('.olt-card').forEach((el, i) => {
        const btn = el.querySelector('button');
        if (btn) btn.click();
    });
}

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}

loadNodes();
</script>
@endpush
