<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('pelanggan', 'tagihan')->latest();

        if ($request->search) {
            $query->whereHas('pelanggan', function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->metode) {
            $query->where('metode', $request->metode);
        }

        if ($request->bulan) {
            $query->whereMonth('created_at', date('m', strtotime($request->bulan)))
                  ->whereYear('created_at', date('Y', strtotime($request->bulan)));
        }

        $pembayarans     = $query->paginate(15);
        $totalBulanIni   = Pembayaran::whereMonth('created_at', now()->month)->sum('jumlah_bayar');
        $totalTransaksi  = Pembayaran::whereMonth('created_at', now()->month)->count();
        $totalCash       = Pembayaran::whereMonth('created_at', now()->month)->where('metode', 'cash')->sum('jumlah_bayar');
        $totalTransfer   = Pembayaran::whereMonth('created_at', now()->month)->where('metode', 'transfer')->sum('jumlah_bayar');

        return view('admin.pembayaran.index', compact(
            'pembayarans', 'totalBulanIni', 'totalTransaksi', 'totalCash', 'totalTransfer'
        ));
    }
}
