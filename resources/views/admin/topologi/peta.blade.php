<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Topologi - ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
    <style>
        :root { --sidebar-width:230px; --sidebar-bg-start:#1a1a2e; --sidebar-bg-end:#0f3460; --accent:#e94560; }
        * { box-sizing:border-box; }
        body { background:#f0f2f5; font-family:'Segoe UI',sans-serif; }
        .sidebar { background:linear-gradient(180deg,var(--sidebar-bg-start) 0%,var(--sidebar-bg-end) 100%); min-height:100vh; width:var(--sidebar-width); position:fixed; top:0; left:0; z-index:1050; display:flex; flex-direction:column; transition:transform 0.3s ease; }
        .sidebar-brand { padding:14px 16px; border-bottom:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; gap:10px; }
        .sidebar-brand .brand-icon { width:70px; height:40px; background:rgba(233,69,96,0.25); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--accent); font-size:1rem; flex-shrink:0; }
        .sidebar-brand .brand-title { color:#fff; font-weight:700; font-size:0.9rem; display:block; }
        .sidebar-brand .brand-sub { color:rgba(255,255,255,0.45); font-size:0.7rem; }
        .sidebar-nav { padding:8px 0; flex:1; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; transition:background 0.2s,color 0.2s; text-decoration:none; }
        .sidebar-nav .nav-link i { width:16px; font-size:0.82rem; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background:rgba(233,69,96,0.25); color:#fff; }
        .sidebar-divider { border-top:1px solid rgba(255,255,255,0.08); margin:6px 14px; }
        .main-content { margin-left:var(--sidebar-width); padding:20px 24px; }
        #petaMap { height:calc(100vh - 300px); border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .stat-card { border:none; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.07); transition:0.2s; }
        .dot { width:12px; height:12px; border-radius:50%; display:inline-block; margin-right:6px; }
        @media (max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .main-content { margin-left:0; padding:70px 14px 14px; }
            #petaMap { height:calc(100vh - 400px); }
        }
    </style>
</head>
<body>

@include('admin.partials.sidebar')

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0"><i class="fas fa-map-marked-alt me-2 text-danger"></i>Peta Topologi Jaringan</h5>
            <small class="text-muted">Sebaran OLT, ODC, ODP, dan ONU</small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <select id="filterOlt" class="form-select form-select-sm" style="width:180px;" onchange="filterByOlt()">
                <option value="">Semua OLT</option>
                @foreach($oltData as $olt)
                <option value="{{ $olt['id'] }}">{{ $olt['name'] }}</option>
                @endforeach
            </select>
            <a href="/admin/topologi" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#fde8ec;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-server text-danger"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="statOlt">{{ count($oltData) }}</div>
                        <div class="small text-muted">OLT Aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#e8d5f5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-sitemap" style="color:#6f42c1;"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="statOdc">{{ count($odcData) }}</div>
                        <div class="small text-muted">ODC</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#fff3cd;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-project-diagram text-warning"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="statOdp">{{ count($odpData) }}</div>
                        <div class="small text-muted">ODP</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#d4edda;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-wifi text-success"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="statOnuUp">{{ $oltData->sum('onu_up') }}</div>
                        <div class="small text-muted">ONU Online</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#f8d7da;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-wifi text-danger"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="statOnuDown">{{ $oltData->sum('onu_down') }}</div>
                        <div class="small text-muted">ONU Offline</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Layer -->
    <div class="card mb-3" style="border:none;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.07);">
        <div class="card-body py-2 px-3 d-flex gap-3 flex-wrap align-items-center">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="showOlt" checked onchange="toggleLayer('olt')">
                <label class="form-check-label small" for="showOlt"><span class="dot" style="background:#dc3545;"></span>OLT</label>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="showOdc" checked onchange="toggleLayer('odc')">
                <label class="form-check-label small" for="showOdc"><span class="dot" style="background:#6f42c1;"></span>ODC</label>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="showOdp" checked onchange="toggleLayer('odp')">
                <label class="form-check-label small" for="showOdp"><span class="dot" style="background:#fd7e14;"></span>ODP</label>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="showOnuUp" checked onchange="toggleLayer('onuUp')">
                <label class="form-check-label small" for="showOnuUp"><span class="dot" style="background:#28a745;"></span>ONU Online</label>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="showOnuDown" checked onchange="toggleLayer('onuDown')">
                <label class="form-check-label small" for="showOnuDown"><span class="dot" style="background:#dc3545;"></span>ONU Offline</label>
            </div>
            <span class="ms-auto small text-muted" id="pinCount">Memuat...</span>
        </div>
    </div>

    <!-- Peta -->
    <div id="petaMap"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
var oltData = {!! json_encode($oltData) !!};
var odcData = {!! json_encode($odcData) !!};
var odpData = {!! json_encode($odpData) !!};

var markers   = { olt: [], odc: [], odp: [], onuUp: [], onuDown: [] };
var polylines = [];
var nodeMap   = {};
var infoWindow, petaMap;
var activeOltId = null;
var urlParams  = new URLSearchParams(window.location.search);
var oltIdParam = urlParams.get('olt_id');
var odcIdParam = urlParams.get('odc_id');
var odpIdParam = urlParams.get('odp_id');

function initMap() {
    petaMap = new google.maps.Map(document.getElementById('petaMap'), {
        center           : { lat: -8.207019, lng: 112.019980 },
        zoom             : 12,
        mapTypeId        : 'hybrid',
        gestureHandling  : 'greedy',
        fullscreenControl: true,
        streetViewControl: true,
        mapTypeControl   : true,
    });
    infoWindow = new google.maps.InfoWindow();
    loadNodes();
}

function loadNodes() {
    fetch('/admin/topologi/api/nodes')
    .then(r => r.json())
    .then(function(data) {
        clearAll();

        // OLT
        data.olts.forEach(function(o) {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = { lat: parseFloat(o.lat), lng: parseFloat(o.lng) };
            var oltInfo = oltData.find(function(x) { return x.id == o.id.replace('olt-',''); });
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : petaMap,
                title    : o.name,
                icon     : { url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', scaledSize: new google.maps.Size(44,44) },
                zIndex   : 10,
                oltId    : o.id.replace('olt-',''),
            });
            marker.addListener('click', function() {
                var info = oltInfo || {};
                infoWindow.setContent(
                    '<div style="font-family:Segoe UI,sans-serif;min-width:200px;">' +
                    '<b style="font-size:14px;">🔴 ' + o.name + '</b><br>' +
                    '<small style="color:#666;">IP: ' + o.ip + '</small><br>' +
                    '<div style="margin-top:6px;display:flex;gap:8px;">' +
                    '<span style="background:#d4edda;color:#155724;padding:2px 8px;border-radius:10px;font-size:11px;">✅ Up: ' + (info.onu_up||0) + '</span>' +
                    '<span style="background:#f8d7da;color:#721c24;padding:2px 8px;border-radius:10px;font-size:11px;">❌ Down: ' + (info.onu_down||0) + '</span>' +
                    '</div>' +
                    '<div style="margin-top:4px;"><small>ODP: ' + (info.odp_count||0) + ' | Total ONU: ' + (info.onu_total||0) + '</small></div>' +
                    '<a href="/admin/topologi/olt/' + o.id.replace('olt-','') + '" style="display:block;margin-top:8px;text-align:center;background:#dc3545;color:white;padding:5px;border-radius:6px;text-decoration:none;font-size:12px;">Detail OLT</a>' +
                    '<a href="https://www.google.com/maps?q=' + o.lat + ',' + o.lng + '" target="_blank" style="display:block;margin-top:4px;text-align:center;background:#4285f4;color:white;padding:5px;border-radius:6px;text-decoration:none;font-size:12px;">📍 Buka di Google Maps</a>' +
                    '</div>'
                );
                infoWindow.open(petaMap, marker);
            });
            markers.olt.push(marker);
        });

        // ODC
        data.odcs.forEach(function(o) {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = { lat: parseFloat(o.lat), lng: parseFloat(o.lng) };
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : petaMap,
                title    : o.name,
                icon     : {
                    path        : google.maps.SymbolPath.CIRCLE,
                    scale       : 10,
                    fillColor   : '#6f42c1',
                    fillOpacity : 1,
                    strokeColor : '#fff',
                    strokeWeight: 2,
                },
                zIndex   : 7,
                oltId    : o.olt_id ? o.olt_id.replace('olt-','') : null,
                odcId    : o.id.replace('odc-',''),
            });
            marker.addListener('click', function() {
                infoWindow.setContent(
                    '<div style="font-family:Segoe UI,sans-serif;min-width:180px;">' +
                    '<b style="color:#6f42c1;">🟣 ' + o.name + '</b><br>' +
                    '<small>Tipe: ODC</small>' +
                    '<br><a href="https://www.google.com/maps?q=' + o.lat + ',' + o.lng + '" target="_blank" style="display:block;margin-top:8px;text-align:center;background:#4285f4;color:white;padding:5px;border-radius:6px;text-decoration:none;font-size:12px;">📍 Buka di Google Maps</a>' +
                    '</div>'
                );
                infoWindow.open(petaMap, marker);
            });
            markers.odc.push(marker);
            // Garis OLT → ODC (ungu)
            var oltPos = nodeMap[o.olt_id];
            if (oltPos) {
                var line = new google.maps.Polyline({
                    path: [oltPos, { lat: parseFloat(o.lat), lng: parseFloat(o.lng) }],
                    strokeColor: '#6f42c1', strokeWeight: 2.5, strokeOpacity: 0.9, map: petaMap,
                });
                polylines.push({ line: line, oltId: o.olt_id ? o.olt_id.replace('olt-','') : null });
            }
        });

        // ODP
        data.odps.forEach(function(o) {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = { lat: parseFloat(o.lat), lng: parseFloat(o.lng) };
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : petaMap,
                title    : o.name,
                icon     : { url: 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png', scaledSize: new google.maps.Size(34,34) },
                zIndex   : 5,
                oltId    : o.olt_id ? o.olt_id.replace('olt-','') : null,
                odpId    : o.id.replace('odp-',''),
            });
            marker.addListener('click', function() {
                infoWindow.setContent(
                    '<div style="font-family:Segoe UI,sans-serif;min-width:180px;">' +
                    '<b>📦 ' + o.name + '</b><br>' +
                    '<small>Tipe: ODP</small><br>' +
                    '<small>Kapasitas: ' + (o.kapasitas||'-') + '</small>' +
                    (o.keterangan ? '<br><small>' + o.keterangan + '</small>' : '') +
                    '<br><a href="https://www.google.com/maps?q=' + o.lat + ',' + o.lng + '" target="_blank" style="display:block;margin-top:8px;text-align:center;background:#4285f4;color:white;padding:5px;border-radius:6px;text-decoration:none;font-size:12px;">📍 Buka di Google Maps</a>' +
                    '</div>'
                );
                infoWindow.open(petaMap, marker);
            });
            markers.odp.push(marker);
            // Garis: parent_odp → ODP (hijau) atau ODC → ODP (oranye) atau OLT → ODP (kuning)
            var parentKey = o.parent_odp_id ? o.parent_odp_id : (o.odc_id ? o.odc_id : o.olt_id);
            var parentPos = nodeMap[parentKey];
            if (parentPos) {
                var lineColor = o.parent_odp_id ? '#28a745' : (o.odc_id ? '#fd7e14' : '#ffc107');
                var line = new google.maps.Polyline({
                    path: [parentPos, { lat: parseFloat(o.lat), lng: parseFloat(o.lng) }],
                    strokeColor: lineColor, strokeWeight: 1.8, strokeOpacity: 0.8, map: petaMap,
                });
                polylines.push({ line: line, oltId: o.olt_id ? o.olt_id.replace('olt-','') : null });
            }
        });

        // ONU
        data.onus.forEach(function(o) {
            if (!o.lat || !o.lng) return;
            var isUp = o.status === 'Up';
            var marker = new google.maps.Marker({
                position : { lat: parseFloat(o.lat), lng: parseFloat(o.lng) },
                map      : petaMap,
                title    : o.name,
                icon     : {
                    url        : isUp ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize : new google.maps.Size(26,26),
                },
                zIndex   : 1,
                oltId    : o.olt_id,
            });
            marker.addListener('click', function() {
                infoWindow.setContent(
                    '<div style="font-family:Segoe UI,sans-serif;min-width:180px;">' +
                    '<b>📡 ' + o.name + '</b><br>' +
                    '<small>MAC: ' + o.mac + '</small><br>' +
                    '<small>Status: <b style="color:' + (isUp?'#28a745':'#dc3545') + '">' + o.status + '</b></small><br>' +
                    '<small>Pelanggan: ' + (o.pelanggan||'-') + '</small>' +
                    '</div>'
                );
                infoWindow.open(petaMap, marker);
            });
            if (isUp) markers.onuUp.push(marker);
            else markers.onuDown.push(marker);
        });

        updatePinCount();
        fitBounds();
        // Auto-filter dan tampilkan layer sesuai parameter URL
        setTimeout(function() {
        if (odpIdParam) {
            // Detail ODP: tampilkan OLT, ODC, ODP saja - sembunyikan ONU
            document.getElementById('showOnuUp').checked   = false;
            document.getElementById('showOnuDown').checked = false;
            toggleLayer('onuUp');
            toggleLayer('onuDown');
            // Zoom ke ODP
            var odpMarker = markers.odp.find(function(m) { return String(m.odpId) === String(odpIdParam); });
            if (odpMarker) {
                petaMap.panTo(odpMarker.getPosition());
                petaMap.setZoom(16);
                google.maps.event.trigger(odpMarker, 'click');
            }
        } else if (odcIdParam) {
            // Detail ODC: tampilkan OLT dan ODC saja - sembunyikan ODP dan ONU
            document.getElementById('showOdp').checked    = false;
            document.getElementById('showOnuUp').checked  = false;
            document.getElementById('showOnuDown').checked = false;
            toggleLayer('odp');
            toggleLayer('onuUp');
            toggleLayer('onuDown');
            // Zoom ke ODC
            var odcMarker = markers.odc.find(function(m) { return String(m.odcId) === String(odcIdParam); });
            if (odcMarker) {
                petaMap.panTo(odcMarker.getPosition());
                petaMap.setZoom(16);
                google.maps.event.trigger(odcMarker, 'click');
            }
        } else if (oltIdParam) {
            document.getElementById('filterOlt').value = oltIdParam;
            filterByOlt();
        }
        }, 1500);
    });
}

function clearAll() {
    ['olt','odc','odp','onuUp','onuDown'].forEach(function(k) {
        markers[k].forEach(function(m) { m.setMap(null); });
        markers[k] = [];
    });
    polylines.forEach(function(p) { p.line.setMap(null); });
    polylines = [];
    nodeMap = {};
}

function filterByOlt() {
    activeOltId = document.getElementById('filterOlt').value;
    if (!activeOltId) {
        document.getElementById('statOlt').textContent     = oltData.length;
        document.getElementById('statOdc').textContent     = odcData.length;
        document.getElementById('statOdp').textContent     = odpData.length;
        document.getElementById('statOnuUp').textContent   = oltData.reduce(function(s,o){ return s+(o.onu_up||0); }, 0);
        document.getElementById('statOnuDown').textContent = oltData.reduce(function(s,o){ return s+(o.onu_down||0); }, 0);
    } else {
        var olt = oltData.find(function(o){ return o.id == activeOltId; });
        if (olt) {
            document.getElementById('statOlt').textContent     = 1;
            document.getElementById('statOdc').textContent     = odcData.filter(function(o){ return o.olt_id == activeOltId; }).length;
            document.getElementById('statOdp').textContent     = odpData.filter(function(o){ return o.olt_id == activeOltId; }).length;
            document.getElementById('statOnuUp').textContent   = olt.onu_up||0;
            document.getElementById('statOnuDown').textContent = olt.onu_down||0;
        }
    }
    ['olt','odc','odp','onuUp','onuDown'].forEach(function(k) {
        markers[k].forEach(function(m) {
            var show = !activeOltId || String(m.oltId) === String(activeOltId);
            m.setVisible(show && getLayerVisible(k));
        });
    });
    polylines.forEach(function(p) {
        var show = !activeOltId || String(p.oltId) === String(activeOltId);
        p.line.setVisible(show);
    });
    updatePinCount();
    if (activeOltId && nodeMap['olt-'+activeOltId]) {
        petaMap.panTo(nodeMap['olt-'+activeOltId]);
        petaMap.setZoom(14);
    } else {
        fitBounds();
    }
}

function getLayerVisible(k) {
    var ids = { olt:'showOlt', odc:'showOdc', odp:'showOdp', onuUp:'showOnuUp', onuDown:'showOnuDown' };
    return document.getElementById(ids[k]).checked;
}

function toggleLayer(layer) {
    var visible = getLayerVisible(layer);
    markers[layer].forEach(function(m) {
        var show = !activeOltId || String(m.oltId) === String(activeOltId);
        m.setVisible(visible && show);
    });
    updatePinCount();
}

function updatePinCount() {
    var count = 0;
    ['olt','odc','odp','onuUp','onuDown'].forEach(function(k) {
        markers[k].forEach(function(m) { if (m.getVisible()) count++; });
    });
    document.getElementById('pinCount').textContent = count + ' pin ditampilkan';
}

function fitBounds() {
    var allPos = [];
    ['olt','odc','odp','onuUp','onuDown'].forEach(function(k) {
        markers[k].forEach(function(m) { if (m.getVisible()) allPos.push(m.getPosition()); });
    });
    if (allPos.length > 0) {
        var bounds = new google.maps.LatLngBounds();
        allPos.forEach(function(p) { bounds.extend(p); });
        petaMap.fitBounds(bounds);
    }
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
</body>
</html>
