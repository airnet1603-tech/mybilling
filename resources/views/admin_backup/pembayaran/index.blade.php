@extends('layouts.admin')

@push('styles')
<style>
    .table td, .table th { padding-top: 0px !important; padding-bottom: 0px !important; vertical-align: middle !important; }
    .stat-card { border:none; border-radius:12px; padding:15px 20px; color:white; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Riwayat Pembayaran</h5>
        <small class="text-muted">{{ now()->isoFormat('MMMM Y') }}</small>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="fs-5 fw-bold">Rp {{ number_format($totalBulanIni,0,',','.') }}</div>
            <div class="opacity-75">Total Bulan Ini</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <div class="fs-5 fw-bold">{{ $totalTransaksi }}</div>
            <div class="opacity-75">Transaksi Bulan Ini</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
            <div class="fs-5 fw-bold">Rp {{ number_format($totalCash,0,',','.') }}</div>
            <div class="opacity-75">Total Cash</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
            <div class="fs-5 fw-bold">Rp {{ number_format($totalTransfer,0,',','.') }}</div>
            <div class="opacity-75">Total Transfer</div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama pelanggan..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="metode" class="form-select form-select-sm">
                    <option value="">Semua Metode</option>
                    <option value="cash"     {{ request('metode')=='cash'     ? 'selected':'' }}>Cash</option>
                    <option value="transfer" {{ request('metode')=='transfer' ? 'selected':'' }}>Transfer</option>
                    <option value="midtrans" {{ request('metode')=='midtrans' ? 'selected':'' }}>Midtrans</option>
                    <option value="xendit"   {{ request('metode')=='xendit'   ? 'selected':'' }}>Xendit</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="month" name="bulan" class="form-control form-control-sm" value="{{ request('bulan') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
            </div>
            <div class="col-auto">
                <a href="/admin/pembayaran" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- TABEL --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small">No. Pembayaran</th>
                        <th class="small">Pelanggan</th>
                        <th class="small">No. Tagihan</th>
                        <th class="small">Jumlah</th>
                        <th class="small">Metode</th>
                        <th class="small">Tanggal</th>
                        <th class="small">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayarans as $p)
                    <tr>
                        <td><code class="small">{{ $p->no_pembayaran }}</code></td>
                        <td>
                            <div class="fw-semibold small">{{ $p->pelanggan->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $p->pelanggan->id_pelanggan ?? '' }}</small>
                        </td>
                        <td>
                            <a href="/admin/tagihan/{{ $p->tagihan_id }}" class="text-decoration-none">
                                <code class="small">{{ $p->tagihan->no_tagihan ?? '-' }}</code>
                            </a>
                        </td>
                        <td class="fw-bold text-success small">Rp {{ number_format($p->jumlah_bayar,0,',','.') }}</td>
                        <td>
                            @if($p->metode == 'cash')
                                <span class="badge bg-success">Cash</span>
                            @elseif($p->metode == 'transfer')
                                <span class="badge bg-primary">Transfer</span>
                            @elseif($p->metode == 'midtrans')
                                <span class="badge bg-info">Midtrans</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($p->metode) }}</span>
                            @endif
                        </td>
                        <td><small>{{ $p->created_at->format('d/m/Y H:i') }}</small></td>
                        <td><small class="text-muted">{{ $p->catatan ?? '-' }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-money-bill-wave fa-3x mb-3 d-block opacity-25"></i>
                            Belum ada riwayat pembayaran
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($pembayarans->hasPages())
    <div class="card-footer bg-white">
        {{ $pembayarans->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
