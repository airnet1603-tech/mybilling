<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    protected function syncKeSemuaRouter($nama, $download, $upload)
    {
        $routers = Router::where('is_active', true)->get();
        $mikrotik = new MikrotikService();
        foreach ($routers as $router) {
            try {
                $mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
                // Kirim object $router supaya local_address, remote_address, dns_server dipakai
                $mikrotik->syncProfile($nama, $download, $upload, $router);
                $mikrotik->disconnect();
            } catch (\Exception $e) {
                \Log::warning("Gagal sync profile ke router {$router->nama}: " . $e->getMessage());
            }
        }
    }

    protected function deleteFromSemuaRouter($nama)
    {
        $routers = Router::where('is_active', true)->get();
        $mikrotik = new MikrotikService();
        foreach ($routers as $router) {
            try {
                $mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
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
            'nama_paket'          => 'required|string|max:255',
            'harga'               => 'required|integer|min:0',
            'kecepatan_download'  => 'required|integer|min:1',
            'kecepatan_upload'    => 'required|integer|min:1',
            'radius_profile'      => 'required|string|max:100',
            'jenis'               => 'required|in:pppoe,hotspot',
            'masa_aktif'          => 'required|integer|min:1',
        ]);

        $paket = Paket::create([
            'nama_paket'         => $request->nama_paket,
            'harga'              => $request->harga,
            'kecepatan_download' => $request->kecepatan_download,
            'kecepatan_upload'   => $request->kecepatan_upload,
            'radius_profile'     => $request->radius_profile,
            'jenis'              => $request->jenis,
            'masa_aktif'         => $request->masa_aktif,
            'deskripsi'          => $request->deskripsi,
            'is_active'          => $request->has('is_active') ? true : false,
        ]);

        if ($request->jenis === 'pppoe') {
            $this->syncKeSemuaRouter($paket->nama_paket, $paket->kecepatan_download, $paket->kecepatan_upload);
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
            'nama_paket'         => $request->nama_paket,
            'harga'              => $request->harga,
            'kecepatan_download' => $request->kecepatan_download,
            'kecepatan_upload'   => $request->kecepatan_upload,
            'radius_profile'     => $request->radius_profile,
            'jenis'              => $request->jenis,
            'masa_aktif'         => $request->masa_aktif,
            'deskripsi'          => $request->deskripsi,
            'is_active'          => $request->has('is_active') ? true : false,
        ]);

        if ($request->jenis === 'pppoe') {
            if ($nameLama !== $request->nama_paket) {
                $this->deleteFromSemuaRouter($nameLama);
            }
            $this->syncKeSemuaRouter($paket->nama_paket, $paket->kecepatan_download, $paket->kecepatan_upload);
        }

        return redirect('/admin/paket')->with('success', 'Paket berhasil diupdate dan disync ke Mikrotik!');
    }

    public function destroy(Paket $paket)
    {
        // Cek pelanggan aktif (bukan soft-deleted)
        if ($paket->pelanggan()->count() > 0) {
            return back()->with('error', 'Paket tidak bisa dihapus karena masih dipakai pelanggan aktif!');
        }

        // Hapus permanen pelanggan yang sudah soft-deleted
        $paket->pelanggan()->withTrashed()->forceDelete();

        $nama = $paket->nama_paket;
        $jenis = $paket->jenis;
        $paket->delete();

        if ($jenis === 'pppoe') {
            $this->deleteFromSemuaRouter($nama);
        }

        return redirect('/admin/paket')->with('success', 'Paket berhasil dihapus!');
    }
}