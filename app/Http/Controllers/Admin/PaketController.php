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

    protected function syncKeRouter($router, $nama, $download, $upload, $burst = [])
    {
        $mikrotik = new MikrotikService();
        try {
            $this->connectRouter($router, $mikrotik);
            $mikrotik->syncProfile($nama, $download, $upload, $router, $burst);
            $mikrotik->disconnect();
        } catch (\Exception $e) {
            \Log::warning("Gagal sync profile ke router {$router->nama}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function deleteFromRouter($router, $nama)
    {
        $mikrotik = new MikrotikService();
        try {
            $this->connectRouter($router, $mikrotik);
            $mikrotik->deleteProfile($nama);
            $mikrotik->disconnect();
        } catch (\Exception $e) {
            \Log::warning("Gagal hapus profile dari router {$router->nama}: " . $e->getMessage());
        }
    }

    public function byRouter(Request $request)
    {
        $pakets = Paket::where('is_active', true)
                       ->where('router_id', $request->router_id)
                       ->get(['id','nama_paket','harga','kecepatan_download','kecepatan_upload']);
        return response()->json($pakets);
    }

    public function index(Request $request)
    {
        $routers      = Router::where('is_active', true)->get();
        $router_id    = $request->get('router_id', session('paket_router_id'));

        if ($router_id) {
            session(['paket_router_id' => $router_id]);
        }

        $selectedRouter = $router_id ? Router::find($router_id) : null;

        $pakets = Paket::when($router_id, fn($q) => $q->where('router_id', $router_id))
                       ->when(!$router_id, fn($q) => $q->whereNull('router_id'))
                       ->latest()
                       ->paginate(12);

        return view('admin.paket.index', compact('pakets', 'routers', 'selectedRouter', 'router_id'));
    }

    public function create(Request $request)
    {
        $routers   = Router::where('is_active', true)->get();
        $router_id = $request->get('router_id', session('paket_router_id'));
        return view('admin.paket.create', compact('routers', 'router_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'router_id'          => 'required|exists:routers,id',
            'nama_paket'         => 'required|string|max:255',
            'harga'              => 'required|integer|min:0',
            'kecepatan_download' => 'required|integer|min:1',
            'kecepatan_upload'   => 'required|integer|min:1',
            'radius_profile'     => 'required|string|max:100',
            'jenis'              => 'required|in:pppoe,hotspot',
            'masa_aktif'         => 'required|integer|min:1',
        ]);

        $paket = Paket::create([
            'router_id'                => $request->router_id,
            'nama_paket'               => $request->nama_paket,
            'harga'                    => $request->harga,
            'kecepatan_download'       => $request->kecepatan_download,
            'kecepatan_upload'         => $request->kecepatan_upload,
            'radius_profile'           => $request->radius_profile,
            'jenis'                    => $request->jenis,
            'masa_aktif'               => $request->masa_aktif,
            'deskripsi'                => $request->deskripsi,
            'is_active'                => $request->has('is_active'),
            'burst_limit_download'     => $request->burst_limit_download     ?? 0,
            'burst_limit_upload'       => $request->burst_limit_upload       ?? 0,
            'burst_threshold_download' => $request->burst_threshold_download ?? 0,
            'burst_threshold_upload'   => $request->burst_threshold_upload   ?? 0,
            'burst_time'               => $request->burst_time               ?? 8,
        ]);

        if ($request->jenis === 'pppoe') {
            $router = Router::find($request->router_id);
            try {
                $this->syncKeRouter($router, $paket->nama_paket, $paket->kecepatan_download, $paket->kecepatan_upload, [
                    'burst_limit_download'     => $paket->burst_limit_download,
                    'burst_limit_upload'       => $paket->burst_limit_upload,
                    'burst_threshold_download' => $paket->burst_threshold_download,
                    'burst_threshold_upload'   => $paket->burst_threshold_upload,
                    'burst_time'               => $paket->burst_time,
                ]);
                $msg = 'Paket berhasil ditambahkan dan disync ke Mikrotik!';
            } catch (\Exception $e) {
                $msg = 'Paket tersimpan, tapi gagal sync ke Mikrotik: ' . $e->getMessage();
            }
        } else {
            $msg = 'Paket berhasil ditambahkan!';
        }

        return redirect('/admin/paket?router_id=' . $request->router_id)->with('success', $msg);
    }

    public function edit(Paket $paket)
    {
        $routers = Router::where('is_active', true)->get();
        return view('admin.paket.edit', compact('paket', 'routers'));
    }

    public function update(Request $request, Paket $paket)
    {
        $request->validate([
            'router_id'          => 'required|exists:routers,id',
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
            'router_id'                => $request->router_id,
            'nama_paket'               => $request->nama_paket,
            'harga'                    => $request->harga,
            'kecepatan_download'       => $request->kecepatan_download,
            'kecepatan_upload'         => $request->kecepatan_upload,
            'radius_profile'           => $request->radius_profile,
            'jenis'                    => $request->jenis,
            'masa_aktif'               => $request->masa_aktif,
            'deskripsi'                => $request->deskripsi,
            'is_active'                => $request->has('is_active'),
            'burst_limit_download'     => $request->burst_limit_download     ?? 0,
            'burst_limit_upload'       => $request->burst_limit_upload       ?? 0,
            'burst_threshold_download' => $request->burst_threshold_download ?? 0,
            'burst_threshold_upload'   => $request->burst_threshold_upload   ?? 0,
            'burst_time'               => $request->burst_time               ?? 8,
        ]);

        if ($request->jenis === 'pppoe') {
            $router = Router::find($request->router_id);
            try {
                if ($nameLama !== $request->nama_paket) {
                    $this->deleteFromRouter($router, $nameLama);
                }
                $this->syncKeRouter($router, $paket->nama_paket, $paket->kecepatan_download, $paket->kecepatan_upload, [
                    'burst_limit_download'     => $paket->burst_limit_download,
                    'burst_limit_upload'       => $paket->burst_limit_upload,
                    'burst_threshold_download' => $paket->burst_threshold_download,
                    'burst_threshold_upload'   => $paket->burst_threshold_upload,
                    'burst_time'               => $paket->burst_time,
                ]);
                $msg = 'Paket berhasil diupdate dan disync ke Mikrotik!';
            } catch (\Exception $e) {
                $msg = 'Paket tersimpan, tapi gagal sync ke Mikrotik: ' . $e->getMessage();
            }
        } else {
            $msg = 'Paket berhasil diupdate!';
        }

        return redirect('/admin/paket?router_id=' . $request->router_id)->with('success', $msg);
    }

    public function destroy(Paket $paket)
    {
        if ($paket->pelanggan()->count() > 0) {
            return back()->with('error', 'Paket tidak bisa dihapus karena masih dipakai pelanggan aktif!');
        }

        $nama      = $paket->nama_paket;
        $jenis     = $paket->jenis;
        $router    = $paket->router;
        $router_id = $paket->router_id;

        $paket->delete();

        if ($jenis === 'pppoe' && $router) {
            $this->deleteFromRouter($router, $nama);
        }

        return redirect('/admin/paket?router_id=' . $router_id)->with('success', 'Paket berhasil dihapus!');
    }
}
