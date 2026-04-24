@extends('layouts.admin')

@section('title', 'Edit Paket - ISP Billing')

@push('styles')
<style>
    .profile-header { background: linear-gradient(135deg, #1a1a2e, #0f3460); border-radius: 12px; padding: 20px; color: white; margin-bottom: 12px; }
    .avatar { width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0; }
    .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
    .card-section-title { font-size: 0.88rem; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Edit Paket</h5>
        <small class="text-muted">{{ $paket->nama_paket }}</small>
    </div>
    <a href="/admin/paket" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i>
    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <div class="profile-header">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar me-3"><i class="fas fa-box"></i></div>
                <div>
                    <div class="fw-bold fs-6">{{ $paket->nama_paket }}</div>
                    <div class="opacity-75 small">{{ strtoupper($paket->jenis) }}</div>
                </div>
            </div>
            @if($paket->is_active)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600;background:#d4edda;color:#155724;">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i> Aktif
                </span>
            @else
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600;background:#e2e3e5;color:#383d41;">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i> Nonaktif
                </span>
            @endif
        </div>
        <div class="card">
            <div class="card-body py-3">
                <div class="card-section-title mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Info Paket</div>
                <div class="mb-2">
                    <div class="info-label">Harga</div>
                    <div class="small fw-semibold text-success">Rp {{ number_format($paket->harga, 0, ',', '.') }} / bulan</div>
                </div>
                <div class="mb-2">
                    <div class="info-label">Kecepatan</div>
                    <div class="small">↓ {{ $paket->kecepatan_download }} Mbps &nbsp;|&nbsp; ↑ {{ $paket->kecepatan_upload }} Mbps</div>
                </div>
                <div class="mb-2">
                    <div class="info-label">Masa Aktif</div>
                    <div class="small">{{ $paket->masa_aktif }} hari</div>
                </div>
                <div>
                    <div class="info-label">Radius Profile</div>
                    <code class="small">{{ $paket->radius_profile ?? '-' }}</code>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body py-3">
                <div class="card-section-title mb-3"><i class="fas fa-edit me-2 text-primary"></i>Edit Detail Paket</div>
                <form method="POST" action="/admin/paket/{{ $paket->id }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="info-label">Nama Paket</div>
                            <input type="text" name="nama_paket" class="form-control form-control-sm" value="{{ old('nama_paket', $paket->nama_paket) }}" required>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Jenis</div>
                            <select name="jenis" class="form-select form-select-sm">
                                <option value="pppoe" {{ $paket->jenis=='pppoe' ? 'selected':'' }}>PPPoE</option>
                                <option value="hotspot" {{ $paket->jenis=='hotspot' ? 'selected':'' }}>Hotspot</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Harga (Rp)</div>
                            <input type="number" name="harga" class="form-control form-control-sm" value="{{ old('harga', $paket->harga) }}" required>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Masa Aktif (hari)</div>
                            <input type="number" name="masa_aktif" class="form-control form-control-sm" value="{{ old('masa_aktif', $paket->masa_aktif) }}" required>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Download (Mbps)</div>
                            <input type="number" name="kecepatan_download" class="form-control form-control-sm" value="{{ old('kecepatan_download', $paket->kecepatan_download) }}" required>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Upload (Mbps)</div>
                            <input type="number" name="kecepatan_upload" class="form-control form-control-sm" value="{{ old('kecepatan_upload', $paket->kecepatan_upload) }}" required>
                        </div>
                        <div class="col-12">
                            <div class="card-section-title mb-2 mt-2"><i class="fas fa-bolt me-2 text-warning"></i>Burst (Opsional)</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Burst Limit Download (Mbps)</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="burst_limit_download" class="form-control" value="{{ old('burst_limit_download', $paket->burst_limit_download) }}" min="0">
                                <span class="input-group-text">Mbps</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Burst Limit Upload (Mbps)</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="burst_limit_upload" class="form-control" value="{{ old('burst_limit_upload', $paket->burst_limit_upload) }}" min="0">
                                <span class="input-group-text">Mbps</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Burst Threshold Download (Mbps)</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="burst_threshold_download" class="form-control" value="{{ old('burst_threshold_download', $paket->burst_threshold_download) }}" min="0">
                                <span class="input-group-text">Mbps</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Burst Threshold Upload (Mbps)</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="burst_threshold_upload" class="form-control" value="{{ old('burst_threshold_upload', $paket->burst_threshold_upload) }}" min="0">
                                <span class="input-group-text">Mbps</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Burst Time (detik)</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="burst_time" class="form-control" value="{{ old('burst_time', $paket->burst_time) }}" min="0">
                                <span class="input-group-text">detik</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-label">Radius Profile</div>
                            <input type="text" name="radius_profile" class="form-control form-control-sm" value="{{ old('radius_profile', $paket->radius_profile) }}">
                        </div>
                        <div class="col-md-12">
                            <div class="info-label">Deskripsi</div>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="2">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" {{ $paket->is_active ? 'checked':'' }}>
                                <label class="form-check-label fw-semibold small" for="isActive">Paket Aktif</label>
                            </div>
                        </div>
                        <div class="col-12 pt-1">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                            <a href="/admin/paket" class="btn btn-secondary btn-sm ms-2"><i class="fas fa-times me-1"></i> Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
