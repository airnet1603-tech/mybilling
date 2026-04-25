@extends('layouts.admin')

@section('title', 'Tambah Paket - ISP Billing')

@push('styles')
<style>
    .profile-header { background: linear-gradient(135deg, #1a1a2e, #0f3460); border-radius: 12px; padding: 20px; color: white; margin-bottom: 12px; }
    .avatar { width: 60px; height: 60px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; flex-shrink: 0; }
    .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
    .card-section-title { font-size: 0.88rem; font-weight: 700; }
    .preview-speed-badge { display: inline-flex; align-items: center; gap: 4px; background: rgba(255,255,255,0.2); border-radius: 20px; padding: 3px 10px; font-size: 0.78rem; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Tambah Paket Internet</h5>
        <small class="text-muted">Buat paket layanan internet baru</small>
    </div>
    <a href="/admin/paket{{ $router_id ? '?router_id='.$router_id : '' }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i>
    <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    <div class="col-md-4">
        <div class="profile-header">
            <div class="d-flex align-items-center mb-3">
                <div class="avatar me-3"><i class="fas fa-box"></i></div>
                <div>
                    <div class="fw-bold fs-6" id="prev-nama">Nama Paket</div>
                    <div class="opacity-75 small" id="prev-jenis">PPPoE</div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <span class="preview-speed-badge"><i class="fas fa-arrow-down fa-xs"></i><span id="prev-dl">0</span> Mbps</span>
                <span class="preview-speed-badge"><i class="fas fa-arrow-up fa-xs"></i><span id="prev-ul">0</span> Mbps</span>
            </div>
        </div>
        <div class="card">
            <div class="card-body py-3">
                <div class="card-section-title mb-3"><i class="fas fa-eye me-2 text-primary"></i>Preview Paket</div>
                <div class="mb-2">
                    <div class="info-label">Router</div>
                    <div class="small fw-semibold text-primary"><i class="fas fa-server me-1"></i><span id="prev-router">-</span></div>
                </div>
                <div class="mb-2">
                    <div class="info-label">Harga</div>
                    <div class="small fw-semibold text-success">Rp <span id="prev-harga">0</span> / bulan</div>
                </div>
                <div class="mb-2">
                    <div class="info-label">Masa Aktif</div>
                    <div class="small"><span id="prev-masa">30</span> hari</div>
                </div>
                <div class="mb-2">
                    <div class="info-label">Radius Profile</div>
                    <code class="small" id="prev-radius">-</code>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <span id="prev-status" class="badge bg-success">Aktif</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <form method="POST" action="/admin/paket" id="formPaket">
            @csrf

            {{-- ROUTER SELECTOR --}}
            <div class="card mb-3 border-primary">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3"><i class="fas fa-server me-2 text-primary"></i>Pilih Router <span class="text-danger">*</span></div>
                    <div class="row g-2">
                        @foreach($routers as $router)
                        <div class="col-md-6">
                            <input type="radio" class="btn-check" name="router_id" id="router_{{ $router->id }}"
                                   value="{{ $router->id }}"
                                   {{ (old('router_id', $router_id) == $router->id) ? 'checked' : '' }}
                                   required onchange="updateRouterPreview()">
                            <label class="btn btn-outline-primary w-100 text-start py-2" for="router_{{ $router->id }}">
                                <i class="fas fa-router me-2"></i>
                                <strong>{{ $router->nama }}</strong><br>
                                <small class="text-muted ms-4">{{ $router->ip_address }}:{{ $router->port }}</small>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @error('router_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Paket</div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <div class="info-label">Nama Paket <span class="text-danger">*</span></div>
                            <input type="text" name="nama_paket" id="nama_paket" class="form-control form-control-sm @error('nama_paket') is-invalid @enderror" value="{{ old('nama_paket') }}" placeholder="Contoh: Paket 20 Mbps" required>
                            @error('nama_paket')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Jenis <span class="text-danger">*</span></div>
                            <select name="jenis" class="form-select form-select-sm" required>
                                <option value="pppoe" {{ old('jenis')=='pppoe' ? 'selected':'' }}>PPPoE</option>
                                <option value="hotspot" {{ old('jenis')=='hotspot' ? 'selected':'' }}>Hotspot</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Harga per Bulan <span class="text-danger">*</span></div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga" id="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}" placeholder="150000" required min="0">
                                @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Masa Aktif (hari) <span class="text-danger">*</span></div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="masa_aktif" id="masa_aktif" class="form-control form-control-sm" style="max-width:70px;" value="{{ old('masa_aktif', 30) }}" required min="1" onchange="hitungTanggal();">
                                <select class="form-select form-select-sm" style="max-width:90px;" onchange="document.getElementById('masa_aktif').value=this.value;hitungTanggal();">
                                    <option value="">Pilih</option>
                                    <option value="7">7 hari</option>
                                    <option value="14">14 hari</option>
                                    <option value="30">30 hari</option>
                                    <option value="60">60 hari</option>
                                    <option value="90">90 hari</option>
                                    <option value="180">180 hari</option>
                                    <option value="365">365 hari</option>
                                </select>
                                <input type="date" id="tgl_expired_preview" class="form-control form-control-sm" style="max-width:150px;" onchange="hitungHari();">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Kecepatan & Radius</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Download (Mbps) <span class="text-danger">*</span></div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="kecepatan_download" id="dl" class="form-control @error('kecepatan_download') is-invalid @enderror" value="{{ old('kecepatan_download') }}" placeholder="20" required min="1">
                                <span class="input-group-text">Mbps</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Upload (Mbps) <span class="text-danger">*</span></div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="kecepatan_upload" id="ul" class="form-control @error('kecepatan_upload') is-invalid @enderror" value="{{ old('kecepatan_upload') }}" placeholder="10" required min="1">
                                <span class="input-group-text">Mbps</span>
                            </div>
                        </div>
                        <div class="col-12"><div class="fw-semibold small text-warning mb-2"><i class="fas fa-bolt me-1"></i>Burst (Opsional)</div></div>
                        <div class="col-md-6"><div class="info-label">Burst Limit Download (Mbps)</div><div class="input-group input-group-sm"><input type="number" name="burst_limit_download" class="form-control" value="{{ old('burst_limit_download', 0) }}" min="0"><span class="input-group-text">Mbps</span></div></div>
                        <div class="col-md-6"><div class="info-label">Burst Limit Upload (Mbps)</div><div class="input-group input-group-sm"><input type="number" name="burst_limit_upload" class="form-control" value="{{ old('burst_limit_upload', 0) }}" min="0"><span class="input-group-text">Mbps</span></div></div>
                        <div class="col-md-6"><div class="info-label">Burst Threshold Download (Mbps)</div><div class="input-group input-group-sm"><input type="number" name="burst_threshold_download" class="form-control" value="{{ old('burst_threshold_download', 0) }}" min="0"><span class="input-group-text">Mbps</span></div></div>
                        <div class="col-md-6"><div class="info-label">Burst Threshold Upload (Mbps)</div><div class="input-group input-group-sm"><input type="number" name="burst_threshold_upload" class="form-control" value="{{ old('burst_threshold_upload', 0) }}" min="0"><span class="input-group-text">Mbps</span></div></div>
                        <div class="col-md-6"><div class="info-label">Burst Time (detik)</div><div class="input-group input-group-sm"><input type="number" name="burst_time" class="form-control" value="{{ old('burst_time', 8) }}" min="0"><span class="input-group-text">detik</span></div></div>
                        <div class="col-12">
                            <div class="info-label">Radius Profile Name <span class="text-danger">*</span></div>
                            <input type="text" name="radius_profile" id="radius_profile" class="form-control form-control-sm @error('radius_profile') is-invalid @enderror" value="{{ old('radius_profile') }}" placeholder="Contoh: paket-20mbps" required>
                            <div class="form-text small text-muted">Harus sama dengan nama profile di FreeRADIUS / MikroTik</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Deskripsi</div>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="2" placeholder="Keterangan tambahan paket...">{{ old('deskripsi') }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked':'' }}>
                                <label class="form-check-label small fw-semibold" for="is_active">Paket Aktif (bisa dipilih pelanggan)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Simpan & Sync ke Mikrotik</button>
                <a href="/admin/paket{{ $router_id ? '?router_id='.$router_id : '' }}" class="btn btn-secondary btn-sm"><i class="fas fa-times me-1"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Data router untuk preview
const routerData = {
    @foreach($routers as $router)
    {{ $router->id }}: '{{ $router->nama }}',
    @endforeach
};

function updateRouterPreview() {
    const selected = document.querySelector('[name="router_id"]:checked');
    document.getElementById('prev-router').textContent = selected ? routerData[selected.value] : '-';
}

function updatePreview() {
    document.getElementById('prev-nama').textContent = document.getElementById('nama_paket').value || 'Nama Paket';
    const jenis = document.querySelector('[name="jenis"]').value;
    document.getElementById('prev-jenis').textContent = jenis === 'pppoe' ? 'PPPoE' : 'Hotspot';
    document.getElementById('prev-dl').textContent = document.getElementById('dl').value || '0';
    document.getElementById('prev-ul').textContent = document.getElementById('ul').value || '0';
    const harga = parseInt(document.getElementById('harga').value) || 0;
    document.getElementById('prev-harga').textContent = harga.toLocaleString('id-ID');
    document.getElementById('prev-masa').textContent = document.querySelector('[name="masa_aktif"]').value || '30';
    document.getElementById('prev-radius').textContent = document.getElementById('radius_profile').value || '-';
    const aktif = document.getElementById('is_active').checked;
    const el = document.getElementById('prev-status');
    el.textContent = aktif ? 'Aktif' : 'Nonaktif';
    el.className = aktif ? 'badge bg-success' : 'badge bg-secondary';
    updateRouterPreview();
}

document.getElementById('nama_paket').addEventListener('input', updatePreview);
document.getElementById('harga').addEventListener('input', updatePreview);
document.getElementById('dl').addEventListener('input', updatePreview);
document.getElementById('ul').addEventListener('input', updatePreview);
document.getElementById('radius_profile').addEventListener('input', updatePreview);
document.querySelector('[name="jenis"]').addEventListener('change', updatePreview);
document.querySelector('[name="masa_aktif"]').addEventListener('input', function(){ updatePreview(); hitungTanggal(); });
document.querySelectorAll('[name="router_id"]').forEach(el => el.addEventListener('change', updatePreview));

function hitungTanggal() {
    var hari = parseInt(document.getElementById('masa_aktif').value) || 0;
    if (hari > 0) {
        var tgl = new Date();
        tgl.setDate(tgl.getDate() + hari);
        document.getElementById('tgl_expired_preview').value = tgl.toISOString().split('T')[0];
    }
}
function hitungHari() {
    var tgl = new Date(document.getElementById('tgl_expired_preview').value);
    var today = new Date();
    today.setHours(0,0,0,0);
    var diff = Math.round((tgl - today) / (1000*60*60*24));
    if (diff > 0) { document.getElementById('masa_aktif').value = diff; updatePreview(); }
}
window.addEventListener('load', function(){ hitungTanggal(); updatePreview(); });
document.getElementById('is_active').addEventListener('change', updatePreview);
</script>
@endpush
