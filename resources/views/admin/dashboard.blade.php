@extends('layouts.admin')

@section('title', 'Dashboard - ISP Billing')

@push('styles')
<style>
    .topbar { background: white; padding: 12px 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 20px; }
    .stat-card { border: none; border-radius: 12px; padding: 18px 20px; color: white; box-shadow: 0 4px 15px rgba(0,0,0,0.12); }
    .stat-card.pelanggan  { background: linear-gradient(135deg, #667eea, #764ba2); }
    .stat-card.aktif      { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .stat-card.tagihan    { background: linear-gradient(135deg, #f093fb, #f5576c); }
    .stat-card.pendapatan { background: linear-gradient(135deg, #4facfe, #00f2fe); }
    .stat-card .icon { font-size: 2rem; opacity: 0.25; }
    .stat-card .stat-number { font-size: 1.8rem; font-weight: 700; line-height: 1; }
    .stat-card .stat-label  { font-size: 0.8rem; opacity: 0.85; margin-top: 4px; }
    .badge-aktif    { background: #d4edda; color: #155724; }
    .badge-isolir   { background: #f8d7da; color: #721c24; }
    .badge-suspend  { background: #fff3cd; color: #856404; }
    .badge-nonaktif { background: #e2e3e5; color: #383d41; }
    .badge-status { font-size: 0.72rem; font-weight: 600; padding: 3px 9px; }
    .overdue-item { padding: 10px 16px; border-bottom: 1px solid #f0f2f5; transition: background 0.15s; }
    .overdue-item:last-child { border-bottom: none; }
    .overdue-item:hover { background: #fafafa; }
</style>
@endpush

@section('content')
<div class="topbar d-flex justify-content-between align-items-center">
    <div>
        <h5 class="mb-0 fw-bold">Dashboard</h5>
        <small class="text-muted">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if(auth()->user()->isAdmin())
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
            <small class="text-muted">Administrator</small>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card pelanggan">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="stat-number">{{ $totalPelanggan }}</div><div class="stat-label">Total Pelanggan</div></div>
                <i class="fas fa-users icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card aktif">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="stat-number">{{ $pelangganAktif }}</div><div class="stat-label">Pelanggan Aktif</div></div>
                <i class="fas fa-check-circle icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card tagihan">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="stat-number">{{ $tagihanUnpaid }}</div><div class="stat-label">Tagihan Belum Bayar</div></div>
                <i class="fas fa-file-invoice icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card pendapatan">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-bold" style="font-size:1.1rem;line-height:1.2;">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                    <div class="stat-label">Pendapatan Bulan Ini</div>
                </div>
                <i class="fas fa-wallet icon"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Pelanggan Terbaru</h6>
                <a href="/admin/pelanggan" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.75rem;">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 small">ID</th>
                            <th class="small">Nama</th>
                            <th class="small">Paket</th>
                            <th class="small">Status</th>
                            <th class="small">Expired</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelangganTerbaru as $p)
                        <tr>
                            <td class="ps-3"><small class="text-muted">{{ $p->id_pelanggan }}</small></td>
                            <td><div class="fw-semibold small">{{ $p->nama }}</div></td>
                            <td><span class="badge bg-primary" style="font-size:0.7rem;">{{ $p->paket->nama_paket ?? '-' }}</span></td>
                            <td><span class="badge badge-{{ $p->status }} badge-status rounded-pill">{{ ucfirst($p->status) }}</span></td>
                            <td><small class="{{ $p->tgl_expired && $p->tgl_expired < now() ? 'text-danger fw-bold' : '' }}">{{ $p->tgl_expired?->format('d/m/Y') ?? '-' }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4 small">Belum ada pelanggan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3 pb-2">
                <h6 class="fw-bold mb-0"><i class="fas fa-exclamation-circle me-2 text-danger"></i>Tagihan Overdue</h6>
            </div>
            <div class="card-body p-0">
                @forelse($tagihanOverdue as $t)
                <div class="overdue-item">
                    <div class="fw-semibold small">{{ $t->pelanggan->nama ?? '-' }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-danger fw-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</small>
                        <small class="text-muted">{{ $t->tgl_jatuh_tempo?->format('d/m/Y') }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-check-circle fa-2x text-success mb-2 d-block"></i>
                    <small>Semua tagihan lunas!</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
