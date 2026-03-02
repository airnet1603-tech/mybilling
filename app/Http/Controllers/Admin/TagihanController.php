<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $query = Tagihan::with('pelanggan', 'paket')->latest();

        if ($request->search) {
            $query->whereHas('pelanggan', function($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                  ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%');
            })->orWhere('no_tagihan', 'like', '%'.$request->search.'%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->bulan) {
            $query->whereMonth('periode_bulan', date('m', strtotime($request->bulan)))
                  ->whereYear('periode_bulan', date('Y', strtotime($request->bulan)));
        }

        $tagihans = $query->paginate(15);

        $totalUnpaid  = Tagihan::where('status', 'unpaid')->count();
        $totalOverdue = Tagihan::where('status', 'overdue')->count();
        $totalPaid    = Tagihan::where('status', 'paid')
                               ->whereMonth('tgl_bayar', now()->month)->count();
        $totalPendapatan = Tagihan::where('status', 'paid')
                                  ->whereMonth('tgl_bayar', now()->month)
                                  ->sum('total');

        return view('admin.tagihan.index', compact(
            'tagihans', 'totalUnpaid', 'totalOverdue', 'totalPaid', 'totalPendapatan'
        ));
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

        Tagihan::create([
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

        return redirect('/admin/tagihan')->with('success', 'Tagihan berhasil dibuat!');
    }

    public function show(Tagihan $tagihan)
    {
        $tagihan->load('pelanggan.paket', 'pembayaran');
        return view('admin.tagihan.show', compact('tagihan'));
    }

    // Konfirmasi bayar manual (cash/transfer)
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

        // Perpanjang expired pelanggan
        $pelanggan = $tagihan->pelanggan;
        $expired   = ($pelanggan->tgl_expired && $pelanggan->tgl_expired > now())
            ? $pelanggan->tgl_expired->addDays($pelanggan->paket->masa_aktif)
            : now()->addDays($pelanggan->paket->masa_aktif);

        $pelanggan->update([
            'status'      => 'aktif',
            'tgl_expired' => $expired,
        ]);

        // Catat pembayaran
        \App\Models\Pembayaran::create([
            'no_pembayaran' => 'PAY-' . now()->format('YmdHis'),
            'tagihan_id'    => $tagihan->id,
            'pelanggan_id'  => $pelanggan->id,
            'jumlah_bayar'  => $tagihan->total,
            'metode'        => $request->metode_bayar,
            'catatan'       => $request->catatan,
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi! Pelanggan aktif kembali.');
    }

    // Generate tagihan massal semua pelanggan aktif
    public function generateMassal()
    {
        $hariJatuhTempo = 10;
        $berhasil = 0;
        $skip     = 0;

        $pelanggans = Pelanggan::with('paket')
            ->whereIn('status', ['aktif', 'isolir'])
            ->get();

        foreach ($pelanggans as $pelanggan) {
            $sudahAda = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->whereMonth('periode_bulan', now()->month)
                ->whereYear('periode_bulan', now()->year)
                ->exists();

            if ($sudahAda) { $skip++; continue; }

            Tagihan::create([
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
            $berhasil++;
        }

        return back()->with('success', "Generate selesai! Berhasil: {$berhasil}, Skip (sudah ada): {$skip}");
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
