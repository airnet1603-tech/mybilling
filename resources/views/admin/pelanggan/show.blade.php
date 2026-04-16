@extends('layouts.admin')
@section('title', 'Detail Pelanggan - ISP Billing')

@section('content')

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">Detail Pelanggan</h5>
            <small class="text-muted">{{ $pelanggan->id_pelanggan }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="/admin/pelanggan" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
            <a href="/admin/pelanggan/{{ $pelanggan->id }}/edit" class="btn btn-warning btn-sm text-white">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">

        {{-- ===== KOLOM KIRI ===== --}}
        <div class="col-md-4">

            {{-- PROFIL --}}
            <div class="profile-header">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6">{{ $pelanggan->nama }}</div>
                        <div class="opacity-75 small">{{ $pelanggan->id_pelanggan }}</div>
                    </div>
                </div>
                <span class="badge-status badge-{{ $pelanggan->status }}">
                    <i class="fas fa-circle" style="font-size:0.5rem;"></i>
                    {{ ucfirst($pelanggan->status) }}
                </span>
            </div>

            {{-- SYNC MIKROTIK --}}
            <div class="sync-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fw-bold small">
                        <i class="fas fa-sync-alt me-2"></i>Sinkronisasi Mikrotik
                    </div>
                    @if($pelanggan->router)
                        <span class="badge bg-success" style="font-size:0.65rem;">Router OK</span>
                    @else
                        <span class="badge bg-danger" style="font-size:0.65rem;">No Router</span>
                    @endif
                </div>
                <div class="opacity-75" style="font-size:0.75rem; margin-bottom:12px;">
                    Sync data PPPoE pelanggan ke router Mikrotik.
                </div>
                <form method="POST" action="{{ route('mikrotik.sync', $pelanggan->id) }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-sm w-100"
                            style="background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:8px 0px 8px 0px;"
                            onclick="return confirm('Sinkronkan pelanggan ini ke Mikrotik?')">
                        <i class="fas fa-sync-alt me-1"></i> Sync ke Mikrotik
                    </button>
                </form>
            </div>

            {{-- UBAH STATUS --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-toggle-on me-2 text-primary"></i>Ubah Status
                    </div>
                    <div class="d-flex flex-column gap-2">

                        @if(auth()->user()->role === 'admin')

                        {{-- AKTIF --}}
                        <form method="POST" action="{{ route('mikrotik.aktifkan', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-aktif {{ $pelanggan->status == 'aktif' ? 'active-status' : '' }}"
                                    onclick="return confirm('Aktifkan pelanggan {{ addslashes($pelanggan->nama) }}?')">
                                <i class="fas fa-check-circle"></i> Aktif
                                @if($pelanggan->status == 'aktif')
                                    <span class="ms-auto badge bg-success" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        {{-- ISOLIR --}}
                        <form method="POST" action="{{ route('mikrotik.isolir', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-isolir {{ $pelanggan->status == 'isolir' ? 'active-status' : '' }}"
                                    onclick="return confirm('Isolir pelanggan {{ addslashes($pelanggan->nama) }}? Koneksi internet akan diputus.')">
                                <i class="fas fa-ban"></i> Isolir
                                @if($pelanggan->status == 'isolir')
                                    <span class="ms-auto badge bg-danger" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        {{-- SUSPEND --}}
                        <form method="POST" action="{{ route('mikrotik.suspend', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-suspend {{ $pelanggan->status == 'suspend' ? 'active-status' : '' }}"
                                    onclick="return confirm('Suspend pelanggan {{ addslashes($pelanggan->nama) }}? Koneksi internet akan diputus.')">
                                <i class="fas fa-pause-circle"></i> Suspend
                                @if($pelanggan->status == 'suspend')
                                    <span class="ms-auto badge bg-warning text-dark" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        {{-- NONAKTIF --}}
                        <form method="POST" action="{{ route('mikrotik.nonaktif', $pelanggan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="status-btn btn-nonaktif {{ $pelanggan->status == 'nonaktif' ? 'active-status' : '' }}"
                                    onclick="return confirm('Nonaktifkan pelanggan {{ addslashes($pelanggan->nama) }}? Koneksi internet akan diputus.')">
                                <i class="fas fa-times-circle"></i> Nonaktif
                                @if($pelanggan->status == 'nonaktif')
                                    <span class="ms-auto badge bg-secondary" style="font-size:0.65rem;">Saat ini</span>
                                @endif
                            </button>
                        </form>

                        @else
                            <p class="text-muted small"><i class="fas fa-lock"></i> Hanya admin yang dapat mengubah status.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- INFO KONTAK --}}
            <div class="card">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-address-card me-2 text-primary"></i>Info Kontak
                    </div>
                    <div class="mb-2">
                        <div class="info-label">No. HP</div>
                        <div class="small">{{ $pelanggan->no_hp ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Email</div>
                        <div class="small">{{ $pelanggan->email ?? '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="info-label">Alamat</div>
                        <div class="small">{{ $pelanggan->alamat ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="info-label">Wilayah</div>
                        <div class="small">{{ $pelanggan->wilayah ?? '-' }}</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== KOLOM KANAN ===== --}}
        <div class="col-md-8">

            {{-- INFO LAYANAN --}}
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="card-section-title mb-3">
                        <i class="fas fa-network-wired me-2 text-primary"></i>Info Layanan
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="info-label">Paket</div>
                            <div class="fw-semibold small">{{ $pelanggan->paket->nama_paket ?? '-' }}</div>
                            <small class="text-muted">
                                {{ $pelanggan->paket->kecepatan_download ?? 0 }} Mbps /
                                {{ $pelanggan->paket->kecepatan_upload ?? 0 }} Mbps
                            </small>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Harga Paket</div>
                            <div class="fw-semibold text-success small">
                                Rp {{ number_format($pelanggan->paket->harga ?? 0, 0, ',', '.') }}
                            </div>
                            <small class="text-muted">per bulan</small>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Jenis Layanan</div>
                            <span class="badge bg-primary">{{ strtoupper($pelanggan->jenis_layanan ?? '-') }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Username PPPoE</div>
                            <code class="small">{{ $pelanggan->username }}</code>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">IP Address</div>
                            <div class="small">{{ $pelanggan->ip_address ?? 'Dinamis' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Router</div>
                            <div class="small">{{ $pelanggan->router->nama ?? $pelanggan->router_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Tanggal Daftar</div>
                            <div class="small">{{ $pelanggan->tgl_daftar?->format('d/m/Y') ?? '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Expired</div>
                            <div class="small {{ $pelanggan->tgl_expired && $pelanggan->tgl_expired < now() ? 'text-danger fw-bold' : '' }}">
                                {{ $pelanggan->tgl_expired?->format('d/m/Y') ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Sisa Hari</div>
                            @if($pelanggan->tgl_expired)
                                @php $sisa = now()->diffInDays($pelanggan->tgl_expired, false) @endphp
                                <div class="small {{ $sisa < 0 ? 'text-danger fw-bold' : ($sisa <= 5 ? 'text-warning fw-bold' : 'text-success') }}">
                                    {{ $sisa < 0 ? 'Expired '.abs($sisa).' hari lalu' : $sisa.' hari lagi' }}
                                </div>
                            @else
                                <div class="small text-muted">-</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT TAGIHAN --}}
            <div class="card">
                <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
                    <div class="card-section-title">
                        <i class="fas fa-file-invoice me-2 text-primary"></i>Riwayat Tagihan
                    </div>
                    <span class="badge bg-secondary">{{ $pelanggan->tagihan->count() }} tagihan</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 small">No. Tagihan</th>
                                <th class="small">Periode</th>
                                <th class="small">Total</th>
                                <th class="small">Status</th>
                                <th class="small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggan->tagihan->sortByDesc('created_at')->take(10) as $t)
                            <tr>
                                <td class="ps-3"><code class="small">{{ $t->no_tagihan }}</code></td>
                                <td><small>{{ $t->periode_bulan?->isoFormat('MMM Y') ?? '-' }}</small></td>
                                <td>
                                    <div class="fw-semibold small">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                                    @if($t->denda > 0)
                                        <small class="text-danger">+denda Rp {{ number_format($t->denda, 0, ',', '.') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($t->status == 'paid')
                                        <span class="badge bg-success">Lunas</span>
                                    @elseif($t->status == 'overdue')
                                        <span class="badge bg-danger">Overdue</span>
                                    @elseif($t->status == 'unpaid')
                                        <span class="badge bg-warning text-dark">Unpaid</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($t->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="/admin/tagihan/{{ $t->id }}" class="btn btn-sm btn-info text-white py-0 px-2">
                                        <i class="fas fa-eye fa-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4 small">
                                    <i class="fas fa-file-invoice fa-2x mb-2 d-block opacity-25"></i>
                                    Belum ada tagihan
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #1a1a2e, #0f3460);
        border-radius: 12px;
        padding: 20px;
        color: white;
        margin-bottom: 12px;
    }
    .avatar {
        width: 60px; height: 60px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
    .badge-aktif    { background: #d4edda; color: #155724; }
    .badge-isolir   { background: #f8d7da; color: #721c24; }
    .badge-suspend  { background: #fff3cd; color: #856404; }
    .badge-nonaktif { background: #e2e3e5; color: #383d41; }
    .info-label { font-size: 0.7rem; color: #6c757d; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 2px; }
    .card-section-title { font-size: 0.88rem; font-weight: 700; }
    .sync-card { background: linear-gradient(135deg, #1a1a2e, #0f3460); border-radius: 12px; padding: 16px; color: white; margin-bottom: 12px; }
    .status-btn { display: flex; align-items: center; gap: 8px; padding: 9px 13px; border-radius: 8px; border: 2px solid transparent; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.2s; background: #f8f9fa; color: #495057; width: 100%; text-align: left; }
    .status-btn:hover { transform: translateX(3px); }
    .status-btn.btn-aktif    { border-color: #28a745; color: #155724; background: #f0fff4; }
    .status-btn.btn-isolir   { border-color: #dc3545; color: #721c24; background: #fff5f5; }
    .status-btn.btn-suspend  { border-color: #ffc107; color: #856404; background: #fffdf0; }
    .status-btn.btn-nonaktif { border-color: #6c757d; color: #383d41; background: #f8f9fa; }
    .status-btn.active-status { box-shadow: 0 2px 8px rgba(0,0,0,0.15); font-weight: 700; }
</style>
@endpush
