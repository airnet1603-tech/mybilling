<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Paket;
use App\Models\Router;
use App\Models\User;
use Illuminate\Http\Request;
class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with('pelanggan', 'tagihan')->latest();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('pelanggan', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->search.'%')
                       ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%');
                })->orWhere('no_pembayaran', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->metode) $query->where('metode', $request->metode);
        if ($request->router_id) {
            $query->whereHas('pelanggan', function($q) use ($request) {
                $q->where('router_id', $request->router_id);
            });
        }
        if ($request->paket_id) {
            $query->whereHas('pelanggan', function($q) use ($request) {
                $q->where('paket_id', $request->paket_id);
            });
        }
        if ($request->user_id) {
            $query->where('created_by', $request->user_id);
        }
        if ($request->bulan) {
            $query->whereMonth('created_at', date('m', strtotime($request->bulan)))
                  ->whereYear('created_at', date('Y', strtotime($request->bulan)));
        }

        $pembayarans = $query->paginate(15);

        // Counter ikut filter
        $cq = Pembayaran::query();
        if ($request->search) {
            $cq->where(function($q) use ($request) {
                $q->whereHas('pelanggan', function($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->search.'%')
                       ->orWhere('id_pelanggan', 'like', '%'.$request->search.'%');
                })->orWhere('no_pembayaran', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->metode) $cq->where('metode', $request->metode);
        if ($request->router_id) {
            $cq->whereHas('pelanggan', function($q) use ($request) {
                $q->where('router_id', $request->router_id);
            });
        }
        if ($request->paket_id) {
            $cq->whereHas('pelanggan', function($q) use ($request) {
                $q->where('paket_id', $request->paket_id);
            });
        }
        if ($request->user_id) {
            $cq->where('created_by', $request->user_id);
        }
        if ($request->bulan) {
            $cq->whereMonth('created_at', date('m', strtotime($request->bulan)))
               ->whereYear('created_at', date('Y', strtotime($request->bulan)));
        }

        $totalBulanIni  = (clone $cq)->sum('jumlah_bayar');
        $totalTransaksi = (clone $cq)->count();
        $totalCash      = (clone $cq)->where('metode', 'cash')->sum('jumlah_bayar');
        $totalTransfer  = (clone $cq)->where('metode', 'transfer')->sum('jumlah_bayar');

        $routers = Router::orderBy('nama')->get();
        $pakets  = Paket::where('is_active', true)->orderBy('nama_paket')->get();
        $paketsByRouter = Paket::where('is_active', true)->orderBy('nama_paket')
            ->get()->groupBy('router_id')->toJson();
        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        if (request()->ajax()) {
            return response()->json([
                'html'           => view('admin.pembayaran._table', compact('pembayarans'))->render(),
                'totalBulanIni'  => number_format($totalBulanIni, 0, ',', '.'),
                'totalTransaksi' => $totalTransaksi,
                'totalCash'      => number_format($totalCash, 0, ',', '.'),
                'totalTransfer'  => number_format($totalTransfer, 0, ',', '.'),
            ]);
        }

        return view('admin.pembayaran.index', compact(
            'pembayarans', 'totalBulanIni', 'totalTransaksi', 'totalCash', 'totalTransfer',
            'routers', 'pakets', 'paketsByRouter', 'users'
        ));
    }
}
