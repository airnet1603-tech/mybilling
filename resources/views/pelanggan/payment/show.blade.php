<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bayar Tagihan | Portal Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body{background:#f0f2f5;font-family:'Segoe UI',sans-serif}
        .topbar{background:linear-gradient(135deg,#1a1a2e,#0f3460)}
        .topbar-inner{padding:14px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px}
        .brand{color:#fff;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:10px}
        .brand-icon{width:34px;height:34px;background:rgba(233,69,96,0.25);border-radius:8px;display:flex;align-items:center;justify-content:center;color:#e94560}
        .nav-portal a{color:rgba(255,255,255,0.75);text-decoration:none;font-size:0.85rem;padding:6px 12px;border-radius:8px;transition:.15s}
        .nav-portal a:hover,.nav-portal a.active{background:rgba(255,255,255,0.1);color:#fff}
        .main{padding:20px;max-width:600px;margin:0 auto}
        .btn-bayar{background:linear-gradient(135deg,#0f3460,#e94560);border:none;color:#fff;padding:14px;font-size:1.1rem;font-weight:600;border-radius:12px;transition:.2s}
        .btn-bayar:hover{opacity:.9;color:#fff;transform:translateY(-1px)}
    </style>
</head>
<body>
<nav class="topbar">
    <div class="topbar-inner">
        <a href="/pelanggan/dashboard" class="brand">
            <div class="brand-icon"><i class="fas fa-wifi"></i></div>
            <span>ISP Billing <small style="font-weight:400;opacity:.7">Portal Pelanggan</small></span>
        </a>
        <div class="nav-portal d-flex gap-1">
            <a href="/pelanggan/dashboard"><i class="fas fa-home me-1"></i>Home</a>
            <a href="/pelanggan/tagihan" class="active"><i class="fas fa-file-invoice me-1"></i>Tagihan</a>
            <a href="/pelanggan/profil"><i class="fas fa-user me-1"></i>Profil</a>
            <a href="/pelanggan/logout"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
        </div>
    </div>
</nav>

<div class="main">

    {{-- Detail Tagihan --}}
    <div class="card shadow-sm mb-4 mt-3">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Detail Tagihan</h5>
        </div>
        <div class="card-body">
            <table class="table table-sm mb-0">
                <tr><td class="text-muted" width="40%">No. Tagihan</td><td><strong>{{ $tagihan->no_tagihan }}</strong></td></tr>
                <tr><td class="text-muted">Periode</td><td>{{ \Carbon\Carbon::parse($tagihan->periode_bulan)->translatedFormat('F Y') }}</td></tr>
                <tr>
                    <td class="text-muted">Jatuh Tempo</td>
                    <td class="{{ now()->gt($tagihan->tgl_jatuh_tempo) ? 'text-danger fw-bold' : '' }}">
                        {{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d/m/Y') }}
                    </td>
                </tr>
                <tr><td class="text-muted">Total</td><td class="fs-5 fw-bold text-primary">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</td></tr>
            </table>
        </div>
    </div>

    {{-- Pilih Metode --}}
    <div class="card shadow-sm mb-4" id="card-pilih-metode">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Pilih Metode Pembayaran</h5></div>
        <div class="card-body">
            <div class="alert alert-info py-2 mb-3">
                <small><i class="fas fa-info-circle me-1"></i>Tersedia: Virtual Account (semua bank), QRIS, GoPay, dan lainnya via Midtrans.</small>
            </div>
            <button class="btn btn-bayar w-100" onclick="bayarMidtrans()">
                <i class="fas fa-shopping-cart me-2"></i>Bayar Sekarang
                <div class="small fw-normal mt-1">VA Semua Bank · QRIS · GoPay · dll</div>
            </button>
        </div>
    </div>

    {{-- Loading --}}
    <div id="loading" class="text-center py-5 d-none">
        <div class="spinner-border text-primary mb-3" role="status"></div>
        <p class="text-muted">Sedang memproses, harap tunggu...</p>
    </div>

    {{-- Error --}}
    <div id="panel-error" class="card shadow-sm mb-4 d-none">
        <div class="card-body text-center py-4">
            <div class="text-danger mb-3" style="font-size:3rem;"><i class="fas fa-times-circle"></i></div>
            <h5 class="text-danger">Gagal Membuat Pembayaran</h5>
            <p class="text-muted" id="error-message">Terjadi kesalahan.</p>
            <button class="btn btn-primary" onclick="resetPanel()">
                <i class="fas fa-redo me-2"></i>Coba Lagi
            </button>
        </div>
    </div>

    {{-- Panel Sukses --}}
    <div id="panel-sukses" class="card shadow-sm border-success d-none">
        <div class="card-body text-center py-5">
            <div class="text-success mb-3" style="font-size:4rem;"><i class="fas fa-check-circle"></i></div>
            <h4 class="text-success fw-bold">Pembayaran Berhasil!</h4>
            <p class="text-muted">Tagihan {{ $tagihan->no_tagihan }} telah lunas.</p>
            <a href="/pelanggan/tagihan" class="btn btn-primary mt-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Tagihan
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const noTagihan = '{{ $tagihan->no_tagihan }}';
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let checkInterval = null;

function bayarMidtrans() {
    document.getElementById('card-pilih-metode').classList.add('d-none');
    document.getElementById('loading').classList.remove('d-none');

    fetch(`/pelanggan/payment/${noTagihan}/midtrans`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('loading').classList.add('d-none');
        if (!data.success || !data.payment_url) {
            document.getElementById('error-message').textContent = data.message || 'Gagal mendapatkan link pembayaran.';
            document.getElementById('panel-error').classList.remove('d-none');
            return;
        }
        // Redirect ke halaman pembayaran Midtrans
        window.location.href = data.payment_url;
    })
    .catch(() => {
        document.getElementById('loading').classList.add('d-none');
        document.getElementById('error-message').textContent = 'Terjadi kesalahan jaringan.';
        document.getElementById('panel-error').classList.remove('d-none');
    });
}

function resetPanel() {
    document.getElementById('card-pilih-metode').classList.remove('d-none');
    document.getElementById('panel-error').classList.add('d-none');
    document.getElementById('loading').classList.add('d-none');
}

// Cek status otomatis saat kembali dari Midtrans
function cekStatus() {
    fetch(`/pelanggan/payment/${noTagihan}/check`, {
        headers: { 'X-CSRF-TOKEN': csrfToken }
    })
    .then(r => r.json())
    .then(data => {
        if (data.paid) {
            clearInterval(checkInterval);
            document.getElementById('card-pilih-metode').classList.add('d-none');
            document.getElementById('panel-sukses').classList.remove('d-none');
            setTimeout(() => window.location.href = '/pelanggan/tagihan', 3000);
        }
    });
}

// Auto cek setiap 5 detik saat halaman aktif (user kembali dari Midtrans)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        cekStatus();
    }
});
</script>
</body>
</html>