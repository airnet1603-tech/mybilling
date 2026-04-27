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
