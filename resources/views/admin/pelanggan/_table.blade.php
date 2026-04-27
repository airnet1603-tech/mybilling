                    @forelse($pelanggans as $p)
                    <tr>
                        <td class="ps-3"><input type="checkbox" class="row-check" value="{{ $p->id }}" onchange="updateBulkBar()"></td>
                        <td class="ps-3"><small class="text-muted">{{ $p->id_pelanggan }}</small></td>
                        <td><div class="fw-semibold small">{{ $p->nama }}</div></td>
                        <td><code class="small" style="font-family: inherit;">{{ $p->username }}</code></td>
                        <td>
                            @if($p->router)
                                <span class="router-badge"><i class="fas fa-network-wired fa-xs me-1"></i>{{ $p->router->nama }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->paket)
                                <span class="badge bg-primary">{{ $p->paket->nama_paket }}</span>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $p->status }} badge-status rounded-pill">{{ ucfirst($p->status) }}</span></td>
                        <td><small class="{{ $p->tgl_expired && $p->tgl_expired < now() ? 'text-danger fw-bold' : '' }}">{{ $p->tgl_expired?->format('d/m/Y') ?? '-' }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/admin/pelanggan/{{ $p->id }}" class="btn btn-sm btn-info text-white py-0 px-2"><i class="fas fa-eye fa-xs"></i></a>
                                <a href="/admin/pelanggan/{{ $p->id }}/edit" class="btn btn-sm btn-warning text-white py-0 px-2"><i class="fas fa-edit fa-xs"></i></a>
                                <form method="POST" action="/admin/pelanggan/{{ $p->id }}" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger py-0 px-2"><i class="fas fa-trash fa-xs"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>
                            <span class="small">Belum ada pelanggan ditemukan</span>
                        </td>
                    </tr>
                    @endforelse
                