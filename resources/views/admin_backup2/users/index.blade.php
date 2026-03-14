<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User — ISP Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--sidebar-width:230px;--sidebar-bg-start:#1a1a2e;--sidebar-bg-end:#0f3460;--accent:#e94560}
        *{box-sizing:border-box}body{background:#f0f2f5;font-family:'Segoe UI',sans-serif}
        .sidebar{background:linear-gradient(180deg,var(--sidebar-bg-start) 0%,var(--sidebar-bg-end) 100%);min-height:100vh;width:var(--sidebar-width);position:fixed;top:0;left:0;z-index:100;display:flex;flex-direction:column}
        .sidebar-brand{padding:14px 16px;border-bottom:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;gap:10px}
        .brand-icon{width:34px;height:34px;background:rgba(233,69,96,0.25);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:1rem}
        .brand-title{color:#fff;font-weight:700;font-size:0.9rem;display:block}.brand-sub{color:rgba(255,255,255,0.45);font-size:0.7rem}
        .sidebar-nav{padding:8px 0;flex:1}
        .sidebar-nav .nav-link{display:flex;align-items:center;gap:10px;padding:9px 18px;color:rgba(255,255,255,0.65);font-size:0.82rem;text-decoration:none;transition:.15s}
        .sidebar-nav .nav-link i{width:16px}
        .sidebar-nav .nav-link:hover,.sidebar-nav .nav-link.active{background:rgba(233,69,96,0.25);color:#fff}
        .sidebar-nav .nav-link.active{background:rgba(233,69,96,0.35)}
        .sidebar-divider{border-top:1px solid rgba(255,255,255,0.08);margin:6px 14px}
        .logout-btn{display:flex;align-items:center;gap:10px;padding:9px 18px;color:rgba(255,255,255,0.5);font-size:0.82rem;background:none;border:none;width:100%;cursor:pointer;transition:.15s}
        .logout-btn:hover{background:rgba(233,69,96,0.25);color:#fff}
        .main-content{margin-left:var(--sidebar-width);padding:20px 24px}
        .topbar{background:#fff;border-radius:12px;padding:14px 20px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 1px 4px rgba(0,0,0,0.06)}
        .badge-admin{background:rgba(233,69,96,0.15);color:#e94560;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600}
        .badge-operator{background:rgba(15,52,96,0.12);color:#0f3460;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600}
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fas fa-wifi"></i></div>
        <div><span class="brand-title">ISP BILLING</span><span class="brand-sub">Management System</span></div>
    </div>
    <nav class="sidebar-nav">
        <a href="/admin/dashboard" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </nav>
</div>
<div class="main-content">
    <div class="topbar">
        <h5 class="mb-0 fw-bold"><i class="fas fa-user-cog me-2 text-danger"></i>Kelola User Billing</h5>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">{{ auth()->user()->name }}</span>
            <span class="badge-admin">{{ ucfirst(auth()->user()->role) }}</span>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-semibold">Daftar User Billing</span>
            <a href="/admin/users/create" class="btn btn-sm btn-danger"><i class="fas fa-plus me-1"></i> Tambah User</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th class="ps-4">#</th><th>Nama</th><th>Email</th><th>Role</th><th>Dibuat</th><th class="text-center">Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td class="ps-4">{{ $i+1 }}</td>
                        <td><div class="fw-semibold">{{ $user->name }}</div>@if($user->id===auth()->id())<small class="text-muted">(Anda)</small>@endif</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role==='admin')<span class="badge-admin"><i class="fas fa-crown me-1"></i>Admin</span>
                            @else<span class="badge-operator"><i class="fas fa-user me-1"></i>Operator</span>@endif
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                            @if($user->id!==auth()->id())
                            <form action="/admin/users/{{ $user->id }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
