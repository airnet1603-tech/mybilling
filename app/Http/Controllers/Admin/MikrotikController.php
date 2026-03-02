<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Pelanggan;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{
    protected $mikrotik;

    public function __construct()
    {
        $this->mikrotik = new MikrotikService();
    }

    // Halaman daftar router
    public function index()
    {
        $routers = Router::all();
        return view('admin.mikrotik.index', compact('routers'));
    }

    // Tambah router
    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required',
            'ip_address' => 'required',
            'port'       => 'required|integer',
            'username'   => 'required',
            'password'   => 'required',
        ]);
        Router::create($request->all());
        return back()->with('success', 'Router berhasil ditambahkan');
    }

    // Hapus router
    public function destroy(Router $router)
    {
        $router->delete();
        return back()->with('success', 'Router berhasil dihapus');
    }

    // Test koneksi
    public function testConnection(Router $router)
    {
        $result = $this->mikrotik->testConnection($router);
        return response()->json($result);
    }

    // Halaman monitoring
    public function monitoring()
    {
        $routers = Router::where('is_active', true)->get();
        return view('admin.mikrotik.monitoring', compact('routers'));
    }

    // API live sessions (dipanggil AJAX)
    public function getSessions(Router $router)
    {
        try {
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $sessions  = $this->mikrotik->getActiveSessions();
            $resource  = $this->mikrotik->getRouterResource();
            $this->mikrotik->disconnect();
            return response()->json([
                'status'   => true,
                'sessions' => $sessions['data'],
                'count'    => count($sessions['data']),
                'resource' => $resource['data'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    // Isolir pelanggan
    public function isolir(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $result = $this->mikrotik->isolir($pelanggan->username);
            $this->mikrotik->disconnect();
            if ($result['status']) $pelanggan->update(['status' => 'isolir']);
            return back()->with($result['status'] ? 'success' : 'error', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // Aktifkan pelanggan
    public function aktifkan(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $result = $this->mikrotik->aktifkan($pelanggan->username);
            $this->mikrotik->disconnect();
            if ($result['status']) $pelanggan->update(['status' => 'aktif']);
            return back()->with($result['status'] ? 'success' : 'error', $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // Sync pelanggan ke Mikrotik
    public function syncPelanggan(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            if (!$router) {
                return back()->with('error', 'Router belum di-assign ke pelanggan ini');
            }

            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);

            // Tambah PPPoE user
            $password = $pelanggan->password_pppoe ?? $pelanggan->username;
            $profile  = $pelanggan->paket->nama_paket ?? 'default';
            $result   = $this->mikrotik->addPppoeUser($pelanggan->username, $password, $profile);

            // Set queue jika ada IP
            if ($pelanggan->ip_address && $pelanggan->paket) {
                $kecepatan = $pelanggan->paket->kecepatan_download ?? 10;
                $this->mikrotik->setQueue(
                    $pelanggan->username,
                    $pelanggan->ip_address,
                    $kecepatan . 'M',
                    $kecepatan . 'M'
                );
            }

            $this->mikrotik->disconnect();
            return back()->with('success', "Pelanggan {$pelanggan->username} berhasil disync ke Mikrotik!");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal sync: ' . $e->getMessage());
        }


    }
// Suspend pelanggan (disable PPPoE + putus sesi, sama seperti isolir tapi status berbeda)
public function suspend(Pelanggan $pelanggan)
{
    try {
        $router = $pelanggan->router;
        if (!$router) return back()->with('error', 'Router belum di-assign ke pelanggan ini.');
        $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
        $result = $this->mikrotik->isolir($pelanggan->username); // disable PPPoE + kick sesi
        $this->mikrotik->disconnect();
        if ($result['status']) $pelanggan->update(['status' => 'suspend']);
        return back()->with($result['status'] ? 'success' : 'error',
            $result['status'] ? "Pelanggan {$pelanggan->nama} berhasil di-suspend." : $result['message']);
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal suspend: ' . $e->getMessage());
    }
}

// Nonaktif pelanggan (disable PPPoE + putus sesi + tandai nonaktif)
public function nonaktif(Pelanggan $pelanggan)
{
    try {
        $router = $pelanggan->router;
        if (!$router) return back()->with('error', 'Router belum di-assign ke pelanggan ini.');
        $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
        $result = $this->mikrotik->isolir($pelanggan->username); // disable PPPoE + kick sesi
        $this->mikrotik->disconnect();
        if ($result['status']) $pelanggan->update(['status' => 'nonaktif']);
        return back()->with($result['status'] ? 'success' : 'error',
            $result['status'] ? "Pelanggan {$pelanggan->nama} berhasil dinonaktifkan." : $result['message']);
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal nonaktifkan: ' . $e->getMessage());
    }
}



}
