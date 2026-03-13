<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Services\MikrotikService;
use Carbon\Carbon;

class AutoIsolir extends Command
{
    protected $signature   = 'billing:auto-isolir';
    protected $description = 'Auto isolir pelanggan yang sudah jatuh tempo dan belum bayar';

    public function handle()
    {
        $this->info('Mulai cek pelanggan jatuh tempo...');

        $pelanggans = Pelanggan::with('router')
            ->where('status', 'aktif')
            ->whereNotNull('tgl_expired')
            ->where('tgl_expired', '<=', Carbon::today())
            ->get();

        if ($pelanggans->isEmpty()) {
            $this->info('Tidak ada pelanggan yang perlu diisolir.');
            return 0;
        }

        $this->info("Ditemukan {$pelanggans->count()} pelanggan jatuh tempo.");

        $berhasil = 0;
        $gagal    = 0;

        foreach ($pelanggans as $pelanggan) {
            try {
                if ($pelanggan->router) {
                    $mikrotik = new MikrotikService();
                    $mikrotik->connect(
                        $pelanggan->router->ip_address,
                        $pelanggan->router->username,
                        $pelanggan->router->password,
                        $pelanggan->router->port
                    );
                    $result = $mikrotik->isolir($pelanggan->username);
                    $mikrotik->disconnect();

                    if (!$result['status']) {
                        throw new \Exception($result['message']);
                    }
                }

                $pelanggan->update(['status' => 'isolir']);
                $this->info("✓ Diisolir: {$pelanggan->nama} ({$pelanggan->username}) - expired: {$pelanggan->tgl_expired}");
                $berhasil++;

            } catch (\Exception $e) {
                $this->error("✗ Gagal isolir {$pelanggan->username}: " . $e->getMessage());
                \Log::warning("Auto isolir gagal untuk {$pelanggan->username}: " . $e->getMessage());
                $gagal++;
            }
        }

        $this->info("Selesai. Berhasil: {$berhasil}, Gagal: {$gagal}");
        return 0;
    }
}
