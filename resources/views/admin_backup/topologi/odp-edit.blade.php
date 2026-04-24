@extends('layouts.admin')

@push('styles')
<style>
    #pickMap { height:380px; border-radius:12px; border:2px solid #dee2e6; }
    .coord-box { background:#f8f9fa; border-radius:10px; padding:12px 16px; border:1px solid #dee2e6; }
    .pin-hint { background:#fff3cd; color:#856404; border-radius:8px; padding:10px 14px; font-size:0.85rem; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><i class="fas fa-project-diagram me-2 text-warning"></i>Edit ODP: {{ $odp->name }}</h5>
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <form method="POST" action="/admin/topologi/odp/{{ $odp->id }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama ODP <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $odp->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">OLT Induk <span class="text-danger">*</span></label>
                        <select name="olt_id" id="olt_id" class="form-select" required onchange="filterAll(this.value)">
                            <option value="">-- Pilih OLT --</option>
                            @foreach($olts as $olt)
                            <option value="{{ $olt->id }}" {{ old('olt_id', $odp->olt_id) == $olt->id ? 'selected' : '' }}>
                                {{ $olt->name }} ({{ $olt->ip_address }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">SFP Port <small class="text-muted">(opsional)</small></label>
                        <select name="sfp_id" id="sfp_id" class="form-select">
                            <option value="">-- Pilih SFP --</option>
                            @foreach($sfps as $sfp)
                            <option value="{{ $sfp->id }}" data-olt="{{ $sfp->olt_id }}" {{ old('sfp_id', $odp->sfp_id) == $sfp->id ? 'selected' : '' }}>
                                {{ $sfp->olt->name }} - {{ $sfp->name }} ({{ $sfp->port ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ODC Induk <small class="text-muted">(opsional)</small></label>
                        <select name="odc_id" id="odc_id" class="form-select">
                            <option value="">-- Langsung ke OLT --</option>
                            @foreach($odcs as $odc)
                            <option value="{{ $odc->id }}" {{ old('odc_id', $odp->odc_id) == $odc->id ? 'selected' : '' }}>
                                {{ $odc->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas Port</label>
                        <input type="number" name="kapasitas" class="form-control" value="{{ old('kapasitas', $odp->kapasitas) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan', $odp->keterangan) }}">
                    </div>

                    <div class="coord-box mb-4">
                        <div class="fw-semibold small mb-2"><i class="fas fa-map-pin me-1 text-warning"></i>Koordinat Lokasi</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Latitude</label>
                                <input type="text" name="lat" id="inputLat" class="form-control form-control-sm" value="{{ old('lat', $odp->lat) }}" required readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Longitude</label>
                                <input type="text" name="lng" id="inputLng" class="form-control form-control-sm" value="{{ old('lng', $odp->lng) }}" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success flex-fill">
                            <i class="fas fa-save me-1"></i> Update ODP
                        </button>
                        <a href="/admin/topologi" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>

                <hr>
                <form method="POST" action="/admin/topologi/odp/{{ $odp->id }}" onsubmit="return confirm('Yakin hapus ODP {{ $odp->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="fas fa-trash me-1"></i> Hapus ODP Ini
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="pin-hint mb-2">
            <i class="fas fa-hand-pointer me-1"></i>
            <strong>Klik pada peta</strong> untuk mengubah lokasi ODP. Geser titik oranye ke posisi yang benar.
        </div>
        <div id="pickMap"></div>
        <div class="text-muted small mt-2"><i class="fas fa-info-circle me-1"></i>Bisa juga ketik nama lokasi di kotak pencarian di peta.</div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places&callback=initPickMap" async defer></script>
<script>
var pickMarker = null;
var currentLat = {{ $odp->lat ?? -8.207019 }};
var currentLng = {{ $odp->lng ?? 112.019980 }};

function initPickMap() {
    var map = new google.maps.Map(document.getElementById('pickMap'), {
        center: { lat: currentLat, lng: currentLng },
        zoom: 15, mapTypeId: 'hybrid', gestureHandling: 'greedy',
    });

    var input = document.createElement('input');
    input.type = 'text';
    input.placeholder = '🔍 Cari lokasi...';
    input.style.cssText = 'margin:10px;padding:8px 12px;width:250px;border-radius:8px;border:1px solid #ccc;font-size:13px;';
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
    var searchBox = new google.maps.places.SearchBox(input);
    searchBox.addListener('places_changed', function() {
        var places = searchBox.getPlaces();
        if (!places.length) return;
        var loc = places[0].geometry.location;
        map.setCenter(loc);
        map.setZoom(16);
        placeMarker(map, loc.lat(), loc.lng());
    });

    placeMarker(map, currentLat, currentLng);
    map.addListener('click', function(e) {
        placeMarker(map, e.latLng.lat(), e.latLng.lng());
    });
}

function placeMarker(map, lat, lng) {
    if (pickMarker) pickMarker.setMap(null);
    pickMarker = new google.maps.Marker({
        position: { lat: lat, lng: lng }, map: map,
        title: 'Lokasi ODP', draggable: true,
        icon: { url: 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png', scaledSize: new google.maps.Size(40, 40) },
        animation: google.maps.Animation.DROP,
    });
    document.getElementById('inputLat').value = lat.toFixed(8);
    document.getElementById('inputLng').value = lng.toFixed(8);
    pickMarker.addListener('dragend', function(e) {
        document.getElementById('inputLat').value = e.latLng.lat().toFixed(8);
        document.getElementById('inputLng').value = e.latLng.lng().toFixed(8);
    });
}

function filterSfp(oltId) {
    var select = document.getElementById('sfp_id');
    select.querySelectorAll('option').forEach(function(opt) {
        if (!opt.value) return;
        opt.style.display = (!oltId || opt.dataset.olt == oltId) ? '' : 'none';
    });
    var selected = select.options[select.selectedIndex];
    if (selected && selected.value && selected.dataset.olt != oltId) {
        select.value = '';
    }
}

function filterAll(oltId) {
    filterSfp(oltId);

    var odcSel  = document.getElementById('odc_id');
    var prevOdc = odcSel.value;

    odcSel.innerHTML = '<option value="">-- Langsung ke OLT --</option>';


    fetch('/admin/topologi/api/odc-by-olt/' + oltId)
        .then(function(r){ return r.json(); })
        .then(function(data){
            data.forEach(function(odc){
                var sel = (odc.id == prevOdc) ? ' selected' : '';
                odcSel.innerHTML += '<option value="' + odc.id + '">' + odc.name + '</option>';
            });
        });
}

document.addEventListener('DOMContentLoaded', function() {
    var oltId = document.getElementById('olt_id').value;
    if (oltId) filterSfp(oltId);
    // Pastikan ODC yang terpilih tetap tampil
});
</script>
@endpush
