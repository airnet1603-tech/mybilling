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
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar-brand { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }

        .sidebar-brand .brand-icon { width: 70px; height: 40px; background: rgba(233,69,96,0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; flex-shrink: 0; }

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

        /* ===== TOPBAR MOBILE ===== */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 54px;
            background: linear-gradient(90deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            z-index: 1060;
            align-items: center;
            padding: 0 14px;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .mobile-topbar .hamburger-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .mobile-topbar .hamburger-btn:hover { background: rgba(255,255,255,0.15); }
        .mobile-topbar .brand-title { color: #fff; font-weight: 700; font-size: 0.95rem; }

        /* ===== OVERLAY ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1045;
        }
        .sidebar-overlay.show { display: block; }

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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
        }
    </style>
</head>

<body>

@include('admin.partials.sidebar')
<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    {{-- TOPBAR --}}
    <div class="topbar d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold">Dashboard</h5>
            <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()->isAdmin())
            <a href="/admin/users" style="display:flex;align-items:center;gap:6px;color:#444;font-size:0.82rem;text-decoration:none;font-weight:500;">
                <i class="fas fa-user-cog" style="font-size:1rem;"></i> Kelola User
            </a>
            @else
            <span style="display:flex;align-items:center;gap:6px;color:#bbb;font-size:0.82rem;font-weight:500;cursor:not-allowed;">
                <i class="fas fa-user-cog" style="font-size:1rem;"></i> Kelola User
            </span>
            @endif
            <span style="color:#ccc;font-size:1rem;">|</span>
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
</body>
</html>