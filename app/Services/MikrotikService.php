<?php

namespace App\Services;

require_once app_path('Libraries/RouterosAPI.php');

use Exception;

class MikrotikService
{
    private $api;
    private $connected = false;
    private $currentIp = 'default';

    public function connect($ip, $username, $password, $port = 8728)
    {
        $this->api = new \RouterosAPI();
        $this->api->connect($ip, $username, $password, $port);
        $this->connected = true;
        $this->currentIp = $ip;
        return $this->api;
    }

    public function disconnect()
    {
        if ($this->connected) $this->api->disconnect();
    }

    public function testConnection($router)
    {
        try {
            $ip = (!empty($router->use_wireguard) && !empty($router->wg_ip)) ? $router->wg_ip : $router->ip_address;
            $this->connect($ip, $router->username, $router->password, $router->port);
            $result = $this->api->comm('/system/identity/print');
            $this->disconnect();
            return ['status' => true, 'message' => 'Koneksi berhasil', 'identity' => $result[0]['name'] ?? '-'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function addPppoeUser($username, $password, $profile = 'default')
    {
        try {
            $all = $this->api->comm('/ppp/secret/print');
            $existing = null;
            foreach ($all as $user) {
                if (($user['name'] ?? '') === $username) {
                    $existing = $user;
                    break;
                }
            }
            if ($existing) {
                $this->api->comm('/ppp/secret/set', [
                    '.id'      => $existing['.id'],
                    'password' => $password,
                    'profile'  => $profile,
                    'disabled' => 'no',
                ]);
                return ['status' => true, 'message' => "User PPPoE $username diupdate"];
            }
            $this->api->comm('/ppp/secret/add', [
                'name'     => $username,
                'password' => $password,
                'service'  => 'pppoe',
                'profile'  => $profile,
                'disabled' => 'no',
            ]);
            return ['status' => true, 'message' => "User PPPoE $username berhasil ditambahkan"];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function isolir($username)
    {
        try {
            $all = $this->api->comm('/ppp/secret/print');
            $existing = null;
            foreach ($all as $user) {
                if (($user['name'] ?? '') === $username) { $existing = $user; break; }
            }
            if (!$existing) return ['status' => false, 'message' => "User tidak ditemukan"];
            $this->api->comm('/ppp/secret/set', ['.id' => $existing['.id'], 'disabled' => 'yes']);
            $active = $this->api->comm('/ppp/active/print');
            foreach ($active as $a) {
                if (($a['name'] ?? '') === $username) {
                    $this->api->comm('/ppp/active/remove', ['.id' => $a['.id']]);
                    break;
                }
            }
            return ['status' => true, 'message' => "Pelanggan $username berhasil diisolir"];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function aktifkan($username)
    {
        try {
            $all = $this->api->comm('/ppp/secret/print');
            $existing = null;
            foreach ($all as $user) {
                if (($user['name'] ?? '') === $username) { $existing = $user; break; }
            }
            if (!$existing) return ['status' => false, 'message' => "User tidak ditemukan"];
            $this->api->comm('/ppp/secret/set', ['.id' => $existing['.id'], 'disabled' => 'no']);
            return ['status' => true, 'message' => "Pelanggan $username berhasil diaktifkan"];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function setQueue($username, $target, $maxUpload, $maxDownload)
    {
        try {
            $all = $this->api->comm('/queue/simple/print');
            $existing = null;
            foreach ($all as $q) {
                if (($q['name'] ?? '') === $username) { $existing = $q; break; }
            }
            if ($existing) {
                $this->api->comm('/queue/simple/set', [
                    '.id'       => $existing['.id'],
                    'target'    => $target,
                    'max-limit' => "$maxUpload/$maxDownload",
                ]);
            } else {
                $this->api->comm('/queue/simple/add', [
                    'name'      => $username,
                    'target'    => $target,
                    'max-limit' => "$maxUpload/$maxDownload",
                ]);
            }
            return ['status' => true, 'message' => "Queue $username berhasil di-set"];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * [FIX] Ambil bytes traffic dari interface PPPoE dinamis RouterOS.
     */
    public function getPppoeInterfaceStats()
    {
        try {
            $interfaces = $this->api->comm('/interface/print');
            $now        = microtime(true);
            
            $snapFile   = sys_get_temp_dir() . '/mikrotik_snap_' . md5($this->currentIp) . '.json';
            $snap       = file_exists($snapFile) ? json_decode(file_get_contents($snapFile), true) : [];
            $newSnap    = ['time' => $now, 'data' => []];
            $stats      = [];

            foreach ($interfaces as $iface) {
                $name = $iface['name'] ?? '';
                if (!preg_match('/^<pppoe-(.+)>$/', $name, $match)) continue;
                $username  = $match[1];
                $bytesIn   = (int) ($iface['rx-byte'] ?? 0);
                $bytesOut  = (int) ($iface['tx-byte'] ?? 0);

                $newSnap['data'][$username] = ['in' => $bytesIn, 'out' => $bytesOut];

                // Hitung rate dari delta bytes
                $rateIn = $rateOut = 0;
                if (!empty($snap['data'][$username]) && isset($snap['time'])) {
                    $elapsed = $now - (float) $snap['time'];
                    if ($elapsed > 0.5) {
                        $deltaIn  = $bytesIn  - (int) $snap['data'][$username]['in'];
                        $deltaOut = $bytesOut - (int) $snap['data'][$username]['out'];
                        if ($deltaIn  >= 0) $rateIn  = (int) (($deltaIn  * 8) / $elapsed);
                        if ($deltaOut >= 0) $rateOut = (int) (($deltaOut * 8) / $elapsed);
                    }
                }

                $stats[$username] = [
                    'bytes_in'  => $bytesIn,
                    'bytes_out' => $bytesOut,
                    'rate_in'   => $rateIn,
                    'rate_out'  => $rateOut,
                ];
            }

            file_put_contents($snapFile, json_encode($newSnap));
            return ['status' => true, 'data' => $stats];
        } catch (Exception $e) {
            return ['status' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * [FIX] Ambil active sessions + merge traffic dari interface PPPoE.
     */
    public function getActiveSessions()
    {
        try {
            $sessions = $this->api->comm('/ppp/active/print');
            $ifaceStats = $this->getPppoeInterfaceStats();
            $ifaceData  = $ifaceStats['data'] ?? [];
            $queueData = [];
            if (empty($ifaceData)) {
                $queues = $this->api->comm('/queue/simple/print', ['.proplist' => 'name,bytes-in,bytes-out,bytes']);
                foreach ($queues as $q) {
                    $qName = $q['name'] ?? '';
                    if (!$qName) continue;
                    $bytesIn  = (int) ($q['bytes-in']  ?? 0);
                    $bytesOut = (int) ($q['bytes-out'] ?? 0);
                    if ($bytesIn === 0 && $bytesOut === 0 && !empty($q['bytes'])) {
                        $parts = explode('/', $q['bytes']);
                        $bytesIn  = (int) ($parts[0] ?? 0);
                        $bytesOut = (int) ($parts[1] ?? 0);
                    }
                    $queueData[$qName] = ['bytes_in' => $bytesIn, 'bytes_out' => $bytesOut];
                }
            }
            foreach ($sessions as &$s) {
                $name = $s['name'] ?? '';
                if (isset($ifaceData[$name])) {
                    $s['rx-byte']  = $ifaceData[$name]['bytes_in'];

                    $s['rate_in']  = $ifaceData[$name]['rate_in']  ?? 0;
                    $s['rate_out'] = $ifaceData[$name]['rate_out'] ?? 0;
                    $s['tx-byte'] = $ifaceData[$name]['bytes_out'];
                } elseif (isset($queueData[$name])) {
                    $s['rx-byte'] = $queueData[$name]['bytes_in'];
                    $s['tx-byte'] = $queueData[$name]['bytes_out'];
                } else {
                    $s['rx-byte'] = (int) ($s['rx-byte'] ?? $s['bytes-in'] ?? 0);
                    $s['tx-byte'] = (int) ($s['tx-byte'] ?? $s['bytes-out'] ?? 0);
                }
            }
            unset($s);
            return ['status' => true, 'data' => $sessions];
        } catch (Exception $e) {
            return ['status' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function getRouterResource()
    {
        try {
            $result = $this->api->comm('/system/resource/print');
            $d = $result[0] ?? [];
            return [
                'status' => true,
                'data' => [
                    'uptime'      => $d['uptime']     ?? '-',
                    'cpu_load'    => ($d['cpu-load']   ?? 0) . '%',
                    'memory_used' => $this->formatBytes(($d['total-memory'] ?? 0) - ($d['free-memory'] ?? 0)),
                    'version'     => $d['version']     ?? '-',
                ],
            ];
        } catch (Exception $e) {
            return ['status' => false, 'data' => []];
        }
    }

    /**
     * Sync PPPoE profile ke Mikrotik dengan setting per router
     */
    public function syncProfile($nama, $download, $upload, $router = null)
    {
        try {
            $rateLimit = $upload . "M/" . $download . "M";

            $params = [
                "name"       => $nama,
                "rate-limit" => $rateLimit,
            ];

            if ($router && !empty($router->local_address)) {
                $params['local-address'] = $router->local_address;
            }

            if ($router && !empty($router->remote_address)) {
                $params['remote-address'] = $router->remote_address;
            }

            if ($router && !empty($router->dns_server)) {
                $params['dns-server'] = $router->dns_server;
            }

            $all = $this->api->comm("/ppp/profile/print");
            $existing = null;
            foreach ($all as $p) {
                if (($p["name"] ?? "") === $nama) { $existing = $p; break; }
            }

            if ($existing) {
                $updateParams = $params;
                unset($updateParams['name']);
                $updateParams['.id'] = $existing['.id'];
                $this->api->comm("/ppp/profile/set", $updateParams);
            } else {
                $this->api->comm("/ppp/profile/add", $params);
            }

            return ["status" => true, "message" => "Profile $nama berhasil disync"];
        } catch (\Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public function getPppoeSecrets()
    {
        try {
            $secrets = $this->api->comm('/ppp/secret/print');
            $actives = $this->api->comm('/ppp/active/print');
            $activeNames = [];
            foreach ($actives as $a) {
                $activeNames[$a['name'] ?? ''] = [
                    'address' => $a['address'] ?? '',
                    'uptime'  => $a['uptime']  ?? '',
                ];
            }
            $result = [];
            foreach ($secrets as $s) {
                if (($s['service'] ?? '') !== 'pppoe') continue;
                $name = $s['name'] ?? '';
                $result[] = [
                    'username' => $name,
                    'password' => $s['password'] ?? '',
                    'profile'  => $s['profile']  ?? 'default',
                    'disabled' => ($s['disabled'] ?? 'false') === 'true',
                    'online'   => isset($activeNames[$name]),
                    'address'  => $activeNames[$name]['address'] ?? '',
                ];
            }
            return ['status' => true, 'data' => $result];
        } catch (Exception $e) {
            return ['status' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function deletePppoeUser($username)
    {
        try {
            $all = $this->api->comm('/ppp/secret/print');
            $existing = null;
            foreach ($all as $user) {
                if (($user['name'] ?? '') === $username) { $existing = $user; break; }
            }
            if (!$existing) return ['status' => true, 'message' => "User tidak ditemukan di Mikrotik"];

            // Putus sesi aktif dulu
            $active = $this->api->comm('/ppp/active/print');
            foreach ($active as $a) {
                if (($a['name'] ?? '') === $username) {
                    $this->api->comm('/ppp/active/remove', ['.id' => $a['.id']]);
                    break;
                }
            }

            // Hapus secret PPPoE
            $this->api->comm('/ppp/secret/remove', ['.id' => $existing['.id']]);

            // Hapus queue jika ada
            $queues = $this->api->comm('/queue/simple/print');
            foreach ($queues as $q) {
                if (($q['name'] ?? '') === $username) {
                    $this->api->comm('/queue/simple/remove', ['.id' => $q['.id']]);
                    break;
                }
            }

            return ['status' => true, 'message' => "User PPPoE $username berhasil dihapus dari Mikrotik"];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteProfile($nama)
    {
        try {
            $all = $this->api->comm("/ppp/profile/print");
            foreach ($all as $p) {
                if (($p["name"] ?? "") === $nama) {
                    $this->api->comm("/ppp/profile/remove", [".id" => $p[".id"]]);
                    break;
                }
            }
            return ["status" => true, "message" => "Profile $nama dihapus"];
        } catch (\Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
        }
    }

    public function getIpPools()
    {
        try {
            $result = $this->api->comm('/ip/pool/print');
            $pools = [];
            foreach ($result as $p) {
                $pools[] = [
                    'name'   => $p['name']   ?? '-',
                    'ranges' => $p['ranges'] ?? '-',
                ];
            }
            return ['status' => true, 'data' => $pools];
        } catch (Exception $e) {
            return ['status' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    public function getDnsServer()
    {
        try {
            $result = $this->api->comm('/ip/dns/print');
            $dns = $result[0]['servers'] ?? '';
            return ['status' => true, 'dns' => $dns];
        } catch (Exception $e) {
            return ['status' => false, 'dns' => '', 'message' => $e->getMessage()];
        }
    }

    public function getPppoeLocalAddress()
    {
        try {
            $result = $this->api->comm('/ppp/profile/print');
            foreach ($result as $p) {
                if (($p['name'] ?? '') === 'default' && !empty($p['local-address'])) {
                    return ['status' => true, 'local_address' => $p['local-address']];
                }
            }
            foreach ($result as $p) {
                if (!empty($p['local-address'])) {
                    return ['status' => true, 'local_address' => $p['local-address']];
                }
            }
            return ['status' => true, 'local_address' => ''];
        } catch (Exception $e) {
            return ['status' => false, 'local_address' => '', 'message' => $e->getMessage()];
        }
    }

    private function formatBytes($bytes)
    {
        $bytes = (int) $bytes;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}