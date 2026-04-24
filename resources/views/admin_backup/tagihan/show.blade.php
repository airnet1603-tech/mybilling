@extends('layouts.admin')

@section('content')
<style>
    .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
    .card-section-title { font-size: 0.88rem; font-weight: 700; }
    .invoice-header {
        background: linear-gradient(135deg, #1a1a2e, #0f3460);
        border-radius: 12px 12px 0 0;
        padding: 24px;
        color: white;
    }
    .badge-paid      { background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-unpaid    { background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-overdue   { background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-cancelled { background: #e2e3e5; color: #383d41; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Detail Tagihan</h5>
        <small class="text-muted">{{ $tagihan->no_tagihan }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/tagihan" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print me-1"></i> Print
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">

    {{-- KOLOM KIRI - INVOICE --}}
    <div class="col-md-8">
        <div class="card">
            <div class="invoice-header">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="fw-bold mb-1">INVOICE</h4>
                        <div class="opacity-75 small">{{ $tagihan->no_tagihan }}</div>
                        <div class="mt-2">
                            <span class="badge-{{ $tagihan->status }} fw-semibold">
                                {{ strtoupper($tagihan->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="text-end">
                        <i class="fas fa-wifi fa-3x opacity-50"></i>
                        <div class="mt-1 fw-bold small">ISP BILLING</div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-label mb-1">Tagihan Kepada</div>
                        <div class="fw-bold fs-6">{{ $tagihan->pelanggan->nama }}</div>
                        <div class="text-muted small">{{ $tagihan->pelanggan->id_pelanggan }}</div>
                        <div class="small">{{ $tagihan->pelanggan->no_hp }}</div>
                        <div class="text-muted small">{{ $tagihan->pelanggan->alamat }}</div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-1 small">
                            <span class="text-muted">Tgl Tagihan:</span>
                            <strong>{{ $tagihan->tgl_tagihan?->format('d/m/Y') }}</strong>
                        </div>
                        <div class="mb-1 small">
                            <span class="text-muted">Jatuh Tempo:</span>
                            <strong class="{{ $tagihan->tgl_jatuh_tempo < now() && $tagihan->status != 'paid' ? 'text-danger' : '' }}">
                                {{ $tagihan->tgl_jatuh_tempo?->format('d/m/Y') }}
                            </strong>
                        </div>
                        <div class="mb-1 small">
                            <span class="text-muted">Periode:</span>
                            <strong>{{ $tagihan->periode_bulan?->isoFormat('MMMM Y') }}</strong>
                        </div>
                        @if($tagihan->tgl_bayar)
                        <div class="small">
                            <span class="text-muted">Tgl Bayar:</span>
                            <strong class="text-success">{{ $tagihan->tgl_bayar?->format('d/m/Y') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="small">Deskripsi</th>
                            <th class="text-end small">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-semibold small">Layanan Internet {{ $tagihan->paket->nama_paket ?? '-' }}</div>
                                <small class="text-muted">
                                    {{ $tagihan->paket->kecepatan_download ?? 0 }} Mbps /
                                    {{ $tagihan->paket->kecepatan_upload ?? 0 }} Mbps &bull;
                                    Periode {{ $tagihan->periode_bulan?->isoFormat('MMMM Y') }}
                                </small>
                            </td>
                            <td class="text-end small">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @if($tagihan->diskon > 0)
                        <tr>
                            <td class="text-success small">Diskon</td>
                            <td class="text-end text-success small">- Rp {{ number_format($tagihan->diskon, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($tagihan->denda > 0)
                        <tr>
                            <td class="text-danger small">Denda Keterlambatan</td>
                            <td class="text-end text-danger small">+ Rp {{ number_format($tagihan->denda, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="table-dark">
                            <th class="small">TOTAL</th>
                            <th class="text-end fs-6">Rp {{ number_format($tagihan->total, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>

                @if($tagihan->catatan)
                <div class="alert alert-light small">
                    <i class="fas fa-sticky-note me-2"></i>{{ $tagihan->catatan }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="col-md-4">

        @if($tagihan->status !== 'paid')
        <div class="card mb-3">
            <div class="card-body py-3">
                <div class="card-section-title mb-3">
                    <i class="fas fa-check-circle me-2 text-success"></i>Konfirmasi Pembayaran
                </div>
                <form method="POST" action="/admin/tagihan/{{ $tagihan->id }}/bayar">
                    @csrf
                    <div class="mb-2">
                        <div class="info-label">Metode Bayar</div>
                        <select name="metode_bayar" class="form-select form-select-sm" required>
                            <option value="cash">💵 Cash / Tunai</option>
                            <option value="transfer">🏦 Transfer Bank</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Catatan</div>
                        <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Opsional">
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="fas fa-check me-1"></i> Tandai Lunas
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                <div class="fw-bold text-success">TAGIHAN LUNAS</div>
                <div class="text-muted small mt-1">{{ $tagihan->tgl_bayar?->format('d/m/Y H:i') }}</div>
                <span class="badge bg-success mt-2">{{ strtoupper($tagihan->metode_bayar ?? '-') }}</span>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-body py-3">
                <div class="card-section-title mb-3">
                    <i class="fas fa-user me-2 text-primary"></i>Info Pelanggan
                </div>
                <div class="mb-2">
                    <div class="info-label">Status</div>
                    <span class="badge bg-{{ $tagihan->pelanggan->status == 'aktif' ? 'success' : 'danger' }}">
                        {{ ucfirst($tagihan->pelanggan->status) }}
                    </span>
                </div>
                <div class="mb-2">
                    <div class="info-label">Username</div>
                    <code class="small">{{ $tagihan->pelanggan->username }}</code>
                </div>
                <div class="mb-2">
                    <div class="info-label">Router</div>
                    <div class="small">{{ $tagihan->pelanggan->router_name ?? '-' }}</div>
                </div>
                <div class="mb-2">
                    <div class="info-label">IP Address</div>
                    <div class="small">{{ $tagihan->pelanggan->ip_address ?? 'Dinamis' }}</div>
                </div>
                <div class="mb-3">
                    <div class="info-label">Expired</div>
                    <div class="small">{{ $tagihan->pelanggan->tgl_expired?->format('d/m/Y') ?? '-' }}</div>
                </div>
                <a href="/admin/pelanggan/{{ $tagihan->pelanggan->id }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-user me-1"></i> Lihat Profil Pelanggan
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
