@extends('layouts.admin')

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #1a1a2e, #0f3460);
        border-radius: 12px;
        padding: 20px;
        color: white;
        margin-bottom: 12px;
    }
    .avatar {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .info-label {
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 2px;
    }
    .card-section-title { font-size: 0.88rem; font-weight: 700; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Buat Tagihan Manual</h5>
        <small class="text-muted">Buat tagihan untuk pelanggan tertentu</small>
    </div>
    <a href="/admin/tagihan" class="btn btn-secondary btn-sm">
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
                <div class="avatar me-3"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <div class="fw-bold fs-6">Tagihan Baru</div>
                    <div class="opacity-75 small">Buat tagihan manual</div>
                </div>
            </div>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600;background:rgba(255,255,255,0.2);color:#fff;">
                <i class="fas fa-circle" style="font-size:0.5rem;"></i> Manual
            </span>
        </div>

        <div class="card">
            <div class="card-body py-3">
                <div class="card-section-title mb-3">
                    <i class="fas fa-user me-2 text-primary"></i>Info Pelanggan
                </div>
                <div id="infoEmpty" class="text-muted small">
                    <i class="fas fa-arrow-right me-1"></i> Pilih pelanggan untuk melihat info
                </div>
                <div id="infoDetail" class="d-none">
                    <div class="mb-2">
                        <div class="info-label">Nama Pelanggan</div>
                        <div class="small fw-semibold" id="info-nama">-</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Paket</div>
                        <div class="small" id="info-paket">-</div>
                    </div>
                    <div>
                        <div class="info-label">Harga Paket</div>
                        <div class="small fw-semibold text-success" id="info-harga">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body py-3">
                <div class="card-section-title mb-3">
                    <i class="fas fa-edit me-2 text-primary"></i>Detail Tagihan
                </div>
                <form method="POST" action="/admin/tagihan">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Pilih Pelanggan <span class="text-danger">*</span></div>
                            <select name="pelanggan_id" class="form-select form-select-sm" required onchange="updateInfo(this)">
                                <option value="">-- Pilih Pelanggan --</option>
                                @foreach($pelanggans as $p)
                                <option value="{{ $p->id }}"
                                        data-nama="{{ $p->nama }}"
                                        data-paket="{{ $p->paket->nama_paket ?? '-' }}"
                                        data-harga="{{ $p->paket->harga ?? 0 }}"
                                        {{ old('pelanggan_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->id_pelanggan }} - {{ $p->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Tanggal Jatuh Tempo <span class="text-danger">*</span></div>
                            <input type="date" name="tgl_jatuh_tempo" class="form-control form-control-sm"
                                   value="{{ old('tgl_jatuh_tempo', now()->addDays(10)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Diskon (Rp)</div>
                            <input type="number" name="diskon" class="form-control form-control-sm"
                                   value="{{ old('diskon', 0) }}" min="0">
                        </div>
                        <div class="col-12">
                            <div class="info-label">Catatan</div>
                            <input type="text" name="catatan" class="form-control form-control-sm"
                                   placeholder="Opsional" value="{{ old('catatan') }}">
                        </div>
                        <div class="col-12 pt-1">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i> Buat Tagihan
                            </button>
                            <a href="/admin/tagihan" class="btn btn-secondary btn-sm ms-2">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateInfo(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (sel.value) {
        document.getElementById('info-nama').textContent  = opt.dataset.nama;
        document.getElementById('info-paket').textContent = opt.dataset.paket;
        document.getElementById('info-harga').textContent = 'Rp ' + parseInt(opt.dataset.harga).toLocaleString('id-ID') + ' / bulan';
        document.getElementById('infoEmpty').classList.add('d-none');
        document.getElementById('infoDetail').classList.remove('d-none');
    } else {
        document.getElementById('infoEmpty').classList.remove('d-none');
        document.getElementById('infoDetail').classList.add('d-none');
    }
}
</script>
@endsection
