<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan – ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* FIX: :root ditutup dengan benar, --sidebar-width tidak duplikat */
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
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand .brand-icon {
            width: 34px; height: 34px;
            background: rgba(233,69,96,0.25);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--accent);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text { line-height: 1.2; }

        .sidebar-brand .brand-title {
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            display: block;
        }

        .sidebar-brand .brand-sub {
            color: rgba(255,255,255,0.45);
            font-size: 0.7rem;
        }

        .sidebar-nav { padding: 8px 0; flex: 1; }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.65);
            padding: 8px 14px;
            border-radius: 7px;
            margin: 1px 8px;
            font-size: 0.83rem;
            display: flex;
            align-items: center;
            gap: 9px;
            transition: background 0.2s, color 0.2s;
            white-space: nowrap;
        }

        .sidebar-nav .nav-link i {
            width: 16px;
            font-size: 0.82rem;
            flex-shrink: 0;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.25); color: #fff; }
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.35); }

        .sidebar-divider {
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 6px 14px;
        }

        .sidebar-nav .logout-btn {
            color: rgba(255,255,255,0.65);
            padding: 8px 14px;
            border-radius: 7px;
            margin: 1px 8px;
            font-size: 0.83rem;
            display: flex;
            align-items: center;
            gap: 9px;
            background: none;
            border: none;
            width: calc(100% - 16px);
            text-align: left;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .sidebar-nav .logout-btn:hover { background: rgba(233,69,96,0.25); color: #fff; }

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card-section-title {
            font-size: 0.88rem;
            font-weight: 700;
        }

        /* FIX: Tambah style responsive seperti index.blade.php */
        .mobile-menu-btn { display: none; }

        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -230px; top: 0; height: 100vh; z-index: 1050; transition: left 0.3s ease; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0 !important; padding: 15px; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
            .sidebar-overlay.show { display: block; }
            .mobile-menu-btn { display: block !important; }
        }
    </style>
</head>
<body>

{{-- FIX: Mobile menu button sesuai index.blade.php --}}
<a href="#" id="menuToggleBtn" class="mobile-menu-btn" onclick="toggleSidebar();return false;"
   style="position:fixed;top:50%;left:0;transform:translateY(-50%);z-index:9999;background:rgba(233,69,96,0.9);color:white;border-radius:0 12px 12px 0;padding:12px 8px;font-size:22px;text-decoration:none;">&#9654;</a>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-wifi"></i></div>
        <div class="brand-text">
            <span class="brand-title">ISP Billing</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">
            <li class="nav-item"><a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a href="/admin/pelanggan" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li class="nav-item"><a href="/admin/paket" class="nav-link"><i class="fas fa-box"></i> Paket Internet</a></li>
            <li class="nav-item"><a href="/admin/tagihan" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Tagihan</a></li>
            <li class="nav-item"><a href="/admin/pembayaran" class="nav-link"><i class="fas fa-money-bill-wave"></i> Pembayaran</a></li>
            <li class="nav-item"><a href="/admin/laporan" class="nav-link active"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li class="nav-item"><a href="/admin/mikrotik" class="nav-link"><i class="fas fa-network-wired"></i> Mikrotik</a></li>
        </ul>

        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/setting" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    {{-- FIX: </button> bukan </a> --}}
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="width:16px;font-size:0.82rem;"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<!-- ===== MAIN CONTENT ===== -->
{{-- FIX: Wrapper div main-content yang hilang --}}
<div class="main-content">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Laporan & Grafik</h5>
            <small class="text-muted">Analisis pendapatan tahun {{ $tahun }}</small>
        </div>
        <form method="GET" class="d-flex gap-2">
            <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#4facfe,#00f2fe)">
                <div class="opacity-75 small">Total Pendapatan {{ $tahun }}</div>
                <div class="fs-5 fw-bold">Rp {{ number_format($totalPendapatanTahun, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#11998e,#38ef7d)">
                <div class="opacity-75 small">Tagihan Lunas</div>
                <div class="fs-3 fw-bold">{{ $totalLunasTahun }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#f093fb,#f5576c)">
                <div class="opacity-75 small">Tagihan Belum Bayar</div>
                <div class="fs-3 fw-bold">{{ $totalUnpaidTahun }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#667eea,#764ba2)">
                <div class="opacity-75 small">Total Tagihan</div>
                <div class="fs-3 fw-bold">{{ $totalTagihanTahun }}</div>
            </div>
        </div>
    </div>

    {{-- GRAFIK PENDAPATAN & METODE --}}
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Grafik Pendapatan Bulanan {{ $tahun }}
                    </div>
                    <canvas id="chartPendapatan" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>Metode Pembayaran
                    </div>
                    <canvas id="chartMetode" height="200"></canvas>
                    <div class="mt-3">
                        @foreach($metodeBayar as $m)
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">{{ ucfirst($m->metode) }}</span>
                            <span class="fw-bold small">Rp {{ number_format($m->total, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK PELANGGAN BARU & PENDAPATAN PER PAKET --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-user-plus me-2 text-success"></i>Pelanggan Baru per Bulan
                    </div>
                    <canvas id="chartPelanggan" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-box me-2 text-warning"></i>Pendapatan per Paket
                    </div>
                    @forelse($pendapatanPerPaket as $p)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold small">{{ $p->paket->nama_paket ?? 'Unknown' }}</span>
                            <span class="text-success fw-bold small">Rp {{ number_format($p->total_pendapatan, 0, ',', '.') }}</span>
                        </div>
                        <div class="mb-1">
                            <small class="text-muted">{{ $p->jumlah }} transaksi</small>
                        </div>
                        @php
                            $persen = $totalPendapatanTahun > 0 ? ($p->total_pendapatan / $totalPendapatanTahun * 100) : 0;
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ $persen }}%"></div>
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
                <div class="card-section-title">
                    <i class="fas fa-list me-2 text-primary"></i>
                    Tagihan Bulan {{ now()->isoFormat('MMMM Y') }} (10 terbaru)
                </div>
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
                        <td class="fw-bold small">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                        <td>
                            @if($t->status == 'paid')
                                <span class="badge bg-success">Lunas</span>
                            @elseif($t->status == 'overdue')
                                <span class="badge bg-danger">Overdue</span>
                            @else
                                <span class="badge bg-warning text-dark">Unpaid</span>
                            @endif
                        </td>
                        <td><small>{{ $t->tgl_bayar?->format('d/m/Y') ?? '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4 small">Belum ada tagihan bulan ini</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- end .main-content --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

// Grafik Pendapatan
new Chart(document.getElementById('chartPendapatan'), {
    type: 'bar',
    data: {
        labels: bulan,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: [
                {{ $pendapatanBulanan[1] }}, {{ $pendapatanBulanan[2] }},
                {{ $pendapatanBulanan[3] }}, {{ $pendapatanBulanan[4] }},
                {{ $pendapatanBulanan[5] }}, {{ $pendapatanBulanan[6] }},
                {{ $pendapatanBulanan[7] }}, {{ $pendapatanBulanan[8] }},
                {{ $pendapatanBulanan[9] }}, {{ $pendapatanBulanan[10] }},
                {{ $pendapatanBulanan[11] }}, {{ $pendapatanBulanan[12] }}
            ],
            backgroundColor: 'rgba(233,69,96,0.8)',
            borderRadius: 6,
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } }
        }
    }
});

// Grafik Pelanggan Baru
new Chart(document.getElementById('chartPelanggan'), {
    type: 'line',
    data: {
        labels: bulan,
        datasets: [{
            label: 'Pelanggan Baru',
            data: [
                {{ $pelangganBaru[1] }}, {{ $pelangganBaru[2] }},
                {{ $pelangganBaru[3] }}, {{ $pelangganBaru[4] }},
                {{ $pelangganBaru[5] }}, {{ $pelangganBaru[6] }},
                {{ $pelangganBaru[7] }}, {{ $pelangganBaru[8] }},
                {{ $pelangganBaru[9] }}, {{ $pelangganBaru[10] }},
                {{ $pelangganBaru[11] }}, {{ $pelangganBaru[12] }}
            ],
            borderColor: '#11998e',
            backgroundColor: 'rgba(17,153,142,0.1)',
            tension: 0.4,
            fill: true,
        }]
    },
    options: { plugins: { legend: { display: false } } }
});

// Grafik Metode Bayar
const metodeData  = {!! json_encode($metodeBayar->pluck('jumlah')->toArray()) !!};
const metodeLabel = {!! json_encode($metodeBayar->pluck('metode')->map(fn($m) => ucfirst($m))->toArray()) !!};
new Chart(document.getElementById('chartMetode'), {
    type: 'doughnut',
    data: {
        labels: metodeLabel.length ? metodeLabel : ['Belum ada'],
        datasets: [{
            data: metodeData.length ? metodeData : [1],
            backgroundColor: ['#e94560','#4facfe','#11998e','#f093fb'],
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});

// FIX: Event listener touch tidak duplikat, toggleSidebar bersih
function toggleSidebar() {
    document.querySelector(".sidebar").classList.toggle("show");
    document.getElementById("sidebarOverlay").classList.toggle("show");
}

document.addEventListener("touchstart", e => window._touchStartX = e.touches[0].clientX);
document.addEventListener("touchend", e => {
    const endX = e.changedTouches[0].clientX;
    if (window._touchStartX < 30 && endX - window._touchStartX > 70) toggleSidebar();
    if (window._touchStartX > 200 && window._touchStartX - endX > 70) {
        document.querySelector(".sidebar").classList.remove("show");
        document.getElementById("sidebarOverlay").classList.remove("show");
    }
});
</script>
</body>
</html>