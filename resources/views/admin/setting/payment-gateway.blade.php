@extends('layouts.admin')

@push('styles')
<style>
    .info-label { font-size:0.7rem; color:#6c757d; font-weight:700; text-transform:uppercase; letter-spacing:0.03em; margin-bottom:4px; }
    .section-title { font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#6c757d; margin-bottom:14px; padding-bottom:8px; border-bottom:2px solid #f0f2f5; }
    .nav-tabs .nav-link { color:#6c757d; border:none; padding:10px 20px; font-size:0.88rem; }
    .nav-tabs .nav-link.active { color:#e94560; border-bottom:2px solid #e94560; font-weight:600; background:none; }
    .gw-tabs .nav-link { color:#6c757d; font-size:0.83rem; padding:7px 16px; border-radius:6px 6px 0 0; border:1px solid transparent; border-bottom:none; }
    .gw-tabs .nav-link.active { color:#e94560; background:#fff; border-color:#dee2e6; font-weight:600; }
    .gw-tabs .nav-link:hover:not(.active) { color:#343a40; background:#f8f9fa; }
    .badge-aktif { background:#d1e7dd; color:#0a3622; font-size:0.7rem; padding:3px 9px; border-radius:20px; }
    .badge-nonaktif { background:#e9ecef; color:#6c757d; font-size:0.7rem; padding:3px 9px; border-radius:20px; }
    .form-switch .form-check-input { width:2.2em; height:1.2em; cursor:pointer; }
    .form-switch .form-check-input:checked { background-color:#e94560; border-color:#e94560; }
    .mode-btn { font-size:0.8rem; padding:5px 14px; border-radius:20px; border:1px solid #dee2e6; background:#f8f9fa; color:#6c757d; cursor:pointer; transition:all .15s; }
    .mode-btn.selected { border-color:#e94560; background:#fff0f2; color:#e94560; font-weight:600; }
    .url-box { background:#f8f9fa; border:1px solid #dee2e6; border-radius:6px; padding:6px 12px; font-size:0.78rem; font-family:monospace; color:#495057; word-break:break-all; }
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

<ul class="nav nav-tabs mb-0">
    <li class="nav-item"><a class="nav-link" href="/admin/setting"><i class="fas fa-building me-1"></i> Umum</a></li>
    <li class="nav-item"><a class="nav-link" href="/admin/setting"><i class="fas fa-file-invoice me-1"></i> Billing</a></li>
    <li class="nav-item"><a class="nav-link" href="/admin/setting"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a></li>
    <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-credit-card me-1"></i> Payment Gateway</a></li>
</ul>

@php
    $gwList = [
        'duitku'   => ['label' => 'Duitku',         'icon' => 'fas fa-wallet'],
        'midtrans' => ['label' => 'Midtrans',        'icon' => 'fas fa-credit-card'],
        'xendit'   => ['label' => 'Xendit',          'icon' => 'fas fa-bolt'],
        'tripay'   => ['label' => 'Tripay',          'icon' => 'fas fa-money-check-alt'],
        'manual'   => ['label' => 'Transfer Manual', 'icon' => 'fas fa-university'],
    ];
    $activeGw = request()->get('gw', 'duitku');
    $s = $settings[$activeGw] ?? [];
    $isActive = ($s['is_active'] ?? '0') === '1';
    $isSandbox = ($s['mode'] ?? 'sandbox') === 'sandbox';
@endphp

<ul class="nav gw-tabs mb-0 mt-3">
    @foreach($gwList as $gwKey => $gwInfo)
    <li class="nav-item">
        <a class="nav-link {{ $activeGw === $gwKey ? 'active' : '' }}" href="?gw={{ $gwKey }}">
            <i class="{{ $gwInfo['icon'] }} me-1"></i> {{ $gwInfo['label'] }}
        </a>
    </li>
    @endforeach
</ul>

<div class="card" style="border-radius:0 12px 12px 12px;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 pb-3" style="border-bottom:2px solid #f0f2f5;">
            <div class="d-flex align-items-center gap-3">
                <h6 class="fw-bold mb-0">
                    <i class="{{ $gwList[$activeGw]['icon'] }} me-2 text-danger"></i>
                    {{ $gwList[$activeGw]['label'] }}
                </h6>
                <span class="{{ $isActive ? 'badge-aktif' : 'badge-nonaktif' }}" id="statusBadge">
                    {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="toggleAktif" {{ $isActive ? 'checked' : '' }} onchange="updateBadge(this)">
                <label class="form-check-label small text-muted" for="toggleAktif">Aktifkan</label>
            </div>
        </div>

        <form method="POST" action="{{ route('setting.payment-gateway.update', $activeGw) }}">
            @csrf @method('PUT')
            <input type="hidden" name="is_active" id="isActiveInput" value="{{ $isActive ? '1' : '0' }}">

            @if($activeGw === 'duitku')
            <div class="section-title"><i class="fas fa-key me-1"></i> Kredensial API</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="info-label">Merchant Code</div>
                    <input type="text" name="merchant_code" class="form-control form-control-sm" value="{{ $s['merchant_code'] ?? '' }}" placeholder="DS12345">
                    <div class="form-text small">Kode merchant dari dashboard Duitku</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">API Key</div>
                    <div class="input-group input-group-sm">
                        <input type="password" name="api_key" id="duitku_api_key" class="form-control" value="{{ $s['api_key'] ?? '' }}" placeholder="••••••••">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('duitku_api_key',this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
            </div>
            <div class="section-title"><i class="fas fa-link me-1"></i> Callback URL</div>
            <div class="url-box mb-1">{{ url('/webhook/duitku') }}</div>
            <div class="form-text small text-muted mb-3">Masukkan URL ini di dashboard Duitku → Pengaturan → Callback URL</div>

            @elseif($activeGw === 'midtrans')
            <div class="section-title"><i class="fas fa-key me-1"></i> Kredensial API</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="info-label">Server Key</div>
                    <div class="input-group input-group-sm">
                        <input type="password" name="server_key" id="mt_server_key" class="form-control" value="{{ $s['server_key'] ?? '' }}" placeholder="SB-Mid-server-xxx">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('mt_server_key',this)"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="form-text small">Dari Access Keys di dashboard Midtrans</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Client Key</div>
                    <input type="text" name="client_key" class="form-control form-control-sm" value="{{ $s['client_key'] ?? '' }}" placeholder="SB-Mid-client-xxx">
                </div>
            </div>
            <div class="section-title"><i class="fas fa-link me-1"></i> Notification URL</div>
            <div class="url-box mb-1">{{ url('/webhook/midtrans') }}</div>
            <div class="form-text small text-muted mb-3">Masukkan di Midtrans → Settings → Configuration → Payment Notification URL</div>

            @elseif($activeGw === 'xendit')
            <div class="section-title"><i class="fas fa-key me-1"></i> Kredensial API</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="info-label">Secret Key</div>
                    <div class="input-group input-group-sm">
                        <input type="password" name="secret_key" id="xen_secret" class="form-control" value="{{ $s['secret_key'] ?? '' }}" placeholder="xnd_production_xxx">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('xen_secret',this)"><i class="fas fa-eye"></i></button>
                    </div>
                    <div class="form-text small">Dari Settings → API Keys di dashboard Xendit</div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Public Key</div>
                    <input type="text" name="public_key" class="form-control form-control-sm" value="{{ $s['public_key'] ?? '' }}" placeholder="xnd_public_xxx">
                </div>
                <div class="col-md-6">
                    <div class="info-label">Webhook Verification Token</div>
                    <div class="input-group input-group-sm">
                        <input type="password" name="webhook_token" id="xen_webhook" class="form-control" value="{{ $s['webhook_token'] ?? '' }}" placeholder="Token verifikasi webhook">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('xen_webhook',this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Business ID <span class="text-muted fw-normal">(opsional)</span></div>
                    <input type="text" name="business_id" class="form-control form-control-sm" value="{{ $s['business_id'] ?? '' }}" placeholder="Opsional">
                </div>
            </div>
            <div class="section-title"><i class="fas fa-link me-1"></i> Webhook URL</div>
            <div class="url-box mb-1">{{ url('/webhook/xendit') }}</div>
            <div class="form-text small text-muted mb-3">Masukkan di Xendit → Settings → Webhooks</div>

            @elseif($activeGw === 'tripay')
            <div class="section-title"><i class="fas fa-key me-1"></i> Kredensial API</div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="info-label">API Key</div>
                    <div class="input-group input-group-sm">
                        <input type="password" name="api_key" id="tp_api" class="form-control" value="{{ $s['api_key'] ?? '' }}" placeholder="DEV-xxx atau PROD-xxx">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('tp_api',this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Private Key</div>
                    <div class="input-group input-group-sm">
                        <input type="password" name="private_key" id="tp_priv" class="form-control" value="{{ $s['private_key'] ?? '' }}" placeholder="Untuk generate signature">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePass('tp_priv',this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">Merchant Code</div>
                    <input type="text" name="merchant_code" class="form-control form-control-sm" value="{{ $s['merchant_code'] ?? '' }}" placeholder="T123456">
                </div>
            </div>
            <div class="section-title"><i class="fas fa-link me-1"></i> Callback URL</div>
            <div class="url-box mb-1">{{ url('/webhook/tripay') }}</div>
            <div class="form-text small text-muted mb-3">Masukkan di Tripay → Merchant → Pengaturan</div>

            @elseif($activeGw === 'manual')
            <div class="section-title"><i class="fas fa-university me-1"></i> Informasi Rekening</div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="info-label">Nama Bank</div>
                    <input type="text" name="nama_bank" class="form-control form-control-sm" value="{{ $s['nama_bank'] ?? '' }}" placeholder="BCA / BRI / Mandiri">
                </div>
                <div class="col-md-4">
                    <div class="info-label">Nomor Rekening</div>
                    <input type="text" name="no_rekening" class="form-control form-control-sm" value="{{ $s['no_rekening'] ?? '' }}" placeholder="1234567890">
                </div>
                <div class="col-md-4">
                    <div class="info-label">Atas Nama</div>
                    <input type="text" name="atas_nama" class="form-control form-control-sm" value="{{ $s['atas_nama'] ?? '' }}" placeholder="Nama pemilik rekening">
                </div>
                <div class="col-12">
                    <div class="info-label">Instruksi Pembayaran</div>
                    <textarea name="instruksi" class="form-control form-control-sm" rows="3" placeholder="Contoh: Transfer ke BCA 1234567890 a/n AirNet ISP, lalu kirim bukti ke WhatsApp admin.">{{ $s['instruksi'] ?? '' }}</textarea>
                    <div class="form-text small">Ditampilkan di halaman tagihan pelanggan</div>
                </div>
            </div>
            @endif

            @if($activeGw !== 'manual')
            <div class="section-title mt-4"><i class="fas fa-toggle-on me-1"></i> Mode Operasi</div>
            <div class="d-flex gap-2 mb-1">
                <button type="button" class="mode-btn {{ $isSandbox ? 'selected' : '' }}" onclick="setMode('sandbox',this)">
                    <i class="fas fa-flask me-1"></i> Sandbox (Testing)
                </button>
                <button type="button" class="mode-btn {{ !$isSandbox ? 'selected' : '' }}" onclick="setMode('production',this)">
                    <i class="fas fa-rocket me-1"></i> Production (Live)
                </button>
            </div>
            <input type="hidden" name="mode" id="modeInput" value="{{ $s['mode'] ?? 'sandbox' }}">
            <div class="form-text small text-danger mt-1">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Gunakan <strong>Sandbox</strong> untuk testing, ganti ke <strong>Production</strong> saat sudah siap live.
            </div>
            @endif

            <div class="d-flex justify-content-end mt-4 pt-3" style="border-top:1px solid #f0f2f5;">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-save me-1"></i> Simpan Pengaturan {{ $gwList[$activeGw]['label'] }}
                </button>
            </div>
        </form>
    </div>
</div>

<div class="mt-3">
    <div class="card">
        <div class="card-body py-2 px-3">
            <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Gateway aktif saat ini: </small>
            @php $aktifList = []; foreach($gwList as $k => $v) { if(($settings[$k]['is_active'] ?? '0') === '1') $aktifList[] = $v['label']; } @endphp
            @if(count($aktifList))
                @foreach($aktifList as $al)<span class="badge-aktif ms-1">{{ $al }}</span>@endforeach
            @else
                <span class="text-muted ms-1 small">Belum ada gateway yang diaktifkan</span>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateBadge(el) {
    const badge = document.getElementById('statusBadge');
    document.getElementById('isActiveInput').value = el.checked ? '1' : '0';
    badge.textContent = el.checked ? 'Aktif' : 'Tidak Aktif';
    badge.className = el.checked ? 'badge-aktif' : 'badge-nonaktif';
}
function setMode(mode, btn) {
    document.getElementById('modeInput').value = mode;
    document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
}
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
}
</script>
@endpush
