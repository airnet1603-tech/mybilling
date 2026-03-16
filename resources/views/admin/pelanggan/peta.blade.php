<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Peta Pelanggan - ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC33huzSRZbZ02tihkJmqqrGhP9Kml32uM&libraries=places" defer></script>
    <style>
        :root { --sidebar-width:230px; --sidebar-bg-start:#1a1a2e; --sidebar-bg-end:#0f3460; --accent:#e94560; }
        * { box-sizing:border-box; }
        body { background:#f0f2f5; font-family:'Segoe UI',sans-serif; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background:linear-gradient(180deg,var(--sidebar-bg-start) 0%,var(--sidebar-bg-end) 100%);
            min-height:100vh;
            width:var(--sidebar-width);
            position:fixed;
            top:0; left:0;
            z-index:1050;
            display:flex;
            flex-direction:column;
            transition: transform 0.3s ease;
        }
        .sidebar-brand { padding:14px 16px; border-bottom:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; gap:10px; }
        .sidebar-brand .brand-icon { width:34px; height:34px; background:rgba(233,69,96,0.25); border-radius:8px; display:flex; align-items:center; justify-content:center; color:var(--accent); font-size:1rem; }
        .sidebar-brand .brand-title { color:#fff; font-weight:700; font-size:0.9rem; display:block; }
        .sidebar-brand .brand-sub { color:rgba(255,255,255,0.45); font-size:0.7rem; }
        .sidebar-nav { padding:8px 0; flex:1; }
        .sidebar-nav .nav-link { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; transition:background 0.2s,color 0.2s; }
        .sidebar-nav .nav-link i { width:16px; font-size:0.82rem; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background:rgba(233,69,96,0.25); color:#fff; }
        .sidebar-nav .nav-link.active { background:rgba(233,69,96,0.35); }
        .sidebar-divider { border-top:1px solid rgba(255,255,255,0.08); margin:6px 14px; }
        .sidebar-nav .logout-btn { color:rgba(255,255,255,0.65); padding:8px 14px; border-radius:7px; margin:1px 8px; font-size:0.83rem; display:flex; align-items:center; gap:9px; background:none; border:none; width:calc(100% - 16px); text-align:left; cursor:pointer; }
        .sidebar-nav .logout-btn:hover { background:rgba(233,69,96,0.25); color:#fff; }

        /* ===== TOPBAR MOBILE ===== */
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
        .main-content { margin-left:var(--sidebar-width); padding:20px 24px; }

        #petaMap { height:calc(100vh - 180px); border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .stat-card { border:none; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.07); }
        .map-label { background: rgba(0,0,0,0.65); padding: 2px 6px; border-radius: 4px; margin-top: 4px; white-space: nowrap; text-shadow: none; }

        /* ===== RESPONSIVE MOBILE ===== */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
            #petaMap { height: calc(100vh - 220px); }
        }
    </style>
</head>
<body>

<!-- Topbar Mobile (hamburger) -->
<div class="mobile-topbar">
    <button class="hamburger-btn" id="hamburgerBtn">
        <i class="fas fa-bars"></i>
    </button>
    <span class="brand-title">ISP Billing</span>
</div>

<!-- Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
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

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0"><i class="fas fa-map-marked-alt me-2 text-danger"></i>Peta Pelanggan</h5>
            <small class="text-muted">Lokasi pelanggan terdaftar</small>
        </div>
        <a href="/admin/pelanggan" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-users text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $total }}</div>
                        <div class="small text-muted">Total Pelanggan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#d4edda;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-map-marker-alt text-success"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $total }}</div>
                        <div class="small text-muted">Ada di Peta</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card card">
                <div class="card-body py-2 px-3 d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:#f8d7da;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-map-marker text-danger"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5">{{ $total }}</div>
                        <div class="small text-muted">Tanpa Koordinat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-3" style="border:none;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.07);">
        <div class="card-body py-2 px-3 d-flex gap-2 flex-wrap align-items-center">
            <input type="text" id="searchPelanggan" class="form-control form-control-sm" style="width:200px;" placeholder="Cari nama/username...">
            <select id="filterStatus" class="form-select form-select-sm" style="width:150px;">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="isolir">Isolir</option>
                <option value="suspend">Suspend</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
            <select id="filterPaket" class="form-select form-select-sm" style="width:150px;">
                <option value="">Semua Paket</option>
                @foreach(\App\Models\Paket::all() as $paket)
                <option value="{{ $paket->nama_paket }}">{{ $paket->nama_paket }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary" onclick="resetFilter()">
                <i class="fas fa-times me-1"></i>Reset
            </button>
            <span class="ms-auto small text-muted" id="pinCount">{{ $total }} pin ditampilkan</span>
        </div>
    </div>

    <!-- Peta -->
    <div id="petaMap"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== HAMBURGER MENU =====
var hamburgerBtn   = document.getElementById('hamburgerBtn');
var sidebar        = document.getElementById('sidebar');
var sidebarOverlay = document.getElementById('sidebarOverlay');

hamburgerBtn.addEventListener('click', function() {
    sidebar.classList.toggle('open');
    sidebarOverlay.classList.toggle('show');
});
sidebarOverlay.addEventListener('click', function() {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('show');
});

// ===== PETA =====
var allMarkers = [];
var infoWindow = null;
var petaMap = null;

var pelangganData = {!! $mapDataJson !!};

function initMap() {
    petaMap = new google.maps.Map(document.getElementById('petaMap'), {
        center: { lat: -7.9, lng: 112.6 },
        zoom: 12,
        mapTypeId: 'hybrid',
        gestureHandling: 'greedy',
        fullscreenControl: true,
        streetViewControl: true,
        mapTypeControl: true,
    });

    infoWindow = new google.maps.InfoWindow();

    var pinColors = {
        'aktif'    : 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
        'isolir'   : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
        'suspend'  : 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
        'nonaktif' : 'http://maps.google.com/mapfiles/ms/icons/grey-dot.png',
    };

    pelangganData.forEach(function(p) {
        var marker = new google.maps.Marker({
            position : { lat: p.lat, lng: p.lng },
            map      : petaMap,
            title    : p.nama,
            icon     : pinColors[p.status] || pinColors['aktif'],
            data     : p,
            label    : {
                text      : p.nama,
                color     : '#ffffff',
                fontSize  : '11px',
                fontWeight: 'bold',
                className : 'map-label',
            },
        });

        marker.addListener('click', function() {
            var statusColor = {aktif:'#28a745',isolir:'#dc3545',suspend:'#ffc107',nonaktif:'#6c757d'};
            var color = statusColor[p.status] || '#6c757d';
            infoWindow.setContent(
                '<div style="min-width:220px;font-family:Segoe UI,sans-serif;">' +
                '<div style="font-weight:700;font-size:14px;margin-bottom:6px;">' + p.nama + '</div>' +
                '<div style="font-size:12px;color:#666;margin-bottom:4px;"><i class="fas fa-user me-1"></i>' + p.username + '</div>' +
                '<div style="font-size:12px;margin-bottom:4px;">' +
                    '<span style="background:' + color + ';color:white;padding:2px 8px;border-radius:10px;font-size:11px;">' + p.status.toUpperCase() + '</span>' +
                '</div>' +
                '<div style="font-size:12px;color:#666;margin-bottom:2px;"><i class="fas fa-box me-1"></i>' + p.paket + '</div>' +
                '<div style="font-size:12px;color:#666;margin-bottom:6px;"><i class="fas fa-calendar me-1"></i>Expired: ' + p.expired + '</div>' +
                '<a href="' + p.url + '" style="display:block;text-align:center;background:#1a73e8;color:white;padding:6px;border-radius:6px;text-decoration:none;font-size:12px;">' +
                '<i class="fas fa-eye me-1"></i>Lihat Detail</a>' +
                '</div>'
            );
            infoWindow.open(petaMap, marker);
        });

        allMarkers.push(marker);
    });

    if (allMarkers.length > 0) {
        var bounds = new google.maps.LatLngBounds();
        allMarkers.forEach(function(m) { bounds.extend(m.getPosition()); });
        petaMap.fitBounds(bounds);
    }

    document.getElementById('searchPelanggan').addEventListener('input', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
    document.getElementById('filterPaket').addEventListener('change', applyFilter);
}

function applyFilter() {
    var search = document.getElementById('searchPelanggan').value.toLowerCase();
    var status = document.getElementById('filterStatus').value;
    var paket  = document.getElementById('filterPaket').value;
    var count  = 0;

    allMarkers.forEach(function(m) {
        var p = m.data;
        var show = true;
        if (search && !p.nama.toLowerCase().includes(search) && !p.username.toLowerCase().includes(search)) show = false;
        if (status && p.status !== status) show = false;
        if (paket  && p.paket  !== paket)  show = false;
        m.setVisible(show);
        if (show) count++;
    });

    document.getElementById('pinCount').textContent = count + ' pin ditampilkan';
}

function resetFilter() {
    document.getElementById('searchPelanggan').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterPaket').value = '';
    applyFilter();
}

window.addEventListener('load', function() {
    var check = setInterval(function() {
        if (typeof google !== 'undefined' && google.maps && google.maps.Map) {
            clearInterval(check);
            initMap();
        }
    }, 100);
});
</script>
</body>
</html>