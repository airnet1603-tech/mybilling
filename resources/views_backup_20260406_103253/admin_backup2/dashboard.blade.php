<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – ISP Billing</title>
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
        .sidebar-brand .brand-title { color: #fff; font-weight: 700; font-size: 0.9rem; display: block; }
        .sidebar-brand .brand-sub { color: rgba(255,255,255,0.45); font-size: 0.7rem; }

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

        .sidebar-nav .nav-link i { width: 16px; font-size: 0.82rem; flex-shrink: 0; }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.25); color: #fff; }
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.35); }

        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 14px; }

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

        /* ===== MAIN ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

        /* ===== TOPBAR ===== */
        .topbar {
            background: white;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            margin-bottom: 20px;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 18px 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        .stat-card.pelanggan  { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-card.aktif      { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-card.tagihan    { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-card.pendapatan { background: linear-gradient(135deg, #4facfe, #00f2fe); }

        .stat-card .icon { font-size: 2rem; opacity: 0.25; }
        .stat-card .stat-number { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label  { font-size: 0.8rem; opacity: 0.85; margin-top: 4px; }

        /* ===== CARDS ===== */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        /* ===== STATUS BADGES ===== */
        .badge-aktif    { background: #d4edda; color: #155724; }
        .badge-isolir   { background: #f8d7da; color: #721c24; }
        .badge-suspend  { background: #fff3cd; color: #856404; }
        .badge-nonaktif { background: #e2e3e5; color: #383d41; }
        .badge-status { font-size: 0.72rem; font-weight: 600; padding: 3px 9px; }

        /* ===== OVERDUE ITEM ===== */
        .overdue-item {
            padding: 10px 16px;
            border-bottom: 1px solid #f0f2f5;
            transition: background 0.15s;
        }
        .overdue-item:last-child { border-bottom: none; }
        .overdue-item:hover { background: #fafafa; }

        /* ===== MOBILE TOGGLE BUTTON (HAMBURGER MODERN) ===== */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 1060;
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--sidebar-bg-start), var(--accent));
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(233,69,96,0.4);
            transition: all 0.3s ease;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 10px;
        }

        .mobile-menu-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(233,69,96,0.5);
        }

        .mobile-menu-btn .bar {
            display: block;
            width: 20px;
            height: 2px;
            background: white;
            border-radius: 2px;
            transition: all 0.3s ease;
            transform-origin: center;
        }

        /* Animasi X saat sidebar terbuka */
        .mobile-menu-btn.is-open .bar:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        .mobile-menu-btn.is-open .bar:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
        .mobile-menu-btn.is-open .bar:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -230px;
                top: 0;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
            }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0 !important; padding: 15px; padding-top: 72px; }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
                backdrop-filter: blur(2px);
            }
            .sidebar-overlay.show { display: block; }
            .mobile-menu-btn { display: flex !important; }
        }
    </style>
</head>

<body>

{{-- TOMBOL HAMBURGER MODERN --}}
<button id="menuToggleBtn" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
</button>

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
            <li class="nav-item">
                <a href="/admin/dashboard" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pelanggan" class="nav-link">
                    <i class="fas fa-users"></i> Pelanggan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/paket" class="nav-link">
                    <i class="fas fa-box"></i> Paket Internet
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/tagihan" class="nav-link">
                    <i class="fas fa-file-invoice-dollar"></i> Tagihan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pembayaran" class="nav-link">
                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/laporan" class="nav-link">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/mikrotik" class="nav-link">
                    <i class="fas fa-network-wired"></i> Mikrotik
                </a>
            </li>
        </ul>

        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin/setting" class="nav-link">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            </li>
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

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    {{-- TOPBAR --}}
    <div class="topbar d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Dashboard</h5>
            <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white"
                 style="width:36px;height:36px;font-size:1rem;">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                <small class="text-muted">Administrator</small>
            </div>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card pelanggan">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $totalPelanggan }}</div>
                        <div class="stat-label">Total Pelanggan</div>
                    </div>
                    <i class="fas fa-users icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card aktif">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $pelangganAktif }}</div>
                        <div class="stat-label">Pelanggan Aktif</div>
                    </div>
                    <i class="fas fa-check-circle icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card tagihan">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number">{{ $tagihanUnpaid }}</div>
                        <div class="stat-label">Tagihan Belum Bayar</div>
                    </div>
                    <i class="fas fa-file-invoice icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card pendapatan">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold" style="font-size:1.1rem;line-height:1.2;">
                            Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                        </div>
                        <div class="stat-label">Pendapatan Bulan Ini</div>
                    </div>
                    <i class="fas fa-wallet icon"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL + OVERDUE --}}
    <div class="row g-3">

        {{-- PELANGGAN TERBARU --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-users me-2 text-primary"></i>Pelanggan Terbaru
                    </h6>
                    <a href="/admin/pelanggan" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.75rem;">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 small">ID</th>
                                <th class="small">Nama</th>
                                <th class="small">Paket</th>
                                <th class="small">Status</th>
                                <th class="small">Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelangganTerbaru as $p)
                            <tr>
                                <td class="ps-3">
                                    <small class="text-muted">{{ $p->id_pelanggan }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold small">{{ $p->nama }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary" style="font-size:0.7rem;">
                                        {{ $p->paket->nama_paket ?? '-' }}
                                    </span>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 small">
                                    Belum ada pelanggan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAGIHAN OVERDUE --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white border-0 pt-3 pb-2">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-exclamation-circle me-2 text-danger"></i>Tagihan Overdue
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($tagihanOverdue as $t)
                    <div class="overdue-item">
                        <div class="fw-semibold small">{{ $t->pelanggan->nama ?? '-' }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-danger fw-semibold">
                                Rp {{ number_format($t->total, 0, ',', '.') }}
                            </small>
                            <small class="text-muted">{{ $t->tgl_jatuh_tempo?->format('d/m/Y') }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                        <small>Semua tagihan lunas!</small>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const btn     = document.getElementById("menuToggleBtn");

    sidebar.classList.toggle("show");
    overlay.classList.toggle("show");
    btn.classList.toggle("is-open"); // hamburger ? X
}

document.addEventListener("touchstart", e => window._touchStartX = e.touches[0].clientX);
document.addEventListener("touchend", e => {
    const endX = e.changedTouches[0].clientX;
    if (window._touchStartX < 30 && endX - window._touchStartX > 70) toggleSidebar();
    if (window._touchStartX > 200 && window._touchStartX - endX > 70) {
        document.querySelector(".sidebar").classList.remove("show");
        document.getElementById("sidebarOverlay").classList.remove("show");
        document.getElementById("menuToggleBtn").classList.remove("is-open");
    }
});
</script>
</body>
</html>