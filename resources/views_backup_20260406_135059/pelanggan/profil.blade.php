<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — Portal Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body{background:#f0f2f5;font-family:'Segoe UI',sans-serif}
        .topbar{background:linear-gradient(135deg,#1a1a2e,#0f3460)}
        .topbar-inner{padding:14px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        .brand{color:#fff;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:10px}
        .brand-icon{width:34px;height:34px;background:rgba(233,69,96,0.25);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#e94560}
        .nav-portal a{color:rgba(255,255,255,0.75);text-decoration:none;font-size:0.85rem;padding:6px 12px;border-radius:8px;transition:.15s}
        .nav-portal a:hover,.nav-portal a.active{background:rgba(255,255,255,0.1);color:#fff}
        .main{padding:20px;max-width:680px;margin:0 auto}
        .info-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f3f3f3}
        .info-row:last-child{border-bottom:none}
        .info-label{color:#6c757d;font-size:0.83rem}
        .info-value{font-weight:600;font-size:0.88rem;text-align:right}
        .form-control:focus{border-color:#e94560;box-shadow:0 0 0 0.2rem rgba(233,69,96,0.15)}
        .btn-save{background:linear-gradient(135deg,#e94560,#0f3460);border:none;color:#fff}
        .btn-save:hover{opacity:0.9;color:#fff}
    </style>
</head>
<body>
<nav class="topbar">
    <div class="topbar-inner">
        <a href="/pelanggan/dashboard" class="brand">
            <div class="brand-icon"><i class="fas fa-wifi"></i></div>
            <div><div style="font-size:0.85rem;font-weight:700">ISP BILLING</div><div style="font-size:0.65rem;color:rgba(255,255,255,0.5)">Portal Pelanggan</div></div>
        </a>
        <div class="d-flex align-items-center gap-2">
            <div class="nav-portal d-flex gap-1">
                <a href="/pelanggan/dashboard"><i class="fas fa-home me-1"></i>Home</a>
                <a href="/pelanggan/tagihan"><i class="fas fa-file-invoice me-1"></i>Tagihan</a>
                <a href="/pelanggan/profil" class="active"><i class="fas fa-user me-1"></i>Profil</a>
            </div>
            <a href="/pelanggan/logout" class="btn btn-sm ms-1" style="background:rgba(233,69,96,0.2);color:#fff;border:1px solid rgba(233,69,96,0.3)"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>
<div class="main">
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-bold py-3"><i class="fas fa-id-card me-2 text-primary"></i>Informasi Akun</div>
        <div class="card-body">
            <div class="info-row"><span class="info-label"><i class="fas fa-user me-2"></i>Nama</span><span class="info-value">{{ $pelanggan->nama }}</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-at me-2"></i>Username</span><span class="info-value">{{ $pelanggan->username }}</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-phone me-2"></i>No. HP</span><span class="info-value">{{ $pelanggan->no_hp ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Alamat</span><span class="info-value">{{ $pelanggan->alamat ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-box me-2"></i>Paket</span><span class="info-value">{{ $pelanggan->paket->nama_paket ?? $pelanggan->paket->nama ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-tachometer-alt me-2"></i>Kecepatan</span><span class="info-value">{{ $pelanggan->paket->kecepatan_download ?? $pelanggan->paket->download ?? '-' }} Mbps</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-calendar-alt me-2"></i>Expired</span><span class="info-value">{{ \Carbon\Carbon::parse($pelanggan->tgl_expired)->format('d/m/Y') }}</span></div>
            <div class="info-row"><span class="info-label"><i class="fas fa-circle me-2"></i>Status</span>
                <span class="info-value">
                    @if($pelanggan->status==='aktif')<span style="color:#198754"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                    @else<span style="color:#e94560"><i class="fas fa-ban me-1"></i>{{ ucfirst($pelanggan->status) }}</span>@endif
                </span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold py-3"><i class="fas fa-key me-2 text-warning"></i>Ganti Password Portal</div>
        <div class="card-body">
            <p class="text-muted small mb-3">Default: password PPPoE Anda. Ubah jika ingin password portal berbeda.</p>
            <form method="POST" action="/pelanggan/profil/password">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password Lama</label>
                    <input type="password" name="password_lama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Password Baru</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-save px-4 btn-sm"><i class="fas fa-save me-2"></i>Simpan Password</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
