<div class="topbar d-flex justify-content-between align-items-center">
    <div>
        <h5 class="mb-0 fw-bold">@yield('page_title', 'Dashboard')</h5>
        <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
        <a href="/admin/users" style="display:flex;align-items:center;gap:6px;color:#444;font-size:0.82rem;text-decoration:none;font-weight:500;">
            <i class="fas fa-user-cog" style="font-size:1rem;"></i> Kelola User
        </a>
        @else
        <span style="display:flex;align-items:center;gap:6px;color:#bbb;font-size:0.82rem;font-weight:500;cursor:not-allowed;">
            <i class="fas fa-user-cog" style="font-size:1rem;"></i> Kelola User
        </span>
        @endif
        <span style="color:#ccc;font-size:1rem;">|</span>
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-secondary text-white" style="width:36px;height:36px;font-size:1rem;">
            <i class="fas fa-user"></i>
        </div>
        <div>
            <div class="fw-semibold small">{{ auth()->user()->name }}</div>
            <small class="text-muted">{{ auth()->user()->isSuperAdmin() ? 'Super Admin' : (auth()->user()->isAdmin() ? 'Administrator' : 'Staff') }}</small>
        </div>
    </div>
</div>
