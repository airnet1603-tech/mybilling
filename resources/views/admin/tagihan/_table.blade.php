@forelse($tagihans as $t)
<tr id="row-{{ $t->id }}">
    <td>
        @if($t->status !== 'paid' && $t->status !== 'cancelled')
        <input type="checkbox" name="tagihan_ids[]" value="{{ $t->id }}"
               class="form-check-input cb-tagihan"
               onchange="updateBulkBar()">
        @endif
    </td>
    <td><code class="small">{{ $t->no_tagihan }}</code></td>
    <td>
        <div class="fw-semibold small">{{ $t->pelanggan->nama ?? '-' }}</div>
        <small class="text-muted">{{ $t->pelanggan->id_pelanggan ?? '' }}</small>
    </td>
    <td><small>{{ $t->paket->nama_paket ?? '-' }}</small></td>
    <td>
        <div class="fw-bold small">Rp {{ number_format($t->total,0,',','.') }}</div>
        @if($t->denda > 0)
        <small class="text-danger">+denda Rp {{ number_format($t->denda,0,',','.') }}</small>
        @endif
    </td>
    <td>
        <small class="{{ $t->tgl_jatuh_tempo < now() && $t->status != 'paid' ? 'text-danger fw-bold' : '' }}">
            {{ $t->tgl_jatuh_tempo?->format('d/m/Y') }}
        </small>
    </td>
    <td><span class="badge-{{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
    <td>
        <div class="d-flex gap-1">
            <a href="/admin/tagihan/{{ $t->id }}" class="btn btn-sm btn-info text-white py-0 px-2" title="Detail">
                <i class="fas fa-eye fa-xs"></i>
            </a>
            @if($t->status !== 'paid')
            <button type="button" class="btn btn-sm btn-success py-0 px-2"
                    onclick="konfirmasi({{ $t->id }})" title="Konfirmasi Bayar">
                <i class="fas fa-check fa-xs"></i>
            </button>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5 text-muted">
        <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
        Belum ada tagihan.
    </td>
</tr>
@endforelse