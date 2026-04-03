<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Portal Pelanggan</title>
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
        .main{padding:20px;max-width:860px;margin:0 auto}
        .status-card{border-radius:14px;padding:22px;color:#fff;position:relative;overflow:hidden;margin-bottom:16px}
        .status-card.aktif{background:linear-gradient(135deg,#0f3460,#1a1a2e)}
        .status-card.isolir{background:linear-gradient(135deg,#e94560,#c0392b)}
        .status-card.nonaktif{background:linear-gradient(135deg,#6c757d,#343a40)}
        .big-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:4.5rem;opacity:0.07}
        .info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:8px;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.15)}
        .info-item .lbl{font-size:0.68rem;opacity:0.6;text-transform:uppercase;letter-spacing:.5px}
        .info-item .val{font-size:0.95rem;font-weight:700;margin-top:2px}
        .section-card{background:#fff;border-radius:12px;padding:18px;box-shadow:0 1px 4px rgba(0,0,0,0.06);margin-bottom:14px}
        .section-card h6{font-weight:700;margin-bottom:12px}
        .tagihan-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f5f5f5}
        .tagihan-row:last-child{border-bottom:none}
        .badge-paid{background:rgba(25,135,84,0.1);color:#198754;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .badge-unpaid{background:rgba(233,69,96,0.1);color:#e94560;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .badge-overdue{background:rgba(220,53,69,0.15);color:#dc3545;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .warning-box{background:rgba(255,193,7,0.1);border:1px solid rgba(255,193,7,0.3);border-radius:10px;padding:12px;margin-bottom:14px;font-size:0.85rem}
        .danger-box{background:rgba(233,69,96,0.08);border:1px solid rgba(233,69,96,0.25);border-radius:10px;padding:12px;margin-bottom:14px;font-size:0.85rem}
    </style>
</head>
<body>
<nav class="topbar">
    <div class="topbar-inner">
        <a href="/pelanggan/dashboard" class="brand">
            <div class="brand-icon"><i class="fas fa-wifi"></i></div>
            <div><div style="font-size:0.85rem;font-weight:700">ISP BILLING</div><div style="font-size:0.65rem;color:rgba(255,255,255,0.5)">Portal Pelanggan</div></div>
        </a>
        <div class="d-flex align-items-center gap-2">
            <div class="nav-portal d-flex gap-1">
                <a href="/pelanggan/dashboard" class="active"><i class="fas fa-home me-1"></i>Home</a>
                <a href="/pelanggan/tagihan"><i class="fas fa-file-invoice me-1"></i>Tagihan</a>
                <a href="/pelanggan/profil"><i class="fas fa-user me-1"></i>Profil</a>
            </div>
            <a href="/pelanggan/logout" class="btn btn-sm ms-1" style="background:rgba(233,69,96,0.2);color:#fff;border:1px solid rgba(233,69,96,0.3)"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>
<div class="main">
    @php
        $expired = \Carbon\Carbon::parse($pelanggan->tgl_expired);
        $today   = \Carbon\Carbon::today();
        $daysLeft = $today->diffInDays($expired, false);
    @endphp

    {{-- Status Card --}}
    <div class="status-card {{ $pelanggan->status }}">
        <i class="fas fa-wifi big-icon"></i>
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
                <div style="font-size:0.7rem;opacity:0.6;letter-spacing:1px;text-transform:uppercase">Selamat datang</div>
                <div style="font-size:1.2rem;font-weight:700;margin:2px 0">{{ $pelanggan->nama }}</div>
                <div style="font-size:0.82rem;opacity:0.75"><i class="fas fa-user me-1"></i>{{ $pelanggan->username }}</div>
            </div>
            <div style="text-align:right">
                @if($pelanggan->status==='aktif')
                    <span style="background:rgba(25,135,84,0.25);color:#fff;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600"><i class="fas fa-check-circle me-1"></i>Aktif</span>
                @elseif($pelanggan->status==='isolir')
                    <span style="background:rgba(255,255,255,0.2);color:#fff;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600"><i class="fas fa-ban me-1"></i>Isolir</span>
                @else
                    <span style="background:rgba(255,255,255,0.15);color:#fff;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600"><i class="fas fa-times-circle me-1"></i>{{ ucfirst($pelanggan->status) }}</span>
                @endif
            </div>
        </div>
        <div class="info-grid">
            <div class="info-item"><div class="lbl">Paket</div><div class="val">{{ $pelanggan->paket->nama_paket ?? $pelanggan->paket->nama ?? '-' }}</div></div>
            <div class="info-item"><div class="lbl">Harga</div><div class="val">Rp {{ number_format($pelanggan->paket->harga ?? 0, 0, ',', '.') }}</div></div>
            <div class="info-item"><div class="lbl">Expired</div><div class="val">{{ $expired->format('d/m/Y') }}</div></div>
            <div class="info-item"><div class="lbl">Sisa Hari</div><div class="val {{ $daysLeft<=3&&$daysLeft>=0?'text-warning':'' }}">{{ $daysLeft>0?$daysLeft.' hari':($daysLeft==0?'Hari ini':'Expired') }}</div></div>
        </div>
    </div>

    @if($pelanggan->status==='isolir')
    <div class="danger-box"><i class="fas fa-ban text-danger me-2"></i><strong>Layanan Anda sedang diisolir.</strong> Segera lunasi tagihan dan hubungi admin untuk reaktivasi.</div>
    @endif

    @if($daysLeft<=5 && $daysLeft>=0 && $pelanggan->status==='aktif')
    <div class="warning-box"><i class="fas fa-exclamation-triangle text-warning me-2"></i><strong>Hampir expired!</strong> Paket Anda berakhir dalam <strong>{{ $daysLeft }} hari</strong>. Segera lakukan pembayaran.</div>
    @endif

    {{-- Tagihan Belum Bayar --}}
    @if($tagihanUnpaid->count()>0)
    <div class="section-card">
        <h6><i class="fas fa-exclamation-circle text-danger me-2"></i>Tagihan Belum Dibayar ({{ $tagihanUnpaid->count() }})</h6>
        @foreach($tagihanUnpaid as $t)
        <div class="tagihan-row">
            <div>
                <div class="fw-semibold" style="font-size:0.9rem">{{ \Carbon\Carbon::parse($t->periode_bulan)->format('F Y') }}</div>
                <small class="text-muted">Jatuh tempo: {{ \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->format('d/m/Y') }}</small>
            </div>
            <div class="text-end">
                <div class="fw-bold" style="color:#e94560">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                @if($t->status==='overdue')
                    <span class="badge-overdue"><i class="fas fa-clock me-1"></i>Overdue</span>
                @else
                    <span class="badge-unpaid">Belum Bayar</span>
                @endif
            </div>
        </div>
        @endforeach
        <div class="mt-2 text-muted" style="font-size:0.78rem"><i class="fas fa-info-circle me-1"></i>Hubungi admin/operator untuk melakukan pembayaran.</div>
    </div>
    @endif

    {{-- Riwayat Lunas --}}
    @if($tagihanPaid->count()>0)
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0"><i class="fas fa-check-circle text-success me-2"></i>Pembayaran Terakhir</h6>
            <a href="/pelanggan/tagihan" class="btn btn-sm btn-outline-secondary" style="font-size:0.75rem">Semua</a>
        </div>
        @foreach($tagihanPaid as $t)
        <div class="tagihan-row">
            <div>
                <div class="fw-semibold" style="font-size:0.9rem">{{ \Carbon\Carbon::parse($t->periode_bulan)->format('F Y') }}</div>
                <small class="text-muted">Dibayar: {{ \Carbon\Carbon::parse($t->tgl_bayar)->format('d/m/Y') }}</small>
            </div>
            <div class="text-end">
                <div class="fw-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                <span class="badge-paid"><i class="fas fa-check me-1"></i>Lunas</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($tagihanUnpaid->count()===0 && $tagihanPaid->count()===0)
    <div class="section-card text-center text-muted py-3">
        <i class="fas fa-file-invoice fa-2x mb-2 d-block"></i>
        Belum ada tagihan
    </div>
    @endif

    {{-- Kontak Support --}}
    <div class="section-card">
        <h6><i class="fas fa-headset text-primary me-2"></i>Kontak Support</h6>
        <div class="d-flex flex-column gap-2" style="font-size:0.88rem">
            <div><i class="fas fa-envelope me-2 text-muted"></i><strong>Email:</strong> support@airnetps.my.id</div>
            <div><i class="fas fa-phone me-2 text-muted"></i><strong>Telepon/WA:</strong> 085645785634 </div>
            <div><i class="fas fa-map-marker-alt me-2 text-muted"></i><strong>Alamat:</strong> Desa Sumberdadap-Kecamatan Pucanglaban-Kabupaten Tulungagung</div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
