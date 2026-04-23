@extends('layouts.admin')

@push('styles')
<style>
    #pickMap { height:500px; border-radius:12px; border:2px solid #dee2e6; }
    .coord-box { background:#f8f9fa; border-radius:10px; padding:12px 16px; border:1px solid #dee2e6; }
    .pin-hint { background:#fff3cd; color:#856404; border-radius:8px; padding:10px 14px; font-size:0.85rem; }
</style>
@endpush

@section('content')

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
                        <select name="olt_id" id="olt_id" class="form-select" required onchange="filterAll(this.value)">
                            <option value="">-- Pilih OLT --</option>
                            @foreach($olts as $olt)
                            <option value="{{ $olt->id }}" {{ old('olt_id') == $olt->id ? 'selected' : '' }}>
                                {{ $olt->name }} ({{ $olt->ip_address }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">SFP Port <small class="text-muted">(opsional)</small></label>
                        <select name="sfp_id" id="sfp_id" class="form-select" onchange="filterOdpBySfp(this.value)">
                            <option value="">-- Pilih SFP --</option>
                            @foreach($sfps as $sfp)
                            <option value="{{ $sfp->id }}" data-olt="{{ $sfp->olt_id }}" {{ old('sfp_id') == $sfp->id ? 'selected' : '' }}>
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

@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places&callback=initPickMap" async defer></script>
<script>
var pickMarker = null;

function initPickMap() {
    var map = new google.maps.Map(document.getElementById('pickMap'), {
        center: { lat: {{ old('lat', -8.207019) }}, lng: {{ old('lng', 112.019980) }} },
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

    @if(old('lat') && old('lng'))
    placeMarker(map, {{ old('lat') }}, {{ old('lng') }});
    @endif

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

function filterAll(oltId) {
    filterSfp(oltId);
    var odcSel = document.getElementById('odc_id');
    var odpSel = document.getElementById('parent_odp_id');
    odcSel.innerHTML = '<option value="">-- Langsung ke OLT --</option>';
    odpSel.innerHTML = '<option value="">-- Tidak ada --</option>';
    if (oltId) {
        fetch('/admin/topologi/api/odc-by-olt/' + oltId).then(function(r){ return r.json(); }).then(function(data){ data.forEach(function(odc){ odcSel.innerHTML += '<option value="' + odc.id + '">' + odc.name + '</option>'; }); });
        fetch('/admin/topologi/api/odp-by-olt/' + oltId).then(function(r){ return r.json(); }).then(function(data){ data.forEach(function(odp){ odpSel.innerHTML += '<option value="' + odp.id + '">' + odp.name + '</option>'; }); });
    }
}

function filterOdpBySfp(sfpId) {
    var odpSel = document.getElementById('parent_odp_id');
    var odcSel = document.getElementById('odc_id');
    var oltId  = document.getElementById('olt_id').value;
    odpSel.innerHTML = '<option value="">-- Tidak ada --</option>';
    odcSel.innerHTML = '<option value="">-- Langsung ke OLT --</option>';
    if (sfpId) {
        fetch('/admin/topologi/api/odp-by-sfp/' + sfpId).then(function(r){ return r.json(); }).then(function(data){ data.forEach(function(odp){ odpSel.innerHTML += '<option value="' + odp.id + '">' + odp.name + '</option>'; }); });
        fetch('/admin/topologi/api/odc-by-sfp/' + sfpId).then(function(r){ return r.json(); }).then(function(data){ data.forEach(function(odc){ odcSel.innerHTML += '<option value="' + odc.id + '">' + odc.name + '</option>'; }); });
    } else if (oltId) {
        fetch('/admin/topologi/api/odp-by-olt/' + oltId).then(function(r){ return r.json(); }).then(function(data){ data.forEach(function(odp){ odpSel.innerHTML += '<option value="' + odp.id + '">' + odp.name + '</option>'; }); });
        fetch('/admin/topologi/api/odc-by-olt/' + oltId).then(function(r){ return r.json(); }).then(function(data){ data.forEach(function(odc){ odcSel.innerHTML += '<option value="' + odc.id + '">' + odc.name + '</option>'; }); });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var oltId = document.getElementById('olt_id').value;
    if (oltId) filterAll(oltId);
});

function filterSfp(oltId) {
    document.querySelectorAll('#sfp_id option[data-olt]').forEach(function(opt) {
        opt.style.display = (!oltId || opt.dataset.olt == oltId) ? '' : 'none';
    });
    document.getElementById('sfp_id').value = '';
}
</script>
@endpush
