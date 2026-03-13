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

    public function index()
    {
        $routers = Router::all();
        return view('admin.mikrotik.index', compact('routers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required',
            'ip_address' => 'required',
            'port'       => 'required|integer',
            'username'   => 'required',
            'password'   => 'required',
        ]);

        Router::create([
            'nama'           => $request->nama,
            'ip_address'     => $request->ip_address,
            'port'           => $request->port,
            'username'       => $request->username,
            'password'       => $request->password,
            'local_address'  => $request->local_address,
            'remote_address' => $request->remote_address,
            'dns_server'     => $request->dns_server,
            'is_active'      => 1,
        ]);

        return back()->with('success', 'Router berhasil ditambahkan');
    }

    /**
     * Import setting dari RB (pool, dns) ? return JSON untuk ditampilkan di modal
     */
    public function importSetting(Router $router)
    {
        try {
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $pools      = $this->mikrotik->getIpPools();
            $dns        = $this->mikrotik->getDnsServer();
            $localAddr  = $this->mikrotik->getPppoeLocalAddress();
            $this->mikrotik->disconnect();

            return response()->json([
                'status'        => true,
                'pools'         => $pools['data']       ?? [],
                'dns'           => $dns['dns']          ?? '',
                'local_address' => $localAddr['local_address'] ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Simpan setting PPPoE hasil import dari RB
     */
    public function updatePppoeSetting(Request $request, Router $router)
    {
        $router->update([
            'local_address'  => $request->local_address,
            'remote_address' => $request->remote_address,
            'dns_server'     => $request->dns_server,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['status' => true, 'message' => "Setting PPPoE router {$router->nama} berhasil disimpan!"]);
        }

        return back()->with('success', "Setting PPPoE router {$router->nama} berhasil disimpan!");
    }

    public function destroy(Router $router)
    {
        $router->delete();
        return back()->with('success', 'Router berhasil dihapus');
    }

    public function testConnection(Router $router)
    {
        $result = $this->mikrotik->testConnection($router);
        return response()->json($result);
    }

    public function monitoring()
    {
        $routers = Router::where('is_active', true)->get();
        return view('admin.mikrotik.monitoring', compact('routers'));
    }

    public function getStats(Router $router)
    {
        try {
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $resource = $this->mikrotik->getRouterResource();
            $sessions = $this->mikrotik->getActiveSessions();
            $this->mikrotik->disconnect();

            if (!$resource['status']) {
                return response()->json(['status' => false]);
            }

            $d = $resource['data'];
            return response()->json([
                'status'      => true,
                'cpu'         => rtrim($d['cpu_load'] ?? '-', '%'),
                'memory'      => $d['memory_used'] ?? '-',
                'uptime'      => $d['uptime'] ?? '-',
                'pppoe_count' => count($sessions['data'] ?? []),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSessions(Router $router)
    {
        try {
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $raw = $this->mikrotik->getActiveSessions();
            $this->mikrotik->disconnect();

            $sessions = array_map(function($s) {
                return [
                    'name'        => $s['name']      ?? '-',
                    'address'     => $s['address']   ?? '-',
                    'uptime'      => $s['uptime']    ?? '-',
                    'bytes_in'    => isset($s['bytes-in'])  ? (int)$s['bytes-in']  : 0,
                    'bytes_out'   => isset($s['bytes-out']) ? (int)$s['bytes-out'] : 0,
                    'mac_address' => $s['caller-id'] ?? '-',
                ];
            }, $raw['data'] ?? []);

            return response()->json([
                'status'   => true,
                'sessions' => $sessions,
                'count'    => count($sessions),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

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

    public function syncPelanggan(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            if (!$router) return back()->with('error', 'Router belum di-assign ke pelanggan ini');

            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);

            $password = $pelanggan->password_pppoe ?? $pelanggan->username;
            $profile  = $pelanggan->paket->nama_paket ?? 'default';
            $this->mikrotik->addPppoeUser($pelanggan->username, $password, $profile);

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

    public function suspend(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            if (!$router) return back()->with('error', 'Router belum di-assign ke pelanggan ini.');
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $result = $this->mikrotik->isolir($pelanggan->username);
            $this->mikrotik->disconnect();
            if ($result['status']) $pelanggan->update(['status' => 'suspend']);
            return back()->with($result['status'] ? 'success' : 'error',
                $result['status'] ? "Pelanggan {$pelanggan->nama} berhasil di-suspend." : $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal suspend: ' . $e->getMessage());
        }
    }

    public function nonaktif(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            if (!$router) return back()->with('error', 'Router belum di-assign ke pelanggan ini.');
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $result = $this->mikrotik->isolir($pelanggan->username);
            $this->mikrotik->disconnect();
            if ($result['status']) $pelanggan->update(['status' => 'nonaktif']);
            return back()->with($result['status'] ? 'success' : 'error',
                $result['status'] ? "Pelanggan {$pelanggan->nama} berhasil dinonaktifkan." : $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal nonaktifkan: ' . $e->getMessage());
        }
    }
}