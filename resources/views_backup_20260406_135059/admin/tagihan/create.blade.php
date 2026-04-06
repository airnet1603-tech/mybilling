<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tagihan – ISP Billing</title>
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
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1045; }
        .sidebar-overlay.show { display: block; }

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

        /* ===== CARDS ===== */
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

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

        .info-label {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .card-section-title { font-size: 0.88rem; font-weight: 700; }

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
            <h5 class="fw-bold mb-0">Buat Tagihan Manual</h5>
            <small class="text-muted">Buat tagihan untuk pelanggan tertentu</small>
        </div>
        <a href="/admin/tagihan" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">

        <div class="col-md-4">
            <div class="profile-header">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar me-3"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div>
                        <div class="fw-bold fs-6">Tagihan Baru</div>
                        <div class="opacity-75 small">Buat tagihan manual</div>
                    </div>
                </div>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600;background:rgba(255,255,255,0.2);color:#fff;">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i> Manual
                </span>
            </div>

            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-user me-2 text-primary"></i>Info Pelanggan
                    </div>
                    <div id="infoEmpty" class="text-muted small">
                        <i class="fas fa-arrow-right me-1"></i> Pilih pelanggan untuk melihat info
                    </div>
                    <div id="infoDetail" class="d-none">
                        <div class="mb-2">
                            <div class="info-label">Nama Pelanggan</div>
                            <div class="small fw-semibold" id="info-nama">-</div>
                        </div>
                        <div class="mb-2">
                            <div class="info-label">Paket</div>
                            <div class="small" id="info-paket">-</div>
                        </div>
                        <div>
                            <div class="info-label">Harga Paket</div>
                            <div class="small fw-semibold text-success" id="info-harga">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-edit me-2 text-primary"></i>Detail Tagihan
                    </div>
                    <form method="POST" action="/admin/tagihan">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-label">Pilih Pelanggan <span class="text-danger">*</span></div>
                                <select name="pelanggan_id" class="form-select form-select-sm" required onchange="updateInfo(this)">
                                    <option value="">-- Pilih Pelanggan --</option>
                                    @foreach($pelanggans as $p)
                                    <option value="{{ $p->id }}"
                                            data-nama="{{ $p->nama }}"
                                            data-paket="{{ $p->paket->nama_paket ?? '-' }}"
                                            data-harga="{{ $p->paket->harga ?? 0 }}"
                                            {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->id_pelanggan }} - {{ $p->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></div>
                                <input type="date" name="tgl_jatuh_tempo" class="form-control form-control-sm"
                                       value="{{ old('tgl_jatuh_tempo', now()->addDays(10)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Diskon (Rp)</div>
                                <input type="number" name="diskon" class="form-control form-control-sm"
                                       value="{{ old('diskon', 0) }}" min="0">
                            </div>
                            <div class="col-12">
                                <div class="info-label">Catatan</div>
                                <input type="text" name="catatan" class="form-control form-control-sm"
                                       placeholder="Opsional" value="{{ old('catatan') }}">
                            </div>
                            <div class="col-12 pt-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-save me-1"></i> Buat Tagihan
                                </button>
                                <a href="/admin/tagihan" class="btn btn-secondary btn-sm ms-2">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
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
function updateInfo(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (sel.value) {
        document.getElementById('info-nama').textContent  = opt.dataset.nama;
        document.getElementById('info-paket').textContent = opt.dataset.paket;
        document.getElementById('info-harga').textContent = 'Rp ' + parseInt(opt.dataset.harga).toLocaleString('id-ID') + ' / bulan';
        document.getElementById('infoEmpty').classList.add('d-none');
        document.getElementById('infoDetail').classList.remove('d-none');
    } else {
        document.getElementById('infoEmpty').classList.remove('d-none');
        document.getElementById('infoDetail').classList.add('d-none');
    }
}

// ===== HAMBURGER MENU (sama seperti setting.blade) =====
sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});
</script>
</body>
</html>