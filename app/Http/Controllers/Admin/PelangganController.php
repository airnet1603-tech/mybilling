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

        // FIX: filter by router
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

        Pelanggan::create([
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
            "router_name"    => $request->router_name,
            "ip_address"     => $request->ip_address,
            "tgl_daftar"     => now(),
            "tgl_expired"    => now()->addDays(30),
            "status"         => "aktif",
            "jenis_layanan"  => $request->jenis_layanan ?? "pppoe",
            "router_id"      => $request->router_id,
        ]);

        return redirect('/admin/pelanggan')->with('success', 'Pelanggan berhasil ditambahkan!');
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

        // FIX: filter by router saat export
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
            fputcsv($file, ['ID Pelanggan', 'Nama', 'Username', 'No HP', 'Email', 'Router', 'Paket', 'Kecepatan', 'Status', 'Tgl Daftar', 'Expired']);
            foreach ($pelanggans as $p) {
                fputcsv($file, [
                    $p->id_pelanggan,
                    $p->nama,
                    $p->username,
                    $p->no_hp,
                    $p->email ?? '-',
                    $p->router->nama ?? '-',
                    $p->paket->nama_paket ?? '-',
                    ($p->paket->kecepatan_download ?? 0) . ' Mbps',
                    $p->status,
                    $p->tgl_daftar?->format('d/m/Y') ?? '-',
                    $p->tgl_expired?->format('d/m/Y') ?? '-',
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
}