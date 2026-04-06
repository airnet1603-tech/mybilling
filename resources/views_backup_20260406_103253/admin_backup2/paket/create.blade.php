<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Paket – ISP Billing</title>
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

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

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

        /* ===== INFO LABEL ===== */
        .info-label {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .card-section-title { font-size: 0.88rem; font-weight: 700; }

        /* ===== PREVIEW ===== */
        .preview-speed-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 0.78rem;
            font-weight: 600;
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
            <h5 class="fw-bold mb-0">Tambah Paket Internet</h5>
            <small class="text-muted">Buat paket layanan internet baru</small>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/paket" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ALERT --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">

        {{-- ===== KOLOM KIRI ===== --}}
        <div class="col-md-4">

            {{-- PREVIEW HEADER --}}
            <div class="profile-header">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar me-3">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6" id="prev-nama">Nama Paket</div>
                        <div class="opacity-75 small" id="prev-jenis">PPPoE</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="preview-speed-badge">
                        <i class="fas fa-arrow-down fa-xs"></i>
                        <span id="prev-dl">0</span> Mbps
                    </span>
                    <span class="preview-speed-badge">
                        <i class="fas fa-arrow-up fa-xs"></i>
                        <span id="prev-ul">0</span> Mbps
                    </span>
                </div>
            </div>

            {{-- PREVIEW DETAIL --}}
            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-eye me-2 text-primary"></i>Preview Paket
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Harga</div>
                        <div class="small fw-semibold text-success">Rp <span id="prev-harga">0</span> / bulan</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Masa Aktif</div>
                        <div class="small"><span id="prev-masa">30</span> hari</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Radius Profile</div>
                        <code class="small" id="prev-radius">-</code>
                    </div>
                    <div>
                        <div class="info-label">Status</div>
                        <span id="prev-status" class="badge bg-success">Aktif</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== KOLOM KANAN ===== --}}
        <div class="col-md-8">

            <form method="POST" action="/admin/paket" id="formPaket">
                @csrf

                {{-- INFO PAKET --}}
                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="card-section-title mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Informasi Paket
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="info-label">Nama Paket <span class="text-danger">*</span></div>
                                <input type="text" name="nama_paket" id="nama_paket"
                                       class="form-control form-control-sm @error('nama_paket') is-invalid @enderror"
                                       value="{{ old('nama_paket') }}"
                                       placeholder="Contoh: Paket 20 Mbps" required>
                                @error('nama_paket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Jenis <span class="text-danger">*</span></div>
                                <select name="jenis" class="form-select form-select-sm" required>
                                    <option value="pppoe"   {{ old('jenis')=='pppoe'   ? 'selected':'' }}>PPPoE</option>
                                    <option value="hotspot" {{ old('jenis')=='hotspot' ? 'selected':'' }}>Hotspot</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Harga per Bulan <span class="text-danger">*</span></div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga" id="harga"
                                           class="form-control @error('harga') is-invalid @enderror"
                                           value="{{ old('harga') }}"
                                           placeholder="150000" required min="0">
                                    @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Masa Aktif (hari) <span class="text-danger">*</span></div>
                                <input type="number" name="masa_aktif"
                                       class="form-control form-control-sm @error('masa_aktif') is-invalid @enderror"
                                       value="{{ old('masa_aktif', 30) }}" required min="1">
                                @error('masa_aktif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KECEPATAN & RADIUS --}}
                <div class="card">
                    <div class="card-body py-3">
                        <div class="card-section-title mb-3">
                            <i class="fas fa-tachometer-alt me-2 text-primary"></i>Kecepatan & Radius
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Download (Mbps) <span class="text-danger">*</span></div>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="kecepatan_download" id="dl"
                                           class="form-control @error('kecepatan_download') is-invalid @enderror"
                                           value="{{ old('kecepatan_download') }}"
                                           placeholder="20" required min="1">
                                    <span class="input-group-text">Mbps</span>
                                    @error('kecepatan_download')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Upload (Mbps) <span class="text-danger">*</span></div>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="kecepatan_upload" id="ul"
                                           class="form-control @error('kecepatan_upload') is-invalid @enderror"
                                           value="{{ old('kecepatan_upload') }}"
                                           placeholder="10" required min="1">
                                    <span class="input-group-text">Mbps</span>
                                    @error('kecepatan_upload')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-label">Radius Profile Name <span class="text-danger">*</span></div>
                                <input type="text" name="radius_profile" id="radius_profile"
                                       class="form-control form-control-sm @error('radius_profile') is-invalid @enderror"
                                       value="{{ old('radius_profile') }}"
                                       placeholder="Contoh: paket-20mbps" required>
                                <div class="form-text small text-muted">Harus sama dengan nama profile di FreeRADIUS / MikroTik</div>
                                @error('radius_profile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <div class="info-label">Deskripsi</div>
                                <textarea name="deskripsi" class="form-control form-control-sm" rows="2"
                                          placeholder="Keterangan tambahan paket...">{{ old('deskripsi') }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active"
                                           id="is_active" {{ old('is_active', true) ? 'checked':'' }}>
                                    <label class="form-check-label small fw-semibold" for="is_active">
                                        Paket Aktif (bisa dipilih pelanggan)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Simpan Paket
                    </button>
                    <a href="/admin/paket" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function updatePreview() {
        document.getElementById('prev-nama').textContent =
            document.getElementById('nama_paket').value || 'Nama Paket';

        const jenis = document.querySelector('[name="jenis"]').value;
        document.getElementById('prev-jenis').textContent = jenis === 'pppoe' ? 'PPPoE' : 'Hotspot';

        document.getElementById('prev-dl').textContent = document.getElementById('dl').value || '0';
        document.getElementById('prev-ul').textContent = document.getElementById('ul').value || '0';

        const harga = parseInt(document.getElementById('harga').value) || 0;
        document.getElementById('prev-harga').textContent = harga.toLocaleString('id-ID');

        document.getElementById('prev-masa').textContent =
            document.querySelector('[name="masa_aktif"]').value || '30';

        const rp = document.getElementById('radius_profile').value;
        document.getElementById('prev-radius').textContent = rp || '-';

        const aktif = document.getElementById('is_active').checked;
        const el = document.getElementById('prev-status');
        el.textContent = aktif ? 'Aktif' : 'Nonaktif';
        el.className   = aktif ? 'badge bg-success' : 'badge bg-secondary';
    }

    document.getElementById('nama_paket').addEventListener('input', updatePreview);
    document.getElementById('harga').addEventListener('input', updatePreview);
    document.getElementById('dl').addEventListener('input', updatePreview);
    document.getElementById('ul').addEventListener('input', updatePreview);
    document.getElementById('radius_profile').addEventListener('input', updatePreview);
    document.querySelector('[name="jenis"]').addEventListener('change', updatePreview);
    document.querySelector('[name="masa_aktif"]').addEventListener('input', updatePreview);
    document.getElementById('is_active').addEventListener('change', updatePreview);

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