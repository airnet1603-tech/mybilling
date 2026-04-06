<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tagihan – ISP Billing</title>
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

        /* ===== CARDS ===== */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
        .card-section-title { font-size: 0.88rem; font-weight: 700; }

        /* ===== INVOICE HEADER ===== */
        .invoice-header {
            background: linear-gradient(135deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            border-radius: 12px 12px 0 0;
            padding: 24px;
            color: white;
        }

        /* ===== STATUS BADGES ===== */
        .badge-paid      { background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-unpaid    { background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-overdue   { background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .badge-cancelled { background: #e2e3e5; color: #383d41; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }

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

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Detail Tagihan</h5>
            <small class="text-muted">{{ $tagihan->no_tagihan }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/tagihan" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">

        {{-- ===== KOLOM KIRI – INVOICE ===== --}}
        <div class="col-md-8">
            <div class="card">
                <div class="invoice-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="fw-bold mb-1">INVOICE</h4>
                            <div class="opacity-75 small">{{ $tagihan->no_tagihan }}</div>
                            <div class="mt-2">
                                <span class="badge-{{ $tagihan->status }} fw-semibold">
                                    {{ strtoupper($tagihan->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <i class="fas fa-wifi fa-3x opacity-50"></i>
                            <div class="mt-1 fw-bold small">ISP BILLING</div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- TAGIHAN KEPADA --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-label mb-1">Tagihan Kepada</div>
                            <div class="fw-bold fs-6">{{ $tagihan->pelanggan->nama }}</div>
                            <div class="text-muted small">{{ $tagihan->pelanggan->id_pelanggan }}</div>
                            <div class="small">{{ $tagihan->pelanggan->no_hp }}</div>
                            <div class="text-muted small">{{ $tagihan->pelanggan->alamat }}</div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="mb-1 small">
                                <span class="text-muted">Tgl Tagihan:</span>
                                <strong>{{ $tagihan->tgl_tagihan?->format('d/m/Y') }}</strong>
                            </div>
                            <div class="mb-1 small">
                                <span class="text-muted">Jatuh Tempo:</span>
                                <strong class="{{ $tagihan->tgl_jatuh_tempo < now() && $tagihan->status != 'paid' ? 'text-danger' : '' }}">
                                    {{ $tagihan->tgl_jatuh_tempo?->format('d/m/Y') }}
                                </strong>
                            </div>
                            <div class="mb-1 small">
                                <span class="text-muted">Periode:</span>
                                <strong>{{ $tagihan->periode_bulan?->isoFormat('MMMM Y') }}</strong>
                            </div>
                            @if($tagihan->tgl_bayar)
                            <div class="small">
                                <span class="text-muted">Tgl Bayar:</span>
                                <strong class="text-success">{{ $tagihan->tgl_bayar?->format('d/m/Y') }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- TABEL ITEM --}}
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th class="small">Deskripsi</th>
                                <th class="text-end small">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-semibold small">Layanan Internet – {{ $tagihan->paket->nama_paket ?? '-' }}</div>
                                    <small class="text-muted">
                                        {{ $tagihan->paket->kecepatan_download ?? 0 }} Mbps /
                                        {{ $tagihan->paket->kecepatan_upload ?? 0 }} Mbps &bull;
                                        Periode {{ $tagihan->periode_bulan?->isoFormat('MMMM Y') }}
                                    </small>
                                </td>
                                <td class="text-end small">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            @if($tagihan->diskon > 0)
                            <tr>
                                <td class="text-success small">Diskon</td>
                                <td class="text-end text-success small">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($tagihan->denda > 0)
                            <tr>
                                <td class="text-danger small">Denda Keterlambatan</td>
                                <td class="text-end text-danger small">+ Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="table-dark">
                                <th class="small">TOTAL</th>
                                <th class="text-end fs-6">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    @if($tagihan->catatan)
                    <div class="alert alert-light small">
                        <i class="fas fa-sticky-note me-2"></i>{{ $tagihan->catatan }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== KOLOM KANAN ===== --}}
        <div class="col-md-4">

            @if($tagihan->status !== 'paid')
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-check-circle me-2 text-success"></i>Konfirmasi Pembayaran
                    </div>
                    <form method="POST" action="/admin/tagihan/{{ $tagihan->id }}/bayar">
                        @csrf
                        <div class="mb-2">
                            <div class="info-label">Metode Bayar</div>
                            <select name="metode_bayar" class="form-select form-select-sm" required>
                                <option value="cash">?? Cash / Tunai</option>
                                <option value="transfer">?? Transfer Bank</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Catatan</div>
                            <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Opsional">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-check me-1"></i> Tandai Lunas
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="card mb-3">
                <div class="card-body text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                    <div class="fw-bold text-success">TAGIHAN LUNAS</div>
                    <div class="text-muted small mt-1">{{ $tagihan->tgl_bayar?->format('d/m/Y H:i') }}</div>
                    <span class="badge bg-success mt-2">{{ strtoupper($tagihan->metode_bayar ?? '-') }}</span>
                </div>
            </div>
            @endif

            {{-- INFO PELANGGAN --}}
            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-user me-2 text-primary"></i>Info Pelanggan
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Status</div>
                        <span class="badge bg-{{ $tagihan->pelanggan->status == 'aktif' ? 'success' : 'danger' }}">
                            {{ ucfirst($tagihan->pelanggan->status) }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Username</div>
                        <code class="small">{{ $tagihan->pelanggan->username }}</code>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Router</div>
                        <div class="small">{{ $tagihan->pelanggan->router_name ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">IP Address</div>
                        <div class="small">{{ $tagihan->pelanggan->ip_address ?? 'Dinamis' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Expired</div>
                        <div class="small">{{ $tagihan->pelanggan->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    <a href="/admin/pelanggan/{{ $tagihan->pelanggan->id }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-user me-1"></i> Lihat Profil Pelanggan
                    </a>
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
<script>
// ===== HAMBURGER MENU (sama seperti setting.blade) =====
sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});
</script>
</body>
</html>