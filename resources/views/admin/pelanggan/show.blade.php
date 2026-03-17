<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelanggan – ISP Billing</title>
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
        .sidebar-brand .brand-icon { width: 34px; height: 34px; background: rgba(233,69,96,0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; }
        .sidebar-brand .brand-title { color: #fff; font-weight: 700; font-size: 0.9rem; display: block; }
        .sidebar-brand .brand-sub { color: rgba(255,255,255,0.45); font-size: 0.7rem; }
        .sidebar-nav { padding: 8px 0; flex: 1; }
        .sidebar-nav .nav-link { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; transition: background 0.2s, color 0.2s; }
        .sidebar-nav .nav-link i { width: 16px; font-size: 0.82rem; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.25); color: #fff; }
        .sidebar-nav .nav-link.active { background: rgba(233,69,96,0.35); }
        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 14px; }
        .sidebar-nav .logout-btn { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; background: none; border: none; width: calc(100% - 16px); text-align: left; cursor: pointer; }
        .sidebar-nav .logout-btn:hover { background: rgba(233,69,96,0.25); color: #fff; }

        /* ===== TOPBAR MOBILE (sama seperti peta.blade) ===== */
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

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

        /* ===== CARDS ===== */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        /* ===== PROFILE HEADER ===== */
        .profile-header {
            background: linear-gradient(135deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            border-radius: 12px;
            padding: 20px;
            color: white;
            margin-bottom: 12px;
        }
        .avatar {
            width: 60px; height: 60px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        /* ===== STATUS BADGES ===== */
        .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-aktif    { background: #d4edda; color: #155724; }
        .badge-isolir   { background: #f8d7da; color: #721c24; }
        .badge-suspend  { background: #fff3cd; color: #856404; }
        .badge-nonaktif { background: #e2e3e5; color: #383d41; }

        /* ===== INFO LABEL ===== */
        .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
        .card-section-title { font-size: 0.88rem; font-weight: 700; }

        /* ===== STATUS BUTTONS ===== */
        .status-btn { display: flex; align-items: center; gap: 8px; padding: 9px 13px; border-radius: 8px; border: 2px solid transparent; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; background: #f8f9fa; color: #495057; width: 100%; text-align: left; }
        .status-btn:hover { transform: translateX(3px); }
        .status-btn.btn-aktif    { border-color: #28a745; color: #155724; background: #f0fff4; }
        .status-btn.btn-isolir   { border-color: #dc3545; color: #721c24; background: #fff5f5; }
        .status-btn.btn-suspend  { border-color: #ffc107; color: #856404; background: #fffdf0; }
        .status-btn.btn-nonaktif { border-color: #6c757d; color: #383d41; background: #f8f9fa; }
        .status-btn.active-status { box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-weight: 700; }

        /* ===== SYNC CARD ===== */
        .sync-card { background: linear-gradient(135deg, #1a1a2e, #0f3460); border-radius: 12px; padding: 16px; color: white; margin-bottom: 12px; }

        /* ===== RESPONSIVE MOBILE (sama seperti peta.blade) ===== */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
        }
    </style>
</head>
<body>

{{-- Topbar Mobile (hamburger) --}}
<div class="mobile-topbar">
    <button class="hamburger-btn" id="hamburgerBtn">
        <i class="fas fa-bars"></i>
    </button>
    <span class="brand-title">ISP Billing</span>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-wifi"></i></div>
        <div class="brand-text">
            <span class="brand-title">ISP Billing</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">
            <li><a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="/admin/pelanggan" class="nav-link active"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="/admin/paket" class="nav-link"><i class="fas fa-box"></i> Paket Internet</a></li>
            <li><a href="/admin/tagihan" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Tagihan</a></li>
            <li><a href="/admin/pembayaran" class="nav-link"><i class="fas fa-money-bill-wave"></i> Pembayaran</a></li>
            <li><a href="/admin/laporan" class="nav-link"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li><a href="/admin/mikrotik" class="nav-link"><i class="fas fa-network-wired"></i> Mikrotik</a></li>
        </ul>
        <div class="sidebar-divider"></div>
        <ul class="nav flex-column">
            <li><a href="/admin/setting" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
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
            <h5 class="fw-bold mb-0">Detail Pelanggan</h5>
            <small class="text-muted">{{ $pelanggan->id_pelanggan }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/pelanggan" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="/admin/pelanggan/{{ $pelanggan->id }}/edit" class="btn btn-warning btn-sm text-white">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
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

    <div class="row g-3">

        {{-- ===== KOLOM KIRI ===== --}}
        <div class="col-md-4">

            {{-- PROFIL --}}
            <div class="profile-header">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6">{{ $pelanggan->nama }}</div>
                        <div class="opacity-75 small">{{ $pelanggan->id_pelanggan }}</div>
                    </div>
                </div>
                <span class="badge-status badge-{{ $pelanggan->status }}">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                    {{ ucfirst($pelanggan->status) }}
                </span>
            </div>

            {{-- SYNC MIKROTIK --}}
            <div class="sync-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fw-bold small">
                        <i class="fas fa-sync-alt me-2"></i>Sinkronisasi Mikrotik
                    </div>
                    @if($pelanggan->router)
                        <span class="badge bg-success" style="font-size:0.65rem;">Router OK</span>
                    @else
                        <span class="badge bg-danger" style="font-size:0.65rem;">No Router</span>
                    @endif
                </div>
                <div class="opacity-75" style="font-size:0.75rem; margin-bottom:12px;">
                    Sync data PPPoE pelanggan ke router Mikrotik.
                </div>
                <form method="POST" action="{{ route('mikrotik.sync', $pelanggan->id) }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm w-100"
                            style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:8px;"
                            onclick="return confirm('Sinkronkan pelanggan ini ke Mikrotik?')">
                        <i class="fas fa-sync-alt me-1"></i> Sync ke Mikrotik
                    </button>
                </form>
            </div>

            {{-- UBAH STATUS --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-toggle-on me-2 text-primary"></i>Ubah Status
                    </div>
                    <div class="d-flex flex-column gap-2">

                        {{-- AKTIF --}}
                        <form method="POST" action="{{ route('mikrotik.aktifkan', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-aktif {{ $pelanggan->status == 'aktif' ? 'active-status' : '' }}"
                                    onclick="return confirm('Aktifkan pelanggan {{ addslashes($pelanggan->nama) }}?')">
                                <i class="fas fa-check-circle"></i> Aktif
                                @if($pelanggan->status == 'aktif')
                                    <span class="ms-auto badge bg-success" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        {{-- ISOLIR --}}
                        <form method="POST" action="{{ route('mikrotik.isolir', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-isolir {{ $pelanggan->status == 'isolir' ? 'active-status' : '' }}"
                                    onclick="return confirm('Isolir pelanggan {{ addslashes($pelanggan->nama) }}? Koneksi internet akan diputus.')">
                                <i class="fas fa-ban"></i> Isolir
                                @if($pelanggan->status == 'isolir')
                                    <span class="ms-auto badge bg-danger" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        {{-- SUSPEND --}}
                        <form method="POST" action="{{ route('mikrotik.suspend', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-suspend {{ $pelanggan->status == 'suspend' ? 'active-status' : '' }}"
                                    onclick="return confirm('Suspend pelanggan {{ addslashes($pelanggan->nama) }}? Koneksi internet akan diputus.')">
                                <i class="fas fa-pause-circle"></i> Suspend
                                @if($pelanggan->status == 'suspend')
                                    <span class="ms-auto badge bg-warning text-dark" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        {{-- NONAKTIF --}}
                        <form method="POST" action="{{ route('mikrotik.nonaktif', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-nonaktif {{ $pelanggan->status == 'nonaktif' ? 'active-status' : '' }}"
                                    onclick="return confirm('Nonaktifkan pelanggan {{ addslashes($pelanggan->nama) }}? Koneksi internet akan diputus.')">
                                <i class="fas fa-times-circle"></i> Nonaktif
                                @if($pelanggan->status == 'nonaktif')
                                    <span class="ms-auto badge bg-secondary" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            {{-- INFO KONTAK --}}
            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-address-card me-2 text-primary"></i>Info Kontak
                    </div>
                    <div class="mb-2">
                        <div class="info-label">No. HP</div>
                        <div class="small">{{ $pelanggan->no_hp ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Email</div>
                        <div class="small">{{ $pelanggan->email ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Alamat</div>
                        <div class="small">{{ $pelanggan->alamat ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Wilayah</div>
                        <div class="small">{{ $pelanggan->wilayah ?? '-' }}</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== KOLOM KANAN ===== --}}
        <div class="col-md-8">

            {{-- INFO LAYANAN --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-network-wired me-2 text-primary"></i>Info Layanan
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">Paket</div>
                            <div class="fw-semibold small">{{ $pelanggan->paket->nama_paket ?? '-' }}</div>
                            <small class="text-muted">
                                {{ $pelanggan->paket->kecepatan_download ?? 0 }} Mbps /
                                {{ $pelanggan->paket->kecepatan_upload ?? 0 }} Mbps
                            </small>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Harga Paket</div>
                            <div class="fw-semibold text-success small">
                                Rp {{ number_format($pelanggan->paket->harga ?? 0, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">per bulan</small>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Jenis Layanan</div>
                            <span class="badge bg-primary">{{ strtoupper($pelanggan->jenis_layanan ?? '-') }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Username PPPoE</div>
                            <code class="small">{{ $pelanggan->username }}</code>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">IP Address</div>
                            <div class="small">{{ $pelanggan->ip_address ?? 'Dinamis' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Router</div>
                            <div class="small">{{ $pelanggan->router->nama ?? $pelanggan->router_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tanggal Daftar</div>
                            <div class="small">{{ $pelanggan->tgl_daftar?->format('d/m/Y') ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Expired</div>
                            <div class="small {{ $pelanggan->tgl_expired && $pelanggan->tgl_expired < now() ? 'text-danger fw-bold' : '' }}">
                                {{ $pelanggan->tgl_expired?->format('d/m/Y') ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Sisa Hari</div>
                            @if($pelanggan->tgl_expired)
                                @php $sisa = now()->diffInDays($pelanggan->tgl_expired, false) @endphp
                                <div class="small {{ $sisa < 0 ? 'text-danger fw-bold' : ($sisa <= 5 ? 'text-warning fw-bold' : 'text-success') }}">
                                    {{ $sisa < 0 ? 'Expired '.abs($sisa).' hari lalu' : $sisa.' hari lagi' }}
                                </div>
                            @else
                                <div class="small text-muted">-</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT TAGIHAN --}}
            <div class="card">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <div class="card-section-title">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>Riwayat Tagihan
                    </div>
                    <span class="badge bg-secondary">{{ $pelanggan->tagihan->count() }} tagihan</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 small">No. Tagihan</th>
                                <th class="small">Periode</th>
                                <th class="small">Total</th>
                                <th class="small">Status</th>
                                <th class="small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggan->tagihan->sortByDesc('created_at')->take(10) as $t)
                            <tr>
                                <td class="ps-3"><code class="small">{{ $t->no_tagihan }}</code></td>
                                <td><small>{{ $t->periode_bulan?->isoFormat('MMM Y') ?? '-' }}</small></td>
                                <td>
                                    <div class="fw-semibold small">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                                    @if($t->denda > 0)
                                        <small class="text-danger">+denda Rp {{ number_format($t->denda, 0, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($t->status == 'paid')
                                        <span class="badge bg-success">Lunas</span>
                                    @elseif($t->status == 'overdue')
                                        <span class="badge bg-danger">Overdue</span>
                                    @elseif($t->status == 'unpaid')
                                        <span class="badge bg-warning text-dark">Unpaid</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="/admin/tagihan/{{ $t->id }}" class="btn btn-sm btn-info text-white py-0 px-2">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 small">
                                    <i class="fas fa-file-invoice fa-2x mb-2 d-block opacity-25"></i>
                                    Belum ada tagihan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== HAMBURGER MENU (sama seperti peta.blade) =====
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
</script>
</body>
</html>