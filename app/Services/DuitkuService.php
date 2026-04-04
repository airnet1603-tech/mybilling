<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuService
{
    private string $merchantCode;
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $s = \App\Models\PaymentSetting::getGateway('duitku');
        $this->merchantCode = $s['merchant_code'] ?? config('duitku.merchant_code');
        $this->apiKey       = $s['api_key']       ?? config('duitku.api_key');
        $mode = $s['mode'] ?? config('duitku.env', 'sandbox');
        $this->baseUrl = $mode === 'production'
            ? 'https://passport.duitku.com/webapi'
            : 'https://sandbox.duitku.com/webapi';
    }

    private function makeSignature(string $merchantOrderId, int $amount): string
    {
        return md5($this->merchantCode . $merchantOrderId . $amount . $this->apiKey);
    }

    private function makeCallbackSignature(string $merchantOrderId, int $amount, string $statusCode): string
    {
        return md5($this->merchantCode . $amount . $merchantOrderId . $statusCode . $this->apiKey);
    }

    public function createTransaction(array $params): array
    {
        $merchantOrderId = preg_replace('/[^a-zA-Z0-9]/', '', $params['no_tagihan']);
        $amount          = (int) $params['amount'];
        $signature       = $this->makeSignature($merchantOrderId, $amount);

        $body = [
            'merchantCode'    => $this->merchantCode,
            'paymentAmount'   => $amount,
            'paymentMethod'   => $params['payment_method'] ?? 'VC',
            'merchantOrderId' => $merchantOrderId,
            'productDetails'  => $params['product_details'] ?? 'Tagihan Internet',
            'customerVaName'  => $params['nama_pelanggan'] ?? 'Pelanggan',
            'email'           => $params['email'] ?? 'pelanggan@email.com',
            'callbackUrl'     => route('webhook.duitku'),
            'returnUrl'       => route('pelanggan.payment.show', $merchantOrderId),
            'signature'       => $signature,
            'expiryPeriod'    => $params['expiry_period'] ?? 1440,
        ];

        Log::info('Duitku Create Transaction', $body);

        $response = Http::post($this->baseUrl . '/api/merchant/v2/inquiry', $body);

        Log::info('Duitku Response', ['status' => $response->status(), 'body' => $response->body()]);

        if (!$response->successful()) {
            throw new \Exception('Gagal membuat transaksi Duitku: ' . $response->body());
        }

        $result = $response->json();

        if (($result['statusCode'] ?? '') !== '00') {
            throw new \Exception('Duitku error: ' . ($result['statusMessage'] ?? 'Unknown error'));
        }

        return $result;
    }

    public function checkTransaction(string $merchantOrderId): array
    {
        $signature = md5($this->merchantCode . $merchantOrderId . $this->apiKey);

        $body = [
            'merchantCode'    => $this->merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature'       => $signature,
        ];

        $response = Http::post($this->baseUrl . '/api/merchant/transactionStatus', $body);

        return $response->json() ?? [];
    }

    public function verifyCallbackSignature(string $merchantOrderId, int $amount, string $statusCode, string $signature): bool
    {
        $expected = $this->makeCallbackSignature($merchantOrderId, $amount, $statusCode);
        return $expected === $signature;
    }
}
