<?php

namespace App\Http\Controllers\Admin\Traits;

use App\Services\MikrotikService;
use Illuminate\Support\Facades\Log;

trait ConnectsToMikrotik
{
    protected function connectRouter($router, MikrotikService $mikrotik, int $retry = 3): void
    {
        $ip        = (!empty($router->use_wireguard) && !empty($router->wg_ip))
                     ? $router->wg_ip
                     : $router->ip_address;
        $lastError = null;

        for ($attempt = 1; $attempt <= $retry; $attempt++) {
            try {
                $mikrotik->connect($ip, $router->username, $router->password, $router->port);
                return;
            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                if ($attempt < $retry) sleep(1);
            }
        }

        Log::error("Gagal konek router [{$router->nama}] setelah {$retry}x: {$lastError}");
        throw new \Exception("Router [{$router->nama}] tidak dapat dihubungi setelah {$retry}x percobaan: {$lastError}");
    }
}
