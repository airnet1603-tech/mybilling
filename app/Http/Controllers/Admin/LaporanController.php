<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;

        $pendapatanBulanan = [];
        $tagihanBulanan    = [];
        for ($i = 1; $i <= 12; $i++) {
            $pendapatanBulanan[$i] = Pembayaran::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->sum('jumlah_bayar');



            $tagihanBulanan[$i] = Tagihan::whereYear('periode_bulan', $tahun)
                ->whereMonth('periode_bulan', $i)
                ->count();
        }

        $totalPendapatanTahun = array_sum($pendapatanBulanan);
        $totalTagihanTahun    = Tagihan::whereYear('periode_bulan', $tahun)->count();
        $totalLunasTahun      = Tagihan::where('status', 'paid')->whereYear('tgl_bayar', $tahun)->count();
        $totalUnpaidTahun     = Tagihan::whereIn('status', ['unpaid', 'overdue'])->whereYear('periode_bulan', $tahun)->count();

        $pendapatanPerPaket = Tagihan::with('paket')
            ->where('status', 'paid')
            ->whereYear('tgl_bayar', $tahun)
            ->select('paket_id', DB::raw('SUM(total) as total_pendapatan'), DB::raw('COUNT(*) as jumlah'))
            ->groupBy('paket_id')
            ->get();

        $pelangganBaru = [];
        for ($i = 1; $i <= 12; $i++) {
            $pelangganBaru[$i] = Pelanggan::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->count();
        }

        $metodeBayar = Pembayaran::whereYear('created_at', $tahun)
            ->select('metode', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(jumlah_bayar) as total'))
            ->groupBy('metode')
            ->get();

        $bulanIni = now()->month;
        $tagihanBulanIni = Tagihan::with('pelanggan', 'paket')
            ->whereMonth('periode_bulan', $bulanIni)
            ->whereYear('periode_bulan', $tahun)
            ->latest()
            ->limit(10)
            ->get();

        // Statistik per user (admin & operator)
        $statistikPerUser = User::whereIn('role', ['admin', 'operator'])
            ->withCount([
                'pembayaran as total_transaksi',
            ])
            ->withSum('pembayaran as total_nominal', 'jumlah_bayar')
            ->get();

        // Daftar bulan yang ada datanya (untuk clear per bulan)
        $bulanTersedia = Pembayaran::whereYear('created_at', $tahun)
            ->selectRaw('MONTH(created_at) as bulan, COUNT(*) as jml')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('bulan')
            ->get();

        // Daftar user yang punya data pembayaran
        $userDenganData = User::whereIn('role', ['admin', 'operator'])
            ->whereHas('pembayaran')
            ->get();

        return view('admin.laporan.index', compact(
            'tahun', 'pendapatanBulanan', 'tagihanBulanan',
            'totalPendapatanTahun', 'totalTagihanTahun',
            'totalLunasTahun', 'totalUnpaidTahun',
            'pendapatanPerPaket', 'pelangganBaru',
            'metodeBayar', 'tagihanBulanIni',
            'statistikPerUser', 'bulanTersedia', 'userDenganData'
        ));
    }

    // Rollback tagihan ke unpaid
    public function rollbackUnpaid(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $tagihan = \App\Models\Tagihan::findOrFail($request->tagihan_id);
        // Hapus pembayaran terkait
        $tagihan->pembayaran()->delete();
        // Reset tagihan ke unpaid
        $tagihan->update([
            'status'       => 'unpaid',
            'tgl_bayar'    => null,
            'metode_bayar' => null,
            'payment_url'  => null,
        ]);
        return back()->with('success', 'Tagihan berhasil direset ke Unpaid.');
    }

    // Clear pembayaran per pelanggan (bonus/gratis)
    public function clearPelanggan(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $pembayaranId = $request->pembayaran_id;
        $pembayaran = \App\Models\Pembayaran::findOrFail($pembayaranId);
        $pembayaran->delete();

        return back()->with('success', 'Pembayaran pelanggan berhasil dihapus dari laporan. Tagihan tetap lunas.');
    }

    // Clear pembayaran per bulan
    public function clearBulan(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $tahun = $request->tahun ?? now()->year;
        $bulan = $request->bulan;

        $pembayaran = Pembayaran::whereYear('created_at', $tahun)
            ->whereMonth('created_at', $bulan)
            ->get();

        foreach ($pembayaran as $p) {
            // Reset tagihan terkait ke unpaid
            if ($p->tagihan) {
                $p->tagihan->update([
                    'status'      => 'unpaid',
                    'tgl_bayar'   => null,
                    'metode_bayar'=> null,
                    'payment_url' => null,
                ]);
            }
            $p->delete();
        }

        return back()->with('success', 'Data pembayaran bulan berhasil dihapus.');
    }

    // Clear pembayaran per tahun
    public function clearTahun(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $tahun = $request->tahun ?? now()->year;

        $pembayaran = Pembayaran::whereYear('created_at', $tahun)->get();

        foreach ($pembayaran as $p) {
            if ($p->tagihan) {
                $p->tagihan->update([
                    'status'      => 'unpaid',
                    'tgl_bayar'   => null,
                    'metode_bayar'=> null,
                    'payment_url' => null,
                ]);
            }
            $p->delete();
        }

        return back()->with('success', 'Semua data pembayaran tahun ' . $tahun . ' berhasil dihapus.');
    }

    // Clear pembayaran per user
    public function clearUser(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $userId = $request->user_id;
        $pembayaran = Pembayaran::where('created_by', $userId)->get();
        foreach ($pembayaran as $p) {

            $p->delete();
        }

        return back()->with('success', 'Data pembayaran user berhasil dihapus dari laporan. Tagihan tetap lunas.');
    }
}
