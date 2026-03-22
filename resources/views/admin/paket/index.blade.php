<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Internet – ISP Billing</title>
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
            width: 70px; height: 40px;
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

        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        /* ===== PAKET CARD ===== */
        .paket-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            transition: transform 0.2s;
        }

        .paket-card:hover { transform: translateY(-3px); }

        .paket-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px 12px 0 0;
            padding: 20px;
            color: white;
        }

        .paket-header.hotspot {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .speed-badge {
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }

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
        <div class="brand-icon"><img src="https://airnetps.my.id/app/icon/icon_airnet.png" style="height:38px;object-fit:contain;background:#ffffff;padding:2px 4px;border-radius:8px 0px 8px 0px;"></div>
        <div class="brand-text">
            <span class="brand-title">ISP Billing</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">
            <li class="nav-item"><a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="nav-item"><a href="/admin/pelanggan" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li class="nav-item"><a href="/admin/paket" class="nav-link active"><i class="fas fa-box"></i> Paket Internet</a></li>
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

<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Paket Internet</h5>
            <small class="text-muted">Kelola paket layanan internet</small>
        </div>
        <a href="/admin/paket/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> Tambah Paket
        </a>
    </div>

    {{-- ALERTS --}}
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

    {{-- PAKET GRID --}}
    <div class="row g-3">
        @forelse($pakets as $paket)
        <div class="col-md-4">
            <div class="paket-card card">
                <div class="paket-header {{ $paket->jenis == 'hotspot' ? 'hotspot' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $paket->nama_paket }}</h5>
                            <span class="speed-badge">
                                <i class="fas fa-arrow-down me-1"></i>{{ $paket->kecepatan_download }} Mbps
                                <i class="fas fa-arrow-up ms-2 me-1"></i>{{ $paket->kecepatan_upload }} Mbps
                            </span>
                        </div>
                        <span class="badge bg-white text-dark">{{ strtoupper($paket->jenis) }}</span>
                    </div>
                    <div class="mt-3">
                        <span class="fs-4 fw-bold">Rp {{ number_format($paket->harga, 0, ',', '.') }}</span>
                        <span class="opacity-75">/bulan</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4 border-end">
                            <div class="fw-bold text-primary">{{ $paket->masa_aktif }}</div>
                            <small class="text-muted">Hari</small>
                        </div>
                        <div class="col-4 border-end">
                            <div class="fw-bold text-success">{{ $paket->pelanggan()->count() }}</div>
                            <small class="text-muted">Pelanggan</small>
                        </div>
                        <div class="col-4">
                            @if($paket->is_active)
                                <div class="fw-bold text-success"><i class="fas fa-check-circle"></i></div>
                                <small class="text-muted">Aktif</small>
                            @else
                                <div class="fw-bold text-danger"><i class="fas fa-times-circle"></i></div>
                                <small class="text-muted">Nonaktif</small>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-server me-1"></i>
                            Radius Profile: <code>{{ $paket->radius_profile }}</code>
                        </small>
                    </div>
                    @if($paket->deskripsi)
                    <p class="text-muted small mb-3">{{ $paket->deskripsi }}</p>
                    @endif
                    <div class="d-flex gap-2">
                        <a href="/admin/paket/{{ $paket->id }}/edit" class="btn btn-warning btn-sm text-white flex-fill">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form method="POST" action="/admin/paket/{{ $paket->id }}" onsubmit="return confirm('Hapus paket ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada paket</h5>
                    <a href="/admin/paket/create" class="btn btn-primary btn-sm mt-2">
                        <i class="fas fa-plus me-1"></i> Tambah Paket Pertama
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($pakets->hasPages())
    <div class="mt-3">{{ $pakets->links() }}</div>
    @endif

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