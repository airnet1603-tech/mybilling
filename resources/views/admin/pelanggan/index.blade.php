<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pelanggan – ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 230px;
            --sidebar-bg-start: #1a1a2e;
            --sidebar-bg-end: #0f3460;
            --accent: #e94560;
        }

        * { box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%);
            min-height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            top: 0; left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }
        .sidebar-brand { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .brand-icon { width: 70px; height: 40px; background: rgba(233,69,96,0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; }
        .sidebar-brand .brand-title { color: #fff; font-weight: 700; font-size: 0.9rem; display: block; }
        .sidebar-brand .brand-sub { color: rgba(255,255,255,0.45); font-size: 0.7rem; }
        .sidebar-nav { padding: 8px 0; flex: 1; }
        .sidebar-nav .nav-link { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; transition: background 0.2s, color 0.2s; white-space: nowrap; }
        .sidebar-nav .nav-link i { width: 16px; font-size: 0.82rem; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.25); color: #fff; }
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.35); }
        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 14px; }
        .sidebar-nav .logout-btn { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; background: none; border: none; width: calc(100% - 16px); text-align: left; cursor: pointer; transition: background 0.2s, color 0.2s; }
        .sidebar-nav .logout-btn:hover { background: rgba(233,69,96,0.25); color: #fff; }

        /* ===== TOPBAR MOBILE ===== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 54px;
            background: linear-gradient(90deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            z-index: 1040;
            align-items: center;
            padding: 0 14px;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .mobile-topbar .hamburger-btn { background: none; border: none; color: #fff; font-size: 1.3rem; cursor: pointer; padding: 4px 8px; border-radius: 6px; }
        .mobile-topbar .hamburger-btn:hover { background: rgba(255,255,255,0.15); }
        .mobile-topbar .brand-title { color: #fff; font-weight: 700; font-size: 0.95rem; }

        /* ===== OVERLAY ===== */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1045; }
        .sidebar-overlay.show { display: block; }

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        .badge-aktif    { background: #d4edda; color: #155724; }
        .badge-isolir   { background: #f8d7da; color: #721c24; }
        .badge-suspend  { background: #fff3cd; color: #856404; }
        .badge-nonaktif { background: #e2e3e5; color: #383d41; }
        .badge-status   { font-size: 0.75rem; font-weight: 600; padding: 3px 10px; }

        .router-badge { font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 20px; background: #e8f0fe; color: #1a56db; }

        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
        }
    </style>
</head>
<body>

<!-- Topbar Mobile (hamburger) -->
<div class="mobile-topbar">
    <button class="hamburger-btn" id="hamburgerBtn">
        <i class="fas fa-bars"></i>
    </button>
    <span class="brand-title">ISP Billing</span>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><img src="https://airnetps.my.id/app/icon/icon_airnet.png" style="height:38px;object-fit:contain;background:#ffffff;padding:2px 4px;border-radius:8px 0px 8px 0px;"></div>
        <div class="brand-text">
            <span class="brand-title">ISP Billing</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">
            <li class="nav-item"><a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a href="/admin/pelanggan" class="nav-link active"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li class="nav-item"><a href="/admin/paket" class="nav-link"><i class="fas fa-box"></i> Paket Internet</a></li>
            <li class="nav-item"><a href="/admin/tagihan" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Tagihan</a></li>
            <li class="nav-item"><a href="/admin/pembayaran" class="nav-link"><i class="fas fa-money-bill-wave"></i> Pembayaran</a></li>
            <li class="nav-item"><a href="/admin/laporan" class="nav-link"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li class="nav-item"><a href="/admin/mikrotik" class="nav-link"><i class="fas fa-network-wired"></i> Mikrotik</a></li>
        </ul>
        <div class="sidebar-divider"></div>
        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/setting" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="width:16px;font-size:0.82rem;"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Manajemen Pelanggan</h5>
            <small class="text-muted">Total: {{ $pelanggans->total() }} pelanggan
                @if(request('router_id'))
                    · Router: <strong>{{ $routers->find(request('router_id'))->nama ?? '' }}</strong>
                @endif
            </small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-sm" onclick="openCsvImport()">
                <i class="fas fa-file-csv me-1"></i> Import CSV
            </button>
            <a href="/admin/pelanggan/peta" class="btn btn-info btn-sm text-white">
                <i class="fas fa-map-marked-alt me-1"></i> Peta
            </a>
            <a href="/admin/pelanggan/create" class="btn btn-danger btn-sm">
                <i class="fas fa-plus me-1"></i> Tambah Pelanggan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- FILTER -->
    <div class="card mb-3">
        <div class="card-body py-2 px-3">
            <form method="GET" id="filterForm" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" id="searchInput"
                           class="form-control form-control-sm"
                           placeholder="Cari nama, username..."
                           value="{{ request('search') }}"
                           autocomplete="off">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Status</option>
                        <option value="aktif"    {{ request('status')=='aktif'    ? 'selected' : '' }}>Aktif</option>
                        <option value="isolir"   {{ request('status')=='isolir'   ? 'selected' : '' }}>Isolir</option>
                        <option value="suspend"  {{ request('status')=='suspend'  ? 'selected' : '' }}>Suspend</option>
                        <option value="nonaktif" {{ request('status')=='nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="paket_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Paket</option>
                        @foreach($pakets ?? [] as $paket)
                            <option value="{{ $paket->id }}" {{ request('paket_id')==$paket->id ? 'selected' : '' }}>
                                {{ $paket->nama_paket }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="router_id" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Router</option>
                        @foreach($routers ?? [] as $router)
                            <option value="{{ $router->id }}" {{ request('router_id')==$router->id ? 'selected' : '' }}>
                                {{ $router->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <a href="/admin/pelanggan" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                </div>
                <div class="col-auto ms-auto">
                    <a href="/admin/pelanggan/export?{{ http_build_query(request()->query()) }}"
                       class="btn btn-success btn-sm">
                        <i class="fas fa-file-csv me-1"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- BULK ACTION BAR -->
    <div id="bulkBar" class="card mb-2 border-primary" style="display:none;">
        <div class="card-body py-2 px-3 d-flex align-items-center gap-2">
            <span class="small fw-semibold text-primary me-2"><span id="bulkCount">0</span> dipilih</span>
            <button class="btn btn-sm btn-danger" onclick="bulkHapus()">
                <i class="fas fa-trash me-1"></i> Hapus Terpilih
            </button>
            <button class="btn btn-sm btn-secondary ms-auto" onclick="clearAll()">
                <i class="fas fa-times me-1"></i> Batal
            </button>
        </div>
    </div>

    <!-- TABEL -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3"><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                            <th class="ps-3 small">ID Pelanggan</th>
                            <th class="small">Nama</th>
                            <th class="small">Username</th>
                            <th class="small">Router</th>
                            <th class="small">Paket</th>
                            <th class="small">Status</th>
                            <th class="small">Expired</th>
                            <th class="small">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggans as $p)
                        <tr>
                            <td class="ps-3"><input type="checkbox" class="row-check" value="{{ $p->id }}" onchange="updateBulkBar()"></td>
                            <td class="ps-3"><small class="text-muted">{{ $p->id_pelanggan }}</small></td>
                            <td><div class="fw-semibold small">{{ $p->nama }}</div></td>
                            <td><code class="small">{{ $p->username }}</code></td>
                            <td>
                                @if($p->router)
                                    <span class="router-badge"><i class="fas fa-network-wired fa-xs me-1"></i>{{ $p->router->nama }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->paket)
                                    <span class="badge bg-primary">{{ $p->paket->nama_paket }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $p->status }} badge-status rounded-pill">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="{{ $p->tgl_expired && $p->tgl_expired < now() ? 'text-danger fw-bold' : '' }}">
                                    {{ $p->tgl_expired?->format('d/m/Y') ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="/admin/pelanggan/{{ $p->id }}" class="btn btn-sm btn-info text-white py-0 px-2" title="Detail">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                    <a href="/admin/pelanggan/{{ $p->id }}/edit" class="btn btn-sm btn-warning text-white py-0 px-2" title="Edit">
                                        <i class="fas fa-edit fa-xs"></i>
                                    </a>
                                    <form method="POST" action="/admin/pelanggan/{{ $p->id }}" onsubmit="return confirm('Hapus pelanggan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger py-0 px-2" title="Hapus">
                                            <i class="fas fa-trash fa-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>
                                <span class="small">Belum ada pelanggan ditemukan</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pelanggans->hasPages())
        <div class="card-footer bg-white border-0 pt-2">
            {{ $pelanggans->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== HAMBURGER MENU =====
var hamburgerBtn   = document.getElementById('hamburgerBtn');
var sidebar        = document.getElementById('sidebar');
var sidebarOverlay = document.getElementById('sidebarOverlay');

hamburgerBtn.addEventListener('click', function () {
    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('show');
});
sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});

// ===== CHECKBOX BULK =====
function toggleAll(el) {
    document.querySelectorAll('.row-check').forEach(c => c.checked = el.checked);
    updateBulkBar();
}
function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = checked.length;
    bar.style.display = checked.length > 0 ? 'block' : 'none';
    document.getElementById('checkAll').checked =
        checked.length === document.querySelectorAll('.row-check').length && checked.length > 0;
}
function clearAll() {
    document.querySelectorAll('.row-check').forEach(c => c.checked = false);
    document.getElementById('checkAll').checked = false;
    updateBulkBar();
}
function bulkHapus() {
    const ids = [...document.querySelectorAll('.row-check:checked')].map(c => c.value);
    if (!ids.length) return;
    if (!confirm('Hapus ' + ids.length + ' pelanggan terpilih?')) return;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/admin/pelanggan/bulk-delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ ids }),
    })
    .then(r => r.json())
    .then(d => { alert(d.message); window.location.reload(); })
    .catch(() => alert('Gagal hapus. Coba lagi.'));
}

// ===== AUTO SUBMIT SEARCH =====
let searchTimer = null;
document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
        document.getElementById('filterForm').submit();
    }, 500);
});
</script>

<!-- MODAL IMPORT CSV -->
<div class="modal fade" id="csvImportModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-file-csv me-2 text-success"></i>Import Pelanggan dari CSV</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Upload -->
                <div id="csvStep1">
                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Format kolom: <code>username, password, nama, no_hp, alamat, wilayah, nama_paket, nama_router, tgl_expired, maps</code>
                        <a href="/admin/csv/template/pelanggan" class="ms-2 btn btn-sm btn-outline-primary py-0">
                            <i class="fas fa-download me-1"></i> Download Template
                        </a>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Pilih File CSV</label>
                        <input type="file" id="csvFile" class="form-control form-control-sm" accept=".csv,.txt">
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="previewCsv()">
                        <i class="fas fa-eye me-1"></i> Preview Data
                    </button>
                </div>

                <!-- Loading -->
                <div id="csvLoading" style="display:none;" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 small">Memproses CSV...</p>
                </div>

                <!-- Step 2: Preview -->
                <div id="csvStep2" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-semibold"><span id="csvTotalRows">0</span> data ditemukan</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="backToCsvStep1()">
                            <i class="fas fa-arrow-left me-1"></i> Ganti File
                        </button>
                    </div>
                    <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                        <table class="table table-sm table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th><input type="checkbox" id="csvCheckAll" onchange="csvToggleAll(this)"></th>
                                    <th class="small">Username</th>
                                    <th class="small">Nama</th>
                                    <th class="small">No HP</th>
                                    <th class="small">Paket</th>
                                    <th class="small">Router</th>
                                    <th class="small">Expired</th>
                                    <th class="small">Maps</th>
                                    <th class="small">Status</th>
                                </tr>
                            </thead>
                            <tbody id="csvPreviewBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="csvFooter" style="display:none;">
                <span class="text-muted small me-auto" id="csvInfo">0 dipilih</span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm" onclick="doImportCsv()">
                    <i class="fas fa-file-import me-1"></i> Import Terpilih
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let csvPreviewData = [];
let csvPakets = [];
let csvRouters = [];

function openCsvImport() {
    document.getElementById('csvStep1').style.display = 'block';
    document.getElementById('csvStep2').style.display = 'none';
    document.getElementById('csvLoading').style.display = 'none';
    document.getElementById('csvFooter').style.display = 'none';
    document.getElementById('csvFile').value = '';
    new bootstrap.Modal(document.getElementById('csvImportModal')).show();
}

function backToCsvStep1() {
    document.getElementById('csvStep1').style.display = 'block';
    document.getElementById('csvStep2').style.display = 'none';
    document.getElementById('csvFooter').style.display = 'none';
}

function previewCsv() {
    const file = document.getElementById('csvFile').files[0];
    if (!file) { alert('Pilih file CSV dulu!'); return; }

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const fd = new FormData();
    fd.append('file', file);
    fd.append('_token', token);

    document.getElementById('csvStep1').style.display = 'none';
    document.getElementById('csvLoading').style.display = 'block';

    fetch('/admin/csv/pelanggan/preview', { method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: fd })
    .then(r => r.json())
    .then(data => {
        document.getElementById('csvLoading').style.display = 'none';
        if (!data.status) { alert(data.message || 'Gagal'); backToCsvStep1(); return; }

        csvPreviewData = data.preview;
        csvPakets      = data.pakets;
        csvRouters     = data.routers;

        const tbody = document.getElementById('csvPreviewBody');
        tbody.innerHTML = '';
        document.getElementById('csvTotalRows').textContent = csvPreviewData.length;

        csvPreviewData.forEach((row, i) => {
            let paketOpts = '<option value="">-- Pilih --</option>';
            csvPakets.forEach(p => paketOpts += `<option value="${p.id}" ${row.paket_id==p.id?'selected':''}>${p.nama}</option>`);
            let routerOpts = '<option value="">-- Pilih --</option>';
            csvRouters.forEach(r => routerOpts += `<option value="${r.id}" ${row.router_id==r.id?'selected':''}>${r.nama}</option>`);

            const mapsVal = row.maps ? row.maps.replace(/"/g, '&quot;') : '';
            const rowClass = row.exists ? 'table-warning' : (row.error ? 'table-danger' : '');

            tbody.innerHTML += `
            <tr class="${rowClass}">
                <td><input type="checkbox" class="csv-check" data-index="${i}" ${row.exists||row.error?'disabled':''} ${!row.exists&&!row.error?'checked':''}></td>
                <td class="small fw-semibold">${row.username}</td>
                <td class="small">${row.nama}</td>
                <td class="small">${row.no_hp||'-'}</td>
                <td><select class="form-select form-select-sm csv-paket" data-index="${i}" style="min-width:90px;">${paketOpts}</select></td>
                <td><select class="form-select form-select-sm csv-router" data-index="${i}" style="min-width:90px;">${routerOpts}</select></td>
                <td><input type="date" class="form-control form-control-sm csv-expired" data-index="${i}" value="${row.tgl_expired||''}" style="min-width:130px;"></td>
                <td>
                    <input type="text" class="form-control form-control-sm csv-maps" data-index="${i}"
                           value="${mapsVal}" placeholder="https://maps.google.com/..."
                           style="min-width:160px;">
                </td>
                <td class="small">${row.exists ? '<span class="badge bg-warning text-dark">Sudah Ada</span>' : (row.error ? `<span class="badge bg-danger" title="${row.error}">Error</span>` : '<span class="badge bg-success">OK</span>')}</td>
            </tr>`;
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('csv-check')) updateCsvInfo();

            // Kalau user ganti pilihan router/paket, re-evaluate baris
            if (e.target.classList.contains('csv-router') || e.target.classList.contains('csv-paket')) {
                const i   = e.target.dataset.index;
                const row = e.target.closest('tr');
                const paketVal  = row.querySelector('.csv-paket').value;
                const routerVal = row.querySelector('.csv-router').value;
                const cb = row.querySelector('.csv-check');
                if (paketVal && routerVal) {
                    cb.disabled = false;
                    cb.checked  = true;
                    row.classList.remove('table-danger');
                    row.querySelector('td:last-child').innerHTML = '<span class="badge bg-success">OK</span>';
                    updateCsvInfo();
                }
            }
        });

        updateCsvInfo();
        document.getElementById('csvStep2').style.display = 'block';
        document.getElementById('csvFooter').style.display = 'flex';
    })
    .catch(() => { document.getElementById('csvLoading').style.display = 'none'; alert('Gagal proses CSV'); backToCsvStep1(); });
}

function csvToggleAll(el) {
    document.querySelectorAll('.csv-check:not(:disabled)').forEach(c => c.checked = el.checked);
    updateCsvInfo();
}

function updateCsvInfo() {
    const n = document.querySelectorAll('.csv-check:checked').length;
    document.getElementById('csvInfo').textContent = n + ' dipilih untuk diimport';
}

function doImportCsv() {
    const items = [];
    document.querySelectorAll('.csv-check:checked').forEach(c => {
        const i   = c.dataset.index;
        const row = csvPreviewData[i];
        items.push({
            username:    row.username,
            password:    row.password,
            nama:        row.nama,
            no_hp:       row.no_hp,
            email:       row.email,
            alamat:      row.alamat,
            wilayah:     row.wilayah,
            latitude:    row.latitude,
            longitude:   row.longitude,
            ip_address:  row.ip_address,
            jenis:       row.jenis,
            paket_id:    document.querySelector(`.csv-paket[data-index="${i}"]`)?.value,
            router_id:   document.querySelector(`.csv-router[data-index="${i}"]`)?.value,
            tgl_expired: document.querySelector(`.csv-expired[data-index="${i}"]`)?.value,
            maps:        document.querySelector(`.csv-maps[data-index="${i}"]`)?.value,
        });
    });

    if (!items.length) { alert('Pilih minimal 1 data!'); return; }

    const btn = document.querySelector('#csvFooter .btn-success');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengimport...';

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/admin/csv/pelanggan/import', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ items }),
    })
    .then(r => r.json())
    .then(d => {
        alert(d.message);
        bootstrap.Modal.getInstance(document.getElementById('csvImportModal')).hide();
        if (d.imported > 0) window.location.reload();
    })
    .catch(() => { alert('Gagal import'); btn.disabled = false; btn.innerHTML = '<i class="fas fa-file-import me-1"></i> Import Terpilih'; });
}
</script>

</body>
</html>