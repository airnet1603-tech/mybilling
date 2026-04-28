<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class WhatsappService
{
    private string $gateway;
    private string $token;
    private string $baseUrl;
    private array $settings;

    public function __construct()
    {
        $this->settings = DB::table('setting')->pluck('value', 'key')->toArray();
        $this->gateway  = $this->settings['wa_gateway']  ?? 'fonnte';
        $this->token    = $this->settings['wa_token']    ?? '';
        $this->baseUrl  = $this->settings['wa_base_url'] ?? 'https://app.wablas.com';
    }

    public function isEnabled(string $jenis): bool
    {
        return ($this->settings['wa_jadwal_' . $jenis] ?? '1') === '1';
    }

    public function getReminderHari(): array
    {
        $raw = $this->settings['wa_jadwal_hari_reminder'] ?? '3,1';
        return array_filter(array_map('intval', explode(',', $raw)));
    }

    private function getQrisUrl(): string
    {
        return $this->settings['wa_qris_url'] ?? '';
    }

    private function getNorek(): string
    {
        return $this->settings['wa_norek'] ?? '';
    }

    private function parseTemplate(string $key, array $vars): string
    {
        $template = $this->settings[$key] ?? '';
        foreach ($vars as $k => $v) {
            $template = str_replace('{' . $k . '}', $v, $template);
        }
        return $template;
    }

    private function appendPaymentInfo(string $message): string
    {
        $norek = $this->getNorek();
        if (!empty($norek)) {
            $message .= "\n\n💳 *Transfer:* {$norek}";
        }
        $qris = $this->getQrisUrl();
        if (!empty($qris)) {
            $message .= "\n🔳 *QRIS:* Scan gambar di bawah ini\n{$qris}";
        }
        $message .= "\n\nHubungi Admin untuk informasi lebih lanjut.";
        return $message;
    }

    public function send(string $phone, string $message, string $image = ''): bool
    {
        if (empty($this->token)) {
            Log::warning('WhatsApp token belum diisi di pengaturan');
            return false;
        }
        try {
            return match($this->gateway) {
                'wablas'   => $this->sendWablas($phone, $message, $image),
                'ultramsg' => $this->sendUltramsg($phone, $message, $image),
                default    => $this->sendFonnte($phone, $message, $image),
            };
        } catch (\Exception $e) {
            Log::error('WhatsApp Error', ['gateway' => $this->gateway, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function sendFonnte(string $phone, string $message, string $image = ''): bool
    {
        $payload = ['target' => $phone, 'message' => $message];
        if (!empty($image)) {
            $payload['url'] = $image;
        }
        $response = Http::withHeaders(['Authorization' => $this->token])
            ->post('https://api.fonnte.com/send', $payload);
        Log::info('Fonnte Response', ['body' => $response->body()]);
        return $response->json('status') === true;
    }

    private function sendWablas(string $phone, string $message, string $image = ''): bool
    {
        $payload = ['phone' => $phone, 'message' => $message];
        if (!empty($image)) {
            $payload['image'] = $image;
        }
        $response = Http::withHeaders(['Authorization' => $this->token])
            ->post(rtrim($this->baseUrl, '/') . '/api/send-message', $payload);
        Log::info('Wablas Response', ['body' => $response->body()]);
        return $response->json('status') === true;
    }

    private function sendUltramsg(string $phone, string $message, string $image = ''): bool
    {
        $instanceId = explode('|', $this->token)[0] ?? '';
        $token      = explode('|', $this->token)[1] ?? $this->token;
        $payload    = ['token' => $token, 'to' => $phone, 'body' => $message];
        if (!empty($image)) {
            $payload['image'] = $image;
        }
        $response = Http::post("https://api.ultramsg.com/{$instanceId}/messages/chat", $payload);
        Log::info('UltraMsg Response', ['body' => $response->body()]);
        return isset($response->json()['sent']) && $response->json()['sent'] === 'true';
    }

    public function sendTagihan(string $phone, string $nama, string $periode, int $total, string $jatuhTempo): bool
    {
        if (!$this->isEnabled('tagihan')) return false;
        $message = $this->parseTemplate('wa_template_tagihan', [
            'nama' => $nama, 'periode' => $periode,
            'total' => number_format($total, 0, ',', '.'), 'jatuh_tempo' => $jatuhTempo,
        ]);
        $message = $this->appendPaymentInfo($message);
        return $this->send($phone, $message, $this->getQrisUrl());
    }

    public function sendJatuhTempo(string $phone, string $nama, string $periode, int $total, int $sisaHari): bool
    {
        if (!$this->isEnabled('reminder')) return false;
        $message = $this->parseTemplate('wa_template_reminder', [
            'nama'      => $nama,
            'periode'   => $periode,
            'total'     => number_format($total, 0, ',', '.'),
            'sisa_hari' => $sisaHari,
        ]);
        $message = $this->appendPaymentInfo($message);
        return $this->send($phone, $message, $this->getQrisUrl());
    }

    public function sendIsolir(string $phone, string $nama, string $periode, int $total): bool
    {
        if (!$this->isEnabled('isolir')) return false;
        $message = $this->parseTemplate('wa_template_isolir', [
            'nama' => $nama, 'periode' => $periode,
            'total' => number_format($total, 0, ',', '.'),
        ]);
        $message = $this->appendPaymentInfo($message);
        return $this->send($phone, $message, $this->getQrisUrl());
    }

    public function sendKonfirmasiBayar(string $phone, string $nama, string $periode, int $total): bool
    {
        if (!$this->isEnabled('konfirmasi')) return false;
        $message = $this->parseTemplate('wa_template_konfirmasi', [
            'nama' => $nama, 'periode' => $periode,
            'total' => number_format($total, 0, ',', '.'),
        ]);
        return $this->send($phone, $message);
    }
}
