@extends('layouts.admin')
@section('title', 'Edit OLT')

@push('head')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
<style>
#map-picker { height: 350px; border-radius: 10px; cursor: crosshair; }
.coord-box { background:#f8f9fa; border-radius:8px; padding:10px 14px; font-size:0.85rem; }
.color-option { width:26px;height:26px;border-radius:50%;cursor:pointer;border:3px solid transparent;display:inline-block;transition:0.2s;flex-shrink:0; }
.color-option.selected { border-color:#333;transform:scale(1.2); }
.icon-option { width:36px;height:36px;border-radius:8px;cursor:pointer;border:2px solid #dee2e6;display:inline-flex;align-items:center;justify-content:center;font-size:16px;transition:0.2s;background:#fff; }
.icon-option.selected { border-color:#0d6efd;background:#e8f0fe; }
.icon-option:hover { border-color:#0d6efd; }
.visual-section { border:1px solid #e9ecef;border-radius:10px;padding:14px;margin-bottom:12px; }
.visual-section-title { font-size:0.85rem;font-weight:600;margin-bottom:10px;display:flex;align-items:center;gap:6px; }
.preview-dot { width:20px;height:20px;border-radius:50%;display:inline-block;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.2); }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <div>
        <h4 class="mb-0 fw-bold">Edit OLT</h4>
        <small class="text-muted">{{ $olt->name }}</small>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <form action="/admin/topologi/olt/{{ $olt->id }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama OLT</label>
                        <input type="text" name="name" class="form-control" value="{{ $olt->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">IP Address</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ $olt->ip_address }}" required>
                        <small class="text-muted">Contoh: 157.15.67.51:8088</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Model / Tipe OLT</label>
                        <select name="model" class="form-select" required>
                            <option value="">-- Pilih Model --</option>
                            @foreach(['HisFocus 4P1GM'=>'HisFocus 4P1GM','HisFocus 8P2GM'=>'HisFocus 8P2GM','HiOSO'=>'HiOSO','HSGQ'=>'HSGQ','ZTE C300'=>'ZTE C300','ZTE C320'=>'ZTE C320','Huawei MA5608T'=>'Huawei MA5608T','Huawei MA5800'=>'Huawei MA5800','FiberHome'=>'FiberHome','Nokia'=>'Nokia'] as $val => $label)
                                <option value="{{ $val }}" {{ $olt->model == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ $olt->username }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="text" name="password" class="form-control" value="{{ $olt->password }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">SNMP Community</label>
                            <input type="text" name="snmp_community" class="form-control" value="{{ $olt->snmp_community ?? 'public' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">API Endpoint</label>
                            <input type="text" name="api_endpoint" class="form-control" value="{{ $olt->api_endpoint ?? '/onuAllPonOnuList.asp' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sync Interval (menit)</label>
                            <input type="number" name="sync_interval" class="form-control" value="{{ $olt->sync_interval ?? 60 }}" min="1">
                        </div>
                    </div>
                    <div class="mb-3" id="hsgq-key-field" style="{{ $olt->model == 'HSGQ' ? '' : 'display:none' }}">
                        <label class="form-label fw-semibold">HSGQ Key</label>
                        <div class="input-group">
                            <input type="text" name="hsgq_key" id="hsgq_key_input" class="form-control font-monospace" value="{{ $olt->hsgq_key }}" placeholder="contoh: 1761d487ba0cde5f285059b5cca9a22c">
                            <button type="button" class="btn btn-outline-primary" id="btn-fetch-key" onclick="fetchHsgqKey()">
                                <i class="fas fa-key"></i> Fetch Key Otomatis
                            </button>
                        </div>
                        <div id="fetch-key-result" class="mt-1"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi <small class="text-muted">(klik peta)</small></label>
                        <div class="coord-box mb-2 d-flex gap-3">
                            <span>📍 Lat: <b id="lat-display">{{ $olt->lat ?? '-' }}</b></span>
                            <span>Lng: <b id="lng-display">{{ $olt->lng ?? '-' }}</b></span>
                        </div>
                        <input type="hidden" name="lat" id="lat-input" value="{{ $olt->lat }}">
                        <input type="hidden" name="lng" id="lng-input" value="{{ $olt->lng }}">
                    </div>

                    {{-- ===== VISUAL SETTINGS ===== --}}
                    <hr>
                    <div class="d-flex align-items-center justify-content-between mb-2" style="cursor:pointer;" onclick="toggleVisual()" id="visual-toggle">
                        <div class="fw-semibold"><i class="fas fa-palette text-primary me-2"></i>Pengaturan Tampilan Peta</div>
                        <span id="visual-arrow" class="text-muted"><i class="fas fa-chevron-down"></i></span>
                    </div>
                    <div id="visual-settings" style="display:none;">

                    @php
                    $colorOptions = ['#dc3545','#fd7e14','#ffc107','#28a745','#17a2b8','#0d6efd','#6f42c1','#e83e8c','#20c997','#343a40','#795548','#607d8b','#ff5722','#fff'];
                    $iconOptions = ['dot'=>'⬤','circle_empty'=>'○','star'=>'★','square'=>'■','triangle'=>'▲','diamond'=>'◆','cross'=>'✚','pin'=>'📍','wifi'=>'📶','tower'=>'📡','home'=>'🏠','building'=>'🏢'];
                    $sections = [
                        ['key'=>'olt',  'label'=>'OLT',  'color_field'=>'olt_color',  'icon_field'=>'olt_icon',  'default_color'=>'#dc3545', 'emoji'=>'🔴'],
                        ['key'=>'odc',  'label'=>'ODC',  'color_field'=>'odc_color',  'icon_field'=>'odc_icon',  'default_color'=>'#6f42c1', 'emoji'=>'🟣'],
                        ['key'=>'odp',  'label'=>'ODP',  'color_field'=>'odp_color',  'icon_field'=>'odp_icon',  'default_color'=>'#fd7e14', 'emoji'=>'🟠'],
                    ];
                    @endphp

                    @foreach($sections as $sec)
                    @php
                        $curColor = old($sec['color_field'], $olt->{$sec['color_field']} ?? $sec['default_color']);
                        $curIcon  = old($sec['icon_field'],  $olt->{$sec['icon_field']}  ?? 'dot');
                    @endphp
                    <div class="visual-section">
                        <div class="visual-section-title">
                            {{ $sec['emoji'] }} {{ $sec['label'] }}
                            <span class="preview-dot ms-auto" id="preview-dot-{{ $sec['key'] }}" style="background:{{ $curColor }};"></span>
                        </div>
                        <input type="hidden" name="{{ $sec['color_field'] }}" id="color-{{ $sec['key'] }}" value="{{ $curColor }}">
                        <input type="hidden" name="{{ $sec['icon_field'] }}"  id="icon-{{ $sec['key'] }}"  value="{{ $curIcon }}">
                        <div class="d-flex gap-1 flex-wrap mb-2">
                            @foreach($colorOptions as $hex)
                            <div class="color-option {{ $curColor==$hex?'selected':'' }}"
                                style="background:{{ $hex }};{{ $hex=='#fff'?'border-color:#dee2e6;':'' }}"
                                onclick="selectColor('{{ $sec['key'] }}','{{ $hex }}',this)"></div>
                            @endforeach
                            <input type="color" value="{{ $curColor }}" class="form-control form-control-color" style="width:26px;height:26px;padding:1px;border-radius:50%;" oninput="selectColorCustom('{{ $sec['key'] }}',this.value)">
                        </div>
                        <div class="d-flex gap-1 flex-wrap">
                            @foreach($iconOptions as $ikey => $iemoji)
                            <div class="icon-option {{ $curIcon==$ikey?'selected':'' }}"
                                title="{{ $ikey }}"
                                onclick="selectIcon('{{ $sec['key'] }}','{{ $ikey }}',this)">{{ $iemoji }}</div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    {{-- Warna Garis --}}
                    <div class="visual-section">
                        <div class="visual-section-title">➖ Warna Garis Penghubung</div>
                        @php
                        $lines = [
                            ['field'=>'line_olt_odc','label'=>'OLT → ODC','default'=>'#6f42c1'],
                            ['field'=>'line_odc_odp','label'=>'ODC → ODP','default'=>'#fd7e14'],
                            ['field'=>'line_odp_odp','label'=>'ODP → ODP','default'=>'#28a745'],
                        ];
                        @endphp
                        @foreach($lines as $line)
                        @php $curLine = old($line['field'], $olt->{$line['field']} ?? $line['default']); @endphp
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <small class="fw-semibold" style="width:100px;">{{ $line['label'] }}</small>
                            <div class="d-flex gap-1 flex-wrap flex-fill">
                                @foreach($colorOptions as $hex)
                                <div class="color-option {{ $curLine==$hex?'selected':'' }}"
                                    style="background:{{ $hex }};{{ $hex=='#fff'?'border-color:#dee2e6;':'' }}"
                                    onclick="selectColor('line-{{ $loop->index }}-{{ $line['field'] }}','{{ $hex }}',this,'{{ $line['field'] }}')"></div>
                                @endforeach
                                <input type="color" value="{{ $curLine }}" class="form-control form-control-color" style="width:26px;height:26px;padding:1px;border-radius:50%;" oninput="selectColorLine('{{ $line['field'] }}',this.value)">
                            </div>
                            <input type="hidden" name="{{ $line['field'] }}" id="line-{{ $line['field'] }}" value="{{ $curLine }}">
                            <span class="preview-dot" id="preview-line-{{ $line['field'] }}" style="background:{{ $curLine }};border-radius:3px;height:6px;width:30px;"></span>
                        </div>
                        @endforeach
                    </div>

                    </div>{{-- end visual-settings --}}
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <button type="button" class="btn btn-danger" onclick="hapusOlt()">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </form>
                <form id="form-hapus" action="/admin/topologi/olt/{{ $olt->id }}" method="POST" style="display:none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-map-marker-alt text-danger"></i> Klik peta untuk ubah lokasi
            </div>
            <div class="card-body p-2">
                <div id="map-picker"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var oltLat = {{ $olt->lat ?? -8.207014 }};
var oltLng = {{ $olt->lng ?? 112.019986 }};
var gmap, gmarker;

function initMapPicker() {
    var center = { lat: oltLat, lng: oltLng };
    gmap = new google.maps.Map(document.getElementById('map-picker'), {
        center: center, zoom: 15, mapTypeId: 'hybrid',
        gestureHandling: 'greedy', fullscreenControl: true,
        streetViewControl: false, mapTypeControl: true,
    });
    gmarker = new google.maps.Marker({
        position: center, map: gmap,
        title: '{{ addslashes($olt->name) }}', draggable: true,
        icon: { url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png', scaledSize: new google.maps.Size(44,44) },
    });
    gmarker.addListener('dragend', function() {
        var pos = gmarker.getPosition();
        document.getElementById('lat-input').value = pos.lat().toFixed(8);
        document.getElementById('lng-input').value = pos.lng().toFixed(8);
        document.getElementById('lat-display').textContent = pos.lat().toFixed(6);
        document.getElementById('lng-display').textContent = pos.lng().toFixed(6);
    });
    gmap.addListener('click', function(e) {
        var pos = e.latLng;
        gmarker.setPosition(pos);
        document.getElementById('lat-input').value = pos.lat().toFixed(8);
        document.getElementById('lng-input').value = pos.lng().toFixed(8);
        document.getElementById('lat-display').textContent = pos.lat().toFixed(6);
        document.getElementById('lng-display').textContent = pos.lng().toFixed(6);
    });
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check); initMapPicker();
        }
    }, 100);
});

function selectColor(key, hex, el, fieldOverride) {
    var field = fieldOverride || key;
    document.getElementById('color-'+key) && (document.getElementById('color-'+key).value = hex);
    if (fieldOverride) document.getElementById('line-'+fieldOverride).value = hex;
    // update preview dot
    var dot = document.getElementById('preview-dot-'+key);
    if (dot) dot.style.background = hex;
    var lineDot = document.getElementById('preview-line-'+field);
    if (lineDot) lineDot.style.background = hex;
    // update selected state (hanya dalam parent yang sama)
    var parent = el.parentElement;
    parent.querySelectorAll('.color-option').forEach(function(e){ e.classList.remove('selected'); });
    el.classList.add('selected');
}

function selectColorCustom(key, hex) {
    document.getElementById('color-'+key).value = hex;
    var dot = document.getElementById('preview-dot-'+key);
    if (dot) dot.style.background = hex;
    var parent = document.getElementById('color-'+key).closest('.visual-section');
    if (parent) parent.querySelectorAll('.color-option').forEach(function(e){ e.classList.remove('selected'); });
}

function selectColorLine(field, hex) {
    document.getElementById('line-'+field).value = hex;
    var dot = document.getElementById('preview-line-'+field);
    if (dot) dot.style.background = hex;
}

function selectIcon(key, ikey, el) {
    document.getElementById('icon-'+key).value = ikey;
    var parent = el.parentElement;
    parent.querySelectorAll('.icon-option').forEach(function(e){ e.classList.remove('selected'); });
    el.classList.add('selected');
}

function fetchHsgqKey() {
    const btn = document.getElementById('btn-fetch-key');
    const result = document.getElementById('fetch-key-result');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Fetching...';
    fetch('/admin/topologi/olt/{{ $olt->id }}/fetch-hsgq-key', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({})
    }).then(r=>r.json()).then(data=>{
        if (data.success) {
            document.getElementById('hsgq_key_input').value = data.key;
            result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> '+data.message+'</span>';
        } else {
            result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> '+data.error+'</span>';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-key"></i> Fetch Key Otomatis';
    });
}

function toggleVisual() {
    var el = document.getElementById("visual-settings");
    var arrow = document.getElementById("visual-arrow");
    if (el.style.display === "none") {
        el.style.display = "block";
        arrow.innerHTML = '<i class="fas fa-chevron-up"></i>';
    } else {
        el.style.display = "none";
        arrow.innerHTML = '<i class="fas fa-chevron-down"></i>';
    }
}

function hapusOlt() {
    if (confirm('Yakin hapus OLT {{ $olt->name }}? Semua ONU akan ikut terhapus!')) {
        document.getElementById('form-hapus').submit();
    }
}
</script>
@endpush
