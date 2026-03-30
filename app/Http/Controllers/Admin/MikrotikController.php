<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Router;
use App\Models\Pelanggan;
use App\Models\Paket;
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
                'pools'         => $pools['data']              ?? [],
                'dns'           => $dns['dns']                 ?? '',
                'local_address' => $localAddr['local_address'] ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

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
                'uptime'      => $d['uptime']      ?? '-',
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

            $sessions = array_map(function ($s) {
                // RouterOS v6 pakai 'bytes-in'/'bytes-out'
                // RouterOS v7 pakai 'rx-byte'/'tx-byte'
                // Fallback ke semua kemungkinan field agar kompatibel
                $bytesIn  = (int) ($s['rx-byte']   ?? $s['bytes-in']  ?? $s['rx-bytes']  ?? 0);
                $bytesOut = (int) ($s['tx-byte']   ?? $s['bytes-out'] ?? $s['tx-bytes']  ?? 0);

                return [
                    'name'        => $s['name']      ?? '-',
                    'address'     => $s['address']   ?? '-',
                    'uptime'      => $s['uptime']    ?? '-',
                    'bytes_in'    => $bytesIn,
                    'bytes_out'   => $bytesOut,
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

    public function previewImportPppoe(Router $router)
    {
        try {
            $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
            $result = $this->mikrotik->getPppoeSecrets();
            $this->mikrotik->disconnect();

            if (!$result['status']) {
                return response()->json(['status' => false, 'message' => $result['message'] ?? 'Gagal ambil data']);
            }

            $existing = Pelanggan::pluck('username')->toArray();
            $pakets   = Paket::all();

            // Deduplikasi username dari Mikrotik
            $seen       = [];
            $uniqueData = [];
            foreach ($result['data'] as $s) {
                $key = strtolower($s['username']);
                if (isset($seen[$key])) continue;
                $seen[$key]   = true;
                $uniqueData[] = $s;
            }

            $data = array_map(function ($s) use ($existing, $pakets) {
                $profileClean = strtolower(trim($s['profile']));
                $matchPaket   = $pakets->first(function ($p) use ($profileClean) {
                    $namaClean = strtolower(trim($p->nama_paket));
                    return $namaClean === $profileClean
                        || str_replace([' ', '-', '_'], '', $namaClean) === str_replace([' ', '-', '_'], '', $profileClean);
                });
                return [
                    'username'   => $s['username'],
                    'password'   => $s['password'],
                    'profile'    => $s['profile'],
                    'online'     => $s['online'],
                    'address'    => $s['address'],
                    'disabled'   => $s['disabled'],
                    'exists'     => in_array($s['username'], $existing),
                    'paket_id'   => $matchPaket ? $matchPaket->id   : null,
                    'paket_nama' => $matchPaket ? $matchPaket->nama_paket : null,
                ];
            }, $uniqueData);

            return response()->json([
                'status' => true,
                'data'   => $data,
                'pakets' => $pakets->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama_paket]),
                'router' => ['id' => $router->id, 'nama' => $router->nama],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function doImportPppoe(Request $request, Router $router)
    {
        $items    = $request->input('items', []);
        $paketId  = $request->input('paket_id');
        $bulan    = max(1, (int) $request->input('bulan', 1));
        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($items as $index => $item) {
            try {
                $username = trim($item['username'] ?? '');
                if (!$username) continue;

                if (Pelanggan::withTrashed()->where('username', $username)->exists()) {
                    Pelanggan::withTrashed()->where('username', $username)->forceDelete();
                    $skipped++;
                    continue;
                }

                $usePaketId = $item['paket_id'] ?? $paketId;
                if (!$usePaketId) {
                    $skipped++;
                    $errors[] = $username . ': paket belum dipilih';
                    continue;
                }

                \DB::transaction(function () use ($username, $item, $usePaketId, $router, $bulan, &$imported) {
                    $lastId      = Pelanggan::lockForUpdate()->max('id') ?? 0;
                    $idPelanggan = 'AR-' . date('Y') . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

                    $tglExpired = !empty($item['tgl_expired'])
                        ? $item['tgl_expired']
                        : now()->addMonths($bulan)->toDateString();

                    Pelanggan::create([
                        'id_pelanggan'   => $idPelanggan,
                        'nama'           => $username,
                        'username'       => $username,
                        'password'       => bcrypt($item['password'] ?? $username),
                        'password_pppoe' => $item['password'] ?? $username,
                        'paket_id'       => $usePaketId,
                        'router_id'      => $router->id,
                        'router_name'    => $router->nama,
                        'tgl_daftar'     => now()->toDateString(),
                        'tgl_expired'    => $tglExpired,
                        'ip_address'     => $item['address'] ?? null,
                        'status'         => ($item['disabled'] ?? false) ? 'isolir' : 'aktif',
                        'jenis_layanan'  => 'pppoe',
                    ]);
                    $imported++;
                });
            } catch (\Exception $e) {
                $errors[] = ($item['username'] ?? 'item-' . $index) . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'status'   => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'message'  => 'Berhasil import ' . $imported . ' pelanggan, ' . $skipped . ' dilewati.'
                . (count($errors) ? ' Ada ' . count($errors) . ' error.' : ''),
        ]);
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
            return back()->with(
                $result['status'] ? 'success' : 'error',
                $result['status'] ? "Pelanggan {$pelanggan->nama} berhasil di-suspend." : $result['message']
            );
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
            return back()->with(
                $result['status'] ? 'success' : 'error',
                $result['status'] ? "Pelanggan {$pelanggan->nama} berhasil dinonaktifkan." : $result['message']
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal nonaktifkan: ' . $e->getMessage());
        }
    }
}