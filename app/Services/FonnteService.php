<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    public function send(string $phone, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);

            Log::info('Fonnte Response', ['body' => $response->body()]);
            return $response->json('status') === true;
        } catch (\Exception $e) {
            Log::error('Fonnte Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendTagihan(string $phone, string $nama, string $periode, int $total, string $jatuhTempo): bool
    {
        $message = "Halo *{$nama}*,\n\n";
        $message .= "Tagihan internet Anda untuk periode *{$periode}* telah tersedia.\n\n";
        $message .= "Total: *Rp " . number_format($total, 0, ',', '.') . "*\n";
        $message .= "Jatuh Tempo: *{$jatuhTempo}*\n\n";
        $message .= "Segera lakukan pembayaran sebelum jatuh tempo.\n";
        $message .= "Info: billing.airnetps.my.id";

        return $this->send($phone, $message);
    }

    public function sendJatuhTempo(string $phone, string $nama, string $periode, int $total, int $sisaHari): bool
    {
        $message = "⚠️ *PENGINGAT TAGIHAN*\n\n";
        $message .= "Halo *{$nama}*,\n\n";
        $message .= "Tagihan internet periode *{$periode}* akan jatuh tempo dalam *{$sisaHari} hari*.\n\n";
        $message .= "Total: *Rp " . number_format($total, 0, ',', '.') . "*\n\n";
        $message .= "Segera lakukan pembayaran untuk menghindari pemutusan layanan.\n";
        $message .= "Info: billing.airnetps.my.id";

        return $this->send($phone, $message);
    }

    public function sendKonfirmasiBayar(string $phone, string $nama, string $periode, int $total): bool
    {
        $message = "✅ *PEMBAYARAN DITERIMA*\n\n";
        $message .= "Halo *{$nama}*,\n\n";
        $message .= "Pembayaran tagihan internet periode *{$periode}* sebesar *Rp " . number_format($total, 0, ',', '.') . "* telah kami terima.\n\n";
        $message .= "Terima kasih telah membayar tepat waktu! 🙏\n";
        $message .= "Info: billing.airnetps.my.id";

        return $this->send($phone, $message);
    }
}
