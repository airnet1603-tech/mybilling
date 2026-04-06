<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? 'Edit' : 'Tambah' }} User — ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--sidebar-width:230px;--sidebar-bg-start:#1a1a2e;--sidebar-bg-end:#0f3460;--accent:#e94560}
        *{box-sizing:border-box}body{background:#f0f2f5;font-family:'Segoe UI',sans-serif}
        .sidebar{background:linear-gradient(180deg,var(--sidebar-bg-start) 0%,var(--sidebar-bg-end) 100%);min-height:100vh;width:var(--sidebar-width);position:fixed;top:0;left:0;z-index:100;display:flex;flex-direction:column}
        .sidebar-brand{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;gap:10px}
        .brand-icon{width:70px;height:40px;background:rgba(233,69,96,0.25);border-radius:8px 0px 8px 0px;display:flex;align-items:center;justify-content:center;color:var(--accent)}
        .brand-title{color:#fff;font-weight:700;font-size:0.9rem;display:block}.brand-sub{color:rgba(255,255,255,0.45);font-size:0.7rem}
        .sidebar-nav{padding:8px 0;flex:1}
        .sidebar-nav .nav-link{display:flex;align-items:center;gap:10px;padding:9px 18px;color:rgba(255,255,255,0.65);font-size:0.82rem;text-decoration:none;transition:.15s}
        .sidebar-nav .nav-link i{width:16px}
        .sidebar-nav .nav-link:hover,.sidebar-nav .nav-link.active{background:rgba(233,69,96,0.25);color:#fff}
        .sidebar-nav .nav-link.active{background:rgba(233,69,96,0.35)}
        .sidebar-divider{border-top:1px solid rgba(255,255,255,0.08);margin:6px 14px}
        .logout-btn{display:flex;align-items:center;gap:10px;padding:9px 18px;color:rgba(255,255,255,0.5);font-size:0.82rem;background:none;border:none;width:100%;cursor:pointer}
        .logout-btn:hover{background:rgba(233,69,96,0.25);color:#fff}
        .main-content{margin-left:var(--sidebar-width);padding:20px 24px}
        .topbar{background:#fff;border-radius:12px;padding:14px 20px;margin-bottom:20px;box-shadow:0 1px 4px rgba(0,0,0,0.06)}
        .form-control:focus,.form-select:focus{border-color:#e94560;box-shadow:0 0 0 0.2rem rgba(233,69,96,0.15)}
        .btn-save{background:linear-gradient(135deg,#e94560,#0f3460);border:none;color:#fff}
        .btn-save:hover{opacity:0.9;color:#fff}
        .role-card{border:2px solid #dee2e6;border-radius:10px;padding:15px;cursor:pointer;transition:.2s;display:block}
        .role-card:hover{border-color:#e94560}.role-card.selected{border-color:#e94560;background:rgba(233,69,96,0.05)}
    </style>
</head>
<body>
@include('admin.partials.sidebar')
        <div><span class="brand-title">ISP BILLING</span><span class="brand-sub">Management System</span></div>
    </div>
    <nav class="sidebar-nav">
        <a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="/admin/pelanggan/peta" class="nav-link"><i class="fas fa-map-marked-alt"></i> Peta
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pelanggan" class="nav-link"><i class="fas fa-users"></i> Pelanggan</a>
        <a href="/admin/paket" class="nav-link"><i class="fas fa-box"></i> Paket Internet</a>
        <a href="/admin/tagihan" class="nav-link"><i class="fas fa-file-invoice"></i> Tagihan</a>
        <a href="/admin/pembayaran" class="nav-link"><i class="fas fa-money-bill-wave"></i> Pembayaran</a>
        <a href="/admin/laporan" class="nav-link"><i class="fas fa-chart-bar"></i> Laporan</a>
        <a href="/admin/mikrotik" class="nav-link"><i class="fas fa-router"></i> Mikrotik</a>
        <div class="sidebar-divider"></div>
        <a href="/admin/users" class="nav-link active"><i class="fas fa-user-cog"></i> Kelola User</a>
        <a href="/admin/setting" class="nav-link"><i class="fas fa-cog"></i> Pengaturan</a>
        <div class="sidebar-divider"></div>
        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button></form>
    </nav>
</div>
<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold"><i class="fas fa-user-plus me-2 text-danger"></i>{{ isset($user) ? 'Edit User: '.$user->name : 'Tambah User Baru' }}</h5>
        <a href="/admin/users" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </div>
    @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach</div>@endif
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ isset($user) ? '/admin/users/'.$user->id : '/admin/users' }}">
                @csrf
                @if(isset($user)) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                        <input type="password" name="password" class="form-control" {{ isset($user)?'':'required' }} minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="role-card {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}" onclick="selectRole(this,'admin')">
                                    <input type="radio" name="role" value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'checked' : '' }} style="display:none">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:40px;height:40px;background:rgba(233,69,96,0.1);border-radius:8px 0px 8px 0px;display:flex;align-items:center;justify-content:center"><i class="fas fa-crown text-danger"></i></div>
                                        <div><div class="fw-bold">Admin</div><small class="text-muted">Akses penuh semua fitur</small></div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="role-card {{ old('role', $user->role ?? 'operator') === 'operator' ? 'selected' : '' }}" onclick="selectRole(this,'operator')">
                                    <input type="radio" name="role" value="operator" {{ old('role', $user->role ?? 'operator') === 'operator' ? 'checked' : '' }} style="display:none">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:40px;height:40px;background:rgba(15,52,96,0.1);border-radius:8px 0px 8px 0px;display:flex;align-items:center;justify-content:center"><i class="fas fa-user-tie" style="color:#0f3460"></i></div>
                                        <div><div class="fw-bold">Operator</div><small class="text-muted">Pelanggan, tagihan, bayar</small></div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-save px-4"><i class="fas fa-save me-2"></i>{{ isset($user) ? 'Simpan Perubahan' : 'Tambah User' }}</button>
                        <a href="/admin/users" class="btn btn-outline-secondary ms-2">Batal</a>
                    </div>
                </div>
            </form>
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
function selectRole(el,val){document.querySelectorAll('.role-card').forEach(c=>c.classList.remove('selected'));el.classList.add('selected');el.querySelector('input').checked=true;}
</script>
</body>
</html>
