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
