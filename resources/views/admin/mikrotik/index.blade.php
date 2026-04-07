<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mikrotik – ISP Billing</title>
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
        .sidebar-nav .logout-btn { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; background: none; border: none; width: calc(100% - 16px); text-align: left; cursor: pointer; }
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

        /* ===== MAIN CONTENT ===== */
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }

        .form-label { margin-bottom: 3px; }
        .section-divider {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            margin: 10px 0 8px;
        }

        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
        }
    #editModal .modal-body { max-height: 60vh; overflow-y: auto; }
</style>
</head>
<body>

{{-- Topbar Mobile (hamburger) --}}

<!-- SIDEBAR -->
@include('admin.partials.sidebar')
<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold mb-0">Manajemen Router Mikrotik</h5>
            <small class="text-muted">Kelola koneksi router</small>
        </div>
        <a href="/admin/wireguard" class="btn btn-sm text-white" style="background:#6f42c1;"><i class="fas fa-shield-alt"></i> WireGuard</a>
    </div>

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

        <!-- FORM TAMBAH ROUTER -->
        @if(auth()->user()->isAdmin())
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-plus me-2 text-primary"></i>Tambah Router</h6>
                    <form method="POST" action="/admin/mikrotik">
                        @csrf

                        <div class="section-divider">Koneksi API</div>

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
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control form-control-sm" required>
                        </div>

                        <div class="section-divider">Setting PPPoE Profile</div>

                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                Local Address
                                <span class="text-muted fw-normal">(gateway PPPoE)</span>
                            </label>
                            <input type="text" name="local_address" class="form-control form-control-sm" placeholder="Contoh: 103.x.x.1">
                            <div class="form-text" style="font-size:0.7rem;">IP publik gateway router ini</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                Remote Address
                                <span class="text-muted fw-normal">(nama pool)</span>
                            </label>
                            <input type="text" name="remote_address" class="form-control form-control-sm" placeholder="Contoh: pool-pppoe">
                            <div class="form-text" style="font-size:0.7rem;">Nama IP Pool yang sudah dibuat di Mikrotik</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">DNS Server</label>
                            <input type="text" name="dns_server" class="form-control form-control-sm" placeholder="Contoh: 8.8.8.8,8.8.4.4">
                            <div class="form-text" style="font-size:0.7rem;">Pisahkan dengan koma jika lebih dari satu</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save me-1"></i> Simpan Router
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- DAFTAR ROUTER -->
        <div class="{{ auth()->user()->isAdmin() ? 'col-md-8' : 'col-md-12' }}">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 small">Nama</th>
                                <th class="small">IP Address</th>
                                <th class="small">PPPoE Setting</th>
                                <th class="small">Monitor</th>
                                <th class="small">Status</th>
                                <th class="small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routers as $router)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold small">{{ $router->nama }}</div>
                                    <small class="text-muted">Port: {{ $router->port }}</small>
                                </td>
                                <td><code class="small">{{ $router->ip_address }}</code></td>
                                <td>
                                    <div style="font-size:0.72rem; line-height:1.6;">
                                        @if($router->local_address)
                                            <span class="text-muted">Local:</span> <code>{{ $router->local_address }}</code><br>
                                        @endif
                                        @if($router->remote_address)
                                            <span class="text-muted">Pool:</span> <code>{{ $router->remote_address }}</code><br>
                                        @endif
                                        @if($router->dns_server)
                                            <span class="text-muted">DNS:</span> <code>{{ $router->dns_server }}</code>
                                        @endif
                                        @if(!$router->local_address && !$router->remote_address && !$router->dns_server)
                                            <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Belum diset</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="/admin/mikrotik/monitoring?router={{ $router->id }}"
                                       class="btn btn-primary btn-sm py-0 px-1"
                                       title="Monitoring Live {{ $router->nama }}"
                                       target="_blank">
                                        <i class="fas fa-desktop fa-xs me-1"></i>
                                        <span style="font-size:0.72rem;">Live</span>
                                    </a>
                                </td>
                                <td>
                                    @if($router->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if(auth()->user()->isAdmin())
                                    <div class="d-flex gap-1">
                                        <button onclick="importPppoe({{ $router->id }}, '{{ $router->nama }}')"
                                            class="btn btn-sm btn-secondary py-0 px-2" title="Import PPPoE ke Billing">
                                            <i class="fas fa-file-import fa-xs"></i>
                                        </button>
                                        <button onclick="testKoneksi({{ $router->id }}, '{{ $router->nama }}')"
                                            class="btn btn-sm btn-info text-white py-0 px-2" title="Test Koneksi">
                                            <i class="fas fa-plug fa-xs"></i>
                                        </button>
                                        <button onclick="editRouter({{ $router->id }}, '{{ $router->nama }}', '{{ $router->local_address }}', '{{ $router->remote_address }}', '{{ $router->dns_server }}', '{{ $router->ip_address }}', '{{ $router->port }}')"
                                            class="btn btn-sm btn-warning py-0 px-2" title="Edit Setting PPPoE">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </button>
                                        <button onclick="importDariRB({{ $router->id }}, '{{ $router->nama }}')"
                                            class="btn btn-sm btn-success py-0 px-2" title="Import Setting dari RB">
                                            <i class="fas fa-download fa-xs"></i>
                                        </button>
                                        <button onclick="setupWireguard({{ $router->id }}, '{{ $router->nama }}')" class="btn btn-sm py-0 px-2 text-white" style="background:#6f42c1;" title="Setup WireGuard"><i class="fas fa-shield-alt fa-xs"></i></button>
                                        <form method="POST" action="/admin/mikrotik/{{ $router->id }}"
                                              onsubmit="return confirm('Hapus router ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger py-0 px-2" title="Hapus">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-muted small"><i class="fas fa-eye fa-xs"></i> View only</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4 small">
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

</div>

<!-- MODAL TEST KONEKSI -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Test Koneksi</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="testResult">
                <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 mb-0 small">Menghubungkan...</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT SETTING PPPoE -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="max-height:95vh;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Setting PPPoE Router</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="text-muted small mb-3">Router: <strong id="editRouterNama"></strong></p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">IP Address Router</label>
                        <input type="text" name="ip_address" id="editIpAddress" class="form-control form-control-sm" placeholder="Contoh: 192.168.1.1 atau 10.10.10.x">
                        <div class="form-text" style="font-size:0.7rem;">IP publik atau WireGuard IP router</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Port API</label>
                        <input type="number" name="port" id="editPort" class="form-control form-control-sm" placeholder="8728">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Local Address (gateway PPPoE)</label>
                        <input type="text" name="local_address" id="editLocalAddress" class="form-control form-control-sm" placeholder="Contoh: 103.x.x.1">
                        <div class="form-text" style="font-size:0.7rem;">IP publik gateway router ini</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Remote Address (nama pool)</label>
                        <input type="text" name="remote_address" id="editRemoteAddress" class="form-control form-control-sm" placeholder="Contoh: pool-pppoe">
                        <div class="form-text" style="font-size:0.7rem;">Nama IP Pool yang sudah dibuat di Mikrotik</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">DNS Server</label>
                        <input type="text" name="dns_server" id="editDnsServer" class="form-control form-control-sm" placeholder="Contoh: 8.8.8.8,8.8.4.4">
                        <div class="form-text" style="font-size:0.7rem;">Pisahkan dengan koma jika lebih dari satu</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT DARI RB -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-download me-2 text-success"></i>Import Setting dari RB</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Router: <strong id="importRouterNama"></strong></p>

                <div id="importLoading" class="text-center py-3">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 small">Membaca setting dari RB...</p>
                </div>

                <div id="importResult" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Local Address</label>
                        <input type="text" id="importLocalAddress" class="form-control form-control-sm" placeholder="Kosong jika tidak ada">
                        <div class="form-text" style="font-size:0.7rem;">Diambil dari PPP Profile RB</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Remote Address (Pool)</label>
                        <select id="importRemoteAddress" class="form-select form-select-sm">
                            <option value="">-- Pilih Pool --</option>
                        </select>
                        <div class="form-text" style="font-size:0.7rem;">Pool yang tersedia di RB</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">DNS Server</label>
                        <input type="text" id="importDnsServer" class="form-control form-control-sm" placeholder="Diambil dari IP DNS RB">
                        <div class="form-text" style="font-size:0.7rem;">Diambil dari IP ? DNS RB</div>
                    </div>
                    <div class="alert alert-info py-2" style="font-size:0.78rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        Setting ini hanya disimpan di billing. <strong>Tidak ada perubahan apapun di RB.</strong>
                    </div>
                </div>

                <div id="importError" style="display:none;" class="text-center py-2">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                    <p class="mt-2 small fw-semibold text-danger" id="importErrorMsg">Gagal konek ke RB</p>
                </div>
            </div>
            <div class="modal-footer" id="importFooter" style="display:none;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm" id="btnSimpanImport" onclick="simpanImport()">
                    <i class="fas fa-save me-1"></i> Simpan Setting
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL IMPORT PPPoE -->
<div class="modal fade" id="importPppoeModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><i class="fas fa-file-import me-2 text-secondary"></i>Import User PPPoE dari Mikrotik</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Router: <strong id="importPppoeRouterNama"></strong></p>

                <div id="importPppoeLoading" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 small">Mengambil data PPPoE dari Mikrotik...</p>
                </div>

                <div id="importPppoeError" style="display:none;" class="alert alert-danger py-2">
                    <i class="fas fa-times-circle me-1"></i>
                    <span id="importPppoeErrorMsg"></span>
                </div>

                <div id="importPppoeResult" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <span class="small text-muted">Paket default:</span>
                            <select id="defaultPaketId" class="form-select form-select-sm" style="width:auto;">
                                <option value="">-- Pilih Paket --</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleSelectAll()">
                                <i class="fas fa-check-square me-1"></i> Pilih Semua
                            </button>
                            <span class="badge bg-success" id="countOnline">0 Online</span>
                            <span class="badge bg-secondary" id="countOffline">0 Offline</span>
                            <span class="badge bg-warning text-dark" id="countExists">0 Sudah Ada</span>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                        <table class="table table-sm table-hover">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th><input type="checkbox" id="checkAll" onchange="toggleAll(this)"></th>
                                    <th class="small">Username</th>
                                    <th class="small">Profile/Paket</th>
                                    <th class="small">IP Address</th>
                                    <th class="small">Status</th>
                                    <th class="small">Paket Billing</th>
                                    <th class="small">
                                        Aktif Sampai
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <input type="checkbox" id="applyGlobalDate" onchange="toggleGlobalDate(this)" title="Terapkan ke semua">
                                            <input type="date" id="globalTglExpired" class="form-control form-control-sm" style="width:140px;font-size:0.75rem;" onchange="applyToAll()" disabled>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="importPppoeTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="importPppoeFooter" style="display:none;">
                <span class="text-muted small me-auto" id="importPppoeInfo"></span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="doImportPppoe()">
                    <i class="fas fa-file-import me-1"></i> Import Terpilih
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL WIREGUARD -->
<div class="modal fade" id="wgModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#6f42c1;color:white;">
                <h6 class="modal-title"><i class="fas fa-shield-alt me-2"></i>Setup WireGuard</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Router: <strong id="wgRouterNama"></strong></p>
                <div class="mb-3">
                    <label class="form-label small fw-semibold"><i class="fas fa-network-wired me-1"></i>Pilih Subnet WireGuard</label>
                    <select id="wgSubnet" class="form-select form-select-sm">
                        <option value="10.10.10">10.10.10.x (Default)</option>
                        <option value="172.16.10">172.16.10.x (Non-Publik)</option>
                    </select>
                </div>
                <div id="wgLoading" class="text-center py-3">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color:#6f42c1;"></i>
                    <p class="mt-2 small">Membuat konfigurasi WireGuard...</p>
                </div>
                <div id="wgResult" style="display:none;">
                    <div class="alert alert-success py-2 small">
                        <i class="fas fa-check-circle me-1"></i>
                        WireGuard berhasil dikonfigurasi! IP Tunnel: <strong id="wgIp"></strong>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            <i class="fas fa-terminal me-1"></i>
                            Paste command berikut di terminal Mikrotik (New Terminal di Winbox):
                        </label>
                        <textarea id="wgConfig" class="form-control form-control-sm font-monospace" rows="4" readonly style="font-size:0.75rem;background:#1e1e1e;color:#00ff00;"></textarea>
                    </div>
                    <div class="alert alert-info py-2 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Setelah paste di Mikrotik, update IP Address router ini di billing menjadi <strong id="wgIpInfo"></strong> dan port <strong>18728</strong>.
                    </div>
                </div>
                <div id="wgError" style="display:none;" class="text-center py-2">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                    <p class="mt-2 small fw-semibold text-danger" id="wgErrorMsg"></p>
                </div>
            </div>
            <div class="modal-footer" id="wgFooter" style="display:none;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-sm text-white" style="background:#6f42c1;" onclick="copyWgConfig()">
                    <i class="fas fa-copy me-1"></i> Copy Config
                </button>
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
// ===== HAMBURGER MENU (sama dengan show.blade) =====
// ===== WIREGUARD =====
let currentWgRouterId = null;

function setupWireguard(id, nama) {
    currentWgRouterId = id;
    document.getElementById('wgRouterNama').textContent = nama;
    document.getElementById('wgLoading').style.display = 'block';
    document.getElementById('wgResult').style.display = 'none';
    document.getElementById('wgError').style.display = 'none';
    document.getElementById('wgFooter').style.display = 'none';

    new bootstrap.Modal(document.getElementById('wgModal')).show();

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/admin/mikrotik/' + id + '/wireguard/setup', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/json' },
        body: JSON.stringify({ subnet: document.getElementById('wgSubnet').value }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('wgLoading').style.display = 'none';
        if (!data.status) {
            document.getElementById('wgError').style.display = 'block';
            document.getElementById('wgErrorMsg').textContent = data.message || 'Gagal setup WireGuard';
            return;
        }
        document.getElementById('wgIp').textContent = data.wg_ip;
        document.getElementById('wgIpInfo').textContent = data.wg_ip;
        document.getElementById('wgConfig').value = data.config;
        document.getElementById('wgResult').style.display = 'block';
        document.getElementById('wgFooter').style.display = 'flex';
    })
    .catch(() => {
        document.getElementById('wgLoading').style.display = 'none';
        document.getElementById('wgError').style.display = 'block';
        document.getElementById('wgErrorMsg').textContent = 'Gagal koneksi ke server';
    });
}

function copyWgConfig() {
    const config = document.getElementById('wgConfig');
    config.select();
    document.execCommand('copy');
    alert('Config berhasil dicopy!');
}

sidebarOverlay.addEventListener('click', function () {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});

// ===== MIKROTIK FUNCTIONS =====
let currentImportRouterId = null;

function importDariRB(id, nama) {
    currentImportRouterId = id;
    document.getElementById('importRouterNama').textContent = nama;
    document.getElementById('importLoading').style.display = 'block';
    document.getElementById('importResult').style.display = 'none';
    document.getElementById('importError').style.display = 'none';
    document.getElementById('importFooter').style.display = 'none';

    const modal = new bootstrap.Modal(document.getElementById('importModal'));
    modal.show();

    fetch('/admin/mikrotik/' + id + '/import-setting')
        .then(res => res.json())
        .then(data => {
            document.getElementById('importLoading').style.display = 'none';
            if (!data.status) {
                document.getElementById('importError').style.display = 'block';
                document.getElementById('importErrorMsg').textContent = data.message || 'Gagal konek ke RB';
                return;
            }
            document.getElementById('importLocalAddress').value = data.local_address || '';
            document.getElementById('importDnsServer').value = data.dns || '';
            const select = document.getElementById('importRemoteAddress');
            select.innerHTML = '<option value="">-- Pilih Pool --</option>';
            (data.pools || []).forEach(pool => {
                const opt = document.createElement('option');
                opt.value = pool.name;
                opt.textContent = pool.name + ' (' + pool.ranges + ')';
                select.appendChild(opt);
            });
            document.getElementById('importResult').style.display = 'block';
            document.getElementById('importFooter').style.display = 'flex';
        })
        .catch(() => {
            document.getElementById('importLoading').style.display = 'none';
            document.getElementById('importError').style.display = 'block';
            document.getElementById('importErrorMsg').textContent = 'Gagal konek ke RB';
        });
}

function simpanImport() {
    const btn = document.getElementById('btnSimpanImport');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formData = new FormData();
    formData.append('_token', token);
    formData.append('_method', 'PATCH');
    formData.append('local_address', document.getElementById('importLocalAddress').value);
    formData.append('remote_address', document.getElementById('importRemoteAddress').value);
    formData.append('dns_server', document.getElementById('importDnsServer').value);

    fetch('/admin/mikrotik/' + currentImportRouterId + '/pppoe-setting', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token },
        body: formData,
    })
    .then(res => {
        if (res.ok || res.redirected) {
            bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
            window.location.reload();
        } else {
            res.text().then(t => {
                console.error('Error response:', t);
                alert('Gagal menyimpan setting. Status: ' + res.status);
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Setting';
        }
    })
    .catch(err => {
        console.error(err);
        alert('Gagal menyimpan setting. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Setting';
    });
}

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

function editRouter(id, nama, local, remote, dns, ip, port) {
    document.getElementById('editRouterNama').textContent = nama;
    document.getElementById('editIpAddress').value = ip || '';
    document.getElementById('editPort').value = port || '8728';
    document.getElementById('editLocalAddress').value = local || '';
    document.getElementById('editRemoteAddress').value = remote || '';
    document.getElementById('editDnsServer').value = dns || '';
    document.getElementById('editForm').action = '/admin/mikrotik/' + id + '/pppoe-setting';
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

let currentImportPppoeRouterId = null;
let pppoeData = [];
let paketData = [];

function importPppoe(id, nama) {
    currentImportPppoeRouterId = id;
    document.getElementById('importPppoeRouterNama').textContent = nama;
    document.getElementById('importPppoeLoading').style.display = 'block';
    document.getElementById('importPppoeResult').style.display = 'none';
    document.getElementById('importPppoeError').style.display = 'none';
    document.getElementById('importPppoeFooter').style.display = 'none';

    new bootstrap.Modal(document.getElementById('importPppoeModal')).show();

    fetch('/admin/mikrotik/' + id + '/pppoe-list')
        .then(r => r.json())
        .then(data => {
            document.getElementById('importPppoeLoading').style.display = 'none';
            if (!data.status) {
                document.getElementById('importPppoeError').style.display = 'block';
                document.getElementById('importPppoeErrorMsg').textContent = data.message || 'Gagal ambil data';
                return;
            }

            pppoeData = data.data;
            paketData = data.pakets;

            const sel = document.getElementById('defaultPaketId');
            sel.innerHTML = '<option value="">-- Pilih Paket --</option>';
            paketData.forEach(p => {
                sel.innerHTML += `<option value="${p.id}">${p.nama}</option>`;
            });

            let online = 0, offline = 0, exists = 0;
            pppoeData.forEach(s => {
                if (s.online) online++;
                else offline++;
                if (s.exists) exists++;
            });
            document.getElementById('countOnline').textContent  = online + ' Online';
            document.getElementById('countOffline').textContent = offline + ' Offline';
            document.getElementById('countExists').textContent  = exists + ' Sudah Ada';

            const tbody = document.getElementById('importPppoeTableBody');
            tbody.innerHTML = '';
            pppoeData.forEach((s, i) => {
                let paketOptions = '<option value="">-- Pilih --</option>';
                paketData.forEach(p => {
                    paketOptions += `<option value="${p.id}" ${s.paket_id == p.id ? 'selected' : ''}>${p.nama}</option>`;
                });
                tbody.innerHTML += `
                <tr class="${s.exists ? 'table-warning' : ''}">
                    <td><input type="checkbox" class="pppoe-check" data-index="${i}" ${s.exists ? 'disabled' : 'checked'}></td>
                    <td class="small fw-semibold">${s.username}</td>
                    <td><code class="small">${s.profile}</code></td>
                    <td class="small">${s.address || '-'}</td>
                    <td>
                        ${s.exists ? '<span class="badge bg-warning text-dark">Sudah Ada</span>' :
                          s.online ? '<span class="badge bg-success">Online</span>' :
                          s.disabled ? '<span class="badge bg-danger">Disabled</span>' :
                          '<span class="badge bg-secondary">Offline</span>'}
                    </td>
                    <td>
                        ${s.exists ? '<span class="text-muted small">-</span>' :
                          `<select class="form-select form-select-sm paket-select" data-index="${i}" style="min-width:100px;">${paketOptions}</select>`}
                    </td>
                    <td>
                        ${s.exists ? '<span class="text-muted small">-</span>' :
                          `<input type="date" class="form-control form-control-sm expired-input" data-index="${i}" style="width:140px;font-size:0.75rem;">`}
                    </td>
                </tr>`;
            });

            updateInfo();
            setTimeout(initAllExpired, 50);
            document.getElementById('importPppoeResult').style.display = 'block';
            document.getElementById('importPppoeFooter').style.display = 'flex';
        })
        .catch(() => {
            document.getElementById('importPppoeLoading').style.display = 'none';
            document.getElementById('importPppoeError').style.display = 'block';
            document.getElementById('importPppoeErrorMsg').textContent = 'Gagal konek ke server';
        });
}

function updateInfo() {
    const checks = document.querySelectorAll('.pppoe-check:not(:disabled):checked');
    document.getElementById('importPppoeInfo').textContent = checks.length + ' user dipilih untuk diimport';
}

function toggleAll(el) {
    document.querySelectorAll('.pppoe-check:not(:disabled)').forEach(c => c.checked = el.checked);
    updateInfo();
}

let allSelected = false;
function toggleSelectAll() {
    allSelected = !allSelected;
    document.querySelectorAll('.pppoe-check:not(:disabled)').forEach(c => c.checked = allSelected);
    document.getElementById('checkAll').checked = allSelected;
    updateInfo();
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('pppoe-check')) updateInfo();
});

function toggleGlobalDate(el) {
    document.getElementById('globalTglExpired').disabled = !el.checked;
    if (el.checked) applyToAll();
}

function applyToAll() {
    const tgl = document.getElementById('globalTglExpired').value;
    if (!tgl) return;
    document.querySelectorAll('.expired-input').forEach(el => el.value = tgl);
}

function initAllExpired() {
    const d = new Date();
    d.setMonth(d.getMonth() + 1);
    const defaultTgl = d.toISOString().split('T')[0];
    document.getElementById('globalTglExpired').value = defaultTgl;
    document.querySelectorAll('.expired-input').forEach(el => el.value = defaultTgl);
}

function doImportPppoe() {
    const defaultPaket = document.getElementById('defaultPaketId').value;
    const items = [];

    document.querySelectorAll('.pppoe-check:not(:disabled):checked').forEach(c => {
        const i = c.dataset.index;
        const s = pppoeData[i];
        const paketSel    = document.querySelector(`.paket-select[data-index="${i}"]`);
        const expiredInput = document.querySelector(`.expired-input[data-index="${i}"]`);
        items.push({
            username:    s.username,
            password:    s.password,
            profile:     s.profile,
            address:     s.address,
            disabled:    s.disabled,
            paket_id:    paketSel ? paketSel.value : null || defaultPaket || null,
            tgl_expired: expiredInput ? expiredInput.value : null,
        });
    });

    if (!items.length) { alert('Pilih minimal 1 user!'); return; }
    if (items.some(i => !i.paket_id)) { alert('Beberapa user belum dipilih paketnya!'); return; }

    const btn = document.querySelector('#importPppoeFooter .btn-primary');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengimport...';

    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('/admin/mikrotik/' + currentImportPppoeRouterId + '/import-pppoe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ items, paket_id: defaultPaket, bulan: 1 }),
    })
    .then(r => r.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('importPppoeModal')).hide();
        alert(data.message);
        if (data.imported > 0) window.location.href = '/admin/pelanggan';
    })
    .catch(() => {
        alert('Gagal import. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-import me-1"></i> Import Terpilih';
    });
}
</script>
</body>
</html>