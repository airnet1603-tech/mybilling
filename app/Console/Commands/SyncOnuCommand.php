<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Olt;
use App\Models\Onu;

class SyncOnuCommand extends Command
{
    protected $signature = 'onu:sync {olt_id? : ID OLT spesifik, kosong = semua OLT}';
    protected $description = 'Sync ONU dari semua OLT atau OLT tertentu';

    public function handle()
    {
        $olt_id = $this->argument('olt_id');
        $olts = $olt_id ? Olt::where('id', $olt_id)->get() : Olt::all();

        if ($olts->isEmpty()) {
            $this->error('OLT tidak ditemukan!');
            return 1;
        }

        foreach ($olts as $olt) {
            $this->info("Sync OLT: {$olt->name} ({$olt->ip_address})...");
            try {
                $controller = new \App\Http\Controllers\Admin\TopologiController();
                $response = $controller->syncOnu($olt->id);
                $data = json_decode($response->getContent(), true);
                if ($data['success'] ?? false) {
                    $this->info("  ✅ Berhasil sync {$data['synced']} ONU");
                    \Log::info("[ONU Sync] OLT {$olt->name}: {$data['synced']} ONU synced");
                } else {
                    $this->warn("  ❌ Gagal: " . ($data['error'] ?? 'Unknown error'));
                    \Log::warning("[ONU Sync] OLT {$olt->name} gagal: " . ($data['error'] ?? 'Unknown'));
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error: " . $e->getMessage());
                \Log::error("[ONU Sync] OLT {$olt->name} error: " . $e->getMessage());
            }
        }

        $this->info("Sync selesai: " . now());
        return 0;
    }
}
