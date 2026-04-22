<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Router;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CsvImportController extends Controller
{
    protected $mikrotik;

    public function __construct()
    {
        $this->mikrotik = new MikrotikService();
    }

    // ===================== PELANGGAN =====================
    public function previewPelanggan(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);

        $rows   = $this->parseCsv($request->file('file'));
        $pakets  = Paket::all();
        $routers = Router::all();

        // Baca header dari baris pertama yang tidak kosong
        $headers = [];
        $dataStart = 0;
        foreach ($rows as $i => $row) {
            $row = array_map('trim', $row);
            if (count($row) < 2) continue;
            $headers = array_map('strtolower', $row);
            $dataStart = $i + 1;
            break;
        }
        // Helper ambil nilai berdasarkan nama kolom
        $col = function($row, $name, $default = '') use ($headers) {
            $idx = array_search($name, $headers);
            return $idx !== false ? (trim($row[$idx] ?? '') ?: $default) : $default;
        };

        $preview = [];
        foreach ($rows as $i => $row) {
            if ($i < $dataStart) continue;
            $row = array_map('trim', $row);
            if (count($row) < 2) continue;

            $username   = $col($row, 'username');
            $password   = $col($row, 'password', $username);
            $nama       = $col($row, 'nama', $username);
            $no_hp      = $col($row, 'no_hp');
            $email      = $col($row, 'email');
            $wilayah    = $col($row, 'wilayah');
            $alamat     = $col($row, 'alamat');
            $latitude   = $col($row, 'latitude');
            $longitude  = $col($row, 'longitude');
            $maps       = $col($row, 'maps');
            $jenis      = $col($row, 'jenis_layanan', 'pppoe');
            $ip_address = $col($row, 'ip_address');
            $paketNama  = $col($row, 'nama_paket');
            $routerNama = $col($row, 'nama_router');
            $tglExpired = $col($row, 'tgl_expired');

            $paket  = $pakets->first(fn($p) => strtolower(trim($p->nama_paket)) === strtolower(trim($paketNama)));
            $router = $routers->first(fn($r) => strtolower(trim($r->nama)) === strtolower(trim($routerNama)));
            $exists = Pelanggan::where('username', $username)->exists();

            $preview[] = [
                'username'   => $username,
                'password'   => $password,
                'nama'       => $nama,
                'no_hp'      => $no_hp,
                'email'      => $email,
                'alamat'     => $alamat,
                'wilayah'    => $wilayah,
                'latitude'   => $latitude,
                'longitude'  => $longitude,
                'maps'       => $maps,
                'jenis'      => $jenis,
                'ip_address' => $ip_address,
                'paket_nama' => $paketNama,
                'paket_id'   => $paket?->id,
                'router_nama'=> $routerNama,
                'router_id'  => $router?->id,
                'tgl_expired'=> $tglExpired,
                'exists'     => $exists,
                'error'      => !$username ? 'Username kosong' : (!$paket ? 'Paket tidak ditemukan' : (!$router ? 'Router tidak ditemukan' : null)),
            ];
        }

        return response()->json([
            'status'  => true,
            'preview' => $preview,
            'pakets'  => $pakets->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama_paket]),
            'routers' => $routers->map(fn($r) => ['id' => $r->id, 'nama' => $r->nama]),
        ]);
    }

    public function importPelanggan(Request $request)
    {
        $items    = $request->input('items', []);
        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($items as $item) {
            try {
                $username = trim($item['username'] ?? '');
                if (!$username) continue;

                // Skip jika sudah ada
                if (Pelanggan::where('username', $username)->exists()) {
                    $skipped++;
                    continue;
                }

                $paketId  = $item['paket_id']  ?? null;
                $routerId = $item['router_id'] ?? null;
                if (!$paketId || !$routerId) {
                    $errors[] = "$username: paket/router belum dipilih";
                    $skipped++;
                    continue;
                }

                $router = Router::find($routerId);
                $paket  = Paket::find($paketId);

                DB::transaction(function() use ($username, $item, $paketId, $routerId, $router, $paket, &$imported) {
$tahun = date('Y');
$last = Pelanggan::whereYear('created_at', $tahun)
            ->withTrashed()
            ->orderByDesc('id_pelanggan')
            ->value('id_pelanggan');
$urutan = $last ? (int) substr($last, -4) + 1 : 1;
$idPelanggan = 'AR-' . $tahun . str_pad($urutan, 4, '0', STR_PAD_LEFT);
while (Pelanggan::withTrashed()->where('id_pelanggan', $idPelanggan)->exists()) {
    $urutan++;
    $idPelanggan = 'AR-' . $tahun . str_pad($urutan, 4, '0', STR_PAD_LEFT);
}
                    $tglExpired = !empty($item['tgl_expired'])
                        ? date('Y-m-d', strtotime($item['tgl_expired']))
                        : now()->addMonths(1)->toDateString();

                    Pelanggan::create([
                        'id_pelanggan'   => $idPelanggan,
                        'nama'           => $item['nama'] ?: $username,
                        'username'       => $username,
                        'password'       => bcrypt($item['password'] ?? $username),
                        'password_pppoe' => $item['password'] ?? $username,
                        'no_hp'          => $item['no_hp'] ?? null,
                        'alamat'         => $item['alamat'] ?? null,
                        'wilayah'        => $item['wilayah'] ?? null,
                        'email'          => $item['email'] ?? null,
                        'latitude'       => $item['latitude'] ?? null,
                        'longitude'      => $item['longitude'] ?? null,
                        'maps'           => $item['maps'] ?? null,
                        'ip_address'     => $item['ip_address'] ?? null,
                        'paket_id'       => $paketId,
                        'router_id'      => $routerId,
                        'router_name'    => $router->nama,
                        'tgl_daftar'     => now()->toDateString(),
                        'tgl_expired'    => $tglExpired,
                        'status'         => 'aktif',

                    ]);

                    // Auto create PPPoE di Mikrotik
                    $this->mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
                    $this->mikrotik->addPppoeUser($username, $item['password'] ?? $username, $paket->nama_paket);
                    $this->mikrotik->disconnect();

                    $imported++;
                });

            } catch (\Exception $e) {
                $errors[] = ($item['username'] ?? '?') . ': ' . $e->getMessage();
            }
        }

        return response()->json([
            'status'   => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'message'  => "Berhasil import $imported pelanggan, $skipped dilewati."
                        . (count($errors) ? ' Error: ' . implode('; ', $errors) : ''),
        ]);
    }

    // ===================== PAKET =====================
    public function importPaket(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $rows = $this->parseCsv($request->file('file'));
        $imported = 0; $skipped = 0; $errors = [];

        foreach ($rows as $i => $row) {
            if ($i === 0) continue;
            $row = array_map('trim', $row);
            $nama  = $row[0] ?? '';
            $harga = preg_replace('/[^0-9]/', '', $row[1] ?? '0');
            $dl    = preg_replace('/[^0-9]/', '', $row[2] ?? '10');
            $ul    = preg_replace('/[^0-9]/', '', $row[3] ?? '10');
            $desk  = $row[4] ?? '';

            if (!$nama) continue;
            if (Paket::where('nama_paket', $nama)->exists()) { $skipped++; continue; }

            try {
                Paket::create([
                    'nama_paket'           => $nama,
                    'harga'                => $harga,
                    'kecepatan_download'   => $dl,
                    'kecepatan_upload'     => $ul,
                    'deskripsi'            => $desk,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "$nama: " . $e->getMessage();
            }
        }

        return response()->json([
            'status'   => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'message'  => "Berhasil import $imported paket, $skipped dilewati."
                        . (count($errors) ? ' Error: ' . implode('; ', $errors) : ''),
        ]);
    }

    // ===================== HELPER =====================
    private function parseCsv($file)
    {
        $rows = [];
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }

    // ===================== DOWNLOAD TEMPLATE =====================
    public function template($type)
    {
        $templates = [
            'pelanggan' => [
                'header' => ['username','password','nama','no_hp','email','wilayah','alamat','latitude','longitude','maps','jenis_layanan','ip_address','nama_paket','nama_router','tgl_expired'],
                'contoh' => ['pelanggan1','pass123','Budi Santoso','081234567890','Jl. Merdeka No.1','Demuk','110k','Router Utama','2026-04-14'],
            ],
            'paket' => [
                'header' => ['nama_paket','harga','kecepatan_download_mbps','kecepatan_upload_mbps','deskripsi'],
                'contoh' => ['110k','110000','10','10','Paket 10Mbps'],
            ],
        ];

        if (!isset($templates[$type])) abort(404);

        $filename = "template_import_{$type}.csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename={$filename}"];

        $callback = function() use ($templates, $type) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $templates[$type]['header']);
            fputcsv($out, $templates[$type]['contoh']);
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
