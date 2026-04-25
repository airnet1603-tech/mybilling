<?php

namespace App\Http\Controllers\Admin;

use App\Services\MikrotikService;
use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PelangganController extends Controller
{
    // FIX 2: Helper connect dengan WireGuard support + retry otomatis
    private function connectRouter($router, MikrotikService $mikrotik, $retry = 3)
    {
        $ip       = (!empty($router->use_wireguard) && !empty($router->wg_ip))
                    ? $router->wg_ip
                    : $router->ip_address;
        $attempt  = 0;
        $lastError = null;

        while ($attempt < $retry) {
            try {
                $mikrotik->connect($ip, $router->username, $router->password, $router->port);
                return true;
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                $attempt++;
                if ($attempt < $retry) sleep(1);
            }
        }

        Log::error("Gagal konek router [{$router->nama}] setelah {$retry}x percobaan: {$lastError}");
        throw new \Exception("Router [{$router->nama}] tidak dapat dihubungi setelah {$retry}x percobaan: {$lastError}");
    }

    public function index(Request $request)
    {
        $query = Pelanggan::with("paket", "router")->latest();

        if ($request->filled("search")) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where("nama", "like", "%".$search."%")
                  ->orWhere("username", "like", "%".$search."%")
                  ->orWhere("no_hp", "like", "%".$search."%")
                  ->orWhere("id_pelanggan", "like", "%".$search."%");
            });
        }

        if ($request->filled("status"))   $query->where("status", $request->status);
        if ($request->filled("paket_id")) $query->where("paket_id", $request->paket_id);
        if ($request->filled("router_id")) $query->where("router_id", $request->router_id);

        $perPage = in_array((int)request("perPage"), [10,25,50,100,150,200,250,500,1000]) ? (int)request("perPage") : 10;
        $pelanggans = $query->paginate($perPage)->withQueryString();
        $pakets     = Paket::where("is_active", true)->get();
        $routers    = \App\Models\Router::where("is_active", true)->get();

        return view("admin.pelanggan.index", compact("pelanggans", "pakets", "routers"));
    }

    public function create()
    {
        $pakets  = Paket::where("is_active", true)->get();
        $routers = \App\Models\Router::where("is_active", true)->get();
        return view("admin.pelanggan.create", compact("pakets", "routers"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "nama"     => "required|string|max:255",
            "username" => "required|unique:pelanggan,username,NULL,id,deleted_at,NULL",
            "password" => "required|min:6",
            "no_hp"    => "required",
            "paket_id" => "required|exists:paket,id",
        ]);

        $tahun = now()->format("Y");
        $last  = Pelanggan::whereYear("created_at", $tahun)
                    ->withTrashed()
                    ->orderByDesc("id_pelanggan")
                    ->value("id_pelanggan");

        $urutan      = $last ? (int) substr($last, -4) + 1 : 1;
        $idPelanggan = "AR-" . $tahun . str_pad($urutan, 4, "0", STR_PAD_LEFT);

        while (Pelanggan::withTrashed()->where("id_pelanggan", $idPelanggan)->exists()) {
            $urutan++;
            $idPelanggan = "AR-" . $tahun . str_pad($urutan, 4, "0", STR_PAD_LEFT);
        }

        $pelanggan = Pelanggan::create([
            "id_pelanggan"   => $idPelanggan,
            "nama"           => $request->nama,
            "username"       => $request->username,
            "password"       => Hash::make($request->password),
            "password_pppoe" => $request->password,
            "pin"            => Hash::make("123456"),
            "no_hp"          => $request->no_hp,
            "email"          => $request->email,
            "alamat"         => $request->alamat,
            "paket_id"       => $request->paket_id,
            "wilayah"        => $request->wilayah,
            "latitude"       => $request->latitude,
            "longitude"      => $request->longitude,
            "maps"           => $request->maps,
            "router_name"    => $request->router_name,
            "ip_address"     => $request->ip_address,
            "tgl_daftar"     => now(),
            "tgl_expired"    => $request->tgl_expired ? \Carbon\Carbon::parse($request->tgl_expired) : now()->addDays(30),
            "status"         => "aktif",
            "jenis_layanan"  => $request->jenis_layanan ?? "pppoe",
            "router_id"      => $request->router_id,
        ]);

        // FIX 2: Pakai connectRouter dengan WireGuard + retry
        try {
            $router = $pelanggan->router;
            $paket  = $pelanggan->paket;
            if ($router && $paket) {
                $mikrotik = new MikrotikService();
                $this->connectRouter($router, $mikrotik);
                $mikrotik->addPppoeUser($pelanggan->username, $request->password, $paket->nama_paket);
                $mikrotik->disconnect();
            }
        } catch (\Exception $e) {
            return redirect('/admin/pelanggan')
                ->with('warning', 'Pelanggan berhasil ditambahkan, tapi gagal sync Mikrotik: ' . $e->getMessage());
        }

        return redirect('/admin/pelanggan')
            ->with('success', 'Pelanggan berhasil ditambahkan dan disync ke Mikrotik!');
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load("paket", "tagihan", "router");
        return view("admin.pelanggan.show", compact("pelanggan"));
    }

    public function export(Request $request)
    {
        $query = Pelanggan::with('paket', 'router');

        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        } else {
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('no_hp', 'like', "%{$search}%")
                      ->orWhere('id_pelanggan', 'like', "%{$search}%");
                });
            }
            if ($request->filled('status'))    $query->where('status', $request->status);
            if ($request->filled('paket_id'))  $query->where('paket_id', $request->paket_id);
            if ($request->filled('router_id')) $query->where('router_id', $request->router_id);
        }

        $pelanggans = $query->get();
        $headers    = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pelanggan_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function() use ($pelanggans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'username','password','nama','no_hp','email',
                'wilayah','alamat','latitude','longitude','maps',
                'jenis_layanan','ip_address','nama_paket','nama_router','tgl_expired'
            ]);
            foreach ($pelanggans as $p) {
                fputcsv($file, [
                    $p->username,
                    $p->password_pppoe ?? '',
                    $p->nama,
                    $p->no_hp    ?? '',
                    $p->email    ?? '',
                    $p->wilayah  ?? '',
                    $p->alamat   ?? '',
                    $p->latitude ?? '',
                    $p->longitude ?? '',
                    $p->maps     ?? '',
                    $p->jenis_layanan ?? 'pppoe',
                    $p->ip_address    ?? '',
                    $p->paket->nama_paket ?? '',
                    $p->router->nama      ?? '',
                    $p->tgl_expired?->format('Y-m-d') ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit(Pelanggan $pelanggan)
    {
        $pakets  = Paket::where("is_active", true)->where("router_id", $pelanggan->router_id)->get();
        $routers = \App\Models\Router::where("is_active", true)->get();
        return view("admin.pelanggan.edit", compact("pelanggan", "pakets", "routers"));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            "nama"     => "required|string|max:255",
            "username" => "required|unique:pelanggan,username," . $pelanggan->id,
            "no_hp"    => "required",
            "paket_id" => "required|exists:paket,id",
        ]);

        $data = $request->except(["password", "_token", "_method"]);

        if ($request->filled("password")) {
            $data["password"]       = Hash::make($request->password);
            $data["password_pppoe"] = $request->password;
        }

        $pelanggan->update($data);

        try {
            $router = $pelanggan->fresh()->router;
            $paket  = $pelanggan->fresh()->paket;
            if ($router && $paket) {
                $mikrotik = new MikrotikService();
                $this->connectRouter($router, $mikrotik);
                $password = $request->filled('password')
                    ? $request->password
                    : $pelanggan->password_pppoe;
                $mikrotik->addPppoeUser($pelanggan->username, $password, $paket->nama_paket);
                $mikrotik->disconnect();
            }
        } catch (\Exception $e) {
            return redirect('/admin/pelanggan?router_id=' . $pelanggan->router_id)
                ->with('warning', 'Data diupdate, tapi gagal sync Mikrotik: ' . $e->getMessage());
        }

        return redirect('/admin/pelanggan?router_id=' . $pelanggan->router_id)
            ->with('success', 'Data pelanggan berhasil diupdate dan disync ke Mikrotik!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;
            if ($router) {
                $mikrotik = new MikrotikService();
                // FIX 2: Pakai connectRouter dengan WireGuard + retry
                $this->connectRouter($router, $mikrotik);
                $mikrotik->deletePppoeUser($pelanggan->username);
                $mikrotik->disconnect();
            }
        } catch (\Exception $e) {
            $pelanggan->forceDelete();
            return redirect('/admin/pelanggan')
                ->with('error', 'Pelanggan dihapus dari database, tapi gagal hapus dari Mikrotik: ' . $e->getMessage());
        }

        $pelanggan->forceDelete();
        return redirect('/admin/pelanggan')
            ->with('success', 'Pelanggan dan secret Mikrotik berhasil dihapus!');
    }

    public function ubahStatus(Request $request, Pelanggan $pelanggan)
    {
        $pelanggan->update(["status" => $request->status]);
        return redirect()->back()->with('success', 'Status pelanggan berhasil diubah!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => false, 'message' => 'Tidak ada data dipilih.']);
        }

        $pelanggans = Pelanggan::whereIn('id', $ids)->get();
        $deleted    = 0;
        $errors     = [];

        // FIX 2: Group per router, buka koneksi sekali per router
        $grouped = $pelanggans->groupBy('router_id');

        foreach ($grouped as $routerId => $group) {
            $router = $group->first()->router;
            if (!$router) {
                // Tidak ada router, hapus DB saja
                foreach ($group as $p) {
                    Pelanggan::withTrashed()->where('id', $p->id)->forceDelete();
                    $deleted++;
                }
                continue;
            }

            $mikrotik  = new MikrotikService();
            $connected = false;

            try {
                // FIX 2: Satu koneksi untuk semua pelanggan di router yang sama
                $this->connectRouter($router, $mikrotik);
                $connected = true;
            } catch (\Exception $e) {
                $errors[] = "Router [{$router->nama}]: " . $e->getMessage();
                Log::error("bulkDelete - gagal konek router [{$router->nama}]: " . $e->getMessage());
            }

            foreach ($group as $p) {
                if ($connected) {
                    try {
                        $mikrotik->deletePppoeUser($p->username);
                    } catch (\Exception $e) {
                        $errors[] = $p->username . ': ' . $e->getMessage();
                    }
                }
                Pelanggan::withTrashed()->where('id', $p->id)->forceDelete();
                $deleted++;
            }

            if ($connected) {
                try { $mikrotik->disconnect(); } catch (\Exception $e) {}
            }
        }

        $msg = $deleted . ' pelanggan berhasil dihapus.';
        if (count($errors)) $msg .= ' Catatan: ' . implode(', ', $errors);

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function peta()
    {
        $pelanggans = \App\Models\Pelanggan::with(['paket', 'router'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->get();

        $onuByPelanggan = \App\Models\Onu::with('odp')
            ->whereNotNull('pelanggan_id')
            ->get()
            ->groupBy('pelanggan_id');

        $total     = \App\Models\Pelanggan::count();
        $totalPeta = $pelanggans->count();
        $tanpaPeta = $total - $totalPeta;

        $mapData = $pelanggans->map(function($p) use ($onuByPelanggan) {
            $onus    = $onuByPelanggan->get($p->id, collect());
            $onuInfo = $onus->map(function($o) {
                return [
                    'onu_id' => $o->onu_id,
                    'name'   => $o->name ?? $o->onu_id,
                    'status' => $o->status,
                    'odp'    => $o->odp ? $o->odp->name : null,
                ];
            })->values()->toArray();

            return [
                'id'       => $p->id,
                'nama'     => $p->nama,
                'username' => $p->username,
                'status'   => $p->status,
                'paket'    => optional($p->paket)->nama_paket ?? '-',
                'router'   => optional($p->router)->nama ?? '-',
                'expired'  => $p->tgl_expired ? $p->tgl_expired->format('d/m/Y') : '-',
                'lat'      => (float) $p->latitude,
                'lng'      => (float) $p->longitude,
                'maps'     => $p->maps ?? '',
                'url'      => '/admin/pelanggan/' . $p->id,
                'onus'     => $onuInfo,
            ];
        })->values()->toArray();

        $mapDataJson = json_encode($mapData);

        return view('admin.pelanggan.peta', compact('total', 'totalPeta', 'tanpaPeta', 'mapDataJson'));
    }
}
