@extends('layouts.admin')

@section('content')
<style>
    .stat-card { border:none; border-radius:12px; padding:15px 20px; color:white; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
    .badge-paid      { background:#d4edda; color:#155724; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
    .badge-unpaid    { background:#fff3cd; color:#856404; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
    .badge-overdue   { background:#f8d7da; color:#721c24; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
    .badge-cancelled { background:#e2e3e5; color:#383d41; padding:4px 10px; border-radius:20px; font-size:0.78rem; font-weight:600; }
    .bulk-bar { display:none; background:linear-gradient(135deg,#1a1a2e,#0f3460); color:#fff; padding:10px 16px; border-radius:10px; margin-bottom:12px; align-items:center; gap:12px; flex-wrap:wrap; }
    .bulk-bar.show { display:flex; }
    tr.selected-row { background:#fff8e1 !important; }
    .table td, .table th { padding-top: 0px !important; padding-bottom: 0px !important; vertical-align: middle !important; }
</style>

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
        <button class="btn btn-success btn-sm" onclick="document.getElementById('modalExportCsv').style.display='flex'">
            <i class="fas fa-file-csv me-1"></i> Export CSV
        </button>
        @if(auth()->user()->isSuperAdmin())
        <button class="btn btn-danger btn-sm" onclick="document.getElementById('modalResetCounter').style.display='flex'">
            <i class="fas fa-trash-alt me-1"></i> Reset Counter
        </button>
        @endif
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
            <div class="fs-5 fw-bold" id="statUnpaid">{{ $totalUnpaid }}</div>
            <div class="opacity-75">Belum Bayar</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#ff6b6b,#ee5a24)">
            <div class="fs-5 fw-bold" id="statOverdue">{{ $totalOverdue }}</div>
            <div class="opacity-75">Jatuh Tempo</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
            <div class="fs-5 fw-bold" id="statPaid">{{ $totalPaid }}</div>
            <div class="opacity-75">Lunas Bulan Ini</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="fs-5 fw-bold" id="statPendapatan">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
            <div class="opacity-75">Pendapatan Bulan Ini</div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form id="filterForm" method="GET" action="/admin/tagihan" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" id="searchInput" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / username / no tagihan..." value="{{ request('search') }}"
                       oninput="clearTimeout(window._st);window._st=setTimeout(doAjaxFilter,400)">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="doAjaxFilter()">
                    <option value="">Semua Status</option>
                    <option value="unpaid"    {{ request('status')=='unpaid'    ? 'selected':'' }}>Unpaid</option>
                    <option value="overdue"   {{ request('status')=='overdue'   ? 'selected':'' }}>Overdue</option>
                    <option value="paid"      {{ request('status')=='paid'      ? 'selected':'' }}>Paid</option>
                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="paket_id" class="form-select form-select-sm" onchange="doAjaxFilter()">
                    <option value="">Semua Paket</option>
                    @foreach($pakets as $paket)
                    <option value="{{ $paket->id }}" {{ request('paket_id') == $paket->id ? 'selected' : '' }}>
                        {{ $paket->nama_paket }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="router_id" class="form-select form-select-sm" onchange="onRouterChange()">
                    <option value="">Semua Router</option>
                    @foreach($routers as $router)
                    <option value="{{ $router->id }}" {{ request('router_id') == $router->id ? 'selected' : '' }}>
                        {{ $router->nama }}
                    </option>
                    @endforeach
                </select>
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
                <table class="table table-hover table-sm mb-0">
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
                    <tbody id="tagihanBody">
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

{{-- MODAL EXPORT CSV --}}
<div id="modalExportCsv" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;width:420px;max-width:90%;">
        <h5 class="fw-bold mb-1"><i class="fas fa-file-csv text-success me-2"></i>Export CSV Tagihan</h5>
        <p class="text-muted small mb-3">Filter data yang ingin diexport ke file CSV.</p>
        <form id="formExportCsv" method="GET" action="/admin/tagihan/export-csv" target="_blank">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Filter Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="overdue">Overdue</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Filter Router</label>
                <select name="router_id" class="form-select form-select-sm">
                    <option value="">Semua Router</option>
                    @foreach($routers as $router)
                    <option value="{{ $router->id }}">{{ $router->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm"
                    onclick="document.getElementById('modalExportCsv').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-success btn-sm"
                    onclick="document.getElementById('modalExportCsv').style.display='none'">
                    <i class="fas fa-download me-1"></i> Download CSV
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RESET COUNTER --}}
<div id="modalResetCounter" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:24px;width:400px;max-width:90%;">
        <h5 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Reset Counter Tagihan</h5>
        <p class="text-muted small mb-3">Semua data <strong>Tagihan</strong> dan <strong>Pembayaran</strong> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan!</p>
        <div class="mb-3">
            <label class="form-label small fw-semibold">Password Admin</label>
            <input type="password" id="resetPassword" class="form-control form-control-sm" placeholder="Masukkan password admin" autocomplete="new-password">
            <div id="resetError" class="text-danger small mt-1" style="display:none;"></div>
        </div>
        <div class="d-flex gap-2 justify-content-end">
            <button class="btn btn-secondary btn-sm" onclick="document.getElementById('modalResetCounter').style.display='none';document.getElementById('resetPassword').value='';document.getElementById('resetError').style.display='none';">Batal</button>
            <button class="btn btn-danger btn-sm" onclick="doResetCounter()"><i class="fas fa-trash-alt me-1"></i>Reset Sekarang</button>
        </div>
    </div>
</div>

<script>
function konfirmasi(id) {
    document.getElementById('formBayar').action = '/admin/tagihan/' + id + '/bayar';
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
}

document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.cb-tagihan').forEach(cb => {
        cb.checked = this.checked;
        const row = cb.closest('tr');
        if (this.checked) row.classList.add('selected-row');
        else row.classList.remove('selected-row');
    });
    updateBulkBar();
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('cb-tagihan')) {
        const row = e.target.closest('tr');
        if (e.target.checked) row.classList.add('selected-row');
        else row.classList.remove('selected-row');
    }
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.cb-tagihan:checked');
    const bar = document.getElementById('bulkBar');
    document.getElementById('jumlahDipilih').textContent = checked.length;
    if (checked.length > 0) bar.classList.add('show');
    else bar.classList.remove('show');
    const all = document.querySelectorAll('.cb-tagihan');
    document.getElementById('checkAll').indeterminate = checked.length > 0 && checked.length < all.length;
    document.getElementById('checkAll').checked = checked.length === all.length && all.length > 0;
}

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

function clearSelection() {
    document.querySelectorAll('.cb-tagihan').forEach(cb => {
        cb.checked = false;
        cb.closest('tr').classList.remove('selected-row');
    });
    document.getElementById('checkAll').checked = false;
    document.getElementById('bulkBar').classList.remove('show');
}
</script>


<script>
var paketsByRouter = {!! $paketsByRouter !!};

function onRouterChange() {
    var routerId = document.querySelector('[name=router_id]').value;
    var paketSelect = document.querySelector('[name=paket_id]');
    var currentPaket = paketSelect.value;
    
    // Reset options
    paketSelect.innerHTML = '<option value="">Semua Paket</option>';
    
    if (routerId && paketsByRouter[routerId]) {
        paketsByRouter[routerId].forEach(function(paket) {
            var opt = document.createElement('option');
            opt.value = paket.id;
            opt.textContent = paket.nama_paket;
            if (paket.id == currentPaket) opt.selected = true;
            paketSelect.appendChild(opt);
        });
    } else {
        // Tampilkan semua paket jika tidak ada router dipilih
        var allPakets = [];
        Object.values(paketsByRouter).forEach(function(pakets) {
            pakets.forEach(function(p) {
                if (!allPakets.find(x => x.id === p.id)) allPakets.push(p);
            });
        });
        allPakets.sort((a,b) => a.nama_paket.localeCompare(b.nama_paket));
        allPakets.forEach(function(paket) {
            var opt = document.createElement('option');
            opt.value = paket.id;
            opt.textContent = paket.nama_paket;
            paketSelect.appendChild(opt);
        });
    }
    doAjaxFilter();
}

function doResetCounter() {
    var password = document.getElementById('resetPassword').value;
    if (!password) { 
        document.getElementById('resetError').textContent = 'Password tidak boleh kosong!';
        document.getElementById('resetError').style.display = 'block';
        return; 
    }
    fetch('/admin/tagihan/reset-counter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '{{ csrf_token() }}'
        },
        body: JSON.stringify({ password: password })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('modalResetCounter').style.display = 'none';
            document.getElementById('resetPassword').value = '';
            alert('✅ ' + data.message);
            window.location.href = '/admin/tagihan';
        } else {
            document.getElementById('resetError').textContent = data.message;
            document.getElementById('resetError').style.display = 'block';
        }
    })
    .catch(() => {
        document.getElementById('resetError').textContent = 'Terjadi kesalahan, coba lagi!';
        document.getElementById('resetError').style.display = 'block';
    });
}

function doAjaxFilter() {
    var f = document.getElementById('filterForm');
    var params = new URLSearchParams();
    var search = f.querySelector('[name=search]').value;
    var status = f.querySelector('[name=status]').value;
    var paket  = f.querySelector('[name=paket_id]').value;
    var router = f.querySelector('[name=router_id]').value;
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (paket)  params.set('paket_id', paket);
    if (router) params.set('router_id', router);

    fetch('/admin/tagihan?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('tagihanBody').innerHTML = data.html;
        // Update stat cards
        document.getElementById('statUnpaid').textContent     = data.totalUnpaid;
        document.getElementById('statOverdue').textContent    = data.totalOverdue;
        document.getElementById('statPaid').textContent       = data.totalPaid;
        document.getElementById('statPendapatan').textContent = 'Rp ' + data.totalPendapatan;
        // Update URL tanpa reload
        history.pushState(null, '', '/admin/tagihan?' + params.toString());
    });
}
</script>
@endsection
