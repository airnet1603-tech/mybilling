<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LaporanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes(['register' => false]);

Route::get('/home', function () {
    return redirect('/admin/dashboard');
})->middleware('auth');

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', function () { return redirect('/admin/dashboard'); });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('pelanggan/export', [PelangganController::class, 'export'])->name('pelanggan.export');
    Route::resource('pelanggan', PelangganController::class);
    Route::post('pelanggan/{pelanggan}/status', [PelangganController::class, 'ubahStatus'])->name('pelanggan.status');
    Route::resource('paket', PaketController::class);
    Route::resource('tagihan', TagihanController::class);
    Route::post('tagihan/generate', [TagihanController::class, 'generateMassal'])->name('tagihan.generate');
    Route::post('tagihan/{tagihan}/bayar', [TagihanController::class, 'konfirmasiBayar'])->name('tagihan.bayar');
    Route::get('pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
    Route::put('setting', [SettingController::class, 'update'])->name('setting.update');
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
});

// Mikrotik Routes
Route::prefix('admin/mikrotik')->middleware('auth')->group(function () {
    Route::get('/',                                    [App\Http\Controllers\Admin\MikrotikController::class, 'index'])->name('mikrotik.index');
    Route::post('/',                                   [App\Http\Controllers\Admin\MikrotikController::class, 'store'])->name('mikrotik.store');
    Route::delete('/{router}',                         [App\Http\Controllers\Admin\MikrotikController::class, 'destroy'])->name('mikrotik.destroy');
    Route::get('/{router}/test',                       [App\Http\Controllers\Admin\MikrotikController::class, 'testConnection'])->name('mikrotik.test');
    Route::get('/monitoring',                          [App\Http\Controllers\Admin\MikrotikController::class, 'monitoring'])->name('mikrotik.monitoring');
    Route::get('/{router}/sessions',                   [App\Http\Controllers\Admin\MikrotikController::class, 'getSessions'])->name('mikrotik.sessions');
    Route::post('/pelanggan/{pelanggan}/isolir',       [App\Http\Controllers\Admin\MikrotikController::class, 'isolir'])->name('mikrotik.isolir');
    Route::post('/pelanggan/{pelanggan}/aktifkan',     [App\Http\Controllers\Admin\MikrotikController::class, 'aktifkan'])->name('mikrotik.aktifkan');
    Route::post('/pelanggan/{pelanggan}/suspend',      [App\Http\Controllers\Admin\MikrotikController::class, 'suspend'])->name('mikrotik.suspend');
    Route::post('/pelanggan/{pelanggan}/nonaktif',     [App\Http\Controllers\Admin\MikrotikController::class, 'nonaktif'])->name('mikrotik.nonaktif');
});

// Sync PPPoE
Route::post('/admin/mikrotik/pelanggan/{pelanggan}/sync', [App\Http\Controllers\Admin\MikrotikController::class, 'syncPelanggan'])->name('mikrotik.sync')->middleware('auth');
    // Sync PPPoE
    Route::post('/admin/mikrotik/pelanggan/{pelanggan}/sync', [App\Http\Controllers\Admin\MikrotikController::class, 'syncPelanggan'])->name('mikrotik.sync')->middleware('auth');
