<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width:230px; --sidebar-bg-start:#1a1a2e; --sidebar-bg-end:#0f3460; --accent:#e94560; }
        * { box-sizing:border-box; }
        body { background:#f0f2f5; font-family:'Segoe UI',sans-serif; }
        .sidebar { background:linear-gradient(180deg,var(--sidebar-bg-start) 0%,var(--sidebar-bg-end) 100%); min-height:100vh; width:var(--sidebar-width); position:fixed; top:0; left:0; z-index:1050; display:flex; flex-direction:column; transition:transform 0.3s ease; }
        .sidebar-brand { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .brand-icon { width: 70px; height: 40px; background: rgba(233,69,96,0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; flex-shrink: 0; }
        .sidebar-brand .brand-text { line-height: 1.2; }
        .sidebar-brand .brand-title { color:#fff; font-weight:700; font-size:0.9rem; display:block; }
        .sidebar-brand .brand-sub { color:rgba(255,255,255,0.45); font-size:0.7rem; }
        .sidebar-nav { padding:8px 0; flex:1; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; transition:background 0.2s,color 0.2s; white-space:nowrap; }
        .sidebar-nav .nav-link i { width:16px; font-size:0.82rem; flex-shrink:0; }
        .sidebar-nav .nav-link:hover,.sidebar-nav .nav-link.active { background:rgba(233,69,96,0.25); color:#fff; }
        .sidebar-nav .nav-link.active { background:rgba(233,69,96,0.35); }
        .sidebar-divider { border-top:1px solid rgba(255,255,255,0.08); margin:6px 14px; }
        .sidebar-nav .logout-btn { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; background:none; border:none; width:calc(100% - 16px); text-align:left; cursor:pointer; transition:background 0.2s,color 0.2s; }
        .sidebar-nav .logout-btn:hover { background:rgba(233,69,96,0.25); color:#fff; }
        .mobile-topbar { display:none; position:fixed; top:0; left:0; right:0; height:54px; background:linear-gradient(90deg,var(--sidebar-bg-start),var(--sidebar-bg-end)); z-index:1060; align-items:center; padding:0 14px; gap:12px; box-shadow:0 2px 8px rgba(0,0,0,0.2); }
        .mobile-topbar .hamburger-btn { background:none; border:none; color:#fff; font-size:1.3rem; cursor:pointer; padding:4px 8px; border-radius:6px; }
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1045; }
        .sidebar-overlay.show { display:block; }
        .main-content { margin-left:var(--sidebar-width); padding:20px 24px; }
        .card { border:none; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.07); }
        .stat-card { border:none; border-radius:12px; padding:15px 20px; color:white; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .badge-paid      { background:#d4edda; color:#155724; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
        .badge-unpaid    { background:#fff3cd; color:#856404; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
        .badge-overdue   { background:#f8d7da; color:#721c24; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
        .badge-cancelled { background:#e2e3e5; color:#383d41; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
        .bulk-bar { display:none; background:linear-gradient(135deg,#1a1a2e,#0f3460); color:#fff; padding:10px 16px; border-radius:10px; margin-bottom:12px; align-items:center; gap:12px; flex-wrap:wrap; }
        .bulk-bar.show { display:flex; }
        tr.selected-row { background:#fff8e1 !important; }
        @media (max-width:768px) {
            .mobile-topbar { display:flex; }
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .main-content { margin-left:0; padding:70px 14px 14px; }
        }
    </style>
</head>
<body>

@include('admin.partials.sidebar')
<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Manajemen Tagihan</h5>
            <small class="text-muted">{{ now()->isoFormat('MMMM Y') }}</small>
        </div>
        <div class="d-flex gap-2">
            <form method="POST" action="/admin/tagihan/generate">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm"
                        onclick="return confirm('Generate tagihan untuk semua pelanggan aktif bulan ini?')">
                    <i class="fas fa-magic me-1"></i> Generate Massal
                </button>
            </form>
            <a href="/admin/tagihan/create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Buat Tagihan
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
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
                <div class="fs-3 fw-bold">{{ $totalUnpaid }}</div>
                <div class="opacity-75">Belum Bayar</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#ff6b6b,#ee5a24)">
                <div class="fs-3 fw-bold">{{ $totalOverdue }}</div>
                <div class="opacity-75">Jatuh Tempo</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
                <div class="fs-3 fw-bold">{{ $totalPaid }}</div>
                <div class="opacity-75">Lunas Bulan Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
                <div class="fs-5 fw-bold">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
                <div class="opacity-75">Pendapatan Bulan Ini</div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Cari nama / no tagihan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="unpaid"    {{ request('status')=='unpaid'    ? 'selected':'' }}>Unpaid</option>
                        <option value="overdue"   {{ request('status')=='overdue'   ? 'selected':'' }}>Overdue</option>
                        <option value="paid"      {{ request('status')=='paid'      ? 'selected':'' }}>Paid</option>
                        <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="month" name="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/tagihan" class="btn btn-secondary btn-sm w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- BULK ACTION BAR --}}
    <div class="bulk-bar" id="bulkBar">
        <div class="fw-semibold">
            <i class="fas fa-check-square me-2"></i>
            <span id="jumlahDipilih">0</span> tagihan dipilih
        </div>
        <div class="d-flex gap-2 align-items-center ms-auto flex-wrap">
            <select id="bulkMetode" class="form-select form-select-sm" style="width:160px;">
                <option value="cash">💵 Cash / Tunai</option>
                <option value="transfer">🏦 Transfer Bank</option>
            </select>
            <input type="text" id="bulkCatatan" class="form-control form-control-sm" style="width:180px;" placeholder="Catatan (opsional)">
            <button class="btn btn-success btn-sm" onclick="bayarMassal()">
                <i class="fas fa-check me-1"></i> Bayar Semua Terpilih
            </button>
            <button class="btn btn-outline-light btn-sm" onclick="clearSelection()">
                <i class="fas fa-times me-1"></i> Batal
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <form id="formBayarMassal" method="POST" action="{{ route('tagihan.bayar-massal') }}">
                    @csrf
                    <input type="hidden" name="metode_bayar" id="hiddenMetode" value="cash">
                    <input type="hidden" name="catatan" id="hiddenCatatan" value="">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px;">
                                    <input type="checkbox" id="checkAll" class="form-check-input"
                                           title="Pilih semua unpaid/overdue">
                                </th>
                                <th class="small">No. Tagihan</th>
                                <th class="small">Pelanggan</th>
                                <th class="small">Paket</th>
                                <th class="small">Total</th>
                                <th class="small">Jatuh Tempo</th>
                                <th class="small">Status</th>
                                <th class="small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tagihans as $t)
                            <tr id="row-{{ $t->id }}">
                                <td>
                                    @if($t->status !== 'paid' && $t->status !== 'cancelled')
                                    <input type="checkbox" name="tagihan_ids[]" value="{{ $t->id }}"
                                           class="form-check-input cb-tagihan"
                                           onchange="updateBulkBar()">
                                    @endif
                                </td>
                                <td><code class="small">{{ $t->no_tagihan }}</code></td>
                                <td>
                                    <div class="fw-semibold small">{{ $t->pelanggan->nama ?? '-' }}</div>
                                    <small class="text-muted">{{ $t->pelanggan->id_pelanggan ?? '' }}</small>
                                </td>
                                <td><small>{{ $t->paket->nama_paket ?? '-' }}</small></td>
                                <td>
                                    <div class="fw-bold small">Rp {{ number_format($t->total,0,',','.') }}</div>
                                    @if($t->denda > 0)
                                    <small class="text-danger">+denda Rp {{ number_format($t->denda,0,',','.') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="{{ $t->tgl_jatuh_tempo < now() && $t->status != 'paid' ? 'text-danger fw-bold' : '' }}">
                                        {{ $t->tgl_jatuh_tempo?->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td><span class="badge-{{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/admin/tagihan/{{ $t->id }}" class="btn btn-sm btn-info text-white py-0 px-2" title="Detail">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        @if($t->status !== 'paid')
                                        <button type="button" class="btn btn-sm btn-success py-0 px-2"
                                                onclick="konfirmasi({{ $t->id }})" title="Konfirmasi Bayar">
                                            <i class="fas fa-check fa-xs"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                                    Belum ada tagihan. Klik Generate Massal untuk membuat tagihan otomatis!
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        @if($tagihans->hasPages())
        <div class="card-footer bg-white">{{ $tagihans->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>

{{-- MODAL KONFIRMASI BAYAR SATUAN --}}
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-check-circle text-success me-2"></i>Konfirmasi Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="formBayar">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Metode Pembayaran</label>
                        <select name="metode_bayar" class="form-select form-select-sm" required>
                            <option value="cash">💵 Cash / Tunai</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catatan (opsional)</label>
                        <input type="text" name="catatan" class="form-control form-control-sm"
                               placeholder="Contoh: Bayar via BCA">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-check me-1"></i> Konfirmasi Lunas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var hamburgerBtn = document.getElementById("hamburgerBtn");
    var sidebar = document.getElementById("sidebar");
    var sidebarOverlay = document.getElementById("sidebarOverlay");
    if(hamburgerBtn) {
        hamburgerBtn.addEventListener("click", function() {
            sidebar.classList.toggle("open");
            sidebarOverlay.classList.toggle("show");
        });
        sidebarOverlay.addEventListener("click", function() {
            sidebar.classList.remove("open");
            sidebarOverlay.classList.remove("show");
        });
    }
});
</script>
<script>
// Konfirmasi bayar satuan
function konfirmasi(id) {
    document.getElementById('formBayar').action = '/admin/tagihan/' + id + '/bayar';
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
}

// Select All checkbox
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.cb-tagihan').forEach(cb => {
        cb.checked = this.checked;
        const row = cb.closest('tr');
        if (this.checked) row.classList.add('selected-row');
        else row.classList.remove('selected-row');
    });
    updateBulkBar();
});

// Update highlight row
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('cb-tagihan')) {
        const row = e.target.closest('tr');
        if (e.target.checked) row.classList.add('selected-row');
        else row.classList.remove('selected-row');
    }
});

// Update bulk bar
function updateBulkBar() {
    const checked = document.querySelectorAll('.cb-tagihan:checked');
    const bar = document.getElementById('bulkBar');
    document.getElementById('jumlahDipilih').textContent = checked.length;
    if (checked.length > 0) bar.classList.add('show');
    else bar.classList.remove('show');

    // Sync checkAll state
    const all = document.querySelectorAll('.cb-tagihan');
    document.getElementById('checkAll').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('checkAll').checked = checked.length === all.length && all.length > 0;
}

// Bayar massal
function bayarMassal() {
    const checked = document.querySelectorAll('.cb-tagihan:checked');
    if (checked.length === 0) { alert('Pilih minimal 1 tagihan!'); return; }

    const metode  = document.getElementById('bulkMetode').value;
    const catatan = document.getElementById('bulkCatatan').value;
    const nama    = metode === 'cash' ? 'Cash/Tunai' : 'Transfer Bank';

    if (!confirm(`Konfirmasi bayar ${checked.length} tagihan dengan metode ${nama}?\n\nProses ini tidak bisa dibatalkan!`)) return;

    document.getElementById('hiddenMetode').value  = metode;
    document.getElementById('hiddenCatatan').value = catatan;
    document.getElementById('formBayarMassal').submit();
}

// Clear selection
function clearSelection() {
    document.querySelectorAll('.cb-tagihan').forEach(cb => {
        cb.checked = false;
        cb.closest('tr').classList.remove('selected-row');
    });
    document.getElementById('checkAll').checked = false;
    document.getElementById('bulkBar').classList.remove('show');
}
</script>
</body>
</html>
