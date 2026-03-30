<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BriApiService
{
    private string $baseUrl;
    private string $clientId;
    private string $clientSecret;
    private string $partnerId;
    private string $channelId;
    private string $privateKeyPath;

    public function __construct()
    {
        $this->baseUrl        = config('bri.base_url');
        $this->clientId       = config('bri.client_id');
        $this->clientSecret   = config('bri.client_secret');
        $this->partnerId      = config('bri.partner_id');
        $this->channelId      = config('bri.channel_id');
        $this->privateKeyPath = storage_path('bri_private_key.pem');
    }

    private function makeExternalId(): string
    {
        // Max 20 karakter numerik sesuai standar BRI SNAP API
        return now()->format('YmdHis') . rand(100, 999);
    }

    private function makeTimestamp(): string
    {
        return now()->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i:s+07:00');
    }

    public function getAccessToken(): string
    {
        [$token,] = $this->getTokenAndTimestamp();
        return $token;
    }

    private function getTokenAndTimestamp(): array
    {
        $timestamp    = $this->makeTimestamp();
        $stringToSign = $this->clientId . '|' . $timestamp;

        $privateKey = openssl_pkey_get_private(file_get_contents($this->privateKeyPath));
        if (!$privateKey) throw new \Exception('Private key BRI tidak valid.');

        openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-TIMESTAMP'  => $timestamp,
            'X-CLIENT-KEY' => $this->clientId,
            'X-SIGNATURE'  => base64_encode($signature),
        ])->post($this->baseUrl . '/snap/v1.0/access-token/b2b', [
            'grantType' => 'client_credentials',
        ]);

        Log::info('BRI Token Response', ['status' => $response->status(), 'body' => $response->body()]);

        if (!$response->successful() || empty($response->json()['accessToken'])) {
            throw new \Exception('Gagal mendapatkan token: ' . $response->body());
        }

        return [$response->json()['accessToken'], $timestamp];
    }

    public function createQris(array $params): array
    {
        [$token, $timestamp] = $this->getTokenAndTimestamp();
        $externalId = $this->makeExternalId();

        Log::info('BRI QRIS ExternalID', ['externalId' => $externalId, 'length' => strlen($externalId)]);

        $body = [
            'partnerReferenceNo' => $params['no_tagihan'],
            'amount' => [
                'value'    => number_format((float)$params['amount'], 2, '.', ''),
                'currency' => 'IDR',
            ],
            'merchantId'     => config('bri.merchant_id'),
            'terminalId'     => config('bri.terminal_id'),
            'validityPeriod' => now()->setTimezone('Asia/Jakarta')->addMinutes(30)->format('Y-m-d\TH:i:s+07:00'),
            'additionalInfo' => ['customerName' => $params['nama_pelanggan'] ?? 'Pelanggan'],
        ];

        $signature = $this->generateHmacSignature('POST', '/snap/v1.0/qr/dynamic-mpm/generate', $token, $body, $timestamp);

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'X-TIMESTAMP'   => $timestamp,
            'X-PARTNER-ID'  => $this->partnerId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID'    => $this->channelId,
            'X-SIGNATURE'   => $signature,
        ])->post($this->baseUrl . '/snap/v1.0/qr/dynamic-mpm/generate', $body);

        Log::info('BRI QRIS Response', ['status' => $response->status(), 'body' => $response->body()]);

        if (!$response->successful()) throw new \Exception('Gagal membuat QRIS: ' . $response->body());
        return $response->json();
    }

    public function checkQrisStatus(string $noTagihan): array
    {
        [$token, $timestamp] = $this->getTokenAndTimestamp();
        $externalId = $this->makeExternalId();
        $body = ['originalPartnerReferenceNo' => $noTagihan, 'serviceCode' => '47'];
        $signature = $this->generateHmacSignature('POST', '/snap/v1.0/qr/dynamic-mpm/query-payment-status', $token, $body, $timestamp);

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'X-TIMESTAMP'   => $timestamp,
            'X-PARTNER-ID'  => $this->partnerId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID'    => $this->channelId,
            'X-SIGNATURE'   => $signature,
        ])->post($this->baseUrl . '/snap/v1.0/qr/dynamic-mpm/query-payment-status', $body);

        return $response->json() ?? [];
    }

    public function createVirtualAccount(array $params): array
    {
        [$token, $timestamp] = $this->getTokenAndTimestamp();
        $externalId = $this->makeExternalId();

        Log::info('BRI VA ExternalID', ['externalId' => $externalId, 'length' => strlen($externalId)]);

        $partnerServiceId = config('bri.partner_service_id');
        $customerNo = substr(str_pad(preg_replace('/[^0-9]/', '', $params['no_tagihan']), 20, '0', STR_PAD_LEFT), -20);
        $vaNumber   = $partnerServiceId . $customerNo;

        Log::info('BRI VA Numbers', [
            'partnerServiceId' => $partnerServiceId,
            'customerNo'       => $customerNo,
            'vaNumber'         => $vaNumber,
            'vaLength'         => strlen($vaNumber),
        ]);

        $body = [
            'partnerServiceId'   => $partnerServiceId,
            'customerNo'         => $customerNo,
            'virtualAccountNo'   => $vaNumber,
            'virtualAccountName' => substr($params['nama_pelanggan'] ?? 'Pelanggan ISP', 0, 255),
            'trxId'              => $externalId,
            'totalAmount' => [
                'value'    => number_format((float)$params['amount'], 2, '.', ''),
                'currency' => 'IDR',
            ],
            'expiredDate'    => now()->setTimezone('Asia/Jakarta')->addHours(24)->format('Y-m-d\TH:i:s+07:00'),
            'additionalInfo' => ['channel' => 'VIRTUAL_ACCOUNT_BRI'],
        ];

        $signature = $this->generateHmacSignature('POST', '/snap/v1.0/transfer-va/create-va', $token, $body, $timestamp);

        Log::info('BRI VA Request Headers', [
            'X-PARTNER-ID'  => $this->partnerId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID'    => $this->channelId,
        ]);

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'X-TIMESTAMP'   => $timestamp,
            'X-PARTNER-ID'  => $this->partnerId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID'    => $this->channelId,
            'X-SIGNATURE'   => $signature,
        ])->post($this->baseUrl . '/snap/v1.0/transfer-va/create-va', $body);

        Log::info('BRI VA Response', ['status' => $response->status(), 'body' => $response->body()]);

        if (!$response->successful()) throw new \Exception('Gagal membuat VA: ' . $response->body());
        return $response->json();
    }

    public function checkVaStatus(string $noTagihan): array
    {
        [$token, $timestamp] = $this->getTokenAndTimestamp();
        $externalId = $this->makeExternalId();
        $partnerServiceId = config('bri.partner_service_id');
        $customerNo = substr(str_pad(preg_replace('/[^0-9]/', '', $noTagihan), 20, '0', STR_PAD_LEFT), -20);

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo'       => $customerNo,
            'virtualAccountNo' => $partnerServiceId . $customerNo,
            'inquiryRequestId' => $externalId,
        ];

        $signature = $this->generateHmacSignature('POST', '/snap/v1.0/transfer-va/inquiry', $token, $body, $timestamp);

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'X-TIMESTAMP'   => $timestamp,
            'X-PARTNER-ID'  => $this->partnerId,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID'    => $this->channelId,
            'X-SIGNATURE'   => $signature,
        ])->post($this->baseUrl . '/snap/v1.0/transfer-va/inquiry', $body);

        return $response->json() ?? [];
    }

    private function generateHmacSignature(string $method, string $path, string $token, array $body, string $timestamp): string
    {
        $bodyHash     = strtolower(hash('sha256', json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)));
        $stringToSign = $method . ':' . $path . ':' . $token . ':' . $bodyHash . ':' . $timestamp;
        return base64_encode(hash_hmac('sha512', $stringToSign, $this->clientSecret, true));
    }
}