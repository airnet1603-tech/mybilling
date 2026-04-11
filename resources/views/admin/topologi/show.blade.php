@extends('layouts.admin')
@section('title', 'Detail OLT - '.$olt->name)

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
<style>
#map { height: 400px; border-radius: 10px; }
.badge-up   { background:#d4edda; color:#155724; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
.badge-down { background:#f8d7da; color:#721c24; padding:2px 10px; border-radius:20px; font-size:0.75rem; }
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
            <div class="card-header bg-white fw-semibold border-0">📡 Daftar ONU</div>
            <div class="card-body p-0">
                <div style="max-height:400px;overflow-y:auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>ID</th><th>Nama</th><th>MAC</th><th>Status</th><th>Pelanggan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($onus as $onu)
                    <tr>
                        <td><small>{{ $onu->onu_id }}</small></td>
                        <td>{{ $onu->name ?? '-' }}</td>
                        <td><small class="text-muted">{{ $onu->mac_address }}</small></td>
                        <td><span class="badge-{{ strtolower($onu->status) }}">{{ $onu->status }}</span></td>
                        <td>{{ $onu->pelanggan?->nama ?? '-' }}</td>
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
var oltLat = {{ $olt->lat ?? -8.207019 }};
var oltLng = {{ $olt->lng ?? 112.019980 }};
var oltName = '{{ addslashes($olt->name) }}';
var oltIp = '{{ $olt->ip_address }}';

function initMap() {
    var center = { lat: oltLat, lng: oltLng };
    var map = new google.maps.Map(document.getElementById('map'), {
        center: center, zoom: 14, mapTypeId: 'hybrid',
        gestureHandling: 'greedy', fullscreenControl: true,
        streetViewControl: true, mapTypeControl: true,
    });
    @if($olt->lat && $olt->lng)
    var marker = new google.maps.Marker({
        position: center, map: map, title: oltName,
        icon: { url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', scaledSize: new google.maps.Size(44,44) },
    });
    var infoWindow = new google.maps.InfoWindow({
        content: '<b>' + oltName + '</b><br><small>' + oltIp + '</small>'
    });
    infoWindow.open(map, marker);
    marker.addListener('click', function() { infoWindow.open(map, marker); });
    @endif
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
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(d => {
        toast(d.success ? `✅ Sync berhasil: ${d.synced} ONU` : `❌ ${d.error}`);
        setTimeout(() => location.reload(), 2000);
    });
}

function toast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3000);
}
</script>
@endpush
