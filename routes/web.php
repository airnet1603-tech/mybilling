<?php
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Pelanggan\PortalController;
use App\Http\Controllers\Pelanggan\DuitkuPaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () { return redirect('/login'); });
Auth::routes(['register' => false]);
Route::get('/home', function () { return redirect('/admin/dashboard'); })->middleware('auth');

// Portal Pelanggan
Route::prefix('pelanggan')->group(function () {
    Route::get('/', function () { return redirect('/pelanggan/login'); });
    Route::get('/login',  [PortalController::class, 'showLogin']);
    Route::post('/login', [PortalController::class, 'login']);
    Route::get('/logout', [PortalController::class, 'logout']);
    Route::middleware('pelanggan.auth')->group(function () {
        Route::get('/dashboard',        [PortalController::class, 'dashboard']);
        Route::get('/tagihan',          [PortalController::class, 'tagihan']);
        Route::get('/profil',           [PortalController::class, 'profil']);
        Route::post('/profil/password', [PortalController::class, 'updatePassword']);
    });
});

// Admin Panel
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', function () { return redirect('/admin/dashboard'); });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('pelanggan/export', [PelangganController::class, 'export'])->name('pelanggan.export');
    Route::get('pelanggan/peta', [PelangganController::class, 'peta'])->name('pelanggan.peta');
    Route::resource('pelanggan', PelangganController::class);
    Route::post('pelanggan/bulk-delete', [PelangganController::class, 'bulkDelete'])->name('pelanggan.bulk-delete');
    Route::post('pelanggan/{pelanggan}/status', [PelangganController::class, 'ubahStatus'])->name('pelanggan.status');
    Route::resource('tagihan', TagihanController::class);
    Route::post('tagihan/generate', [TagihanController::class, 'generateMassal'])->name('tagihan.generate');
    Route::post('tagihan/bayar-massal', [TagihanController::class, 'bayarMassal'])->name('tagihan.bayar-massal');
    Route::post('tagihan/{tagihan}/bayar', [TagihanController::class, 'konfirmasiBayar'])->name('tagihan.bayar');
    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');

    // Akses Admin & Operator
    Route::middleware('role:admin,operator')->group(function () {
        Route::resource('paket', PaketController::class);
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::middleware('role:admin')->group(function () {
            Route::delete('laporan/rollback-unpaid', [LaporanController::class, 'rollbackUnpaid'])->name('laporan.rollback-unpaid');
            Route::delete('laporan/clear-pelanggan', [LaporanController::class, 'clearPelanggan'])->name('laporan.clear-pelanggan');
            Route::delete('laporan/clear-bulan',  [LaporanController::class, 'clearBulan'])->name('laporan.clear-bulan');
            Route::delete('laporan/clear-tahun',  [LaporanController::class, 'clearTahun'])->name('laporan.clear-tahun');
            Route::delete('laporan/clear-user',   [LaporanController::class, 'clearUser'])->name('laporan.clear-user');
        });
    });

    // Akses Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::get('setting',  [SettingController::class, 'index'])->name('setting.index');
        Route::put('setting',  [SettingController::class, 'update'])->name('setting.update');
            Route::get('setting/payment-gateway', [App\Http\Controllers\Admin\PaymentGatewayController::class, 'index'])->name('setting.payment-gateway.index');
            Route::put('setting/payment-gateway/{gateway}', [App\Http\Controllers\Admin\PaymentGatewayController::class, 'update'])->name('setting.payment-gateway.update');
        Route::resource('users', UserController::class)->names('users');
    });
});

// Mikrotik (Admin & Operator)
Route::prefix('admin/mikrotik')->middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/',                    [App\Http\Controllers\Admin\MikrotikController::class, 'index'])->name('mikrotik.index');
    Route::post('/',                   [App\Http\Controllers\Admin\MikrotikController::class, 'store'])->name('mikrotik.store');
    Route::delete('/{router}',         [App\Http\Controllers\Admin\MikrotikController::class, 'destroy'])->name('mikrotik.destroy');
    Route::patch('/{router}/pppoe-setting',  [App\Http\Controllers\Admin\MikrotikController::class, 'updatePppoeSetting'])->name('mikrotik.pppoe-setting');
    Route::get('/{router}/import-setting',   [App\Http\Controllers\Admin\MikrotikController::class, 'importSetting'])->name('mikrotik.import-setting');
    Route::get('/{router}/test',       [App\Http\Controllers\Admin\MikrotikController::class, 'testConnection'])->name('mikrotik.test');
    Route::get('/monitoring',          [App\Http\Controllers\Admin\MikrotikController::class, 'monitoring'])->name('mikrotik.monitoring');
    Route::get('/{router}/stats',      [App\Http\Controllers\Admin\MikrotikController::class, 'getStats'])->name('mikrotik.stats');
    Route::get('/{router}/sessions',   [App\Http\Controllers\Admin\MikrotikController::class, 'getSessions'])->name('mikrotik.sessions');
    Route::post('/pelanggan/{pelanggan}/isolir',   [App\Http\Controllers\Admin\MikrotikController::class, 'isolir'])->name('mikrotik.isolir');
    Route::post('/pelanggan/{pelanggan}/aktifkan', [App\Http\Controllers\Admin\MikrotikController::class, 'aktifkan'])->name('mikrotik.aktifkan');
    Route::post('/pelanggan/{pelanggan}/suspend',  [App\Http\Controllers\Admin\MikrotikController::class, 'suspend'])->name('mikrotik.suspend');
    Route::post('/pelanggan/{pelanggan}/nonaktif', [App\Http\Controllers\Admin\MikrotikController::class, 'nonaktif'])->name('mikrotik.nonaktif');
    Route::get('/{router}/pppoe-list',     [App\Http\Controllers\Admin\MikrotikController::class, 'previewImportPppoe'])->name('mikrotik.pppoe-list');
    Route::post('/{router}/import-pppoe',  [App\Http\Controllers\Admin\MikrotikController::class, 'doImportPppoe'])->name('mikrotik.import-pppoe');
    Route::post('/pelanggan/{pelanggan}/sync',     [App\Http\Controllers\Admin\MikrotikController::class, 'syncPelanggan'])->name('mikrotik.sync');
});

// Duitku Payment (Portal Pelanggan)
Route::prefix('pelanggan')->middleware('pelanggan.auth')->group(function () {
    Route::get('/payment/{noTagihan}',        [DuitkuPaymentController::class, 'show'])->name('pelanggan.payment.show');
    Route::post('/payment/{noTagihan}/qris',  [DuitkuPaymentController::class, 'createQris'])->name('pelanggan.payment.qris');
    Route::post('/payment/{noTagihan}/va',    [DuitkuPaymentController::class, 'createVa'])->name('pelanggan.payment.va');
    Route::get('/payment/{noTagihan}/check',  [DuitkuPaymentController::class, 'checkStatus'])->name('pelanggan.payment.check');
});

// Duitku Webhook (tanpa auth)
Route::post('/webhook/duitku', [DuitkuPaymentController::class, 'webhook'])->name('webhook.duitku');

// Midtrans Payment (Portal Pelanggan)
use App\Http\Controllers\Pelanggan\MidtransPaymentController;

Route::prefix('pelanggan')->middleware('pelanggan.auth')->group(function () {
    Route::post('/payment/{noTagihan}/midtrans', [MidtransPaymentController::class, 'create'])->name('pelanggan.payment.midtrans');
    Route::get('/payment/{noTagihan}/check-midtrans', [MidtransPaymentController::class, 'checkStatus'])->name('pelanggan.payment.check.midtrans');
});

// Midtrans Webhook (tanpa auth)
Route::post('/webhook/midtrans', [MidtransPaymentController::class, 'webhook'])->name('webhook.midtrans');
// CSV Import
Route::prefix('admin/csv')->middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/template/{type}',        [App\Http\Controllers\Admin\CsvImportController::class, 'template'])->name('csv.template');
    Route::post('/pelanggan/preview',     [App\Http\Controllers\Admin\CsvImportController::class, 'previewPelanggan'])->name('csv.pelanggan.preview');
    Route::post('/pelanggan/import',      [App\Http\Controllers\Admin\CsvImportController::class, 'importPelanggan'])->name('csv.pelanggan.import');
    Route::post('/paket/import',          [App\Http\Controllers\Admin\CsvImportController::class, 'importPaket'])->name('csv.paket.import');
});
Route::post('/admin/mikrotik/{router}/wireguard/setup', [App\Http\Controllers\Admin\MikrotikController::class, 'setupWireguard'])->name('mikrotik.wireguard.setup');
Route::get('/admin/mikrotik/{router}/wireguard/config', [App\Http\Controllers\Admin\MikrotikController::class, 'getWireguardConfig'])->name('mikrotik.wireguard.config');

// Topologi OLT
Route::prefix('admin/topologi')->middleware(['auth', 'role:admin,operator'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\TopologiController::class, 'index'])->name('topologi.index');
    

    Route::get('/olt/{id}/edit', [App\Http\Controllers\Admin\TopologiController::class, 'editOlt'])->name('topologi.olt.edit');
    Route::put('/olt/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'updateOlt'])->name('topologi.olt.update');
    Route::delete('/olt/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'destroyOlt'])->name('topologi.olt.destroy');
    Route::get('/olt/create', [App\Http\Controllers\Admin\TopologiController::class, 'createOlt'])->name('topologi.olt.create');
    Route::post('/olt/store', [App\Http\Controllers\Admin\TopologiController::class, 'storeOlt'])->name('topologi.olt.store');
    Route::get('/olt/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'showOlt'])->name('topologi.olt');
    Route::post('/sync-onu/{olt_id}', [App\Http\Controllers\Admin\TopologiController::class, 'syncOnu'])->name('topologi.sync');
    Route::get('/peta', [App\Http\Controllers\Admin\TopologiController::class, 'petaTopologi'])->name('topologi.peta');
    Route::get('/api/nodes', [App\Http\Controllers\Admin\TopologiController::class, 'apiNodes'])->name('topologi.api.nodes');
    Route::get('/odc/create', [App\Http\Controllers\Admin\TopologiController::class, 'createOdc'])->name('topologi.odc.create');
    Route::post('/odc/store', [App\Http\Controllers\Admin\TopologiController::class, 'storeOdc'])->name('topologi.odc.store');
    Route::get('/odc/{id}/edit', [App\Http\Controllers\Admin\TopologiController::class, 'editOdc'])->name('topologi.odc.edit');
    Route::put('/odc/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'updateOdc'])->name('topologi.odc.update');
    Route::delete('/odc/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'destroyOdc'])->name('topologi.odc.destroy');
    Route::get('/odp/create', [App\Http\Controllers\Admin\TopologiController::class, 'createOdp'])->name('topologi.odp.create');
    Route::post('/odp/store', [App\Http\Controllers\Admin\TopologiController::class, 'storeOdp'])->name('topologi.odp.store');
    Route::get('/odp/{id}/edit', [App\Http\Controllers\Admin\TopologiController::class, 'editOdp'])->name('topologi.odp.edit');
    Route::put('/odp/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'updateOdp'])->name('topologi.odp.update');
    Route::delete('/odp/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'destroyOdp'])->name('topologi.odp.destroy');
    Route::post('/olt/{id}/fetch-hsgq-key', [App\Http\Controllers\Admin\TopologiController::class, 'fetchHsgqKey'])->name('topologi.olt.fetchHsgqKey');
    // SFP
    Route::get('/sfp/create', [App\Http\Controllers\Admin\TopologiController::class, 'createSfp'])->name('topologi.sfp.create');
    Route::post('/sfp/store', [App\Http\Controllers\Admin\TopologiController::class, 'storeSfp'])->name('topologi.sfp.store');
    Route::get('/sfp/{id}/edit', [App\Http\Controllers\Admin\TopologiController::class, 'editSfp'])->name('topologi.sfp.edit');
    Route::put('/sfp/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'updateSfp'])->name('topologi.sfp.update');
    Route::delete('/sfp/{id}', [App\Http\Controllers\Admin\TopologiController::class, 'destroySfp'])->name('topologi.sfp.destroy');
    Route::get('/api/sfp-by-olt/{olt_id}', [App\Http\Controllers\Admin\TopologiController::class, 'apiSfpByOlt'])->name('topologi.api.sfp');
    Route::get('/api/odc-by-sfp/{sfp_id}', [App\Http\Controllers\Admin\TopologiController::class, 'apiOdcBySfp'])->name('topologi.api.odc.sfp');
    Route::get('/api/odc-by-olt/{olt_id}', [App\Http\Controllers\Admin\TopologiController::class, 'apiOdcByOlt'])->name('topologi.api.odc');
    Route::get('/api/odp-by-sfp/{sfp_id}', [App\Http\Controllers\Admin\TopologiController::class, 'apiOdpBySfp'])->name('topologi.api.odp.sfp');
    Route::get('/api/odp-by-olt/{olt_id}', [App\Http\Controllers\Admin\TopologiController::class, 'apiOdpByOlt'])->name('topologi.api.odp');
    Route::post('/onu/{onu_id}/assign-odp', [App\Http\Controllers\Admin\TopologiController::class, 'assignOnu'])->name('topologi.onu.assign');
});


// Resolve Google Maps short URL
Route::get('/admin/utils/resolve-maps-url', function(\Illuminate\Http\Request $request) {
    $url = $request->query('url');
    if (!$url) return response()->json(['error' => 'No URL'], 400);
    try {
        $client = new \GuzzleHttp\Client(['allow_redirects' => ['max' => 5, 'track_redirects' => true], 'timeout' => 10, 'verify' => false]);
        $res = $client->get($url);
        $redirectHistory = $res->getHeaderLine('X-Guzzle-Redirect-History');
        if ($redirectHistory) {
            // Split by comma+space or just take last URL properly
            $urls = preg_split('/,(?=https?:\/\/)/', $redirectHistory);
            $finalUrl = trim(end($urls));
        } else {
            $finalUrl = $url;
        }
        $patterns = [
            '/@(-?\d+\.?\d+),(-?\d+\.?\d+)/',
            '/[?&]q=(-?\d+\.?\d+),(-?\d+\.?\d+)/',
            '/search\/(-?\d+\.?\d+),\+?(-?\d+\.?\d+)/',
            '/\!3d(-?\d+\.?\d+)\!4d(-?\d+\.?\d+)/',
        ];
        foreach ($patterns as $pattern) {
            preg_match($pattern, $finalUrl, $m);
            if ($m) return response()->json(['lat' => $m[1], 'lng' => $m[2], 'url' => $finalUrl]);
        }
        return response()->json(['error' => 'Koordinat tidak ditemukan', 'url' => $finalUrl]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
