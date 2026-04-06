<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran – ISP Billing</title>
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
            z-index: 1060;
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

        .stat-card {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

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
@include('admin.partials.sidebar')
<!-- ===== MAIN CONTENT ===== -->
<div class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Riwayat Pembayaran</h5>
            <small class="text-muted">{{ now()->isoFormat('MMMM Y') }}</small>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#4facfe,#00f2fe)">
                <div class="fs-5 fw-bold">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</div>
                <div class="opacity-75">Total Bulan Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#667eea,#764ba2)">
                <div class="fs-3 fw-bold">{{ $totalTransaksi }}</div>
                <div class="opacity-75">Transaksi Bulan Ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#11998e,#38ef7d)">
                <div class="fs-5 fw-bold">Rp {{ number_format($totalCash, 0, ',', '.') }}</div>
                <div class="opacity-75">Total Cash</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(135deg,#f093fb,#f5576c)">
                <div class="fs-5 fw-bold">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                <div class="opacity-75">Total Transfer</div>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Cari nama pelanggan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="metode" class="form-select form-select-sm">
                        <option value="">Semua Metode</option>
                        <option value="cash"     {{ request('metode')=='cash'     ? 'selected':'' }}>Cash</option>
                        <option value="transfer" {{ request('metode')=='transfer' ? 'selected':'' }}>Transfer</option>
                        <option value="midtrans" {{ request('metode')=='midtrans' ? 'selected':'' }}>Midtrans</option>
                        <option value="xendit"   {{ request('metode')=='xendit'   ? 'selected':'' }}>Xendit</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="month" name="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                </div>
                <div class="col-auto">
                    <a href="/admin/pembayaran" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small">No. Pembayaran</th>
                            <th class="small">Pelanggan</th>
                            <th class="small">No. Tagihan</th>
                            <th class="small">Jumlah</th>
                            <th class="small">Metode</th>
                            <th class="small">Tanggal</th>
                            <th class="small">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $p)
                        <tr>
                            <td><code class="small">{{ $p->no_pembayaran }}</code></td>
                            <td>
                                <div class="fw-semibold small">{{ $p->pelanggan->nama ?? '-' }}</div>
                                <small class="text-muted">{{ $p->pelanggan->id_pelanggan ?? '' }}</small>
                            </td>
                            <td>
                                <a href="/admin/tagihan/{{ $p->tagihan_id }}" class="text-decoration-none">
                                    <code class="small">{{ $p->tagihan->no_tagihan ?? '-' }}</code>
                                </a>
                            </td>
                            <td class="fw-bold text-success small">
                                Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($p->metode == 'cash')
                                    <span class="badge bg-success">Cash</span>
                                @elseif($p->metode == 'transfer')
                                    <span class="badge bg-primary">Transfer</span>
                                @elseif($p->metode == 'midtrans')
                                    <span class="badge bg-info">Midtrans</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($p->metode) }}</span>
                                @endif
                            </td>
                            <td><small>{{ $p->created_at->format('d/m/Y H:i') }}</small></td>
                            <td><small class="text-muted">{{ $p->catatan ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-money-bill-wave fa-3x mb-3 d-block opacity-25"></i>
                                Belum ada riwayat pembayaran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($pembayarans->hasPages())
        <div class="card-footer bg-white">
            {{ $pembayarans->appends(request()->query())->links() }}
        </div>
        @endif
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
// ===== HAMBURGER MENU (sama seperti peta.blade) =====
sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});
</script>
</body>
</html>