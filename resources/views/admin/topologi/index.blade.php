@extends('layouts.admin')
@section('title', 'Topologi OLT')

@push('head')
<style>
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
        <button class="btn btn-sm" style="background:#6f42c1;color:#fff;" onclick="togglePanel('odc')">
            <i class="fas fa-sitemap"></i> ODC
        </button>
        <button class="btn btn-warning btn-sm" onclick="togglePanel('odp')">
            <i class="fas fa-project-diagram"></i> ODP
        </button>
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
                <div class="card olt-card mb-2 p-2 position-relative" onclick="focusOlt({{ $olt->lat }}, {{ $olt->lng }}, '{{ $olt->name }}')">
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
                        <button onclick="toggleOdc({{ $olt->id }}, event)" class="btn btn-xs btn-outline-secondary" style="font-size:0.7rem;padding:1px 8px;background:#e8d5f5;border-color:#6f42c1;color:#6f42c1;">ODC</button>
                        <button onclick="toggleOdp({{ $olt->id }}, event)" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:1px 8px;">ODP</button>
                    </div>
                    {{-- Panel ODC per OLT --}}
                    <div id="odc-panel-{{ $olt->id }}" style="display:none;position:absolute;left:105%;top:0;width:280px;background:#f3eeff;border-radius:8px;padding:8px;border-left:3px solid #6f42c1;box-shadow:0 4px 15px rgba(0,0,0,0.15);z-index:999;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-bold" style="color:#6f42c1;"><i class="fas fa-sitemap me-1"></i>Daftar ODC</small>
                            <a href="/admin/topologi/odc/create" class="btn btn-xs" style="font-size:0.65rem;padding:1px 6px;background:#6f42c1;color:#fff;">+ Tambah</a>
                        </div>
                        <div id="odc-list-{{ $olt->id }}"></div>
                    </div>
                    {{-- Panel ODP per OLT --}}
                    <div id="odp-panel-{{ $olt->id }}" style="display:none;position:absolute;left:105%;top:0;width:280px;background:#fff8e1;border-radius:8px;padding:8px;border-left:3px solid #fd7e14;box-shadow:0 4px 15px rgba(0,0,0,0.15);z-index:999;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-bold" style="color:#fd7e14;"><i class="fas fa-project-diagram me-1"></i>Daftar ODP</small>
                            <a href="/admin/topologi/odp/create" class="btn btn-xs btn-warning" style="font-size:0.65rem;padding:1px 6px;">+ Tambah</a>
                        </div>
                        <div id="odp-list-{{ $olt->id }}"></div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-server fa-2x mb-2"></i><br>
                    Belum ada OLT.<br>
                    <a href="/admin/topologi/olt/create">Tambah OLT</a>
                </div>
                    {{-- Panel ODC per OLT --}}
                    <div id="odc-panel-{{ $olt->id }}" style="display:none;position:absolute;left:105%;top:0;width:280px;background:#f3eeff;border-radius:8px;padding:8px;border-left:3px solid #6f42c1;box-shadow:0 4px 15px rgba(0,0,0,0.15);z-index:999;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-bold" style="color:#6f42c1;"><i class="fas fa-sitemap me-1"></i>Daftar ODC</small>
                            <a href="/admin/topologi/odc/create" class="btn btn-xs" style="font-size:0.65rem;padding:1px 6px;background:#6f42c1;color:#fff;">+ Tambah</a>
                        </div>
                        <div id="odc-list-{{ $olt->id }}"></div>
                    </div>

                    {{-- Panel ODP per OLT --}}
                    <div id="odp-panel-{{ $olt->id }}" style="display:none;position:absolute;left:105%;top:0;width:280px;background:#fff8e1;border-radius:8px;padding:8px;border-left:3px solid #fd7e14;box-shadow:0 4px 15px rgba(0,0,0,0.15);z-index:999;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="fw-bold" style="color:#fd7e14;"><i class="fas fa-project-diagram me-1"></i>Daftar ODP</small>
                            <a href="/admin/topologi/odp/create" class="btn btn-xs btn-warning" style="font-size:0.65rem;padding:1px 6px;">+ Tambah</a>
                        </div>
                        <div id="odp-list-{{ $olt->id }}"></div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Daftar ODC --}}
    <div class="col-md-3" id="panel-odc" style="display:none !important;">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0">
                <i class="fas fa-sitemap" style="color:#6f42c1;"></i> Daftar ODC <a href="/admin/topologi/odc/create" class="btn btn-sm ms-2" style="background:#6f42c1;color:#fff;font-size:0.7rem;padding:1px 8px;"><i class="fas fa-plus"></i> Tambah</a>
            </div>
            <div class="card-body p-2" style="max-height:400px;overflow-y:auto;">
                @forelse($odcs as $odc)
                <div class="card mb-2 p-2" style="border-left:4px solid #6f42c1;">
                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $odc->name }}</div>
                    <small class="text-muted">OLT: {{ $odc->olt->name ?? '-' }}</small><br>
                    <small>Kapasitas: {{ $odc->kapasitas ?? '-' }}</small>
                    <div class="mt-1">
                        <a href="/admin/topologi/odc/{{ $odc->id }}/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:1px 8px;">Edit</a>
                        <form method="POST" action="/admin/topologi/odc/{{ $odc->id }}" style="display:inline;" onsubmit="return confirm('Hapus ODC {{ $odc->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.7rem;padding:1px 8px;">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-sitemap fa-2x mb-2"></i><br>Belum ada ODC.<br>
                    <a href="/admin/topologi/odc/create">Tambah ODC</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Daftar ODP --}}
    <div class="col-md-3" id="panel-odp" style="display:none !important;">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0">
                <i class="fas fa-project-diagram text-warning"></i> Daftar ODP <a href="/admin/topologi/odp/create" class="btn btn-warning btn-sm ms-2" style="font-size:0.7rem;padding:1px 8px;"><i class="fas fa-plus"></i> Tambah</a>
            </div>
            <div class="card-body p-2" style="max-height:400px;overflow-y:auto;">
                @forelse($odps as $odp)
                <div class="card mb-2 p-2" style="border-left:4px solid #fd7e14;">
                    <div class="fw-semibold" style="font-size:0.85rem;">{{ $odp->name }}</div>
                    <small class="text-muted">OLT: {{ $odp->olt->name ?? '-' }}</small><br>
                    <small>ODC: {{ $odp->odc->name ?? '-' }}</small>
                    <div class="mt-1">
                        <a href="/admin/topologi/odp/{{ $odp->id }}/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:1px 8px;">Edit</a>
                        <form method="POST" action="/admin/topologi/odp/{{ $odp->id }}" style="display:inline;" onsubmit="return confirm('Hapus ODP {{ $odp->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.7rem;padding:1px 8px;">Hapus</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-project-diagram fa-2x mb-2"></i><br>Belum ada ODP.<br>
                    <a href="/admin/topologi/odp/create">Tambah ODP</a>
                </div>
                @endforelse
            </div>
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
var odcData = {!! json_encode($odcs->map(fn($o) => ['id'=>$o->id,'name'=>$o->name,'olt_id'=>$o->olt_id,'kapasitas'=>$o->kapasitas,'lat'=>$o->lat,'lng'=>$o->lng])) !!};
var odpData = {!! json_encode($odps->map(fn($o) => ['id'=>$o->id,'name'=>$o->name,'olt_id'=>$o->olt_id,'odc_id'=>$o->odc_id,'parent_odp_id'=>$o->parent_odp_id,'kapasitas'=>$o->kapasitas,'lat'=>$o->lat,'lng'=>$o->lng])) !!};
var gmap = null;
var infoWindow = null;
var nodeMap = {};
var allPolylines = [];

function initMap() {
    gmap = new google.maps.Map(document.getElementById('map'), {
        center          : { lat: -8.207019, lng: 112.019980 },
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

function togglePanel(type) {
    var odc = document.getElementById('panel-odc');
    var odp = document.getElementById('panel-odp');
    if (type === 'odc') {
        odc.style.display = odc.style.display === 'none' ? 'block' : 'none';
        odp.style.display = 'none';
    } else {
        odp.style.display = odp.style.display === 'none' ? 'block' : 'none';
        odc.style.display = 'none';
    }
}


function toggleOdc(oltId, e) {
    e.stopPropagation();
    var panel = document.getElementById('odc-panel-' + oltId);
    var list  = document.getElementById('odc-list-' + oltId);
    var isOpen = panel.style.display !== 'none';
    document.querySelectorAll('[id^="odc-panel-"]').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="odp-panel-"]').forEach(p => p.style.display = 'none');
    if (isOpen) return;
    var odcs = odcData.filter(o => o.olt_id == oltId);
    var token = document.querySelector('meta[name="csrf-token"]').content;
    list.innerHTML = odcs.length ? odcs.map(o =>
        '<div style="background:#fff;border-radius:6px;padding:6px 8px;margin-bottom:4px;font-size:0.78rem;position:relative;">' +
        '<div class="d-flex justify-content-between align-items-start">' +
        '<b>' + o.name + '</b>' +
        '</div>' +
        '<small>Kapasitas: ' + (o.kapasitas||'-') + '</small><br>' +
        '<div style="margin-top:4px;display:flex;justify-content:space-between;align-items:center;">' +
        '<div>' +
        '<a href="/admin/topologi/odc/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a> ' +
        '<a href="/admin/topologi/peta?odc_id=' + o.id + '&olt_id=' + oltId + '" class="btn btn-xs btn-outline-primary" style="font-size:0.65rem;padding:1px 6px;">Detail</a> ' +
        '<form method="POST" action="/admin/topologi/odc/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODC ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
        '</div>' +
        '<button onclick="toggleOdpByOdc(' + o.id + ', ' + oltId + ', event)" style="font-size:0.65rem;padding:1px 8px;background:#fd7e14;color:#fff;border:none;border-radius:4px;cursor:pointer;">ODP</button>' +
        '</div>' +
        '<div id="odp-by-odc-' + o.id + '" style="display:none;position:absolute;left:105%;top:0;width:280px;background:#fff8e1;border-radius:8px;padding:8px;border-left:3px solid #fd7e14;box-shadow:0 4px 15px rgba(0,0,0,0.15);z-index:1000;">' +
        '<div class="d-flex justify-content-between align-items-center mb-1">' +
        '<small class="fw-bold" style="color:#fd7e14;"><i class=\"fas fa-project-diagram me-1\"></i>Daftar ODP</small>' +
        '<a href="/admin/topologi/odp/create?odc_id=' + o.id + '&olt_id=' + oltId + '" class="btn btn-xs btn-warning" style="font-size:0.65rem;padding:1px 6px;">+ Tambah</a>' +
        '</div>' +
        '<div id="odp-by-odc-list-' + o.id + '"></div>' +
        '</div>' +
        '</div>'
    ).join('') : '<small class="text-muted">Belum ada ODC untuk OLT ini.</small>';
    panel.style.display = 'block';
}

function toggleOdpByOdc(odcId, oltId, e) {
    e.stopPropagation();
    var panel = document.getElementById('odp-by-odc-' + odcId);
    var list  = document.getElementById('odp-by-odc-list-' + odcId);
    var isOpen = panel.style.display !== 'none';
    panel.style.display = isOpen ? 'none' : 'block';
    if (isOpen) return;
    // Ambil semua ODP dari ODC ini secara rekursif
    function getAllOdpByOdc(odcId) {
        var result = [];
        var queue = odpData.filter(o => o.odc_id == odcId).map(o => o.id);
        var visited = [];
        // Tambahkan ODP langsung dari ODC
        odpData.filter(o => o.odc_id == odcId).forEach(o => result.push(o));
        // Telusuri child ODP secara rekursif
        while (queue.length > 0) {
            var currentId = queue.shift();
            if (visited.includes(currentId)) continue;
            visited.push(currentId);
            var children = odpData.filter(o => o.parent_odp_id == currentId);
            children.forEach(function(o) {
                result.push(o);
                queue.push(o.id);
            });
        }
        return result;
    }
    var odps = getAllOdpByOdc(odcId);
    var token = document.querySelector('meta[name="csrf-token"]').content;
    list.innerHTML = odps.length ? odps.map(o =>
        '<div style="background:#fff;border-radius:4px;padding:4px 6px;margin-top:4px;font-size:0.75rem;">' +
        '<b>' + o.name + '</b><br>' +
        '<a href="/admin/topologi/odp/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.6rem;padding:1px 5px;">Edit</a> ' +
        '<a href="/admin/topologi/peta?odp_id=' + o.id + '&odc_id=' + odcId + '" class="btn btn-xs btn-outline-primary" style="font-size:0.6rem;padding:1px 5px;">Detail</a> ' +
        '<form method="POST" action="/admin/topologi/odp/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODP ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.6rem;padding:1px 5px;">Hapus</button></form>' +
        '</div>'
    ).join('') : '<small class="text-muted">Belum ada ODP untuk ODC ini.</small>';
}

function openMaps(lat, lng, e) {
    e.stopPropagation();
    if (!lat || !lng) { alert('Koordinat belum diset!'); return; }
    window.open('https://www.google.com/maps?q=' + lat + ',' + lng, '_blank');
}

function toggleOdp(oltId, e) {
    e.stopPropagation();
    var panel = document.getElementById('odp-panel-' + oltId);
    var list  = document.getElementById('odp-list-' + oltId);
    var isOpen = panel.style.display !== 'none';
    document.querySelectorAll('[id^="odc-panel-"]').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="odp-panel-"]').forEach(p => p.style.display = 'none');
    if (isOpen) return;
    var odps = odpData.filter(o => o.olt_id == oltId);
    var token = document.querySelector('meta[name="csrf-token"]').content;
    list.innerHTML = odps.length ? odps.map(o =>
        '<div style="background:#fff;border-radius:6px;padding:6px 8px;margin-bottom:4px;font-size:0.78rem;position:relative;">' +
        '<b>' + o.name + '</b><br>' +
        '<a href="/admin/topologi/odp/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a> ' +
        '<form method="POST" action="/admin/topologi/odp/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODP ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form></div>'
    ).join('') : '<small class="text-muted">Belum ada ODP untuk OLT ini.</small>';
    panel.style.display = 'block';
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
