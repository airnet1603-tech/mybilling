<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggan – ISP Billing</title>
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

        /* ===== MAIN ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        .section-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6c757d;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f2f5;
        }

        .paket-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }

        .paket-option:hover { border-color: #adb5bd; }

        .paket-option.selected {
            border-color: #0d6efd;
            background: #f0f5ff;
        }

        .paket-option input[type="radio"] { display: none; }

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
                <a href="/admin/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pelanggan" class="nav-link active">
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

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Edit Pelanggan</h5>
            <small class="text-muted">{{ $pelanggan->id_pelanggan }} – {{ $pelanggan->nama }}</small>
        </div>
        <a href="/admin/pelanggan/{{ $pelanggan->id }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- ERRORS --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0 small">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="/admin/pelanggan/{{ $pelanggan->id }}">
        @csrf @method('PUT')

        <div class="row g-3">

            {{-- ===== KOLOM KIRI ===== --}}
            <div class="col-md-8">

                {{-- DATA PRIBADI --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-user me-1"></i> Data Pribadi
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control form-control-sm @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $pelanggan->nama) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">No. HP <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp" class="form-control form-control-sm @error('no_hp') is-invalid @enderror"
                                       value="{{ old('no_hp', $pelanggan->no_hp) }}" required>
                                @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                       value="{{ old('email', $pelanggan->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Wilayah</label>
                                <input type="text" name="wilayah" class="form-control form-control-sm"
                                       value="{{ old('wilayah', $pelanggan->wilayah) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Alamat</label>
                                <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ old('alamat', $pelanggan->alamat) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DATA KONEKSI --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-network-wired me-1"></i> Data Koneksi
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Username PPPoE <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control form-control-sm @error('username') is-invalid @enderror"
                                       value="{{ old('username', $pelanggan->username) }}" required>
                                @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Password Baru</label>
                                <input type="text" name="password" class="form-control form-control-sm"
                                       placeholder="Kosongkan jika tidak diubah">
                                <div class="form-text">Isi hanya jika ingin mengubah password.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">IP Address</label>
                                <input type="text" name="ip_address" class="form-control form-control-sm"
                                       value="{{ old('ip_address', $pelanggan->ip_address) }}"
                                       placeholder="Kosongkan jika dinamis">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Router</label>
                                <select name="router_id" class="form-select form-select-sm">
                                    <option value="">-- Pilih Router --</option>
                                    @foreach($routers as $router)
                                        <option value="{{ $router->id }}" {{ (old('router_id', $pelanggan->router_id) == $router->id) ? 'selected' : '' }}>
                                            {{ $router->nama }} ({{ $router->ip_address }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Jenis Layanan</label>
                                <select name="jenis_layanan" class="form-select form-select-sm">
                                    <option value="pppoe"   {{ old('jenis_layanan', $pelanggan->jenis_layanan)=='pppoe'   ? 'selected':'' }}>PPPoE</option>
                                    <option value="hotspot" {{ old('jenis_layanan', $pelanggan->jenis_layanan)=='hotspot' ? 'selected':'' }}>Hotspot</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Tanggal Expired</label>
                                <input type="date" name="tgl_expired" class="form-control form-control-sm"
                                       value="{{ old('tgl_expired', $pelanggan->tgl_expired?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== KOLOM KANAN ===== --}}
            <div class="col-md-4">

                {{-- PAKET INTERNET --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="section-title">
                            <i class="fas fa-box me-1"></i> Paket Internet
                        </div>
                        @foreach($pakets as $paket)
                        <label class="paket-option d-block {{ old('paket_id', $pelanggan->paket_id) == $paket->id ? 'selected' : '' }}"
                               for="paket{{ $paket->id }}">
                            <input type="radio" name="paket_id" id="paket{{ $paket->id }}"
                                   value="{{ $paket->id }}"
                                   {{ old('paket_id', $pelanggan->paket_id) == $paket->id ? 'checked' : '' }}>
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold small">{{ $paket->nama_paket }}</div>
                                    <small class="text-muted">
                                        <i class="fas fa-arrow-down fa-xs"></i> {{ $paket->kecepatan_download }} Mbps &nbsp;
                                        <i class="fas fa-arrow-up fa-xs"></i> {{ $paket->kecepatan_upload }} Mbps
                                    </small>
                                </div>
                                <div class="text-success fw-bold small text-end">
                                    Rp {{ number_format($paket->harga, 0, ',', '.') }}
                                    <div class="text-muted fw-normal" style="font-size:0.7rem;">/bulan</div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                    <a href="/admin/pelanggan/{{ $pelanggan->id }}" class="btn btn-outline-secondary btn-sm">
                        Batal
                    </a>
                </div>

            </div>
        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.paket-option').forEach(label => {
        label.addEventListener('click', function () {
            document.querySelectorAll('.paket-option').forEach(l => l.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

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