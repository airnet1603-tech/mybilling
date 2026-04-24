@extends('layouts.admin')
@section('title', 'Topologi OLT')

@push('head')
<style>
.olt-card { cursor:pointer; transition:0.2s; border-left:4px solid #e94560; }
.olt-card:hover { transform:translateY(-2px); box-shadow:0 4px 15px rgba(0,0,0,0.1); }
.olt-card.active { background:#fff0f3; border-left:4px solid #e94560; }
.panel-col { display:none; }
.panel-col.show { display:block; }
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
        <a href="/admin/topologi/sfp/create" class="btn btn-sm btn-primary">
            <i class="fas fa-plug"></i> SFP
        </a>
        <a href="/admin/topologi/odc/create" class="btn btn-sm" style="background:#6f42c1;color:#fff;">
            <i class="fas fa-sitemap"></i> ODC
        </a>
        <a href="/admin/topologi/odp/create" class="btn btn-warning btn-sm">
            <i class="fas fa-project-diagram"></i> ODP
        </a>
    </div>
</div>

<div class="row g-3">
    {{-- Kolom OLT --}}
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0">
                <i class="fas fa-server text-danger"></i> Daftar OLT
            </div>
            <div class="card-body p-2">
                @forelse($olts as $olt)
                <div class="card olt-card mb-2 p-2" id="olt-card-{{ $olt->id }}">
                    <div class="fw-semibold">{{ $olt->name }}</div>
                    <small class="text-muted">{{ $olt->ip_address }}</small><br>
                    <small>🔵 ODP: {{ $olt->odps_count }} &nbsp; 📡 ONU: {{ $olt->onus_count }}</small>
                    <div class="mt-1 d-flex flex-wrap gap-1">
                        <a href="/admin/topologi/olt/{{ $olt->id }}" class="btn btn-xs btn-outline-primary" style="font-size:0.7rem;padding:1px 8px;">Detail</a>
                        <a href="/admin/topologi/olt/{{ $olt->id }}/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:1px 8px;">Edit</a>
                        <button onclick="syncOnu({{ $olt->id }}, event)" class="btn btn-xs btn-outline-success" style="font-size:0.7rem;padding:1px 8px;">Sync</button>
                        <button onclick="showSfp({{ $olt->id }}, event)" class="btn btn-xs btn-outline-primary" style="font-size:0.7rem;padding:1px 8px;">SFP</button>
                        <button onclick="showOdc({{ $olt->id }}, event)" class="btn btn-xs" style="font-size:0.7rem;padding:1px 8px;background:#e8d5f5;border:1px solid #6f42c1;color:#6f42c1;">ODC</button>
                        <button onclick="showOdp({{ $olt->id }}, event)" class="btn btn-xs btn-outline-warning" style="font-size:0.7rem;padding:1px 8px;">ODP</button>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-3">
                    <i class="fas fa-server fa-2x mb-2"></i><br>Belum ada OLT.<br>
                    <a href="/admin/topologi/olt/create">Tambah OLT</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Kolom SFP --}}
    <div class="col-md-3 panel-col" id="panel-sfp">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-plug text-primary"></i> Daftar SFP <small class="text-muted" id="sfp-olt-label"></small></span>
                <div class="d-flex gap-1 align-items-center">
                    <a href="/admin/topologi/sfp/create" class="btn btn-xs btn-primary" style="font-size:0.65rem;padding:1px 8px;">+ Tambah</a>
                    <button onclick="closePanel('sfp')" class="btn btn-xs btn-outline-secondary" style="font-size:0.65rem;padding:1px 6px;">✕</button>
                </div>
            </div>
            <div class="card-body p-2" style="max-height:75vh;overflow-y:auto;" id="sfp-list-main"></div>
        </div>
    </div>

    {{-- Kolom ODC --}}
    <div class="col-md-3 panel-col" id="panel-odc">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-sitemap" style="color:#6f42c1;"></i> Daftar ODC <small class="text-muted" id="odc-olt-label"></small></span>
                <div class="d-flex gap-1 align-items-center">
                    <a href="/admin/topologi/odc/create" class="btn btn-xs" style="font-size:0.65rem;padding:1px 8px;background:#6f42c1;color:#fff;">+ Tambah</a>
                    <button onclick="closePanel('odc')" class="btn btn-xs btn-outline-secondary" style="font-size:0.65rem;padding:1px 6px;">✕</button>
                </div>
            </div>
            <div class="card-body p-2" style="max-height:75vh;overflow-y:auto;" id="odc-list-main"></div>
        </div>
    </div>

    {{-- Kolom ODP --}}
    <div class="col-md-3 panel-col" id="panel-odp">
        <div class="card h-100">
            <div class="card-header fw-semibold bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-project-diagram text-warning"></i> Daftar ODP <small class="text-muted" id="odp-label"></small></span>
                <div class="d-flex gap-1 align-items-center">
                    <a href="/admin/topologi/odp/create" class="btn btn-xs btn-warning" style="font-size:0.65rem;padding:1px 8px;">+ Tambah</a>
                    <button onclick="closePanel('odp')" class="btn btn-xs btn-outline-secondary" style="font-size:0.65rem;padding:1px 6px;">✕</button>
                </div>
            </div>
            <div class="card-body p-2" style="max-height:75vh;overflow-y:auto;" id="odp-list-main"></div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast" style="display:none;position:fixed;bottom:20px;right:20px;z-index:9999;background:#333;color:#fff;padding:10px 18px;border-radius:10px;font-size:0.85rem;"></div>
@endsection

@push('scripts')
<script>
var odcData = {!! json_encode($odcs->map(fn($o) => ['id'=>$o->id,'name'=>$o->name,'olt_id'=>$o->olt_id,'sfp_id'=>$o->sfp_id,'kapasitas'=>$o->kapasitas,'lat'=>$o->lat,'lng'=>$o->lng])) !!};
var odpData = {!! json_encode($odps->map(fn($o) => ['id'=>$o->id,'name'=>$o->name,'olt_id'=>$o->olt_id,'sfp_id'=>$o->sfp_id,'odc_id'=>$o->odc_id,'parent_odp_id'=>$o->parent_odp_id,'kapasitas'=>$o->kapasitas,'lat'=>$o->lat,'lng'=>$o->lng])) !!};
var activeOltId = null;
var activeOdcId = null;

function setActiveCard(oltId) {
    document.querySelectorAll('.olt-card').forEach(c => c.classList.remove('active'));
    var card = document.getElementById('olt-card-' + oltId);
    if (card) card.classList.add('active');
}

function showSfp(oltId, e) {
    e.stopPropagation();
    var token = document.querySelector('meta[name="csrf-token"]').content;
    var sfpPanelOpen = document.getElementById('panel-sfp').classList.contains('show');
    if (activeOltId == oltId && sfpPanelOpen) {
        closePanel('sfp');
        return;
    }
    activeOltId = oltId;
    setActiveCard(oltId);
    var oltCard = document.getElementById('olt-card-' + oltId);
    var oltName = oltCard ? oltCard.querySelector('.fw-semibold').textContent : '';
    document.getElementById('sfp-olt-label').textContent = '— ' + oltName;

    // Sembunyikan panel lain
    document.getElementById('panel-odc').classList.remove('show');
    document.getElementById('panel-odp').classList.remove('show');
    document.getElementById('panel-sfp').classList.add('show');

    var list = document.getElementById('sfp-list-main');
    list.innerHTML = '<div class="text-center py-3"><small class="text-muted">Memuat...</small></div>';

    fetch('/admin/topologi/api/sfp-by-olt/' + oltId)
    .then(r => r.json())
    .then(function(sfps) {
        list.innerHTML = sfps.length ? sfps.map(s =>
            '<div class="card mb-2 p-2" style="border-left:3px solid #0d6efd;font-size:0.82rem;">' +
            '<div class="fw-semibold">' + s.name + '</div>' +
            '<small class="text-muted">Port: ' + (s.port||'-') + '</small><br>' +
            '<small class="text-muted">' + (s.keterangan||'') + '</small>' +
            '<div class="mt-1 d-flex gap-1 flex-wrap">' +
            '<a href="/admin/topologi/sfp/' + s.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a>' +
            '<button onclick="showOdcBySfp(' + s.id + ', ' + oltId + ', \'' + s.name + '\', event)" class="btn btn-xs" style="font-size:0.65rem;padding:1px 6px;background:#6f42c1;color:#fff;border:none;">ODC</button>' +
            '<button onclick="showOdpBySfp(' + s.id + ', ' + oltId + ', \'' + s.name + '\', event)" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">ODP</button>' +
            '<form method="POST" action="/admin/topologi/sfp/' + s.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus SFP \' + s.name + \'?\')">' +
            '<input type="hidden" name="_token" value="' + token + '">' +
            '<input type="hidden" name="_method" value="DELETE">' +
            '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
            '</div></div>'
        ).join('') : '<div class="text-center text-muted py-3"><small>Belum ada SFP untuk OLT ini.</small><br><a href="/admin/topologi/sfp/create" class="btn btn-sm btn-outline-primary mt-2" style="font-size:0.75rem;">+ Tambah SFP</a></div>';
    });
}

function showOdcBySfp(sfpId, oltId, sfpName, e) {
    e.stopPropagation();
    var token = document.querySelector('meta[name="csrf-token"]').content;
    document.getElementById('odc-olt-label').textContent = '— SFP: ' + sfpName;
    // FIX Bug4: variabel 'odcs' di atas tidak dipakai, dihapus
    var odcsBySfp = odcData.filter(o => o.sfp_id == sfpId);
    var list = document.getElementById('odc-list-main');
    list.innerHTML = odcsBySfp.length ? odcsBySfp.map(o =>
        '<div class="card mb-2 p-2" style="border-left:3px solid #6f42c1;font-size:0.82rem;">' +
        '<div class="fw-semibold">' + o.name + '</div>' +
        '<small class="text-muted">Kapasitas: ' + (o.kapasitas||'-') + '</small>' +
        '<div class="mt-1 d-flex gap-1 flex-wrap">' +
        '<a href="/admin/topologi/odc/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a>' +
        '<a href="/admin/topologi/peta?odc_id=' + o.id + '&olt_id=' + oltId + '" class="btn btn-xs btn-outline-primary" style="font-size:0.65rem;padding:1px 6px;">Detail</a>' +
        '<button onclick="showOdpByOdc(' + o.id + ', ' + oltId + ', \'' + o.name + '\', event)" class="btn btn-xs" style="font-size:0.65rem;padding:1px 6px;background:#fd7e14;color:#fff;border:none;">ODP</button>' +
        '<form method="POST" action="/admin/topologi/odc/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODC ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
        '</div></div>'
    ).join('') : '<div class="text-center text-muted py-3"><small>Belum ada ODC untuk SFP ini.</small><br><a href="/admin/topologi/odc/create" class="btn btn-sm btn-outline-secondary mt-2" style="font-size:0.75rem;">+ Tambah ODC</a></div>';
    document.getElementById('panel-odc').classList.add('show');
    document.getElementById('panel-odp').classList.remove('show');
}

function showOdpBySfp(sfpId, oltId, sfpName, e) {
    e.stopPropagation();
    var token = document.querySelector('meta[name="csrf-token"]').content;
    document.getElementById('odp-label').textContent = '— SFP: ' + sfpName;
    // Ambil ODC yang terhubung ke SFP ini
    var odcIds = odcData.filter(o => o.sfp_id == sfpId).map(o => o.id);
    // Tampilkan ODP yang sfp_id cocok ATAU odc_id-nya terhubung ke SFP ini
    var odps = odpData.filter(o => o.sfp_id == sfpId || odcIds.includes(o.odc_id));
    var list = document.getElementById('odp-list-main');
    list.innerHTML = odps.length ? odps.map(o =>
        '<div class="card mb-2 p-2" style="border-left:3px solid #fd7e14;font-size:0.82rem;">' +
        '<div class="fw-semibold">' + o.name + '</div>' +
        '<small class="text-muted">Kapasitas: ' + (o.kapasitas||'-') + '</small>' +
        '<div class="mt-1 d-flex gap-1 flex-wrap">' +
        '<a href="/admin/topologi/odp/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a>' +
        '<a href="/admin/topologi/peta?odp_id=' + o.id + '" class="btn btn-xs btn-outline-primary" style="font-size:0.65rem;padding:1px 6px;">Detail</a>' +
        '<form method="POST" action="/admin/topologi/odp/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODP ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
        '</div></div>'
    ).join('') : '<div class="text-center text-muted py-3"><small>Belum ada ODP untuk SFP ini.</small><br><a href="/admin/topologi/odp/create" class="btn btn-sm btn-outline-secondary mt-2" style="font-size:0.75rem;">+ Tambah ODP</a></div>';
    document.getElementById('panel-odp').classList.add('show');
    document.getElementById('panel-odc').classList.remove('show');
}

function closePanel(type) {
    document.getElementById('panel-' + type).classList.remove('show');
    if (type === 'sfp' || type === 'odc') {
        document.getElementById('panel-odc').classList.remove('show');
        document.getElementById('panel-odp').classList.remove('show');
        document.getElementById('panel-sfp').classList.remove('show');
        activeOltId = null;
        activeOdcId = null;
        document.querySelectorAll('.olt-card').forEach(c => c.classList.remove('active'));
    }
    if (type === 'odp') activeOdcId = null;
}

function showOdc(oltId, e) {
    e.stopPropagation();
    var token = document.querySelector('meta[name="csrf-token"]').content;
    // Toggle: kalau klik OLT yang sama, tutup
    if (activeOltId == oltId) {
        closePanel('odc');
        return;
    }
    activeOltId = oltId;
    activeOdcId = null;
    setActiveCard(oltId);

    // Update label
    var oltCard = document.getElementById('olt-card-' + oltId);
    var oltName = oltCard ? oltCard.querySelector('.fw-semibold').textContent : '';
    document.getElementById('odc-olt-label').textContent = '— ' + oltName;

    // Filter ODC by OLT
    var odcs = odcData.filter(o => o.olt_id == oltId);
    var list = document.getElementById('odc-list-main');

    list.innerHTML = odcs.length ? odcs.map(o =>
        '<div class="card mb-2 p-2" style="border-left:3px solid #6f42c1;font-size:0.82rem;">' +
        '<div class="fw-semibold">' + o.name + '</div>' +
        '<small class="text-muted">Kapasitas: ' + (o.kapasitas||'-') + '</small>' +
        '<div class="mt-1 d-flex gap-1 flex-wrap">' +
        '<a href="/admin/topologi/odc/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a>' +
        '<a href="/admin/topologi/peta?odc_id=' + o.id + '&olt_id=' + oltId + '" class="btn btn-xs btn-outline-primary" style="font-size:0.65rem;padding:1px 6px;">Detail</a>' +
        '<form method="POST" action="/admin/topologi/odc/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODC ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
        '<button onclick="showOdpByOdc(' + o.id + ', ' + oltId + ', \'' + o.name + '\', event)" class="btn btn-xs" style="font-size:0.65rem;padding:1px 6px;background:#fd7e14;color:#fff;border:none;">ODP</button>' +
        '</div></div>'
    ).join('') : '<div class="text-center text-muted py-3"><small>Belum ada ODC untuk OLT ini.</small><br><a href="/admin/topologi/odc/create" class="btn btn-sm btn-outline-secondary mt-2" style="font-size:0.75rem;">+ Tambah ODC</a></div>';

    document.getElementById('panel-odc').classList.add('show');
    document.getElementById('panel-odp').classList.remove('show');
}

function showOdp(oltId, e) {
    e.stopPropagation();
    var token = document.querySelector('meta[name="csrf-token"]').content;
    var odpPanelOpen = document.getElementById('panel-odp').classList.contains('show');
    var odcPanelOpen2 = document.getElementById('panel-odc').classList.contains('show');
    if (activeOltId == oltId && activeOdcId == null && odpPanelOpen && !odcPanelOpen2) {
        closePanel('odp');
        return;
    }
    activeOltId = oltId;
    activeOdcId = null;
    setActiveCard(oltId);

    var oltCard = document.getElementById('olt-card-' + oltId);
    var oltName = oltCard ? oltCard.querySelector('.fw-semibold').textContent : '';
    document.getElementById('odp-label').textContent = '— ' + oltName;

    var odps = odpData.filter(o => o.olt_id == oltId);
    var list = document.getElementById('odp-list-main');

    list.innerHTML = odps.length ? odps.map(o =>
        '<div class="card mb-2 p-2" style="border-left:3px solid #fd7e14;font-size:0.82rem;">' +
        '<div class="fw-semibold">' + o.name + '</div>' +
        '<small class="text-muted">Kapasitas: ' + (o.kapasitas||'-') + '</small>' +
        '<div class="mt-1 d-flex gap-1 flex-wrap">' +
        '<a href="/admin/topologi/odp/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a>' +
        '<a href="/admin/topologi/peta?odp_id=' + o.id + '" class="btn btn-xs btn-outline-primary" style="font-size:0.65rem;padding:1px 6px;">Detail</a>' +
        '<form method="POST" action="/admin/topologi/odp/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODP ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
        '</div></div>'
    ).join('') : '<div class="text-center text-muted py-3"><small>Belum ada ODP untuk OLT ini.</small><br><a href="/admin/topologi/odp/create" class="btn btn-sm btn-outline-secondary mt-2" style="font-size:0.75rem;">+ Tambah ODP</a></div>';

    document.getElementById('panel-odc').classList.remove('show');
    document.getElementById('panel-odp').classList.add('show');
}

function showOdpByOdc(odcId, oltId, odcName, e) {
    e.stopPropagation();
    var token = document.querySelector('meta[name="csrf-token"]').content;
    if (activeOdcId == odcId && document.getElementById('panel-odp').classList.contains('show')) {
        document.getElementById('panel-odp').classList.remove('show');
        activeOdcId = null;
        return;
    }
    activeOdcId = odcId;
    document.getElementById('odp-label').textContent = '— ' + odcName;

    function getAllOdpByOdc(odcId) {
        var result = [];
        var seen = [];
        var queue = odpData.filter(o => o.odc_id == odcId).map(o => o.id);
        odpData.filter(o => o.odc_id == odcId).forEach(o => {
            if (!seen.includes(o.id)) { seen.push(o.id); result.push(o); }
        });
        while (queue.length > 0) {
            var cid = queue.shift();
            odpData.filter(o => o.parent_odp_id == cid).forEach(o => {
                if (!seen.includes(o.id)) {
                    seen.push(o.id);
                    result.push(o);
                    queue.push(o.id);
                }
            });
        }
        return result;
    }

    var odps = getAllOdpByOdc(odcId);
    var list = document.getElementById('odp-list-main');

    list.innerHTML = odps.length ? odps.map(o =>
        '<div class="card mb-2 p-2" style="border-left:3px solid #fd7e14;font-size:0.82rem;">' +
        '<div class="fw-semibold">' + o.name + '</div>' +
        '<small class="text-muted">Kapasitas: ' + (o.kapasitas||'-') + '</small>' +
        '<div class="mt-1 d-flex gap-1 flex-wrap">' +
        '<a href="/admin/topologi/odp/' + o.id + '/edit" class="btn btn-xs btn-outline-warning" style="font-size:0.65rem;padding:1px 6px;">Edit</a>' +
        '<a href="/admin/topologi/peta?odp_id=' + o.id + '&odc_id=' + odcId + '" class="btn btn-xs btn-outline-primary" style="font-size:0.65rem;padding:1px 6px;">Detail</a>' +
        '<form method="POST" action="/admin/topologi/odp/' + o.id + '" style="display:inline;" onsubmit="return confirm(\'Hapus ODP ' + o.name + '?\')">' +
        '<input type="hidden" name="_token" value="' + token + '">' +
        '<input type="hidden" name="_method" value="DELETE">' +
        '<button type="submit" class="btn btn-xs btn-outline-danger" style="font-size:0.65rem;padding:1px 6px;">Hapus</button></form>' +
        '</div></div>'
    ).join('') : '<div class="text-center text-muted py-3"><small>Belum ada ODP untuk ODC ini.</small><br><a href="/admin/topologi/odp/create?odc_id=' + odcId + '" class="btn btn-sm btn-outline-secondary mt-2" style="font-size:0.75rem;">+ Tambah ODP</a></div>';

    document.getElementById('panel-odp').classList.add('show');
}

function syncOnu(olt_id, e) {
    e.stopPropagation();
    toast('Sync ONU...');
    fetch('/admin/topologi/sync-onu/' + olt_id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(r => r.json()).then(d => {
        toast(d.success ? '✅ Sync berhasil: ' + d.synced + ' ONU' : '❌ ' + d.error);
    });
}

function syncAllOnu() {
    toast('Sync semua ONU...');
    document.querySelectorAll('[id^="olt-card-"]').forEach(function(card) {
        var oltId = card.id.replace('olt-card-', '');
        syncOnu(oltId, { stopPropagation: function(){} });
    });
}

function toast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(function() { t.style.display = 'none'; }, 3000);
}
</script>
@endpush
