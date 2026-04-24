<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\ConnectsToMikrotik;
use App\Models\Paket;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    use ConnectsToMikrotik;

    // FIX 2: Pakai connectRouter dengan WireGuard + retry
    protected function syncKeSemuaRouter($nama, $download, $upload, $burst = [])
    {
        $routers = Router::where('is_active', true)->get();
        foreach ($routers as $router) {
            $mikrotik = new MikrotikService();
            try {
                $this->connectRouter($router, $mikrotik);
                $mikrotik->syncProfile($nama, $download, $upload, $router, $burst);
                $mikrotik->disconnect();
            } catch (\Exception $e) {
                \Log::warning("Gagal sync profile ke router {$router->nama}: " . $e->getMessage());
            }
        }
    }

    // FIX 2: Pakai connectRouter dengan WireGuard + retry
    protected function deleteFromSemuaRouter($nama)
    {
        $routers = Router::where('is_active', true)->get();
        foreach ($routers as $router) {
            $mikrotik = new MikrotikService();
            try {
                $this->connectRouter($router, $mikrotik);
                $mikrotik->deleteProfile($nama);
                $mikrotik->disconnect();
            } catch (\Exception $e) {
                \Log::warning("Gagal hapus profile dari router {$router->nama}: " . $e->getMessage());
            }
        }
    }

    public function index()
    {
        $pakets = Paket::latest()->paginate(10);
        return view('admin.paket.index', compact('pakets'));
    }

    public function create()
    {
        return view('admin.paket.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket'         => 'required|string|max:255',
            'harga'              => 'required|integer|min:0',
            'kecepatan_download' => 'required|integer|min:1',
            'kecepatan_upload'   => 'required|integer|min:1',
            'radius_profile'     => 'required|string|max:100',
            'jenis'              => 'required|in:pppoe,hotspot',
            'masa_aktif'         => 'required|integer|min:1',
        ]);

        $paket = Paket::create([
            'nama_paket'               => $request->nama_paket,
            'harga'                    => $request->harga,
            'kecepatan_download'       => $request->kecepatan_download,
            'kecepatan_upload'         => $request->kecepatan_upload,
            'radius_profile'           => $request->radius_profile,
            'jenis'                    => $request->jenis,
            'masa_aktif'               => $request->masa_aktif,
            'deskripsi'                => $request->deskripsi,
            'is_active'                => $request->has('is_active') ? true : false,
            'burst_limit_download'     => $request->burst_limit_download     ?? 0,
            'burst_limit_upload'       => $request->burst_limit_upload       ?? 0,
            'burst_threshold_download' => $request->burst_threshold_download ?? 0,
            'burst_threshold_upload'   => $request->burst_threshold_upload   ?? 0,
            'burst_time'               => $request->burst_time               ?? 8,
        ]);

        if ($request->jenis === 'pppoe') {
            $this->syncKeSemuaRouter($paket->nama_paket, $paket->kecepatan_download, $paket->kecepatan_upload, ['burst_limit_download' => $paket->burst_limit_download, 'burst_limit_upload' => $paket->burst_limit_upload, 'burst_threshold_download' => $paket->burst_threshold_download, 'burst_threshold_upload' => $paket->burst_threshold_upload, 'burst_time' => $paket->burst_time]);
        }

        return redirect('/admin/paket')->with('success', 'Paket berhasil ditambahkan dan disync ke Mikrotik!');
    }

    public function edit(Paket $paket)
    {
        return view('admin.paket.edit', compact('paket'));
    }

    public function update(Request $request, Paket $paket)
    {
        $request->validate([
            'nama_paket'         => 'required|string|max:255',
            'harga'              => 'required|integer|min:0',
            'kecepatan_download' => 'required|integer|min:1',
            'kecepatan_upload'   => 'required|integer|min:1',
            'radius_profile'     => 'required|string|max:100',
            'jenis'              => 'required|in:pppoe,hotspot',
            'masa_aktif'         => 'required|integer|min:1',
        ]);

        $nameLama = $paket->nama_paket;

        $paket->update([
            'nama_paket'               => $request->nama_paket,
            'harga'                    => $request->harga,
            'kecepatan_download'       => $request->kecepatan_download,
            'kecepatan_upload'         => $request->kecepatan_upload,
            'radius_profile'           => $request->radius_profile,
            'jenis'                    => $request->jenis,
            'masa_aktif'               => $request->masa_aktif,
            'deskripsi'                => $request->deskripsi,
            'is_active'                => $request->has('is_active') ? true : false,
            'burst_limit_download'     => $request->burst_limit_download     ?? 0,
            'burst_limit_upload'       => $request->burst_limit_upload       ?? 0,
            'burst_threshold_download' => $request->burst_threshold_download ?? 0,
            'burst_threshold_upload'   => $request->burst_threshold_upload   ?? 0,
            'burst_time'               => $request->burst_time               ?? 8,
        ]);

        if ($request->jenis === 'pppoe') {
            if ($nameLama !== $request->nama_paket) {
                $this->deleteFromSemuaRouter($nameLama);
            }
            $this->syncKeSemuaRouter($paket->nama_paket, $paket->kecepatan_download, $paket->kecepatan_upload, ['burst_limit_download' => $paket->burst_limit_download, 'burst_limit_upload' => $paket->burst_limit_upload, 'burst_threshold_download' => $paket->burst_threshold_download, 'burst_threshold_upload' => $paket->burst_threshold_upload, 'burst_time' => $paket->burst_time]);
        }

        return redirect('/admin/paket')->with('success', 'Paket berhasil diupdate dan disync ke Mikrotik!');
    }

    public function destroy(Paket $paket)
    {
        if ($paket->pelanggan()->count() > 0) {
            return back()->with('error', 'Paket tidak bisa dihapus karena masih dipakai pelanggan aktif!');
        }

        $paket->pelanggan()->withTrashed()->forceDelete();

        $nama  = $paket->nama_paket;
        $jenis = $paket->jenis;
        $paket->delete();

        if ($jenis === 'pppoe') {
            $this->deleteFromSemuaRouter($nama);
        }

        return redirect('/admin/paket')->with('success', 'Paket berhasil dihapus!');
    }
}
