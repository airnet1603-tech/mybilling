@extends('layouts.admin')

@push('styles')
<style>
    #pickMap { height:380px; border-radius:12px; border:2px solid #dee2e6; }
    .coord-box { background:#f8f9fa; border-radius:10px; padding:12px 16px; border:1px solid #dee2e6; }
    .pin-hint { background:#e8d5f5; color:#6f42c1; border-radius:8px; padding:10px 14px; font-size:0.85rem; }
</style>
@endpush

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><i class="fas fa-sitemap me-2" style="color:#6f42c1;"></i>Tambah ODC Baru</h5>
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row g-4">
    <!-- FORM -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <form method="POST" action="/admin/topologi/odc/store">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama ODC <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="contoh: ODC-Pule-01" value="{{ old('name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">OLT Induk <span class="text-danger">*</span></label>
                        <select name="olt_id" id="olt_id" class="form-select" required onchange="filterSfp(this.value)">
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
                        <select name="sfp_id" id="sfp_id" class="form-select">
                            <option value="">-- Pilih SFP --</option>
                            @foreach($sfps as $sfp)
                            <option value="{{ $sfp->id }}" data-olt="{{ $sfp->olt_id }}" {{ old('sfp_id') == $sfp->id ? 'selected' : '' }}>
                                {{ $sfp->olt->name }} - {{ $sfp->name }} ({{ $sfp->port ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas Port</label>
                        <input type="number" name="kapasitas" class="form-control" placeholder="16" value="{{ old('kapasitas', 16) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Opsional" value="{{ old('keterangan') }}">
                    </div>

                    <div class="coord-box mb-4">
                        <div class="fw-semibold small mb-2"><i class="fas fa-map-pin me-1" style="color:#6f42c1;"></i>Koordinat Lokasi</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Latitude</label>
                                <input type="text" name="lat" id="inputLat" class="form-control form-control-sm" placeholder="Klik peta →" value="{{ old('lat') }}" required readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Longitude</label>
                                <input type="text" name="lng" id="inputLng" class="form-control form-control-sm" placeholder="Klik peta →" value="{{ old('lng') }}" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-save me-1"></i> Simpan ODC
                        </button>
                        <a href="/admin/topologi" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PETA PICKER -->
    <div class="col-md-7">
        <div class="pin-hint mb-2">
            <i class="fas fa-hand-pointer me-1"></i>
            <strong>Klik pada peta</strong> untuk menentukan lokasi ODC. Titik ungu akan muncul di lokasi yang dipilih.
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
    var defaultLat = {{ old('lat', -7.5) }};
    var defaultLng = {{ old('lng', 111.9) }};

    var map = new google.maps.Map(document.getElementById('pickMap'), {
        center          : { lat: defaultLat, lng: defaultLng },
        zoom            : 13,
        mapTypeId       : 'hybrid',
        gestureHandling : 'greedy',
    });

    var input   = document.createElement('input');
    input.type  = 'text';
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
        position : { lat: lat, lng: lng },
        map      : map,
        title    : 'Lokasi ODC',
        icon     : {
            path        : google.maps.SymbolPath.CIRCLE,
            scale       : 12,
            fillColor   : '#6f42c1',
            fillOpacity : 1,
            strokeColor : '#fff',
            strokeWeight: 2.5,
        },
        animation: google.maps.Animation.DROP,
    });
    document.getElementById('inputLat').value = lat.toFixed(8);
    document.getElementById('inputLng').value = lng.toFixed(8);
}

function filterSfp(oltId) {
    document.querySelectorAll('#sfp_id option[data-olt]').forEach(function(opt) {
        opt.style.display = (!oltId || opt.dataset.olt === oltId) ? '' : 'none';
    });
    document.getElementById('sfp_id').value = '';
}
</script>
@endpush
