<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalPelanggan'   => Pelanggan::count(),
            'pelangganAktif'   => Pelanggan::where('status', 'aktif')->count(),
            'tagihanUnpaid'    => Tagihan::whereIn('status', ['unpaid', 'overdue'])->count(),
            'pendapatanBulanIni' => Tagihan::where('status', 'paid')
                                    ->whereMonth('tgl_bayar', now()->month)
                                    ->whereYear('tgl_bayar', now()->year)
                                    ->sum('total'),
            'pelangganTerbaru' => Pelanggan::with('paket')->latest()->limit(5)->get(),
            'tagihanOverdue'   => Tagihan::with('pelanggan')
                                    ->where('status', 'overdue')
                                    ->latest()->limit(5)->get(),
        ]);
    }
}
