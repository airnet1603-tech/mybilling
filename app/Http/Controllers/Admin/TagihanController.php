<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\ConnectsToMikrotik;
use App\Models\Pelanggan;
use App\Models\Router;
use App\Models\Paket;
use App\Models\Tagihan;
use App\Services\MikrotikService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    use ConnectsToMikrotik;

    public function index(Request $request)
    {
        $query = Tagihan::with('pelanggan', 'paket')->latest();
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('pelanggan', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->search.'%')
                       ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%')
                       ->orWhere('username', 'like', '%'.$request->search.'%');
                })->orWhere('no_tagihan', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->status) $query->where('status', $request->status);
        if ($request->bulan) {
            $query->whereMonth('periode_bulan', date('m', strtotime($request->bulan)))
                  ->whereYear('periode_bulan', date('Y', strtotime($request->bulan)));
        }
        if ($request->router_id) {
            $query->whereHas('pelanggan', function($q) use ($request) {
                $q->where('router_id', $request->router_id);
            });
        }
        if ($request->paket_id) {
            $query->where('paket_id', $request->paket_id);
        }
        $tagihans        = $query->paginate(15);


        // Base query untuk counter ikut filter
        $counterQuery = Tagihan::query();
        if ($request->search) {
            $counterQuery->where(function($q) use ($request) {
                $q->whereHas('pelanggan', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->search.'%')
                       ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%')
                       ->orWhere('username', 'like', '%'.$request->search.'%');
                })->orWhere('no_tagihan', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->router_id) {
            $counterQuery->whereHas('pelanggan', function($q) use ($request) {
                $q->where('router_id', $request->router_id);
            });
        }
        if ($request->paket_id) {
            $counterQuery->where('paket_id', $request->paket_id);
        }
        $totalUnpaid     = (clone $counterQuery)->where('status', 'unpaid')->count();
        $totalOverdue    = (clone $counterQuery)->where('status', 'overdue')->count();
        $totalPaid       = (clone $counterQuery)->where('status', 'paid')->whereMonth('tgl_bayar', now()->month)->count();
        $totalPendapatan = (clone $counterQuery)->where('status', 'paid')->whereMonth('tgl_bayar', now()->month)->sum('total');

        if (request()->ajax()) {
            return response()->json([
                'html'            => view('admin.tagihan._table', compact('tagihans'))->render(),
                'totalUnpaid'     => $totalUnpaid,
                'totalOverdue'    => $totalOverdue,
                'totalPaid'       => $totalPaid,
                'totalPendapatan' => number_format($totalPendapatan, 0, ',', '.'),
            ]);
        }
        $routers = Router::orderBy('nama')->get();
        $pakets  = Paket::where('is_active', true)->orderBy('nama_paket')->get();
        $paketsByRouter = Paket::where('is_active', true)->orderBy('nama_paket')
            ->get()->groupBy('router_id')->toJson();
        return view('admin.tagihan.index', compact('tagihans', 'totalUnpaid', 'totalOverdue', 'totalPaid', 'totalPendapatan', 'routers', 'pakets', 'paketsByRouter'));
    }

    public function create()
    {
        $pelanggans = Pelanggan::with('paket')->where('status', 'aktif')->get();
        return view('admin.tagihan.create', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id'    => 'required|exists:pelanggan,id',
            'tgl_jatuh_tempo' => 'required|date',
        ]);
        $pelanggan = Pelanggan::with('paket')->findOrFail($request->pelanggan_id);
        $harga     = $pelanggan->paket->harga;
        $tagihan   = Tagihan::create([
            'no_tagihan'      => Tagihan::generateNomor(),
            'pelanggan_id'    => $pelanggan->id,
            'paket_id'        => $pelanggan->paket_id,
            'jumlah'          => $harga,
            'denda'           => 0,
            'diskon'          => $request->diskon ?? 0,
            'total'           => $harga - ($request->diskon ?? 0),
            'periode_bulan'   => now()->startOfMonth(),
            'tgl_tagihan'     => now(),
            'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
            'status'          => 'unpaid',
            'catatan'         => $request->catatan,
        ]);

        if ($pelanggan->no_hp) {
            try {
                $whatsapp = new WhatsappService();
                $whatsapp->sendTagihan(
                    $pelanggan->no_hp,
                    $pelanggan->nama,
                    now()->format('F Y'),
                    $tagihan->total,
                    \Carbon\Carbon::parse($request->tgl_jatuh_tempo)->format('d/m/Y')
                );
            } catch (\Exception $e) {
                \Log::warning('Gagal kirim WA tagihan: ' . $e->getMessage());
            }
        }

        return redirect('/admin/tagihan')->with('success', 'Tagihan berhasil dibuat!');
    }

    public function show(Tagihan $tagihan)
    {
        $tagihan->load('pelanggan.paket', 'pembayaran');
        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function konfirmasiBayar(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'metode_bayar' => 'required|in:cash,transfer,midtrans,xendit',
        ]);
        $tagihan->update([
            'status'       => 'paid',
            'tgl_bayar'    => now(),
            'metode_bayar' => $request->metode_bayar,
            'catatan'      => $request->catatan,
        ]);
        $pelanggan = $tagihan->pelanggan;
        $expired   = ($pelanggan->tgl_expired && $pelanggan->tgl_expired > now())
            ? $pelanggan->paket->hitungExpired($pelanggan->tgl_expired)
            : $pelanggan->paket->hitungExpired();
        $pelanggan->update(['status' => 'aktif', 'tgl_expired' => $expired]);

        // FIX 2: Pakai connectRouter dengan WireGuard + retry
        if ($pelanggan->router) {
            try {
                $mikrotik = new MikrotikService();
                $this->connectRouter($pelanggan->router, $mikrotik);
                $mikrotik->aktifkan($pelanggan->username);
                $mikrotik->disconnect();
            } catch (\Exception $e) {
                \Log::warning("Gagal aktifkan MikroTik untuk {$pelanggan->username}: " . $e->getMessage());
            }
        }

        \App\Models\Pembayaran::create([
            'no_pembayaran' => 'PAY-' . now()->format('YmdHis'),
            'tagihan_id'    => $tagihan->id,
            'pelanggan_id'  => $pelanggan->id,
            'jumlah_bayar'  => $tagihan->total,
            'metode'        => $request->metode_bayar,
            'catatan'       => $request->catatan,
            'created_by'    => auth()->id(),
        ]);

        if ($pelanggan->no_hp) {
            try {
                $whatsapp = new WhatsappService();
                $whatsapp->sendKonfirmasiBayar(
                    $pelanggan->no_hp,
                    $pelanggan->nama,
                    \Carbon\Carbon::parse($tagihan->periode_bulan)->format('F Y'),
                    $tagihan->total
                );
            } catch (\Exception $e) {
                \Log::warning('Gagal kirim WA konfirmasi: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi! Pelanggan aktif kembali.');
    }

    public function generateMassal()
    {
        $hariJatuhTempo = 10;
        $berhasil       = 0;
        $skip           = 0;
        $pelanggans     = Pelanggan::with('paket')->whereIn('status', ['aktif', 'isolir'])->get();
        $whatsapp         = new WhatsappService();

        foreach ($pelanggans as $pelanggan) {
            $sudahAda = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->whereMonth('periode_bulan', now()->month)
                ->whereYear('periode_bulan', now()->year)
                ->exists();
            if ($sudahAda) { $skip++; continue; }

            $tagihan = Tagihan::create([
                'no_tagihan'      => Tagihan::generateNomor(),
                'pelanggan_id'    => $pelanggan->id,
                'paket_id'        => $pelanggan->paket_id,
                'jumlah'          => $pelanggan->paket->harga,
                'denda'           => 0,
                'diskon'          => 0,
                'total'           => $pelanggan->paket->harga,
                'periode_bulan'   => now()->startOfMonth(),
                'tgl_tagihan'     => now(),
                'tgl_jatuh_tempo' => now()->day($hariJatuhTempo),
                'status'          => 'unpaid',
            ]);

            if ($pelanggan->no_hp) {
                try {
                    $whatsapp->sendTagihan(
                        $pelanggan->no_hp,
                        $pelanggan->nama,
                        now()->format('F Y'),
                        $tagihan->total,
                        now()->day($hariJatuhTempo)->format('d/m/Y')
                    );
                } catch (\Exception $e) {
                    \Log::warning('Gagal kirim WA: ' . $e->getMessage());
                }
            }
            $berhasil++;
        }
        return back()->with('success', "Generate selesai! Berhasil: {$berhasil}, Skip (sudah ada): {$skip}");
    }

    public function bayarMassal(Request $request)
    {
        $request->validate([
            'tagihan_ids'  => 'required|array',
            'metode_bayar' => 'required|in:cash,transfer,midtrans,xendit',
        ]);

        $berhasil = 0;
        $skip     = 0;
        $whatsapp   = new WhatsappService();

        // FIX 2: Load semua tagihan sekaligus, group per router
        $tagihans = Tagihan::with('pelanggan.paket', 'pelanggan.router')
            ->whereIn('id', $request->tagihan_ids)
            ->get();

        $grouped = $tagihans->groupBy(fn($t) => $t->pelanggan->router_id ?? 0);

        foreach ($grouped as $routerId => $groupTagihans) {
            $mikrotik  = null;
            $connected = false;
            $router    = $groupTagihans->first()->pelanggan->router ?? null;

            // FIX 2: Buka koneksi sekali per router
            if ($router) {
                try {
                    $mikrotik = new MikrotikService();
                    $this->connectRouter($router, $mikrotik);
                    $connected = true;
                } catch (\Exception $e) {
                    \Log::warning("bayarMassal - gagal konek router [{$router->nama}]: " . $e->getMessage());
                }
            }

            foreach ($groupTagihans as $tagihan) {
                if (!$tagihan || $tagihan->status === 'paid') { $skip++; continue; }

                $tagihan->update([
                    'status'       => 'paid',
                    'tgl_bayar'    => now(),
                    'metode_bayar' => $request->metode_bayar,
                    'catatan'      => $request->catatan,
                ]);

                $pelanggan = $tagihan->pelanggan;
                $expired   = ($pelanggan->tgl_expired && $pelanggan->tgl_expired > now())
                    ? $pelanggan->paket->hitungExpired($pelanggan->tgl_expired)
                    : $pelanggan->paket->hitungExpired();
                $pelanggan->update(['status' => 'aktif', 'tgl_expired' => $expired]);

                // FIX 2: Pakai koneksi yang sudah ada
                if ($connected && $mikrotik) {
                    try {
                        $mikrotik->aktifkan($pelanggan->username);
                    } catch (\Exception $e) {
                        \Log::warning("Gagal aktifkan MikroTik [{$pelanggan->username}]: " . $e->getMessage());
                    }
                }

                \App\Models\Pembayaran::create([
                    'no_pembayaran' => 'PAY-' . now()->format('YmdHis') . '-' . $tagihan->id,
                    'tagihan_id'    => $tagihan->id,
                    'pelanggan_id'  => $pelanggan->id,
                    'jumlah_bayar'  => $tagihan->total,
                    'metode'        => $request->metode_bayar,
                    'catatan'       => $request->catatan,
                    'created_by'    => auth()->id(),
                ]);

                if ($pelanggan->no_hp) {
                    try {
                        $whatsapp->sendKonfirmasiBayar(
                            $pelanggan->no_hp,
                            $pelanggan->nama,
                            \Carbon\Carbon::parse($tagihan->periode_bulan)->format('F Y'),
                            $tagihan->total
                        );
                    } catch (\Exception $e) {
                        \Log::warning('Gagal kirim WA: ' . $e->getMessage());
                    }
                }
                $berhasil++;
            }

            if ($connected && $mikrotik) {
                try { $mikrotik->disconnect(); } catch (\Exception $e) {}
            }
        }

        return back()->with('success', "Pembayaran massal selesai! Berhasil: {$berhasil}, Skip: {$skip}");
    }

    public function exportCsv(\Illuminate\Http\Request $request)
    {
        $query = Tagihan::with('pelanggan', 'paket')->latest();

        if ($request->status) $query->where('status', $request->status);
        if ($request->router_id) {
            $query->whereHas('pelanggan', function($q) use ($request) {
                $q->where('router_id', $request->router_id);
            });
        }

        $tagihans = $query->get();

        $filename = 'tagihan-' . now()->format('Ymd-His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($tagihans) {
            $file = fopen('php://output', 'w');
            // BOM untuk Excel agar baca UTF-8 dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['No Tagihan', 'Nama Pelanggan', 'ID Pelanggan', 'Paket', 'Total', 'Jatuh Tempo', 'Status', 'Tgl Bayar', 'Metode Bayar']);
            foreach ($tagihans as $t) {
                fputcsv($file, [
                    $t->no_tagihan,
                    $t->pelanggan->nama ?? '-',
                    $t->pelanggan->id_pelanggan ?? '-',
                    $t->paket->nama_paket ?? '-',
                    $t->total,
                    $t->tgl_jatuh_tempo?->format('d/m/Y'),
                    $t->status,
                    $t->tgl_bayar ? \Carbon\Carbon::parse($t->tgl_bayar)->format('d/m/Y') : '-',
                    $t->metode_bayar ?? '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function resetCounter(\Illuminate\Http\Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
        }
        if (!\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Password salah!']);
        }
        // Disable foreign key check sementara
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \App\Models\Pembayaran::truncate();
        Tagihan::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
        return response()->json(['success' => true, 'message' => 'Semua data tagihan & pembayaran berhasil dihapus!']);
    }

    public function destroy(Tagihan $tagihan)
    {
        if ($tagihan->status === 'paid') {
            return back()->with('error', 'Tagihan yang sudah dibayar tidak bisa dihapus!');
        }
        $tagihan->delete();
        return back()->with('success', 'Tagihan berhasil dihapus!');
    }
}
