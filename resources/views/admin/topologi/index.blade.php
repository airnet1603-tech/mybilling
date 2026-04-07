@extends('layouts.admin')
@section('title', 'Topologi OLT')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
#map { height: calc(100vh - 140px); border-radius: 12px; }
.olt-card { cursor: pointer; transition: 0.2s; border-left: 4px solid #e94560; }
.olt-card:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
.badge-up   { background:#d4edda; color:#155724; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.badge-down { background:#f8d7da; color:#721c24; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.legend { position:absolute; bottom:30px; left:50px; z-index:999; background:#fff; padding:10px 14px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.15); font-size:0.8rem; }
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
                <div class="legend">
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([-7.5, 111.9], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

const icons = {
    OLT: L.divIcon({ html: '<div style="background:#e94560;width:16px;height:16px;border-radius:50%;border:3px solid #fff;box-shadow:0 0 6px rgba(0,0,0,0.4)"></div>', className:'', iconAnchor:[8,8] }),
    ODP: L.divIcon({ html: '<div style="background:#f59e0b;width:13px;height:13px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,0.3)"></div>', className:'', iconAnchor:[6,6] }),
    ONT_UP:   L.divIcon({ html: '<div style="background:#10b981;width:10px;height:10px;border-radius:50%;border:2px solid #fff"></div>', className:'', iconAnchor:[5,5] }),
    ONT_DOWN: L.divIcon({ html: '<div style="background:#ef4444;width:10px;height:10px;border-radius:50%;border:2px solid #fff"></div>', className:'', iconAnchor:[5,5] }),
};

const markersLayer = L.layerGroup().addTo(map);
const linesLayer   = L.layerGroup().addTo(map);
const nodeMap      = {};

function loadNodes() {
    fetch('/admin/topologi/api/nodes')
    .then(r => r.json())
    .then(data => {
        markersLayer.clearLayers();
        linesLayer.clearLayers();

        // OLT markers
        data.olts.forEach(o => {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = [o.lat, o.lng];
            L.marker([o.lat, o.lng], {icon: icons.OLT})
             .bindPopup(`<b>🔴 OLT: ${o.name}</b><br>IP: ${o.ip}`)
             .addTo(markersLayer);
        });

        // ODP markers + garis ke OLT
        data.odps.forEach(o => {
            if (!o.lat || !o.lng) return;
            nodeMap[o.id] = [o.lat, o.lng];
            L.marker([o.lat, o.lng], {icon: icons.ODP})
             .bindPopup(`<b>🟡 ${o.type}: ${o.name}</b>`)
             .addTo(markersLayer);
            if (nodeMap[o.olt_id]) {
                L.polyline([nodeMap[o.olt_id], [o.lat, o.lng]], {color:'#f59e0b', weight:2, opacity:0.7}).addTo(linesLayer);
            }
        });

        // ONT markers + garis ke ODP
        data.onus.forEach(o => {
            if (!o.lat || !o.lng) return;
            const icon = o.status === 'Up' ? icons.ONT_UP : icons.ONT_DOWN;
            L.marker([o.lat, o.lng], {icon})
             .bindPopup(`<b>📡 ONU: ${o.name}</b><br>MAC: ${o.mac}<br>Status: ${o.status}<br>Pelanggan: ${o.pelanggan ?? '-'}`)
             .addTo(markersLayer);
            if (o.odp_id && nodeMap[o.odp_id]) {
                L.polyline([nodeMap[o.odp_id], [o.lat, o.lng]], {color: o.status==='Up'?'#10b981':'#ef4444', weight:1.5, opacity:0.6}).addTo(linesLayer);
            }
        });
    });
}

function focusOlt(lat, lng, name) {
    if (!lat || !lng) { toast('OLT belum punya koordinat!'); return; }
    map.flyTo([lat, lng], 15);
}

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
