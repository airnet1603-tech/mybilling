<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .sidebar-nav .nav-link { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; transition:background 0.2s,color 0.2s; }
        .sidebar-nav .nav-link i { width:16px; font-size:0.82rem; }
        .sidebar-nav .nav-link:hover,.sidebar-nav .nav-link.active { background:rgba(233,69,96,0.25); color:#fff; }
        .sidebar-nav .nav-link.active { background:rgba(233,69,96,0.35); }
        .sidebar-divider { border-top:1px solid rgba(255,255,255,0.08); margin:6px 14px; }
        .sidebar-nav .logout-btn { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; background:none; border:none; width:calc(100% - 16px); text-align:left; cursor:pointer; }
        .sidebar-nav .logout-btn:hover { background:rgba(233,69,96,0.25); color:#fff; }
        .mobile-topbar { display:none; position:fixed; top:0; left:0; right:0; height:54px; background:linear-gradient(90deg,var(--sidebar-bg-start),var(--sidebar-bg-end)); z-index:1060; align-items:center; padding:0 14px; gap:12px; box-shadow:0 2px 8px rgba(0,0,0,0.2); }
        .mobile-topbar .hamburger-btn { background:none; border:none; color:#fff; font-size:1.3rem; cursor:pointer; padding:4px 8px; border-radius:6px; }
        .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1045; }
        .sidebar-overlay.show { display:block; }
        .main-content { margin-left:var(--sidebar-width); padding:20px 24px; }
        .card { border:none; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.07); }
        .stat-card { border:none; border-radius:12px; padding:15px 20px; color:white; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .card-section-title { font-size:0.88rem; font-weight:700; }
        .btn-clear-disabled { opacity:0.45; cursor:not-allowed; pointer-events:none; }
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

    {{-- ALERT --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0">Laporan & Grafik</h5>
            <small class="text-muted">Analisis pendapatan tahun {{ $tahun }}</small>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form method="GET" class="d-flex gap-2">
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </form>

            {{-- TOMBOL CLEAR TAHUN --}}
            @if(auth()->user()->isAdmin())
                <button class="btn btn-danger btn-sm" onclick="confirmClearTahun()">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Data {{ $tahun }}
                </button>
            @else
                <button class="btn btn-secondary btn-sm btn-clear-disabled" title="Hanya admin yang dapat menghapus data">
                    <i class="fas fa-trash-alt me-1"></i> Hapus Data {{ $tahun }}
                </button>
            @endif
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
                <div class="opacity-75 small">Total Pendapatan {{ $tahun }}</div>
                <div class="fs-5 fw-bold">Rp {{ number_format($totalPendapatanTahun,0,',','.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
                <div class="opacity-75 small">Tagihan Lunas</div>
                <div class="fs-3 fw-bold">{{ $totalLunasTahun }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
                <div class="opacity-75 small">Tagihan Belum Bayar</div>
                <div class="fs-3 fw-bold">{{ $totalUnpaidTahun }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
                <div class="opacity-75 small">Total Tagihan</div>
                <div class="fs-3 fw-bold">{{ $totalTagihanTahun }}</div>
            </div>
        </div>
    </div>

    {{-- CLEAR PER BULAN --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="card-section-title mb-3">
                <i class="fas fa-calendar-times me-2 text-danger"></i>Hapus Data per Bulan
                @if(!auth()->user()->isAdmin())
                    <span class="badge bg-secondary ms-2">Khusus Admin</span>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $namaBulan = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp
                @for($b = 1; $b <= 12; $b++)
                @php
                    $adaData = $bulanTersedia->firstWhere('bulan', $b);
                @endphp
                @if(auth()->user()->isAdmin())
                    @if($adaData)
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmClearBulan({{ $b }}, '{{ $namaBulan[$b] }}')">
                            <i class="fas fa-times me-1"></i>{{ $namaBulan[$b] }}
                            <span class="badge bg-danger ms-1">{{ $adaData->jml }}</span>
                        </button>
                    @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            {{ $namaBulan[$b] }} <span class="badge bg-secondary ms-1">0</span>
                        </button>
                    @endif
                @else
                    <button class="btn btn-outline-secondary btn-sm btn-clear-disabled" title="Hanya admin">
                        {{ $namaBulan[$b] }}
                        @if($adaData)<span class="badge bg-secondary ms-1">{{ $adaData->jml }}</span>@endif
                    </button>
                @endif
                @endfor
            </div>
            <small class="text-muted mt-2 d-block">
                <i class="fas fa-info-circle me-1"></i>
                Tombol merah = ada data. Menghapus pembayaran akan mereset tagihan ke status <strong>unpaid</strong>.
            </small>
        </div>
    </div>

    {{-- STATISTIK & CLEAR PER USER --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="card-section-title mb-3">
                <i class="fas fa-users me-2 text-primary"></i>Statistik & Hapus Data per User
                @if(!auth()->user()->isAdmin())
                    <span class="badge bg-secondary ms-2">Hapus: Khusus Admin</span>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">User</th>
                            <th class="small">Role</th>
                            <th class="small">Total Transaksi</th>
                            <th class="small">Total Nominal</th>
                            <th class="small">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($statistikPerUser as $u)
                        <tr>
                            <td>
                                <div class="fw-semibold small">{{ $u->name }}</div>
                                <small class="text-muted">{{ $u->email }}</small>
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @else
                                    <span class="badge bg-primary">Operator</span>
                                @endif
                            </td>
                            <td><span class="fw-bold">{{ $u->total_transaksi ?? 0 }}</span> transaksi</td>
                            <td class="fw-bold text-success small">Rp {{ number_format($u->total_nominal ?? 0,0,',','.') }}</td>
                            <td>
                                @if(auth()->user()->isAdmin())
                                    @if(($u->total_transaksi ?? 0) > 0)
                                        <button class="btn btn-danger btn-sm" onclick="confirmClearUser({{ $u->id }}, '{{ $u->name }}')">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    @else
                                        <button class="btn btn-outline-secondary btn-sm" disabled>Tidak ada data</button>
                                    @endif
                                @else
                                    <button class="btn btn-secondary btn-sm btn-clear-disabled" title="Hanya admin">
                                        <i class="fas fa-lock me-1"></i>Terkunci
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3 small">Belum ada data user</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3"><i class="fas fa-chart-line me-2 text-primary"></i>Grafik Pendapatan Bulanan {{ $tahun }}</div>
                    <canvas id="chartPendapatan" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3"><i class="fas fa-chart-pie me-2 text-primary"></i>Metode Pembayaran</div>
                    <canvas id="chartMetode" height="200"></canvas>
                    <div class="mt-3">
                        @foreach($metodeBayar as $m)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">{{ ucfirst($m->metode) }}</span>
                            <span class="fw-bold small">Rp {{ number_format($m->total,0,',','.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3"><i class="fas fa-user-plus me-2 text-success"></i>Pelanggan Baru per Bulan</div>
                    <canvas id="chartPelanggan" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3"><i class="fas fa-box me-2 text-warning"></i>Pendapatan per Paket</div>
                    @forelse($pendapatanPerPaket as $p)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold small">{{ $p->paket->nama_paket ?? 'Unknown' }}</span>
                            <span class="text-success fw-bold small">Rp {{ number_format($p->total_pendapatan,0,',','.') }}</span>
                        </div>
                        <small class="text-muted">{{ $p->jumlah }} transaksi</small>
                        @php $persen = $totalPendapatanTahun > 0 ? ($p->total_pendapatan / $totalPendapatanTahun * 100) : 0; @endphp
                        <div class="progress mt-1" style="height:8px;">
                            <div class="progress-bar bg-danger" style="width:{{ $persen }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">Belum ada data</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL TAGIHAN BULAN INI --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="px-3 pt-3 pb-2">
                <div class="card-section-title"><i class="fas fa-list me-2 text-primary"></i>Tagihan Bulan {{ now()->isoFormat('MMMM Y') }} (10 terbaru)</div>
            </div>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small">No. Tagihan</th>
                        <th class="small">Pelanggan</th>
                        <th class="small">Paket</th>
                        <th class="small">Total</th>
                        <th class="small">Status</th>
                        <th class="small">Tgl Bayar</th>
                        <th class="small">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihanBulanIni as $t)
                    <tr>
                        <td><code class="small">{{ $t->no_tagihan }}</code></td>
                        <td>
                            <div class="fw-semibold small">{{ $t->pelanggan->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $t->pelanggan->id_pelanggan ?? '' }}</small>
                        </td>
                        <td><small>{{ $t->paket->nama_paket ?? '-' }}</small></td>
                        <td class="fw-bold small">Rp {{ number_format($t->total,0,',','.') }}</td>
                        <td>
                            @if($t->status == 'paid') <span class="badge bg-success">Lunas</span>
                            @elseif($t->status == 'overdue') <span class="badge bg-danger">Overdue</span>
                            @else <span class="badge bg-warning text-dark">Unpaid</span>
                            @endif
                        </td>
                        <td><small>{{ $t->tgl_bayar?->format('d/m/Y') ?? '-' }}</small></td>
                        <td>
                            @if($t->status == 'paid')
                                @if(auth()->user()->isAdmin())
                                <div class="d-flex gap-1">
                                @if($t->pembayaran->count() > 0)
                                <button class="btn btn-danger btn-sm py-0 px-2"
                                    onclick="confirmHapusPelanggan({{ $t->pembayaran->first()->id }}, '{{ $t->pelanggan->nama ?? '' }}')"
                                    title="Hapus dari laporan pendapatan">
                                    <i class="fas fa-times fa-xs"></i>
                                </button>
                                @endif
                                <button class="btn btn-warning btn-sm py-0 px-2"
                                    onclick="confirmRollback({{ $t->id }}, '{{ $t->pelanggan->nama ?? '' }}')"
                                    title="Reset ke Unpaid">
                                    <i class="fas fa-undo fa-xs"></i>
                                </button>
                                </div>
                                @else
                                <div class="d-flex gap-1">
                                <button class="btn btn-secondary btn-sm py-0 px-2" disabled title="Hanya admin">
                                    <i class="fas fa-lock fa-xs"></i>
                                </button>
                                <button class="btn btn-secondary btn-sm py-0 px-2" disabled title="Hanya admin">
                                    <i class="fas fa-lock fa-xs"></i>
                                </button>
                                </div>
                                @endif
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4 small">Belum ada tagihan bulan ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- HIDDEN FORMS --}}
<form id="formClearTahun" method="POST" action="{{ route('laporan.clear-tahun') }}" style="display:none">
    @csrf @method('DELETE')
    <input type="hidden" name="tahun" value="{{ $tahun }}">
</form>
<form id="formClearBulan" method="POST" action="{{ route('laporan.clear-bulan') }}" style="display:none">
    @csrf @method('DELETE')
    <input type="hidden" name="tahun" value="{{ $tahun }}">
    <input type="hidden" name="bulan" id="inputBulan" value="">
</form>
<form id="formRollbackUnpaid" method="POST" action="{{ route('laporan.rollback-unpaid') }}" style="display:none">
    @csrf @method('DELETE')
    <input type="hidden" name="tagihan_id" id="inputTagihanId" value="">
</form>
<form id="formClearPelanggan" method="POST" action="{{ route('laporan.clear-pelanggan') }}" style="display:none">
    @csrf @method('DELETE')
    <input type="hidden" name="pembayaran_id" id="inputPembayaranId" value="">
</form>
<form id="formClearUser" method="POST" action="{{ route('laporan.clear-user') }}" style="display:none">
    @csrf @method('DELETE')
    <input type="hidden" name="user_id" id="inputUserId" value="">
</form>

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


// Confirm Clear Tahun
function confirmClearTahun() {
    if (confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus SEMUA data pembayaran tahun {{ $tahun }}.\nSemua tagihan terkait akan direset ke status UNPAID.\n\nApakah Anda yakin?')) {
        document.getElementById('formClearTahun').submit();
    }
}

// Confirm Clear Bulan
function confirmClearBulan(bulan, namaBulan) {
    if (confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus data pembayaran bulan ' + namaBulan + ' {{ $tahun }}.\nSemua tagihan terkait akan direset ke status UNPAID.\n\nApakah Anda yakin?')) {
        document.getElementById('inputBulan').value = bulan;
        document.getElementById('formClearBulan').submit();
    }
}

// Confirm Hapus Pelanggan dari Laporan
function confirmHapusPelanggan(pembayaranId, namaPelanggan) {
    if (confirm('Hapus pembayaran ' + namaPelanggan + ' dari laporan?\n\nTagihan tetap LUNAS, hanya dihapus dari hitungan pendapatan.')) {
        document.getElementById('inputPembayaranId').value = pembayaranId;
        document.getElementById('formClearPelanggan').submit();
    }
}

// Confirm Rollback Unpaid
function confirmRollback(tagihanId, namaPelanggan) {
    if (confirm('Reset tagihan ' + namaPelanggan + ' ke UNPAID?\n\nPembayaran akan dihapus dan tagihan kembali ke status Unpaid.')) {
        document.getElementById('inputTagihanId').value = tagihanId;
        document.getElementById('formRollbackUnpaid').submit();
    }
}

// Confirm Clear User
function confirmClearUser(userId, namaUser) {
    if (confirm('⚠️ PERHATIAN!\n\nAnda akan menghapus semua data pembayaran yang diinput oleh:\n' + namaUser + '\n\nSemua tagihan terkait akan direset ke status UNPAID.\n\nApakah Anda yakin?')) {
        document.getElementById('inputUserId').value = userId;
        document.getElementById('formClearUser').submit();
    }
}

// Charts
const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
new Chart(document.getElementById('chartPendapatan'), {
    type: 'bar',
    data: {
        labels: bulan,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: [{{ $pendapatanBulanan[1] }},{{ $pendapatanBulanan[2] }},{{ $pendapatanBulanan[3] }},{{ $pendapatanBulanan[4] }},{{ $pendapatanBulanan[5] }},{{ $pendapatanBulanan[6] }},{{ $pendapatanBulanan[7] }},{{ $pendapatanBulanan[8] }},{{ $pendapatanBulanan[9] }},{{ $pendapatanBulanan[10] }},{{ $pendapatanBulanan[11] }},{{ $pendapatanBulanan[12] }}],
            backgroundColor: 'rgba(233,69,96,0.8)',
            borderRadius: 6,
        }]
    },
    options: { plugins:{legend:{display:false}}, scales:{y:{ticks:{callback:v=>'Rp '+v.toLocaleString('id-ID')}}}}
});
new Chart(document.getElementById('chartPelanggan'), {
    type: 'line',
    data: {
        labels: bulan,
        datasets: [{
            label: 'Pelanggan Baru',
            data: [{{ $pelangganBaru[1] }},{{ $pelangganBaru[2] }},{{ $pelangganBaru[3] }},{{ $pelangganBaru[4] }},{{ $pelangganBaru[5] }},{{ $pelangganBaru[6] }},{{ $pelangganBaru[7] }},{{ $pelangganBaru[8] }},{{ $pelangganBaru[9] }},{{ $pelangganBaru[10] }},{{ $pelangganBaru[11] }},{{ $pelangganBaru[12] }}],
            borderColor: '#11998e', backgroundColor: 'rgba(17,153,142,0.1)', tension: 0.4, fill: true,
        }]
    },
    options: { plugins:{legend:{display:false}} }
});
const metodeData  = {!! json_encode($metodeBayar->pluck('jumlah')->toArray()) !!};
const metodeLabel = {!! json_encode($metodeBayar->pluck('metode')->map(fn($m)=>ucfirst($m))->toArray()) !!};
new Chart(document.getElementById('chartMetode'), {
    type: 'doughnut',
    data: {
        labels: metodeLabel.length ? metodeLabel : ['Belum ada'],
        datasets: [{ data: metodeData.length ? metodeData : [1], backgroundColor:['#e94560','#4facfe','#11998e','#f093fb'] }]
    },
    options: { plugins:{legend:{position:'bottom'}} }
});
</script>
</body>
</html>
