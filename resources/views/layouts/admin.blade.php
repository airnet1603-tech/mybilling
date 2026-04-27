<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ISP Billing')</title>
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
        .sidebar { background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-end) 100%); min-height: 100vh; width: var(--sidebar-width); position: fixed; top: 0; left: 0; z-index: 1050; display: flex; flex-direction: column; transition: transform 0.3s ease; }
        .sidebar-brand { padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; gap: 10px; }
        .sidebar-brand .brand-icon { width: 70px; height: 40px; background: rgba(233,69,96,0.25); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; flex-shrink: 0; }
        .sidebar-brand .brand-text { line-height: 1.2; }
        .sidebar-brand .brand-title { color: #fff; font-weight: 700; font-size: 0.9rem; display: block; }
        .sidebar-brand .brand-sub { color: rgba(255,255,255,0.45); font-size: 0.7rem; }
        .sidebar-nav { padding: 8px 0; flex: 1; }
        .sidebar-nav .nav-link { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; transition: background 0.2s, color 0.2s; white-space: nowrap; }
        .sidebar-nav .nav-link i { width: 16px; font-size: 0.82rem; flex-shrink: 0; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { background: rgba(255,255,255,0.08); color: #fff; border-left: 2px solid rgba(255,255,255,0.5); border-bottom: none; padding-left: 10px; position: relative; }
        .sidebar-nav .nav-link:hover::after, .sidebar-nav .nav-link.active::after { content: ""; position: absolute; bottom: 0; left: 3px; right: 3px; height: 1px; background: linear-gradient(to right, rgba(255,255,255,0.3), rgba(255,255,255,0.05)); }
        .sidebar-nav .nav-link.active { background: rgba(255,255,255,0.12); border-left: 2px solid rgba(255,255,255,0.8); }
        .sidebar-divider { border-top: 1px solid rgba(255,255,255,0.08); margin: 6px 14px; }
        .sidebar-nav .logout-btn { color: rgba(255,255,255,0.65); padding: 8px 14px; border-radius: 7px; margin: 1px 8px; font-size: 0.83rem; display: flex; align-items: center; gap: 9px; background: none; border: none; width: calc(100% - 16px); text-align: left; cursor: pointer; transition: background 0.2s, color 0.2s; }
        .sidebar-nav .logout-btn:hover { background: rgba(192,192,192,0.08); color: #fff; }
        .mobile-topbar { display: none; position: fixed; top: 0; left: 0; right: 0; height: 54px; background: linear-gradient(90deg, var(--sidebar-bg-start), var(--sidebar-bg-end)); z-index: 1060; align-items: center; padding: 0 14px; gap: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
        .mobile-topbar .hamburger-btn { background: none; border: none; color: #fff; font-size: 1.3rem; cursor: pointer; padding: 4px 8px; border-radius: 6px; }
        .mobile-topbar .hamburger-btn:hover { background: rgba(255,255,255,0.15); }
        .mobile-topbar .brand-title { color: #fff; font-weight: 700; font-size: 0.95rem; }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1045; }
        .sidebar-overlay.show { display: block; }
        .topbar { background: white; padding: 12px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 20px; }
        .main-content { margin-left: var(--sidebar-width); padding: 20px 24px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }
        .badge-aktif    { background: #d4edda; color: #155724; }
        .badge-isolir   { background: #f8d7da; color: #721c24; }
        .badge-suspend  { background: #fff3cd; color: #856404; }
        .badge-nonaktif { background: #e2e3e5; color: #383d41; }
        .badge-status   { font-size: 0.75rem; font-weight: 600; padding: 3px 10px; }
        .router-badge   { font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 20px; background: #e8f0fe; color: #1a56db; }
        .pagination .page-link svg { width: 8px !important; height: 8px !important; }
        .pagination { margin-bottom: 0; }
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 14px 14px; }
        }
    </style>
    @stack('styles')
    @stack('head')
</head>
<body>

@include('admin.partials.sidebar')

<div class="main-content">
    @hasSection('show_topbar')
        @include('admin.partials.topbar')
    @endif
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('show');
        });
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
        });
    }
</script>
@stack('scripts')
</body>
</html>
