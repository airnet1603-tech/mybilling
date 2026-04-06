<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan � ISP Billing</title>
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

        .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 4px; }

        .section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #6c757d; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid #f0f2f5; }

        .nav-tabs .nav-link { color: #6c757d; border: none; padding: 10px 20px; font-size: 0.88rem; }
        .nav-tabs .nav-link.active { color: var(--accent); border-bottom: 2px solid var(--accent); font-weight: 600; background: none; }

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
            <h5 class="fw-bold mb-0">Pengaturan Sistem</h5>
            <small class="text-muted">Konfigurasi sistem billing ISP</small>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="/admin/setting">
        @csrf
        @method('PUT')

        <ul class="nav nav-tabs mb-4" id="settingTab">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#umum">
                    <i class="fas fa-building me-1"></i> Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#billing">
                    <i class="fas fa-file-invoice me-1"></i> Billing
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#whatsapp">
                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/setting/payment-gateway">
                    <i class="fas fa-credit-card me-1"></i> Payment Gateway
                </a>
            </li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane fade show active" id="umum">
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-building me-1"></i> Informasi ISP</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Nama ISP</div>
                                <input type="text" name="nama_isp" class="form-control form-control-sm"
                                       value="{{ $settings['nama_isp'] ?? '' }}" placeholder="Contoh: AirNet ISP">
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">No. HP Admin</div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" name="no_admin" class="form-control"
                                           value="{{ $settings['no_admin'] ?? '' }}" placeholder="812xxxxxxxx">
                                </div>
                                <div class="form-text small text-muted">Untuk pemberitahuan & kontak pelanggan</div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Alamat ISP</div>
                                <textarea name="alamat_isp" class="form-control form-control-sm" rows="2"
                                          placeholder="Alamat kantor ISP">{{ $settings['alamat_isp'] ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Info Pembayaran</div>
                                <textarea name="info_pembayaran" class="form-control form-control-sm" rows="3"
                                          placeholder="Contoh: BCA 1234567890 a/n Nama ISP">{{ $settings['info_pembayaran'] ?? '' }}</textarea>
                                <div class="form-text small text-muted">Ditampilkan di pesan WA tagihan pelanggan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="billing">
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-file-invoice me-1"></i> Pengaturan Billing Otomatis</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Tanggal Jatuh Tempo</div>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="hari_jatuh_tempo" class="form-control"
                                           value="{{ $settings['hari_jatuh_tempo'] ?? '10' }}" min="1" max="28">
                                    <span class="input-group-text">setiap bulan</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Denda Keterlambatan</div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="denda_terlambat" class="form-control"
                                           value="{{ $settings['denda_terlambat'] ?? '10000' }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Hari Sebelum Isolir</div>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="hari_isolir" class="form-control"
                                           value="{{ $settings['hari_isolir'] ?? '3' }}" min="1">
                                    <span class="input-group-text">hari setelah JT</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info small">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Cara kerja auto billing:</strong> Setiap tanggal 1 sistem otomatis buat tagihan semua pelanggan aktif.
                                    Setiap hari sistem cek jatuh tempo dan isolir pelanggan yang belum bayar.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="whatsapp">
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fab fa-whatsapp me-1"></i> Konfigurasi WhatsApp Gateway</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Gateway WA</div>
                                <select name="wa_gateway" class="form-select form-select-sm">
                                    <option value="fonnte"   {{ ($settings['wa_gateway'] ?? '') == 'fonnte'   ? 'selected':'' }}>Fonnte</option>
                                    <option value="wablas"   {{ ($settings['wa_gateway'] ?? '') == 'wablas'   ? 'selected':'' }}>WABLAS</option>
                                    <option value="ultramsg" {{ ($settings['wa_gateway'] ?? '') == 'ultramsg' ? 'selected':'' }}>UltraMsg</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Token / API Key</div>
                                <input type="text" name="wa_token" class="form-control form-control-sm"
                                       value="{{ $settings['wa_token'] ?? '' }}" placeholder="Token dari provider gateway">
                            </div>
                            <div class="col-12">
                                <div class="info-label">Base URL (khusus WABLAS)</div>
                                <input type="text" name="wa_base_url" class="form-control form-control-sm"
                                       value="{{ $settings['wa_base_url'] ?? '' }}" placeholder="https://app.wablas.com">
                            </div>
                            <div class="col-12">
                                <div class="alert alert-warning small">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>WA Otomatis dikirim saat:</strong> Tagihan dibuat, H-3 & H-1 sebelum jatuh tempo, saat isolir, dan konfirmasi pembayaran.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary btn-sm px-4">
                <i class="fas fa-save me-1"></i> Simpan Semua Pengaturan
            </button>
        </div>

    </form>
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