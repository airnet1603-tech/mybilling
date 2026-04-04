<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;
    private string $baseUrl;

    public function __construct()
    {
        $s = \App\Models\PaymentSetting::getGateway('midtrans');
        $this->serverKey = $s['server_key'] ?? config('midtrans.server_key');
        $mode = $s['mode'] ?? 'sandbox';
        $this->baseUrl = $mode === 'production'
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    private function authHeader(): string
    {
        return 'Basic ' . base64_encode($this->serverKey . ':');
    }

    public function createTransaction(array $params): array
    {
        $orderId = preg_replace('/[^a-zA-Z0-9\-]/', '', $params['no_tagihan']);
        $amount  = (int) $params['amount'];

        $body = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $params['nama_pelanggan'] ?? 'Pelanggan',
                'email'      => $params['email'] ?? 'pelanggan@isp.com',
                'phone'      => $params['phone'] ?? '',
            ],
            'item_details' => [[
                'id'       => $orderId,
                'price'    => $amount,
                'quantity' => 1,
                'name'     => $params['product_details'] ?? 'Tagihan Internet',
            ]],
            'enabled_payments' => $params['enabled_payments'] ?? ['bni_va', 'bri_va', 'mandiri_va', 'permata_va', 'other_va', 'qris'],
            'expiry' => [
                'duration' => $params['expiry_duration'] ?? 24,
                'unit'     => 'hours',
            ],
        ];

        Log::info('Midtrans Create Transaction', $body);

        $response = Http::withHeaders([
            'Authorization' => $this->authHeader(),
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . '/v1/payment-links', $body);

        Log::info('Midtrans Response', ['status' => $response->status(), 'body' => $response->body()]);

        if (!$response->successful()) {
            throw new \Exception('Gagal membuat transaksi Midtrans: ' . $response->body());
        }

        return $response->json();
    }

    public function checkTransaction(string $orderId): array
    {
        $orderId = preg_replace('/[^a-zA-Z0-9\-]/', '', $orderId);

        $response = Http::withHeaders([
            'Authorization' => $this->authHeader(),
        ])->get($this->baseUrl . '/v2/' . $orderId . '/status');

        return $response->json() ?? [];
    }

    public function verifyWebhook(array $data): bool
    {
        $signatureKey = hash('sha512',
            $data['order_id'] .
            $data['status_code'] .
            $data['gross_amount'] .
            $this->serverKey
        );
        return $signatureKey === ($data['signature_key'] ?? '');
    }
}
