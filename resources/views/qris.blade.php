<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - {{ $settings['nama_isp'] ?? 'ISP Billing' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.10); max-width: 420px; width: 100%; }
        .qris-img { max-width: 280px; width: 100%; border-radius: 12px; border: 1px solid #eee; }
        .isp-name { font-weight: 700; font-size: 1.2rem; color: #1a1a2e; }
        .norek-box { background: #f8f9fa; border-radius: 10px; padding: 12px 16px; font-size: 0.95rem; }
        .btn-download { background: #0d6efd; color: #fff; border-radius: 8px; padding: 10px 28px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card p-4 mx-3">
        <div class="text-center mb-3">
            <div class="isp-name">{{ $settings['nama_isp'] ?? 'ISP Billing' }}</div>
            <div class="text-muted small">{{ $settings['alamat_isp'] ?? '' }}</div>
        </div>

        @if(!empty($settings['wa_qris_url']))
        <div class="text-center mb-3">
            <img src="{{ asset('images/payment/qris.jpg') }}" class="qris-img" alt="QRIS">
        </div>
        <div class="text-center mb-3">
            <a href="{{ asset('images/payment/qris.jpg') }}" download="QRIS-{{ $settings['nama_isp'] ?? 'ISP' }}.jpg"
               class="btn btn-download">
                ⬇️ Download QRIS
            </a>
        </div>
        @endif

        @if(!empty($settings['info_pembayaran']))
        <div class="norek-box mb-3">
            <div class="text-muted small mb-1" style="font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Info Pembayaran</div>
            <div>{{ $settings['info_pembayaran'] }}</div>
        </div>
        @endif

        <div class="text-center text-muted small">
            Hubungi admin: <a href="https://wa.me/62{{ $settings['no_admin'] ?? '' }}">+62{{ $settings['no_admin'] ?? '' }}</a>
        </div>
    </div>
</body>
</html>
