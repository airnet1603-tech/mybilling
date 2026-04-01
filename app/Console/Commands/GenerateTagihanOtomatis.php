<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Carbon\Carbon;

class GenerateTagihanOtomatis extends Command
{
    protected $signature   = 'tagihan:generate-otomatis';
    protected $description = 'Auto-generate tagihan untuk pelanggan yang expired H-7';

    public function handle()
    {
        $hariJatuhTempo = 10;
        $targetExpired  = now()->addDays(7)->toDateString();
        $berhasil = 0;
        $skip     = 0;

        $pelanggans = Pelanggan::with('paket')
            ->whereIn('status', ['aktif', 'isolir'])
            ->whereDate('tgl_expired', $targetExpired)
            ->get();

        foreach ($pelanggans as $pelanggan) {
            if (!$pelanggan->paket) { $skip++; continue; }

            // Cek sudah ada tagihan bulan depan belum
            $bulanDepan = now()->addMonth();
            $sudahAda = Tagihan::where('pelanggan_id', $pelanggan->id)
                ->whereMonth('periode_bulan', $bulanDepan->month)
                ->whereYear('periode_bulan', $bulanDepan->year)
                ->exists();

            if ($sudahAda) { $skip++; continue; }

            Tagihan::create([
                'no_tagihan'      => Tagihan::generateNomor(),
                'pelanggan_id'    => $pelanggan->id,
                'paket_id'        => $pelanggan->paket_id,
                'jumlah'          => $pelanggan->paket->harga,
                'denda'           => 0,
                'diskon'          => 0,
                'total'           => $pelanggan->paket->harga,
                'periode_bulan'   => $bulanDepan->startOfMonth(),
                'tgl_tagihan'     => now(),
                'tgl_jatuh_tempo' => $bulanDepan->day($hariJatuhTempo),
                'status'          => 'unpaid',
            ]);
            $berhasil++;
        }

        $this->info("Auto-generate selesai! Berhasil: {$berhasil}, Skip: {$skip}");
        \Log::info("Auto-generate tagihan: Berhasil={$berhasil}, Skip={$skip}");
    }
}
