<?php

namespace App\Http\Controllers\Admin;

use App\Services\MikrotikService;
use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelangganController extends Controller
{
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

        if ($request->filled("status")) {
            $query->where("status", $request->status);
        }

        if ($request->filled("paket_id")) {
            $query->where("paket_id", $request->paket_id);
        }

        if ($request->filled("router_id")) {
            $query->where("router_id", $request->router_id);
        }

        $pelanggans = $query->paginate(10)->withQueryString();
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
            "username" => "required|unique:pelanggan,username",
            "password" => "required|min:6",
            "no_hp"    => "required",
            "paket_id" => "required|exists:paket,id",
        ]);

        $tahun = now()->format("Y");

        $last = Pelanggan::whereYear("created_at", $tahun)
                    ->withTrashed()
                    ->orderByDesc("id_pelanggan")
                    ->value("id_pelanggan");

        $urutan = $last ? (int) substr($last, -4) + 1 : 1;

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
            "tgl_expired"    => now()->addDays(30),
            "status"         => "aktif",
            "jenis_layanan"  => $request->jenis_layanan ?? "pppoe",
            "router_id"      => $request->router_id,
        ]);

        // Auto sync ke Mikrotik
        try {
            $router = $pelanggan->router;
            $paket  = $pelanggan->paket;
            if ($router && $paket) {
                $mikrotik = new MikrotikService();
                $mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
                $mikrotik->addPppoeUser($pelanggan->username, $request->password, $paket->nama_paket);
                $mikrotik->disconnect();
            }
        } catch (\Exception $e) {
            return redirect('/admin/pelanggan')->with('warning', 'Pelanggan berhasil ditambahkan, tapi gagal sync Mikrotik: ' . $e->getMessage());
        }

        return redirect('/admin/pelanggan')->with('success', 'Pelanggan berhasil ditambahkan dan disync ke Mikrotik!');
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load("paket", "tagihan", "router");
        return view("admin.pelanggan.show", compact("pelanggan"));
    }

    public function export(Request $request)
    {
        $query = Pelanggan::with('paket', 'router');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%")
                  ->orWhere('id_pelanggan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

        if ($request->filled('router_id')) {
            $query->where('router_id', $request->router_id);
        }

        $pelanggans = $query->get();

        $headers = [
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
                    $p->no_hp ?? '',
                    $p->email ?? '',
                    $p->wilayah ?? '',
                    $p->alamat ?? '',
                    $p->latitude ?? '',
                    $p->longitude ?? '',
                    $p->maps ?? '',
                    $p->jenis_layanan ?? 'pppoe',
                    $p->ip_address ?? '',
                    $p->paket->nama_paket ?? '',
                    $p->router->nama ?? '',
                    $p->tgl_expired?->format('Y-m-d') ?? '',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function edit(Pelanggan $pelanggan)
    {
        $pakets  = Paket::where("is_active", true)->get();
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

        return redirect('/admin/pelanggan/' . $pelanggan->id)->with('success', 'Data pelanggan berhasil diupdate!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        try {
            $router = $pelanggan->router;

            if ($router) {
                $mikrotik = new MikrotikService();
                $mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
                $mikrotik->deletePppoeUser($pelanggan->username);
                $mikrotik->disconnect();
            }

        } catch (\Exception $e) {
            $pelanggan->delete();
            return redirect('/admin/pelanggan')->with('error', 'Pelanggan dihapus dari database, tapi gagal hapus dari Mikrotik: ' . $e->getMessage());
        }

        $pelanggan->delete();
        return redirect('/admin/pelanggan')->with('success', 'Pelanggan dan secret Mikrotik berhasil dihapus!');
    }

    public function ubahStatus(Request $request, Pelanggan $pelanggan)
    {
        $pelanggan->update(["status" => $request->status]);
        return redirect()->back()->with('success', 'Status pelanggan berhasil diubah!');
    }

    public function bulkDelete(\Illuminate\Http\Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => false, 'message' => 'Tidak ada data dipilih.']);
        }

        $pelanggans = \App\Models\Pelanggan::whereIn('id', $ids)->get();
        $mikrotik   = new \App\Services\MikrotikService();
        $deleted    = 0;
        $errors     = [];

        foreach ($pelanggans as $p) {
            try {
                if ($p->router) {
                    $mikrotik->connect($p->router->ip_address, $p->router->username, $p->router->password, $p->router->port);
                    $mikrotik->deletePppoeUser($p->username);
                    $mikrotik->disconnect();
                }
            } catch (\Exception $e) {
                $errors[] = $p->username . ': ' . $e->getMessage();
            }

            \App\Models\Pelanggan::withTrashed()->where('id', $p->id)->forceDelete();
            $deleted++;
        }

        $msg = $deleted . ' pelanggan berhasil dihapus.';
        if (count($errors)) $msg .= ' Gagal hapus dari Mikrotik: ' . implode(', ', $errors);

        return response()->json(['status' => true, 'message' => $msg]);
    }

    public function peta()
    {
        // Ambil pelanggan yang punya koordinat (latitude & longitude)
        $pelanggans = \App\Models\Pelanggan::with(['paket', 'router'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', '')
            ->where('longitude', '!=', '')
            ->get();

        $total     = \App\Models\Pelanggan::count();
        $totalPeta = $pelanggans->count();
        $tanpaPeta = $total - $totalPeta;

        $mapData = $pelanggans->map(function($p) {
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
            ];
        })->values()->toArray();

        $mapDataJson = json_encode($mapData);

        return view('admin.pelanggan.peta', compact('total', 'totalPeta', 'tanpaPeta', 'mapDataJson'));
    }
}