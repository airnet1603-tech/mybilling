<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikrotik – ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* FIX: :root ditutup dengan benar, --sidebar-width tidak duplikat */
        :root {
            --sidebar-width: 230px;
            --sidebar-bg-start: #1a1a2e;
            --sidebar-bg-end: #0f3460;
            --accent: #e94560;
        }

        * { box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

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

        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }

        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        /* FIX: Tambah style responsive seperti index.blade.php */
        .mobile-menu-btn { display: none; }

        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -230px; top: 0; height: 100vh; z-index: 1050; transition: left 0.3s ease; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0 !important; padding: 15px; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; }
            .sidebar-overlay.show { display: block; }
            .mobile-menu-btn { display: block !important; }
        }
    </style>
</head>
<body>

{{-- FIX: Mobile menu button sesuai index.blade.php --}}
<a href="#" id="menuToggleBtn" class="mobile-menu-btn" onclick="toggleSidebar();return false;"
   style="position:fixed;top:50%;left:0;transform:translateY(-50%);z-index:9999;background:rgba(233,69,96,0.9);color:white;border-radius:0 12px 12px 0;padding:12px 8px;font-size:22px;text-decoration:none;">&#9654;</a>

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
            <li class="nav-item"><a href="/admin/paket" class="nav-link"><i class="fas fa-box"></i> Paket Internet</a></li>
            <li class="nav-item"><a href="/admin/tagihan" class="nav-link"><i class="fas fa-file-invoice-dollar"></i> Tagihan</a></li>
            <li class="nav-item"><a href="/admin/pembayaran" class="nav-link"><i class="fas fa-money-bill-wave"></i> Pembayaran</a></li>
            <li class="nav-item"><a href="/admin/laporan" class="nav-link"><i class="fas fa-chart-bar"></i> Laporan</a></li>
            <li class="nav-item"><a href="/admin/mikrotik" class="nav-link active"><i class="fas fa-network-wired"></i> Mikrotik</a></li>
        </ul>

        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <li class="nav-item"><a href="/admin/setting" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a></li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    {{-- FIX: </button> bukan </a> --}}
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="width:16px;font-size:0.82rem;"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<!-- ===== MAIN CONTENT ===== -->
{{-- FIX: Wrapper div main-content yang hilang --}}
<div class="main-content">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Manajemen Router Mikrotik</h5>
            <small class="text-muted">Kelola koneksi router</small>
        </div>
        <a href="/admin/mikrotik/monitoring" class="btn btn-primary btn-sm">
            <i class="fas fa-desktop me-1"></i> Monitoring Live
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        {{-- FIX: </button> bukan </a> --}}
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

        {{-- FORM TAMBAH ROUTER --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus me-2 text-primary"></i>Tambah Router</h6>
                    <form method="POST" action="/admin/mikrotik">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Nama Router</label>
                            <input type="text" name="nama" class="form-control form-control-sm" placeholder="Contoh: Router Utama" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">IP Address</label>
                            <input type="text" name="ip_address" class="form-control form-control-sm" placeholder="192.168.1.1" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Port API</label>
                            <input type="number" name="port" class="form-control form-control-sm" value="8728" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control form-control-sm" placeholder="admin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>
                        {{-- FIX: </button> bukan </a> --}}
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save me-1"></i> Simpan Router
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- DAFTAR ROUTER --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 small">Nama</th>
                                <th class="small">IP Address</th>
                                <th class="small">Port</th>
                                <th class="small">Status</th>
                                <th class="small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routers as $router)
                            <tr>
                                <td class="ps-3"><div class="fw-semibold small">{{ $router->nama }}</div></td>
                                <td><code class="small">{{ $router->ip_address }}</code></td>
                                <td><small>{{ $router->port }}</small></td>
                                <td>
                                    @if($router->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        {{-- FIX: </button> bukan </a> --}}
                                        <button onclick="testKoneksi({{ $router->id }}, '{{ $router->nama }}')"
                                            class="btn btn-sm btn-info text-white py-0 px-2" title="Test Koneksi">
                                            <i class="fas fa-plug fa-xs"></i>
                                        </button>
                                        <form method="POST" action="/admin/mikrotik/{{ $router->id }}"
                                              onsubmit="return confirm('Hapus router ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger py-0 px-2" title="Hapus">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 small">
                                    Belum ada router. Tambahkan router di form kiri.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end .main-content --}}

{{-- MODAL TEST KONEKSI --}}
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Test Koneksi</h6>
                {{-- FIX: </button> bukan </a> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="testResult">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 mb-0 small">Menghubungkan...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function testKoneksi(id, nama) {
    const modal = new bootstrap.Modal(document.getElementById('testModal'));
    document.getElementById('testResult').innerHTML = '<i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 mb-0 small">Menghubungkan ke ' + nama + '...</p>';
    modal.show();
    fetch('/admin/mikrotik/' + id + '/test')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('testResult').innerHTML = '<i class="fas fa-check-circle fa-2x text-success"></i><p class="mt-2 mb-0 small fw-semibold">Koneksi Berhasil!</p><small class="text-muted">Identity: ' + (data.identity ?? '-') + '</small>';
            } else {
                document.getElementById('testResult').innerHTML = '<i class="fas fa-times-circle fa-2x text-danger"></i><p class="mt-2 mb-0 small fw-semibold">Koneksi Gagal</p><small class="text-muted">' + data.message + '</small>';
            }
        });
}

// FIX: Event listener touch tidak duplikat, toggleSidebar bersih
function toggleSidebar() {
    document.querySelector(".sidebar").classList.toggle("show");
    document.getElementById("sidebarOverlay").classList.toggle("show");
}

document.addEventListener("touchstart", e => window._touchStartX = e.touches[0].clientX);
document.addEventListener("touchend", e => {
    const endX = e.changedTouches[0].clientX;
    if (window._touchStartX < 30 && endX - window._touchStartX > 70) toggleSidebar();
    if (window._touchStartX > 200 && window._touchStartX - endX > 70) {
        document.querySelector(".sidebar").classList.remove("show");
        document.getElementById("sidebarOverlay").classList.remove("show");
    }
});
</script>
</body>
</html>