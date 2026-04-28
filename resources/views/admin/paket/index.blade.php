@extends('layouts.admin')

@section('title', 'Paket Internet - ISP Billing')

@push('styles')
<style>
    .paket-card { border: none; border-radius: 12px 0px 12px 0px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); transition: transform 0.2s; }
    .paket-card:hover { transform: translateY(-3px); }
    .paket-header { background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 12px 0px 0 0; padding: 8px 12px; color: white; }
    .paket-header.hotspot { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .speed-badge { background: rgba(255,255,255,0.2); border-radius: 20px; padding: 2px 8px; font-size: 0.72rem; }
    .router-bar { background: linear-gradient(135deg, #1a1a2e, #0f3460); border-radius: 10px; padding: 12px 16px; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="fw-bold mb-0">Paket Internet</h5>
        <small class="text-muted">Kelola paket layanan internet</small>
    </div>
    @if($router_id)
    <a href="/admin/paket/create?router_id={{ $router_id }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Tambah Paket
    </a>
    @endif
</div>

{{-- Router Selector --}}
<div class="router-bar mb-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="text-white small fw-semibold"><i class="fas fa-server me-2"></i>Pilih Router:</div>
        <div class="d-flex gap-2 flex-wrap">
            @foreach($routers as $router)
            <a href="/admin/paket?router_id={{ $router->id }}"
               class="btn btn-sm {{ $router_id == $router->id ? 'btn-warning text-dark fw-bold' : 'btn-outline-light' }}">
                <i class="fas fa-router me-1"></i>{{ $router->nama }}
                @if($router_id == $router->id)
                    <i class="fas fa-check ms-1"></i>
                @endif
            </a>
            @endforeach
        </div>
        @if($selectedRouter)
        <div class="ms-auto">
            <span class="badge bg-success"><i class="fas fa-circle me-1" style="font-size:0.6rem;"></i>{{ $selectedRouter->ip_address }}</span>
        </div>
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if(!$router_id)
<div class="card text-center py-5">
    <div class="card-body">
        <i class="fas fa-server fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Pilih router terlebih dahulu</h5>
        <p class="text-muted small">Klik salah satu router di atas untuk melihat paketnya</p>
    </div>
</div>
@else
<div class="row g-3">
    @forelse($pakets as $paket)
    <div class="col-md-3">
        <div class="paket-card card">
            <div class="paket-header {{ $paket->jenis == 'hotspot' ? 'hotspot' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold mb-1" style="font-size:0.85rem;">{{ $paket->nama_paket }}</div>
                        <span class="speed-badge"><i class="fas fa-arrow-down me-1"></i>{{ $paket->kecepatan_download }} Mbps <i class="fas fa-arrow-up ms-2 me-1"></i>{{ $paket->kecepatan_upload }} Mbps</span>
                    </div>
                    <span class="badge bg-white text-dark">{{ strtoupper($paket->jenis) }}</span>
                </div>
                <div class="mt-1" style="line-height:1.2;">
                    <span class="fw-bold" style="font-size:0.9rem;">Rp {{ number_format($paket->harga, 0, ',', '.') }}</span>
                    <span class="opacity-75 small">/bulan</span>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="row text-center mb-2">
                    <div class="col-4 border-end">
                        <div class="fw-bold text-primary small">{{ $paket->masa_aktif }}</div>
                        <small class="text-muted" style="font-size:0.7rem;">{{ ucfirst($paket->tipe_masa_aktif ?? 'Hari') }}</small>
                    </div>
                    <div class="col-4 border-end">
                        <div class="fw-bold text-success small">{{ $paket->pelanggan()->count() }}</div>
                        <small class="text-muted" style="font-size:0.7rem;">Pelanggan</small>
                    </div>
                    <div class="col-4">
                        @if($paket->is_active)
                            <div class="fw-bold text-success small"><i class="fas fa-check-circle"></i></div>
                            <small class="text-muted" style="font-size:0.7rem;">Aktif</small>
                        @else
                            <div class="fw-bold text-danger small"><i class="fas fa-times-circle"></i></div>
                            <small class="text-muted" style="font-size:0.7rem;">Nonaktif</small>
                        @endif
                    </div>
                </div>
                <div class="mb-2">
                    <small class="text-muted" style="font-size:0.72rem;"><i class="fas fa-server me-1"></i>Radius Profile: <code>{{ $paket->radius_profile }}</code></small>
                </div>
                @if($paket->deskripsi)
                <p class="text-muted small mb-2" style="font-size:0.72rem;">{{ $paket->deskripsi }}</p>
                @endif
                <div class="d-flex gap-2">
                    <a href="/admin/paket/{{ $paket->id }}/edit" class="btn btn-warning btn-sm text-white flex-fill py-1"><i class="fas fa-edit me-1"></i> Edit</a>
                    <form method="POST" action="/admin/paket/{{ $paket->id }}" onsubmit="return confirm('Hapus paket ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm py-1"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada paket di router ini</h5>
                <a href="/admin/paket/create?router_id={{ $router_id }}" class="btn btn-primary btn-sm mt-2"><i class="fas fa-plus me-1"></i> Tambah Paket</a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($pakets->hasPages())
<div class="mt-3">{{ $pakets->appends(['router_id' => $router_id])->links() }}</div>
@endif
@endif
@endsection
