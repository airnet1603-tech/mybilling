@extends('layouts.admin')
@section('title', 'Detail OLT - '.$olt->name)

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=places" defer></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
#map { height: 400px; border-radius: 10px; }
.badge-up   { background:#d4edda; color:#155724; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.badge-down { background:#f8d7da; color:#721c24; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.odp-select { font-size:0.75rem; padding:2px 4px; border-radius:6px; border:1px solid #dee2e6; max-width:140px; }
.odp-select:focus { outline:none; border-color:#0d6efd; box-shadow:0 0 0 2px rgba(13,110,253,.15); }

.select2-container .select2-selection--single { height: 28px !important; font-size: 0.75rem !important; }
.select2-container .select2-selection__rendered { line-height: 26px !important; font-size: 0.75rem !important; }
.select2-dropdown { font-size: 0.75rem !important; }
.select2-search__field { font-size: 0.75rem !important; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h4 class="mb-0 fw-bold">{{ $olt->name }}</h4>
        <small class="text-muted">{{ $olt->ip_address }} &bull; {{ $olt->model }}</small>
    </div>
    <div class="ms-auto">
        <a href="/admin/topologi/peta?olt_id={{ $olt->id }}" class="btn btn-primary btn-sm">
            <i class="fas fa-map-marked-alt"></i> Lihat di Peta
        </a>
        <button class="btn btn-success btn-sm" onclick="syncOnu()">
            <i class="fas fa-sync"></i> Sync ONU
        </button>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold text-primary">{{ $onus->count() }}</div>
            <small class="text-muted">Total ONU</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold text-success">{{ $onus->where('status','Up')->count() }}</div>
            <small class="text-muted">ONU Online</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold text-danger">{{ $onus->where('status','Down')->count() }}</div>
            <small class="text-muted">ONU Offline</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-3">
            <div class="fs-2 fw-bold text-warning">{{ $odps->count() }}</div>
            <small class="text-muted">Total ODP</small>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white fw-semibold border-0">🗺️ Peta Lokasi</div>
            <div class="card-body p-2">
                <div id="map"></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white fw-semibold border-0 d-flex align-items-center justify-content-between">
                <span>📡 Daftar ONU</span>
                <div class="d-flex gap-2 align-items-center">
                    <input type="text" id="searchOnu" class="form-control form-control-sm" placeholder="Cari nama/MAC..." style="width:160px" oninput="filterOnu()">
                    <select id="filterStatus" class="form-select form-select-sm" style="width:110px" onchange="filterOnu()">
                        <option value="">Semua</option>
                        <option value="Up">Online</option>
                        <option value="Down">Offline</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div style="max-height:500px;overflow-y:auto;">
                <table class="table table-sm table-hover mb-0" id="onuTable">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>ODP</th>
                            <th>Pelanggan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($onus as $onu)
                    <tr data-status="{{ $onu->status }}" data-name="{{ strtolower($onu->name.$onu->mac_address) }}">
                        <td><small class="text-muted">{{ $onu->onu_id }}</small></td>
                        <td>
                            <div>{{ $onu->name ?? '-' }}</div>
                            <small class="text-muted" style="font-size:0.7rem;">{{ $onu->mac_address }}</small>
                        </td>
                        <td><span class="badge-{{ strtolower($onu->status) }}">{{ $onu->status }}</span></td>
                        <td>
                            <select class="odp-select" onchange="assignOdp({{ $onu->id }}, this.value)">
                                <option value="">-- ODP --</option>
                                @foreach($odps as $odp)
                                <option value="{{ $odp->id }}" {{ $onu->odp_id == $odp->id ? 'selected' : '' }}>
                                    {{ $odp->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td><button onclick="goToOnu({{ $onu->id }})" class="btn btn-sm" style="padding:1px 7px;font-size:0.75rem;background:#e8f4fd;border:1px solid #90cdf4;color:#2b6cb0;border-radius:6px;" title="Lihat di peta">📍</button></td>
                        <td><select class="pelanggan-select" data-onu-id="{{ $onu->id }}"><option value="">-- Pelanggan --</option>@foreach(\App\Models\Pelanggan::orderBy('nama')->get() as $p)<option value="{{ $p->id }}" {{ $onu->pelanggan_id == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>@endforeach</select></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada ONU. Klik Sync ONU.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast" style="display:none;position:fixed;bottom:20px;right:20px;z-index:9999;background:#333;color:#fff;padding:10px 18px;border-radius:10px;"></div>
@endsection

@push('scripts')
<script>
var onuMarkers = {};
var map = null;
var oltLat = {{ $olt->lat ?? -8.207019 }};
var oltLng = {{ $olt->lng ?? 112.019980 }};
var oltName = '{{ addslashes($olt->name) }}';
var oltIp = '{{ $olt->ip_address }}';
var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function filterOnu() {
    var search = document.getElementById('searchOnu').value.toLowerCase();
    var status = document.getElementById('filterStatus').value;
    document.querySelectorAll('#onuTable tbody tr').forEach(function(row) {
        var name = row.getAttribute('data-name') || '';
        var st   = row.getAttribute('data-status') || '';
        var show = (!search || name.includes(search)) && (!status || st === status);
        row.style.display = show ? '' : 'none';
    });
}

function assignOdp(onuId, odpId) {
    // Ambil pelanggan_id yang sudah ada di baris ini
    var row = document.querySelector('tr[data-onu-id="' + onuId + '"]') ||
              [...document.querySelectorAll('#onuTable tbody tr')].find(r => {
                  var sel = r.querySelector('.odp-select');
                  return sel && sel.getAttribute('onchange') && sel.getAttribute('onchange').includes('(' + onuId + ',');
              });
    var pelangganSel = row ? row.querySelector('.pelanggan-select') : null;
    var pelangganId  = pelangganSel ? (pelangganSel.val ? $(pelangganSel).val() : pelangganSel.value) : null;

    fetch('/admin/topologi/onu/' + onuId + '/assign-odp', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ odp_id: odpId || null, pelanggan_id: pelangganId || null })
    }).then(r => r.json()).then(d => {
        toast(d.success ? '✅ ODP berhasil disimpan!' : '❌ Gagal menyimpan');
    }).catch(() => toast('❌ Error koneksi'));
}

function makeMarkerIcon(color, icon, size) {
    size = size || 24;
    var h = Math.round(size * 1.4);
    var cx = size / 2;
    var cy = size * 0.42;
    var r  = size * 0.36;
    var s = size * 0.28;
    var shapes = {
        dot  : '<circle cx="'+cx+'" cy="'+cy+'" r="'+(r*0.4)+'" fill="#fff"/>',
        wifi : '<path d="M'+(cx-r*0.55)+','+(cy-r*0.1)+' Q'+cx+','+(cy-r*0.7)+' '+(cx+r*0.55)+','+(cy-r*0.1)+'" fill="none" stroke="#fff" stroke-width="'+(size*0.07)+'"/><path d="M'+(cx-r*0.35)+','+(cy+r*0.15)+' Q'+cx+','+(cy-r*0.2)+' '+(cx+r*0.35)+','+(cy+r*0.15)+'" fill="none" stroke="#fff" stroke-width="'+(size*0.07)+'"/><circle cx="'+cx+'" cy="'+(cy+r*0.4)+'" r="'+(r*0.15)+'" fill="#fff"/>',
        pin  : '<circle cx="'+cx+'" cy="'+(cy-r*0.15)+'" r="'+(r*0.45)+'" fill="#fff"/><line x1="'+cx+'" y1="'+(cy+r*0.3)+'" x2="'+cx+'" y2="'+(cy+r*0.6)+'" stroke="#fff" stroke-width="'+(size*0.1)+'"/>',
    };
    var inner = shapes[icon] || shapes["dot"];
    var svg = "<svg xmlns='http://www.w3.org/2000/svg' width='"+size+"' height='"+h+"' viewBox='0 0 "+size+" "+h+"'>" +
        "<circle cx='"+cx+"' cy='"+cy+"' r='"+r+"' fill='"+color+"' stroke='#fff' stroke-width='1.5'/>"+
        "<polygon points='"+(cx-r*0.35)+","+(cy+r*0.7)+" "+(cx+r*0.35)+","+(cy+r*0.7)+" "+cx+","+h+"' fill='"+color+"'/>"+
        inner + "</svg>";
    return {
        url: "data:image/svg+xml;charset=UTF-8," + encodeURIComponent(svg),
        scaledSize: new google.maps.Size(size, h),
        anchor: new google.maps.Point(cx, h),
    };
}

function initMap() {
    var center = { lat: oltLat, lng: oltLng };
    map = new google.maps.Map(document.getElementById('map'), {
        center: center, zoom: 14, mapTypeId: 'hybrid',
        gestureHandling: 'greedy', fullscreenControl: true,
    });
    @if($olt->lat && $olt->lng)
    var marker = new google.maps.Marker({
        position: center, map: map, title: oltName,
        icon: makeMarkerIcon('{{ $olt->olt_color ?? "#dc3545" }}', 'wifi', 28),
    });
    var infoWindow = new google.maps.InfoWindow({ content: '<b>'+oltName+'</b><br><small>'+oltIp+'</small>' });
    infoWindow.open(map, marker);
    marker.addListener('click', function() { infoWindow.open(map, marker); });
    @endif

    // Tampilkan ODP di peta
    @foreach($odps as $odp)
    @if($odp->lat && $odp->lng)
    (function() {
        var odpMarker = new google.maps.Marker({
            position: { lat: {{ $odp->lat }}, lng: {{ $odp->lng }} },
            map: map,
            title: '{{ addslashes($odp->name) }}',
            icon: makeMarkerIcon('#fd7e14', 'pin', 22),
        });
        var odpInfo = new google.maps.InfoWindow({
            content: '<b>{{ addslashes($odp->name) }}</b><br><small>ODP</small>'
        });
        odpMarker.addListener('click', function() { odpInfo.open(map, odpMarker); });
    })();
    @endif
    @endforeach

        // Marker ODC
        @foreach($odcs as $odc)
        @if($odc->lat && $odc->lng)
        (function() {
            var odcMarker = new google.maps.Marker({
                position: { lat: {{ $odc->lat }}, lng: {{ $odc->lng }} },
                map: map,
                title: '{{ addslashes($odc->name) }}',
                icon: makeMarkerIcon('{{ $olt->odc_color ?? "#6f42c1" }}', 'pin', 24),
                zIndex: 8,
            });
            var odcInfo = new google.maps.InfoWindow({
                content: '<b>🟣 {{ addslashes($odc->name) }}</b><br><small>Tipe: ODC</small><br><small>Kapasitas: {{ $odc->kapasitas }}</small>'
            });
            odcMarker.addListener('click', function() { odcInfo.open(map, odcMarker); });
        })();
        @endif
        @endforeach

        // Garis OLT ke ODC
        @foreach($odcs as $odc)
        @if($odc->lat && $odc->lng && $olt->lat && $olt->lng)
        new google.maps.Polyline({
            path: [{ lat: {{ $olt->lat }}, lng: {{ $olt->lng }} }, { lat: {{ $odc->lat }}, lng: {{ $odc->lng }} }],
            strokeColor: '{{ $olt->line_olt_odc ?? "#6f42c1" }}', strokeWeight: 2.5, strokeOpacity: 0.9, map: map,
        });
        @endif
        @endforeach

        // Garis ODC/ODP ke ODP
        @foreach($odps as $odp)
        @if($odp->lat && $odp->lng)
        @php
            // Prioritas: parent_odp_id > odc_id > olt
            $parentOdp = $odp->parent_odp_id ? $odps->firstWhere('id', $odp->parent_odp_id) : null;
            if ($parentOdp) {
                $parentLat = $parentOdp->lat;
                $parentLng = $parentOdp->lng;
                $lineColor = $olt->line_odp_odp ?? '#28a745';
            } elseif ($odp->odc_id) {
                $odc = $odcs->firstWhere('id', $odp->odc_id);
                $parentLat = $odc?->lat ?? null;
                $parentLng = $odc?->lng ?? null;
                $lineColor = $olt->line_odc_odp ?? '#fd7e14';
            } else {
                $parentLat = $olt->lat;
                $parentLng = $olt->lng;
                $lineColor = '#ffc107';
            }
        @endphp
        @if($parentLat && $parentLng)
        new google.maps.Polyline({
            path: [{ lat: {{ $parentLat }}, lng: {{ $parentLng }} }, { lat: {{ $odp->lat }}, lng: {{ $odp->lng }} }],
            strokeColor: '{{ $lineColor }}', strokeWeight: 1.8, strokeOpacity: 0.8, map: map,
        });
        @endif
        @endif
        @endforeach

        // Garis ODP ke ONU
        @foreach($odps as $odp)
        @if($odp->lat && $odp->lng)
        @foreach($onus->where('odp_id', $odp->id) as $onu)
        @php
            $lat = $onu->pelanggan?->latitude ?? $onu->odp?->lat ?? null;
            $lng = $onu->pelanggan?->longitude ?? $onu->odp?->lng ?? null;
        @endphp
        @if($lat && $lng)
        new google.maps.Polyline({
            path: [{ lat: {{ $odp->lat }}, lng: {{ $odp->lng }} }, { lat: {{ $lat }}, lng: {{ $lng }} }],
            strokeColor: '{{ $olt->line_odp_odp ?? "#28a745" }}', strokeWeight: 1.5, strokeOpacity: 0.8, map: map,
        });
        @endif
        @endforeach
        @endif
        @endforeach

    // Tampilkan ONU di peta
    @foreach($onus as $onu)
    @php
        $lat = $onu->pelanggan?->latitude ?? $onu->odp?->lat ?? null;
        $lng = $onu->pelanggan?->longitude ?? $onu->odp?->lng ?? null;
    @endphp
    @if($lat && $lng)
    (function() {
        var onuMarker = new google.maps.Marker({
            position: { lat: {{ $lat }}, lng: {{ $lng }} },
            map: map,
            title: '{{ addslashes($onu->name ?? $onu->onu_id) }}',
            icon: makeMarkerIcon('{{ $onu->status === "Up" ? "#28a745" : "#dc3545" }}', 'wifi', 18),
            zIndex: 1,
        });
        var onuInfo = new google.maps.InfoWindow({
            content: '<b>📡 {{ addslashes($onu->name ?? $onu->onu_id) }}</b><br>' +
                     '<small>Status: <b style="color:{{ $onu->status === "Up" ? "#28a745" : "#dc3545" }}">{{ $onu->status }}</b></small><br>' +
                     '<small>MAC: {{ $onu->mac_address }}</small><br>' +
                     '<small>Pelanggan: {{ addslashes($onu->pelanggan?->nama ?? "-") }}</small><br>' +
                     '<a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank" ' +
                     'style="display:block;margin-top:6px;text-align:center;background:#4285f4;color:white;' +
                     'padding:4px 8px;border-radius:6px;text-decoration:none;font-size:11px;">'
                     + '📍 Buka Google Maps</a>'
        });
        onuMarker.addListener('click', function() { onuInfo.open(map, onuMarker); });
        onuMarkers[{{ $onu->id }}] = { marker: onuMarker, info: onuInfo };
    })();
    @endif
    @endforeach
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check); initMap();
        }
    }, 100);
});

function syncOnu() {
    toast('Sync ONU dari OLT...');
    fetch('/admin/topologi/sync-onu/{{ $olt->id }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    }).then(r => r.json()).then(d => {
        toast(d.success ? '✅ Sync berhasil: '+d.synced+' ONU' : '❌ '+d.error);
        setTimeout(() => location.reload(), 2000);
    });
}

function goToOnu(onuId) {
    var o = onuMarkers[onuId];
    if (!o) {
        toast('ONU belum ada koordinat - assign pelanggan dulu & isi koordinat di edit pelanggan');
        var tbl = document.getElementById('onuTable');
        if (tbl) tbl.closest('.card').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    var pos = o.marker.getPosition();
    document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
    setTimeout(function() {
        map.panTo(pos);
        map.setZoom(18);
        o.info.open(map, o.marker);
    }, 400);
}

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}

    // Init Select2 untuk dropdown pelanggan
    $('.pelanggan-select').select2({ placeholder: '-- Pelanggan --', allowClear: true, width: '180px' });
    $(document).on('change', '.pelanggan-select', function() {
        assignPelanggan($(this).data('onu-id'), $(this).val());
    });

function assignPelanggan(onuId, pelangganId) {
    // Ambil odp_id yang sudah ada di baris ini
    var odpSel = [...document.querySelectorAll('.odp-select')].find(s => {
        return s.getAttribute('onchange') && s.getAttribute('onchange').includes('(' + onuId + ',');
    });
    var odpId = odpSel ? odpSel.value : null;

    fetch('/admin/topologi/onu/' + onuId + '/assign-odp', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({ odp_id: odpId || null, pelanggan_id: pelangganId || null })
    }).then(r => r.json()).then(d => {
        toast(d.success ? '✅ Pelanggan berhasil disimpan!' : '❌ Gagal menyimpan');
    }).catch(() => toast('❌ Error koneksi'));
}
</script>
@endpush
