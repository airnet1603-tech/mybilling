@extends('pelanggan.layouts.app')
@section('title', 'Bayar Tagihan')
@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">

      <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Detail Tagihan</h5>
        </div>
        <div class="card-body">
          <table class="table table-sm mb-0">
            <tr><td class="text-muted" width="40%">No. Tagihan</td><td><strong>{{ $tagihan->no_tagihan }}</strong></td></tr>
            <tr><td class="text-muted">Periode</td><td>{{ \Carbon\Carbon::parse($tagihan->periode_bulan)->translatedFormat('F Y') }}</td></tr>
            <tr><td class="text-muted">Jatuh Tempo</td>
              <td class="{{ now()->gt($tagihan->tgl_jatuh_tempo) ? 'text-danger fw-bold' : '' }}">
                {{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d/m/Y') }}
              </td>
            </tr>
            <tr><td class="text-muted">Total</td><td class="fs-5 fw-bold text-primary">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</td></tr>
          </table>
        </div>
      </div>

      <div class="card shadow-sm mb-4" id="card-pilih-metode">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Pilih Metode Pembayaran</h5></div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6">
              <button class="btn btn-outline-primary w-100 py-3" onclick="pilihMetode('qris')">
                <div class="fw-bold fs-4 mb-1">&#9644; QRIS</div>
                <small class="text-muted">Scan QR - semua e-wallet</small>
              </button>
            </div>
            <div class="col-6">
              <button class="btn btn-outline-primary w-100 py-3" onclick="pilihMetode('va')">
                <div class="fw-bold fs-5 mb-1">🏦 Virtual Account</div>
                <small class="text-muted">Transfer Bank BRI</small>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div id="loading" class="text-center py-5 d-none">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <p class="text-muted">Sedang memproses...</p>
      </div>

      <div id="panel-qris" class="card shadow-sm mb-4 d-none">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>Bayar via QRIS</h5>
          <button class="btn btn-sm btn-outline-light" onclick="gantiMetode()">Ganti Metode</button>
        </div>
        <div class="card-body text-center py-4">
          <p class="text-muted mb-3">Scan QR Code menggunakan e-wallet atau mobile banking manapun.</p>
          <div id="qris-container" class="mb-3 d-flex justify-content-center"></div>
          <div class="badge bg-warning text-dark fs-6 mb-3">
            <i class="fas fa-clock me-1"></i>Berlaku hingga pukul: <span id="qris-expired">-</span>
          </div>
          <p class="fw-bold fs-5 mb-3">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</p>
          <div class="alert alert-info py-2 mb-3"><small>Setelah bayar, klik tombol di bawah untuk konfirmasi</small></div>
          <button class="btn btn-success w-100" onclick="cekStatus()">
            <i class="fas fa-sync me-2"></i>Cek Status Pembayaran
          </button>
        </div>
      </div>

      <div id="panel-va" class="card shadow-sm mb-4 d-none">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-university me-2"></i>Virtual Account BRI</h5>
          <button class="btn btn-sm btn-outline-light" onclick="gantiMetode()">Ganti Metode</button>
        </div>
        <div class="card-body">
          <p class="text-muted mb-3">Transfer ke nomor VA berikut via ATM / Mobile Banking BRI:</p>
          <div class="bg-light rounded p-3 mb-3 text-center">
            <p class="text-muted mb-1 small">Nomor Virtual Account BRI</p>
            <div class="d-flex align-items-center justify-content-center gap-2">
              <h3 class="mb-0 fw-bold" id="va-number" style="letter-spacing:3px">-</h3>
              <button class="btn btn-sm btn-outline-secondary" onclick="copyVa()"><i class="fas fa-copy"></i></button>
            </div>
          </div>
          <table class="table table-sm mb-3">
            <tr><td class="text-muted">Total Bayar</td><td class="fw-bold text-primary">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</td></tr>
            <tr><td class="text-muted">Berlaku Hingga</td><td id="va-expired">-</td></tr>
          </table>
          <div class="alert alert-warning py-2 mb-3">
            <small><i class="fas fa-exclamation-triangle me-1"></i>Transfer sesuai nominal persis. Beda 1 rupiah akan gagal.</small>
          </div>
          <button class="btn btn-info text-white w-100" onclick="cekStatus()">
            <i class="fas fa-sync me-2"></i>Cek Status Pembayaran
          </button>
        </div>
      </div>

      <div id="panel-sukses" class="card shadow-sm border-success d-none">
        <div class="card-body text-center py-5">
          <div class="text-success mb-3" style="font-size:4rem;">✅</div>
          <h4 class="text-success fw-bold">Pembayaran Berhasil!</h4>
          <p class="text-muted">Tagihan {{ $tagihan->no_tagihan }} telah lunas.</p>
          <a href="{{ route('pelanggan.tagihan') }}" class="btn btn-primary mt-2">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Tagihan
          </a>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const noTagihan = '{{ $tagihan->no_tagihan }}';
let checkInterval = null;

function pilihMetode(metode) {
    document.getElementById('card-pilih-metode').classList.add('d-none');
    document.getElementById('loading').classList.remove('d-none');

    fetch(`/pelanggan/payment/${noTagihan}/${metode}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loading').classList.add('d-none');
        if (!data.success) { alert('Error: ' + data.message); gantiMetode(); return; }
        if (metode === 'qris') tampilQris(data);
        else tampilVa(data);
    })
    .catch(() => { document.getElementById('loading').classList.add('d-none'); alert('Terjadi kesalahan.'); gantiMetode(); });
}

function tampilQris(data) {
    document.getElementById('panel-qris').classList.remove('d-none');
    document.getElementById('qris-expired').textContent = data.expired_at;
    const container = document.getElementById('qris-container');
    container.innerHTML = '';
    if (data.qris_image) {
        new QRCode(container, { text: data.qris_image, width: 250, height: 250, correctLevel: QRCode.CorrectLevel.M });
    } else {
        container.innerHTML = '<p class="text-danger">Gagal generate QR. Coba refresh.</p>';
    }
    startAutoCheck(5000);
}

function tampilVa(data) {
    document.getElementById('panel-va').classList.remove('d-none');
    document.getElementById('va-number').textContent = data.va_number;
    document.getElementById('va-expired').textContent = data.expired_at;
    startAutoCheck(10000);
}

function startAutoCheck(interval) {
    if (checkInterval) clearInterval(checkInterval);
    checkInterval = setInterval(cekStatus, interval);
}

function cekStatus() {
    fetch(`/pelanggan/payment/${noTagihan}/check`, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        if (data.paid) {
            clearInterval(checkInterval);
            ['panel-qris','panel-va'].forEach(id => document.getElementById(id).classList.add('d-none'));
            document.getElementById('panel-sukses').classList.remove('d-none');
            setTimeout(() => window.location.href = '{{ route("pelanggan.tagihan") }}', 3000);
        }
    });
}

function gantiMetode() {
    clearInterval(checkInterval);
    document.getElementById('card-pilih-metode').classList.remove('d-none');
    ['panel-qris','panel-va','loading'].forEach(id => document.getElementById(id).classList.add('d-none'));
}

function copyVa() {
    const va = document.getElementById('va-number').textContent;
    navigator.clipboard.writeText(va).then(() => {
        const btn = event.target.closest('button');
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => btn.innerHTML = '<i class="fas fa-copy"></i>', 2000);
    });
}
</script>
@endpush
