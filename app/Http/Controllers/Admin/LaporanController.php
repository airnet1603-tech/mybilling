<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->tahun ?? now()->year;

        // Pendapatan per bulan (12 bulan)
        $pendapatanBulanan = [];
        $tagihanBulanan    = [];
        for ($i = 1; $i <= 12; $i++) {
            $pendapatanBulanan[$i] = Tagihan::where('status', 'paid')
                ->whereYear('tgl_bayar', $tahun)
                ->whereMonth('tgl_bayar', $i)
                ->sum('total');

            $tagihanBulanan[$i] = Tagihan::whereYear('periode_bulan', $tahun)
                ->whereMonth('periode_bulan', $i)
                ->count();
        }

        // Statistik tahun ini
        $totalPendapatanTahun = array_sum($pendapatanBulanan);
        $totalTagihanTahun    = Tagihan::whereYear('periode_bulan', $tahun)->count();
        $totalLunasTahun      = Tagihan::where('status', 'paid')->whereYear('tgl_bayar', $tahun)->count();
        $totalUnpaidTahun     = Tagihan::whereIn('status', ['unpaid', 'overdue'])->whereYear('periode_bulan', $tahun)->count();

        // Pendapatan per paket
        $pendapatanPerPaket = Tagihan::with('paket')
            ->where('status', 'paid')
            ->whereYear('tgl_bayar', $tahun)
            ->select('paket_id', DB::raw('SUM(total) as total_pendapatan'), DB::raw('COUNT(*) as jumlah'))
            ->groupBy('paket_id')
            ->get();

        // Pelanggan baru per bulan
        $pelangganBaru = [];
        for ($i = 1; $i <= 12; $i++) {
            $pelangganBaru[$i] = Pelanggan::whereYear('created_at', $tahun)
                ->whereMonth('created_at', $i)
                ->count();
        }

        // Metode pembayaran
        $metodeBayar = Pembayaran::whereYear('created_at', $tahun)
            ->select('metode', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(jumlah_bayar) as total'))
            ->groupBy('metode')
            ->get();

        // Tagihan bulan ini detail
        $bulanIni = now()->month;
        $tagihanBulanIni = Tagihan::with('pelanggan', 'paket')
            ->whereMonth('periode_bulan', $bulanIni)
            ->whereYear('periode_bulan', $tahun)
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.laporan.index', compact(
            'tahun', 'pendapatanBulanan', 'tagihanBulanan',
            'totalPendapatanTahun', 'totalTagihanTahun',
            'totalLunasTahun', 'totalUnpaidTahun',
            'pendapatanPerPaket', 'pelangganBaru',
            'metodeBayar', 'tagihanBulanIni'
        ));
    }
}
