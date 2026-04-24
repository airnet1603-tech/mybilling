@extends('layouts.admin')

@push('styles')
<style>
    .info-label { font-size:0.7rem; color:#6c757d; font-weight:700; text-transform:uppercase; letter-spacing:0.03em; margin-bottom:4px; }
    .section-title { font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#6c757d; margin-bottom:14px; padding-bottom:8px; border-bottom:2px solid #f0f2f5; }
    .nav-tabs .nav-link { color:#6c757d; border:none; padding:10px 20px; font-size:0.88rem; }
    .nav-tabs .nav-link.active { color:#e94560; border-bottom:2px solid #e94560; font-weight:600; background:none; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Pengaturan Sistem</h5>
        <small class="text-muted">Konfigurasi sistem billing ISP</small>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="/admin/setting">
    @csrf
    @method('PUT')

    <ul class="nav nav-tabs mb-4" id="settingTab">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#umum">
                <i class="fas fa-building me-1"></i> Umum
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#billing">
                <i class="fas fa-file-invoice me-1"></i> Billing
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#whatsapp">
                <i class="fab fa-whatsapp me-1"></i> WhatsApp
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/admin/setting/payment-gateway">
                <i class="fas fa-credit-card me-1"></i> Payment Gateway
            </a>
        </li>
    </ul>

    <div class="tab-content">

        {{-- TAB UMUM --}}
        <div class="tab-pane fade show active" id="umum">
            <div class="card">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-building me-1"></i> Informasi ISP</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Nama ISP</div>
                            <input type="text" name="nama_isp" class="form-control form-control-sm"
                                   value="{{ $settings['nama_isp'] ?? '' }}" placeholder="Contoh: AirNet ISP">
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">No. HP Admin</div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">+62</span>
                                <input type="text" name="no_admin" class="form-control"
                                       value="{{ $settings['no_admin'] ?? '' }}" placeholder="812xxxxxxxx">
                            </div>
                            <div class="form-text small text-muted">Untuk pemberitahuan & kontak pelanggan</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Alamat ISP</div>
                            <textarea name="alamat_isp" class="form-control form-control-sm" rows="2"
                                      placeholder="Alamat kantor ISP">{{ $settings['alamat_isp'] ?? '' }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Info Pembayaran</div>
                            <textarea name="info_pembayaran" class="form-control form-control-sm" rows="3"
                                      placeholder="Contoh: BCA 1234567890 a/n Nama ISP">{{ $settings['info_pembayaran'] ?? '' }}</textarea>
                            <div class="form-text small text-muted">Ditampilkan di pesan WA tagihan pelanggan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB BILLING --}}
        <div class="tab-pane fade" id="billing">
            <div class="card">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-file-invoice me-1"></i> Pengaturan Billing Otomatis</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">Tanggal Jatuh Tempo</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="hari_jatuh_tempo" class="form-control"
                                       value="{{ $settings['hari_jatuh_tempo'] ?? '10' }}" min="1" max="28">
                                <span class="input-group-text">setiap bulan</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Denda Keterlambatan</div>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="denda_terlambat" class="form-control"
                                       value="{{ $settings['denda_terlambat'] ?? '10000' }}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Hari Sebelum Isolir</div>
                            <div class="input-group input-group-sm">
                                <input type="number" name="hari_isolir" class="form-control"
                                       value="{{ $settings['hari_isolir'] ?? '3' }}" min="1">
                                <span class="input-group-text">hari setelah JT</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Cara kerja auto billing:</strong> Setiap tanggal 1 sistem otomatis buat tagihan semua pelanggan aktif.
                                Setiap hari sistem cek jatuh tempo dan isolir pelanggan yang belum bayar.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB WHATSAPP --}}
        <div class="tab-pane fade" id="whatsapp">
            <div class="card">
                <div class="card-body">
                    <div class="section-title"><i class="fab fa-whatsapp me-1"></i> Konfigurasi WhatsApp Gateway</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Gateway WA</div>
                            <select name="wa_gateway" class="form-select form-select-sm">
                                <option value="fonnte"   {{ ($settings['wa_gateway'] ?? '') == 'fonnte'   ? 'selected':'' }}>Fonnte</option>
                                <option value="wablas"   {{ ($settings['wa_gateway'] ?? '') == 'wablas'   ? 'selected':'' }}>WABLAS</option>
                                <option value="ultramsg" {{ ($settings['wa_gateway'] ?? '') == 'ultramsg' ? 'selected':'' }}>UltraMsg</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Token / API Key</div>
                            <input type="text" name="wa_token" class="form-control form-control-sm"
                                   value="{{ $settings['wa_token'] ?? '' }}" placeholder="Token dari provider gateway">
                        </div>
                        <div class="col-12">
                            <div class="info-label">Base URL (khusus WABLAS)</div>
                            <input type="text" name="wa_base_url" class="form-control form-control-sm"
                                   value="{{ $settings['wa_base_url'] ?? '' }}" placeholder="https://app.wablas.com">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-warning small">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>WA Otomatis dikirim saat:</strong> Tagihan dibuat, H-3 & H-1 sebelum jatuh tempo, saat isolir, dan konfirmasi pembayaran.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn btn-primary btn-sm px-4">
            <i class="fas fa-save me-1"></i> Simpan Semua Pengaturan
        </button>
    </div>

</form>

@endsection
