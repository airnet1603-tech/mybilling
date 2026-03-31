<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Router;
use App\Services\MikrotikService;

class MikrotikPoller extends Command
{
    protected $signature   = 'mikrotik:poll {--interval=3}';
    protected $description = 'Background poller MikroTik — simpan ke cache file';

    public function handle()
    {
        $interval = (int) $this->option('interval');
        $this->info("Poller jalan, interval {$interval} detik. Ctrl+C untuk stop.");

        while (true) {
            $routers = Router::where('is_active', 1)->get();
            foreach ($routers as $router) {
                try {
                    $mikrotik = new MikrotikService();
                    $mikrotik->connect($router->ip_address, $router->username, $router->password, $router->port);
                    $sessions  = $mikrotik->getActiveSessions();
                    $resource  = $mikrotik->getRouterResource();
                    $mikrotik->disconnect();

                    $cacheFile = sys_get_temp_dir() . '/mikrotik_cache_' . $router->id . '.json';
                    file_put_contents($cacheFile, json_encode([
                        'updated_at' => time(),
                        'sessions'   => $sessions['data']   ?? [],
                        'resource'   => $resource['data']   ?? [],
                    ]));
                } catch (\Exception $e) {
                    // Lanjut ke router berikutnya kalau error
                }
            }
            sleep($interval);
        }
    }
}
