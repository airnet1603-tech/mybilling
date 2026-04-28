<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Services\WhatsappService;
use Carbon\Carbon;

class WaReminderJatuhTempo extends Command
{
    protected $signature   = 'wa:reminder-jatuh-tempo';
    protected $description = 'Kirim WA reminder jatuh tempo sesuai hari yang dikonfigurasi di pengaturan';

    public function handle()
    {
        $wa      = new WhatsappService();
        $today   = Carbon::today();
        $haris   = $wa->getReminderHari(); // ambil dari DB, misal [7,3,1]

        if (empty($haris)) {
            $this->warn('Tidak ada konfigurasi hari reminder.');
            return;
        }

        // Buat list tanggal target
        $targetDates = array_map(fn($h) => $today->copy()->addDays($h)->toDateString(), $haris);

        $targets = Pelanggan::with('paket')
            ->where('status', 'aktif')
            ->whereNotNull('tgl_expired')
            ->whereIn(\DB::raw('DATE(tgl_expired)'), $targetDates)
            ->get();

        $this->info("Hari reminder: " . implode(', ', array_map(fn($h) => "H-{$h}", $haris)));
        $this->info("Target pelanggan: {$targets->count()}");

        $terkirim = 0;
        foreach ($targets as $pelanggan) {
            if (empty($pelanggan->no_hp)) continue;
            $expired  = Carbon::parse($pelanggan->tgl_expired);
            $sisaHari = $today->diffInDays($expired);
            $periode  = $expired->format('F Y');
            $total    = $pelanggan->paket->harga ?? 0;
            $result   = $wa->sendJatuhTempo($pelanggan->no_hp, $pelanggan->nama, $periode, $total, $sisaHari);
            if ($result) $terkirim++;
            $this->info("WA ke {$pelanggan->nama} ({$pelanggan->no_hp}) - sisa {$sisaHari} hari: " . ($result ? '✅ OK' : '❌ GAGAL'));
        }

        $this->info("Selesai. Total terkirim: {$terkirim} dari {$targets->count()} pelanggan.");
    }
}
