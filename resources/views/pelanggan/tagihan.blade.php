<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tagihan — Portal Pelanggan</title>
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
        .badge-paid{background:rgba(25,135,84,0.1);color:#198754;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .badge-unpaid{background:rgba(233,69,96,0.1);color:#e94560;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .badge-overdue{background:rgba(220,53,69,0.15);color:#dc3545;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .badge-cancelled{background:rgba(108,117,125,0.1);color:#6c757d;padding:2px 10px;border-radius:20px;font-size:0.71rem;font-weight:600}
        .pay-option{border:2px solid #e9ecef;border-radius:12px;padding:16px;cursor:pointer;transition:.2s;text-align:center}
        .pay-option:hover{border-color:#0d6efd;background:#f0f7ff}
        .pay-option .label{font-weight:600;font-size:0.9rem;margin-top:8px}
        .pay-option .sub{font-size:0.75rem;color:#6c757d}
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
                <a href="/pelanggan/dashboard"><i class="fas fa-home me-1"></i>Home</a>
                <a href="/pelanggan/tagihan" class="active"><i class="fas fa-file-invoice me-1"></i>Tagihan</a>
                <a href="/pelanggan/profil"><i class="fas fa-user me-1"></i>Profil</a>
            </div>
            <a href="/pelanggan/logout" class="btn btn-sm ms-1" style="background:rgba(233,69,96,0.2);color:#fff;border:1px solid rgba(233,69,96,0.3)"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="main">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-file-invoice me-2 text-primary"></i>Riwayat Tagihan</h5>
        <span class="text-muted small">{{ $pelanggan->nama }}</span>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Periode</th>
                        <th>Tagihan</th>
                        <th>Jatuh Tempo</th>
                        <th>Tgl Bayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tagihans as $t)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($t->periode_bulan)->format('M Y') }}</div>
                            <small class="text-muted">{{ $t->no_tagihan }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                            @if($t->denda > 0)
                                <small class="text-danger">+denda Rp {{ number_format($t->denda, 0, ',', '.') }}</small>
                            @endif
                        </td>
                        <td><small>{{ \Carbon\Carbon::parse($t->tgl_jatuh_tempo)->format('d/m/Y') }}</small></td>
                        <td><small>{{ $t->tgl_bayar ? \Carbon\Carbon::parse($t->tgl_bayar)->format('d/m/Y') : '-' }}</small></td>
                        <td>
                            @if($t->status === 'paid')
                                <span class="badge-paid"><i class="fas fa-check me-1"></i>Lunas</span>
                            @elseif($t->status === 'overdue')
                                <span class="badge-overdue"><i class="fas fa-exclamation me-1"></i>Overdue</span>
                            @elseif($t->status === 'cancelled')
                                <span class="badge-cancelled">Batal</span>
                            @else
                                <span class="badge-unpaid"><i class="fas fa-clock me-1"></i>Belum Bayar</span>
                            @endif
                        </td>
                        <td>
                            @if(in_array($t->status, ['unpaid', 'overdue']))
                                <button class="btn btn-primary btn-sm py-0 px-2"
                                    onclick="bukaPilihBayar('{{ $t->no_tagihan }}', {{ $t->total + $t->denda }})">
                                    <i class="fas fa-credit-card fa-xs me-1"></i> Bayar
                                </button>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada tagihan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tagihans->hasPages())
            <div class="card-footer bg-white">{{ $tagihans->links() }}</div>
        @endif
    </div>
</div>

<!-- MODAL PILIH METODE BAYAR -->
<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h6 class="fw-bold mb-0">Pilih Metode Pembayaran</h6>
                    <small class="text-muted" id="modalTagihanInfo"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3 p-3 rounded-3" style="background:#f8f9fa;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Total Tagihan</span>
                        <span class="fw-bold" id="modalJumlah"></span>
                    </div>
                </div>
                <div class="row g-3" id="pilihanMetode">
                    <div class="col-6">
                        <div class="pay-option" onclick="pilihBayar('va')">
                            <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/5c/Bank_BRI_2020.svg/120px-Bank_BRI_2020.svg.png" height="36" alt="BRI">
                            <div class="label">BRI Virtual Account</div>
                            <div class="sub">Transfer ATM / m-Banking</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="pay-option" onclick="pilihBayar('qris')">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a2/Logo_QRIS.svg/120px-Logo_QRIS.svg.png" height="36" alt="QRIS">
                            <div class="label">QRIS</div>
                            <div class="sub">GoPay, OVO, Dana, dll</div>
                        </div>
                    </div>
                </div>
                <div id="loadingBayar" style="display:none;" class="text-center py-3">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 small text-muted">Memproses...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let activeNoTagihan = null;

function bukaPilihBayar(noTagihan, amount) {
    activeNoTagihan = noTagihan;
    document.getElementById('modalTagihanInfo').textContent = noTagihan;
    document.getElementById('modalJumlah').textContent = 'Rp ' + amount.toLocaleString('id-ID');
    document.getElementById('loadingBayar').style.display = 'none';
    document.getElementById('pilihanMetode').style.display = 'flex';
    document.querySelectorAll('.pay-option').forEach(el => el.style.opacity = '1');
    new bootstrap.Modal(document.getElementById('modalBayar')).show();
}

function pilihBayar(tipe) {
    document.getElementById('pilihanMetode').style.display = 'none';
    document.getElementById('loadingBayar').style.display = 'block';

    fetch(`/pelanggan/payment/${activeNoTagihan}/${tipe}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Gagal memproses');
        window.location.href = `/pelanggan/payment/${activeNoTagihan}`;
    })
    .catch(err => {
        document.getElementById('loadingBayar').innerHTML =
            `<div class="alert alert-danger"><i class="fas fa-times-circle me-2"></i>${err.message}</div>
             <button class="btn btn-sm btn-outline-secondary mt-2" onclick="resetModal()">Coba Lagi</button>`;
    });
}

function resetModal() {
    document.getElementById('loadingBayar').style.display = 'none';
    document.getElementById('loadingBayar').innerHTML =
        '<i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 small text-muted">Memproses...</p>';
    document.getElementById('pilihanMetode').style.display = 'flex';
    document.querySelectorAll('.pay-option').forEach(el => el.style.opacity = '1');
}
</script>
</body>
</html>
