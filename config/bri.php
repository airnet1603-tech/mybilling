<?php
return [
    'base_url'           => env('BRI_BASE_URL', 'https://sandbox.partner.api.bri.co.id'),
    'client_id'          => env('BRI_CLIENT_ID'),
    'client_secret'      => env('BRI_CLIENT_SECRET'),
    'partner_id'         => env('BRI_PARTNER_ID', 'ISP-BILLING'),
    'channel_id'         => env('BRI_CHANNEL_ID', 'SANDBOX'),
    'merchant_id'        => env('BRI_MERCHANT_ID', 'SANDBOX_MERCHANT'),
    'terminal_id'        => env('BRI_TERMINAL_ID', 'SANDBOX_TERM'),
    'va_prefix'          => env('BRI_VA_PREFIX', '88888'),
    'partner_service_id' => env('BRI_PARTNER_SERVICE_ID', '00088888'),
];
