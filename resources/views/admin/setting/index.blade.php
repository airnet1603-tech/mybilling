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

<form method="POST" action="/admin/setting" enctype="multipart/form-data">
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
                        <div class="col-12">
                            <div class="info-label">Upload QRIS <small class="text-muted">(jpg/png, opsional)</small></div>
                            @if(!empty($settings['wa_qris_url']))
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <img src="{{ asset('images/payment/qris.jpg') }}" style="height:80px;border-radius:6px;border:1px solid #ddd;">
                                <a href="{{ asset('images/payment/qris.jpg') }}" target="_blank" class="small">Lihat QRIS</a>
                                <a href="/admin/setting/qris/delete" class="small text-danger ms-2"
                                   onclick="return confirm('Hapus QRIS?');">🗑 Hapus</a>
                            </div>
                            @endif
                            <input type="file" name="qris_file" class="form-control form-control-sm" accept="image/*">
                            <div class="form-text small text-muted">Dikirim bersama pesan WA tagihan & reminder</div>
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
            {{-- JADWAL WA --}}
            <div class="card mt-3">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-clock me-1"></i> Jadwal Pengiriman WA</div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="wa_jadwal_tagihan" value="1" id="jt" {{ ($settings['wa_jadwal_tagihan'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jt">Kirim saat tagihan dibuat</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="wa_jadwal_reminder" value="1" id="jr" {{ ($settings['wa_jadwal_reminder'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jr">Kirim reminder jatuh tempo</label>
                            </div>
                            <div class="mt-1">
                                <small class="text-muted">Hari pengiriman (pisah koma, contoh: 7,3,1)</small>
                                <input type="text" name="wa_jadwal_hari_reminder" class="form-control form-control-sm mt-1"
                                    value="{{ $settings['wa_jadwal_hari_reminder'] ?? '3,1' }}"
                                    placeholder="3,1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="wa_jadwal_isolir" value="1" id="ji" {{ ($settings['wa_jadwal_isolir'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="ji">Kirim saat isolir</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="wa_jadwal_konfirmasi" value="1" id="jk" {{ ($settings['wa_jadwal_konfirmasi'] ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="jk">Kirim saat konfirmasi bayar</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- TEMPLATE WA --}}
            <div class="card mt-3">
                <div class="card-body">
                    <div class="section-title"><i class="fas fa-comment-alt me-1"></i> Template Pesan WA</div>
                    <div class="alert alert-info small">Variabel: <code>{nama}</code> <code>{periode}</code> <code>{total}</code> <code>{jatuh_tempo}</code> <code>{sisa_hari}</code></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="info-label">Template Tagihan Baru</div>
                            <textarea name="wa_template_tagihan" class="form-control form-control-sm" rows="3">{{ $settings['wa_template_tagihan'] ?? '' }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Template Reminder Jatuh Tempo <small class="text-muted">(gunakan <code>{sisa_hari}</code> untuk jumlah hari)</small></div>
                            <textarea name="wa_template_reminder" class="form-control form-control-sm" rows="3">{{ $settings['wa_template_reminder'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Template Isolir</div>
                            <textarea name="wa_template_isolir" class="form-control form-control-sm" rows="3">{{ $settings['wa_template_isolir'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Template Konfirmasi Bayar</div>
                            <textarea name="wa_template_konfirmasi" class="form-control form-control-sm" rows="3">{{ $settings['wa_template_konfirmasi'] ?? '' }}</textarea>
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
