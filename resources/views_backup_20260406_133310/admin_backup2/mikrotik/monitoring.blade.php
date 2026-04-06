<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Live – ISP Billing</title>
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

        .router-status-card {
            background: linear-gradient(135deg, var(--sidebar-bg-start), var(--sidebar-bg-end));
            border-radius: 12px;
            padding: 16px 20px;
            color: white;
            margin-bottom: 12px;
        }

        .live-dot {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #28a745;
            animation: blink 1.2s infinite;
            margin-right: 6px;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.2; }
        }

        .stat-mini {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 10px 14px;
            text-align: center;
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 16px; left: 16px;
            z-index: 1060;
            width: 42px; height: 42px;
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

        .mobile-menu-btn:hover { transform: scale(1.08); }
        .mobile-menu-btn .bar {
            display: block; width: 20px; height: 2px;
            background: white; border-radius: 2px;
            transition: all 0.3s ease; transform-origin: center;
        }
        .mobile-menu-btn.is-open .bar:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .mobile-menu-btn.is-open .bar:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .mobile-menu-btn.is-open .bar:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        @media (max-width: 768px) {
            .sidebar { position: fixed; left: -230px; top: 0; height: 100vh; z-index: 1050; transition: left 0.3s ease; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0 !important; padding: 15px; padding-top: 72px; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1040; backdrop-filter: blur(2px); }
            .sidebar-overlay.show { display: block; }
            .mobile-menu-btn { display: flex !important; }
        }
    </style>
</head>
<body>

@php
    // Filter router berdasarkan ?router=id jika ada
    $routerId = request('router');
    $filteredRouters = $routerId
        ? $routers->where('id', $routerId)
        : $routers;
    $singleRouter = $routerId ? $filteredRouters->first() : null;
@endphp

<button id="menuToggleBtn" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
</button>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- SIDEBAR -->
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
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt" style="width:16px;font-size:0.82rem;"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">
                <span class="live-dot"></span> Monitoring Live
                @if($singleRouter)
                    &mdash; {{ $singleRouter->nama }}
                @endif
            </h5>
            <small class="text-muted">
                @if($singleRouter)
                    {{ $singleRouter->ip_address }}:{{ $singleRouter->port }}
                @else
                    Status realtime semua router Mikrotik
                @endif
            </small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-secondary" id="lastUpdate">–</span>
            <button class="btn btn-outline-primary btn-sm" onclick="refreshAll()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
            <a href="/admin/mikrotik" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- ROUTER CARDS -->
    @forelse($filteredRouters as $router)
    <div class="router-status-card" id="router-card-{{ $router->id }}">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <div class="fw-bold fs-6">
                    <i class="fas fa-network-wired me-2"></i>{{ $router->nama }}
                </div>
                <div class="opacity-75 small">
                    <code style="color:rgba(255,255,255,0.8);">{{ $router->ip_address }}:{{ $router->port }}</code>
                </div>
            </div>
            <span class="badge" id="status-badge-{{ $router->id }}" style="background:rgba(255,255,255,0.2);">
                <i class="fas fa-spinner fa-spin fa-xs me-1"></i> Memuat...
            </span>
        </div>

        <div class="row g-2" id="router-stats-{{ $router->id }}">
            <div class="col-6 col-md-3">
                <div class="stat-mini">
                    <div class="opacity-75" style="font-size:0.68rem;">CPU Load</div>
                    <div class="fw-bold" id="cpu-{{ $router->id }}">–</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-mini">
                    <div class="opacity-75" style="font-size:0.68rem;">Memory</div>
                    <div class="fw-bold" id="mem-{{ $router->id }}">–</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-mini">
                    <div class="opacity-75" style="font-size:0.68rem;">Uptime</div>
                    <div class="fw-bold" style="font-size:0.8rem;" id="uptime-{{ $router->id }}">–</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-mini">
                    <div class="opacity-75" style="font-size:0.68rem;">PPPoE Online</div>
                    <div class="fw-bold text-warning" id="pppoe-count-{{ $router->id }}">–</div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL SESSION AKTIF -->
    <div class="card mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
            <div class="fw-bold" style="font-size:0.88rem;">
                <i class="fas fa-users me-2 text-primary"></i>
                Session Aktif – {{ $router->nama }}
            </div>
            <span class="badge bg-primary" id="session-count-{{ $router->id }}">0</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small">Username</th>
                            <th class="small">IP Address</th>
                            <th class="small">Uptime</th>
                            <th class="small">? Download</th>
                            <th class="small">? Upload</th>
                            <th class="small">MAC Address</th>
                        </tr>
                    </thead>
                    <tbody id="sessions-table-{{ $router->id }}">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4 small">
                                <i class="fas fa-spinner fa-spin me-2"></i>Memuat session...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @empty
    <div class="card">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-network-wired fa-3x mb-3 opacity-25"></i>
            <h6>Router tidak ditemukan</h6>
            <a href="/admin/mikrotik" class="btn btn-primary btn-sm mt-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Mikrotik
            </a>
        </div>
    </div>
    @endforelse

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const routerIds = [
    @foreach($filteredRouters as $router)
    {{ $router->id }},
    @endforeach
];

function formatBytes(bytes) {
    if (!bytes || bytes == 0) return '0 B';
    const k = 1024;
    const sizes = ['B','KB','MB','GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function loadRouterStats(id) {
    fetch('/admin/mikrotik/' + id + '/stats')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('status-badge-' + id);
            if (data.status) {
                badge.innerHTML = '<i class="fas fa-circle fa-xs me-1" style="color:#28a745;"></i> Online';
                badge.style.background = 'rgba(40,167,69,0.3)';
                document.getElementById('cpu-' + id).textContent    = (data.cpu ?? '–') + '%';
                document.getElementById('mem-' + id).textContent    = data.memory ?? '–';
                document.getElementById('uptime-' + id).textContent = data.uptime ?? '–';
                document.getElementById('pppoe-count-' + id).textContent = data.pppoe_count ?? '–';
            } else {
                badge.innerHTML = '<i class="fas fa-circle fa-xs me-1" style="color:#dc3545;"></i> Offline';
                badge.style.background = 'rgba(220,53,69,0.3)';
                ['cpu','mem','uptime','pppoe-count'].forEach(k =>
                    document.getElementById(k + '-' + id).textContent = '–'
                );
            }
        })
        .catch(() => {
            const badge = document.getElementById('status-badge-' + id);
            badge.innerHTML = '<i class="fas fa-circle fa-xs me-1" style="color:#ffc107;"></i> Error';
            badge.style.background = 'rgba(255,193,7,0.3)';
        });
}

function loadSessions(id) {
    fetch('/admin/mikrotik/' + id + '/sessions')
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('sessions-table-' + id);
            const countEl = document.getElementById('session-count-' + id);

            if (!data.sessions || data.sessions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4 small">Tidak ada session aktif</td></tr>';
                countEl.textContent = '0';
                return;
            }

            countEl.textContent = data.sessions.length;
            tbody.innerHTML = data.sessions.map(s => `
                <tr>
                    <td class="ps-3"><code class="small">${s.name ?? '-'}</code></td>
                    <td><small>${s.address ?? '-'}</small></td>
                    <td><small>${s.uptime ?? '-'}</small></td>
                    <td><small class="text-primary fw-semibold">${formatBytes(s.bytes_in ?? 0)}</small></td>
                    <td><small class="text-success fw-semibold">${formatBytes(s.bytes_out ?? 0)}</small></td>
                    <td><small class="text-muted">${s.mac_address ?? '-'}</small></td>
                </tr>
            `).join('');
        })
        .catch(() => {
            const tbody = document.getElementById('sessions-table-' + id);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3 small"><i class="fas fa-exclamation-circle me-1"></i>Gagal memuat session</td></tr>';
        });
}

function refreshAll() {
    routerIds.forEach(id => {
        loadRouterStats(id);
        loadSessions(id);
    });
    const now = new Date();
    document.getElementById('lastUpdate').textContent =
        'Update: ' + now.toLocaleTimeString('id-ID');
}

refreshAll();
setInterval(refreshAll, 30000);

function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const btn     = document.getElementById("menuToggleBtn");
    sidebar.classList.toggle("show");
    overlay.classList.toggle("show");
    btn.classList.toggle("is-open");
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