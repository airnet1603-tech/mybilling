@extends('layouts.admin')

@push('styles')
<style>
    .router-status-card { background:linear-gradient(135deg,#1a1a2e,#0f3460); border-radius:12px; padding:16px 20px; color:white; margin-bottom:12px; }
    .live-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:#28a745; animation:blink 1.2s infinite; margin-right:6px; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.2} }
    .stat-mini { background:rgba(255,255,255,0.1); border-radius:10px; padding:10px 14px; text-align:center; }
    .speed-up { color:#28a745; font-weight:600; }
    .speed-down { color:#0d6efd; font-weight:600; }
    .search-box { max-width:260px; }
    .pagination-wrap .btn { min-width:34px; }
    @media (max-width:768px) {
        .search-box { max-width:100%; }
    }
</style>
@endpush

@section('content')

@php
    $routerId = request('router');
    $filteredRouters = $routerId ? $routers->where('id', $routerId) : $routers;
    $singleRouter = $routerId ? $filteredRouters->first() : null;
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">
            <span class="live-dot"></span> Monitoring Live
            @if($singleRouter) &mdash; {{ $singleRouter->nama }} @endif
        </h5>
        <small class="text-muted">
            @if($singleRouter) {{ $singleRouter->ip_address }}:{{ $singleRouter->port }}
            @else Status realtime semua router Mikrotik @endif
        </small>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-secondary" id="lastUpdate">–</span>
        <button class="btn btn-outline-primary btn-sm" onclick="refreshAll()">
            <i class="fas fa-sync-alt me-1"></i> Refresh
        </button>
        <a href="/admin/mikrotik" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

@forelse($filteredRouters as $router)
<div class="router-status-card" id="router-card-{{ $router->id }}">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <div class="fw-bold fs-6"><i class="fas fa-network-wired me-2"></i>{{ $router->nama }}</div>
            <div class="opacity-75 small"><code style="color:rgba(255,255,255,0.8);">{{ $router->ip_address }}:{{ $router->port }}</code></div>
        </div>
        <span class="badge" id="status-badge-{{ $router->id }}" style="background:rgba(255,255,255,0.2);">
            <i class="fas fa-spinner fa-spin fa-xs me-1"></i> Memuat...
        </span>
    </div>
    <div class="row g-2">
        <div class="col-6 col-md-3"><div class="stat-mini"><div class="opacity-75" style="font-size:0.68rem;">CPU Load</div><div class="fw-bold" id="cpu-{{ $router->id }}">–</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-mini"><div class="opacity-75" style="font-size:0.68rem;">Memory</div><div class="fw-bold" id="mem-{{ $router->id }}">–</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-mini"><div class="opacity-75" style="font-size:0.68rem;">Uptime</div><div class="fw-bold" style="font-size:0.8rem;" id="uptime-{{ $router->id }}">–</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-mini"><div class="opacity-75" style="font-size:0.68rem;">PPPoE Online</div><div class="fw-bold text-warning" id="pppoe-count-{{ $router->id }}">–</div></div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="fw-bold" style="font-size:0.88rem;">
                <i class="fas fa-users me-2 text-primary"></i>
                Session Aktif – {{ $router->nama }}
                <span class="badge bg-primary ms-1" id="session-count-{{ $router->id }}">0</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select id="perpage-{{ $router->id }}" class="form-select form-select-sm" style="width:75px;" onchange="onPerPageChange({{ $router->id }})">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                    <option value="1000">Semua</option>
                </select>
                <div class="input-group search-box">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted" style="font-size:0.8rem;"></i></span>
                    <input type="text"
                           id="search-{{ $router->id }}"
                           class="form-control form-control-sm border-start-0 ps-0"
                           placeholder="Cari username, IP, MAC..."
                           oninput="onSearch({{ $router->id }})"
                           style="font-size:0.82rem;">
                    <button class="btn btn-outline-secondary btn-sm" onclick="clearSearch({{ $router->id }})" title="Reset">
                        <i class="fas fa-times" style="font-size:0.75rem;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 small">#</th>
                        <th class="small">Username</th>
                        <th class="small">IP Address</th>
                        <th class="small">Uptime</th>
                        <th class="small">⬇ Download</th>
                        <th class="small">⬆ Upload</th>
                        <th class="small">Speed ⬇/⬆</th>
                        <th class="small">MAC Address</th>
                    </tr>
                </thead>
                <tbody id="sessions-table-{{ $router->id }}">
                    <tr><td colspan="8" class="text-center text-muted py-4 small"><i class="fas fa-spinner fa-spin me-2"></i>Memuat session...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center px-3 py-2 border-top bg-light" id="pagination-wrap-{{ $router->id }}" style="display:none!important;">
            <small class="text-muted" id="pagination-info-{{ $router->id }}"></small>
            <div class="d-flex gap-1 flex-wrap pagination-wrap" id="pagination-btns-{{ $router->id }}"></div>
        </div>
    </div>
</div>

@empty
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-network-wired fa-3x mb-3 opacity-25"></i>
        <h6>Router tidak ditemukan</h6>
        <a href="/admin/mikrotik" class="btn btn-primary btn-sm mt-2"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </div>
</div>
@endforelse

@endsection

@push('scripts')
<script>
const routerIds = [@foreach($filteredRouters as $router){{ $router->id }},@endforeach];
const state = {};
routerIds.forEach(id => {
    state[id] = { page: 1, search: '', perPage: 25, totalPages: 1, total: 0 };
});

function formatBytes(b) {
    if (!b || b == 0) return '0 B';
    const k = 1024, s = ['B','KB','MB','GB','TB'];
    const i = Math.floor(Math.log(b) / Math.log(k));
    return parseFloat((b / Math.pow(k, i)).toFixed(1)) + ' ' + s[i];
}
function formatSpeed(bps) {
    if (!bps || bps == 0) return '<span class="text-muted">0 bps</span>';
    const k = 1000, s = ['bps','Kbps','Mbps','Gbps'];
    const i = Math.floor(Math.log(bps) / Math.log(k));
    return parseFloat((bps / Math.pow(k, i)).toFixed(1)) + ' ' + s[i];
}

function loadRouterStats(id) {
    fetch('/admin/mikrotik/' + id + '/stats')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('status-badge-' + id);
            if (data.status) {
                badge.innerHTML = '<i class="fas fa-circle fa-xs me-1" style="color:#28a745;"></i> Online';
                badge.style.background = 'rgba(40,167,69,0.3)';
                document.getElementById('cpu-' + id).textContent    = (data.cpu ?? '–') + '%';
                document.getElementById('mem-' + id).textContent    = data.memory ?? '–';
                document.getElementById('uptime-' + id).textContent = data.uptime ?? '–';
                document.getElementById('pppoe-count-' + id).textContent = data.pppoe_count ?? '–';
            } else {
                badge.innerHTML = '<i class="fas fa-circle fa-xs me-1" style="color:#dc3545;"></i> Offline';
                badge.style.background = 'rgba(220,53,69,0.3)';
                ['cpu','mem','uptime','pppoe-count'].forEach(k =>
                    document.getElementById(k + '-' + id).textContent = '–');
            }
        }).catch(() => {
            const badge = document.getElementById('status-badge-' + id);
            badge.innerHTML = '<i class="fas fa-circle fa-xs me-1" style="color:#ffc107;"></i> Error';
            badge.style.background = 'rgba(255,193,7,0.3)';
        });
}

function loadSessions(id) {
    const { page, search, perPage } = state[id];
    const params = new URLSearchParams({ page, search, perPage });
    fetch('/admin/mikrotik/' + id + '/sessions?' + params)
        .then(r => r.json())
        .then(data => {
            const tbody   = document.getElementById('sessions-table-' + id);
            const countEl = document.getElementById('session-count-' + id);
            const pWrap   = document.getElementById('pagination-wrap-' + id);
            const pInfo   = document.getElementById('pagination-info-' + id);
            const pBtns   = document.getElementById('pagination-btns-' + id);
            if (!data.sessions || data.sessions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4 small">Tidak ada session aktif</td></tr>';
                countEl.textContent = '0';
                pWrap.style.display = 'none';
                return;
            }
            state[id].totalPages = data.total_pages;
            state[id].total      = data.total;
            countEl.textContent  = data.total;
            const offset = (data.current_page - 1) * data.per_page;
            tbody.innerHTML = data.sessions.map((s, idx) => `
                <tr>
                    <td class="ps-3 text-muted" style="font-size:0.75rem;">${offset + idx + 1}</td>
                    <td><code style="font-size:0.8rem;">${s.name ?? '-'}</code></td>
                    <td><small>${s.address ?? '-'}</small></td>
                    <td><small>${s.uptime ?? '-'}</small></td>
                    <td><small class="speed-down">${formatBytes(s.bytes_in ?? 0)}</small></td>
                    <td><small class="speed-up">${formatBytes(s.bytes_out ?? 0)}</small></td>
                    <td style="font-size:0.78rem;white-space:nowrap;">
                        <span class="speed-down">${formatSpeed(s.rate_in ?? 0)}</span>
                        <span class="text-muted mx-1">/</span>
                        <span class="speed-up">${formatSpeed(s.rate_out ?? 0)}</span>
                    </td>
                    <td><small class="text-muted">${s.mac_address ?? '-'}</small></td>
                </tr>
            `).join('');
            pWrap.style.display = '';
            const cur   = data.current_page;
            const total = data.total_pages;
            pInfo.textContent = `Menampilkan ${offset + 1}–${Math.min(offset + data.per_page, data.total)} dari ${data.total} user`;
            let btns = '';
            btns += `<button class="btn btn-sm ${cur <= 1 ? 'btn-outline-secondary disabled' : 'btn-outline-primary'}" onclick="goPage(${id}, ${cur - 1})"><i class="fas fa-chevron-left" style="font-size:0.7rem;"></i></button>`;
            let startP = Math.max(1, cur - 2);
            let endP   = Math.min(total, startP + 4);
            startP     = Math.max(1, endP - 4);
            if (startP > 1) btns += `<button class="btn btn-sm btn-outline-secondary" onclick="goPage(${id},1)">1</button>`;
            if (startP > 2) btns += `<span class="btn btn-sm btn-outline-secondary disabled">…</span>`;
            for (let p = startP; p <= endP; p++) {
                btns += `<button class="btn btn-sm ${p === cur ? 'btn-primary' : 'btn-outline-primary'}" onclick="goPage(${id}, ${p})">${p}</button>`;
            }
            if (endP < total - 1) btns += `<span class="btn btn-sm btn-outline-secondary disabled">…</span>`;
            if (endP < total)     btns += `<button class="btn btn-sm btn-outline-secondary" onclick="goPage(${id},${total})">${total}</button>`;
            btns += `<button class="btn btn-sm ${cur >= total ? 'btn-outline-secondary disabled' : 'btn-outline-primary'}" onclick="goPage(${id}, ${cur + 1})"><i class="fas fa-chevron-right" style="font-size:0.7rem;"></i></button>`;
            pBtns.innerHTML = btns;
        })
        .catch(() => {
            document.getElementById('sessions-table-' + id).innerHTML =
                '<tr><td colspan="8" class="text-center text-danger py-3 small"><i class="fas fa-exclamation-circle me-1"></i>Gagal memuat session</td></tr>';
        });
}

function goPage(id, page) {
    if (page < 1 || page > state[id].totalPages) return;
    state[id].page = page;
    loadSessions(id);
}

function onPerPageChange(id) {
    const sel = document.getElementById('perpage-' + id);
    state[id].perPage = parseInt(sel.value);
    state[id].page = 1;
    loadSessions(id);
}
let searchTimer = {};
function onSearch(id) {
    clearTimeout(searchTimer[id]);
    searchTimer[id] = setTimeout(() => {
        state[id].search = document.getElementById('search-' + id).value;
        state[id].page   = 1;
        loadSessions(id);
    }, 400);
}

function clearSearch(id) {
    document.getElementById('search-' + id).value = '';
    state[id].search = '';
    state[id].page   = 1;
    loadSessions(id);
}

function refreshAll() {
    routerIds.forEach(id => {
        loadRouterStats(id);
        loadSessions(id);
    });
    const now = new Date();
    document.getElementById('lastUpdate').textContent = 'Update: ' + now.toLocaleTimeString('id-ID');
}

refreshAll();
setInterval(refreshAll, 2000);
</script>
@endpush
