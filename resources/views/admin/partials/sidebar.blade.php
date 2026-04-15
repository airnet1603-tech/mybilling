{{-- Mobile Topbar --}}
<div class="mobile-topbar">
    <button class="hamburger-btn" id="hamburgerBtn">
        <i class="fas fa-bars"></i>
    </button>
    <span class="brand-title">ISP Billing</span>
</div>

{{-- Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Sidebar --}}
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <img src="https://airnetps.my.id/app/icon/icon_airnet.png" style="height:38px;object-fit:contain;background:#ffffff;padding:2px 4px;border-radius:8px 0px 8px 0px;">
        </div>
        <div class="brand-text">
            <span class="brand-title">ISP Billing</span>
            <span class="brand-sub">Management System</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav flex-column mb-0">
            <li class="nav-item">
                <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pelanggan/peta" class="nav-link {{ request()->is('admin/pelanggan/peta') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i> Peta
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/topologi" class="nav-link {{ request()->is('admin/topologi*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i> OLT
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pelanggan" class="nav-link {{ request()->is('admin/pelanggan') || (request()->is('admin/pelanggan/*') && !request()->is('admin/pelanggan/peta')) ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Pelanggan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/paket" class="nav-link {{ request()->is('admin/paket*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Paket Internet
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/tagihan" class="nav-link {{ request()->is('admin/tagihan*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i> Tagihan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/pembayaran" class="nav-link {{ request()->is('admin/pembayaran*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/laporan" class="nav-link {{ request()->is('admin/laporan*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/mikrotik" class="nav-link {{ request()->is('admin/mikrotik*') ? 'active' : '' }}">
                    <i class="fas fa-network-wired"></i> Mikrotik
                </a>
            </li>
        </ul>

        <div class="sidebar-divider"></div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin/setting" class="nav-link {{ request()->is('admin/setting*') ? 'active' : '' }}">
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
