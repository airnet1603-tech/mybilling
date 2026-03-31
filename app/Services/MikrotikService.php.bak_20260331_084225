<?php

namespace App\Services;

require_once app_path('Libraries/RouterosAPI.php');

use Exception;

class MikrotikService
{
    private $api;
    private $connected = false;

    public function connect($ip, $username, $password, $port = 8728)
    {
        $this->api = new \RouterosAPI();
        $this->api->connect($ip, $username, $password, $port);
        $this->connected = true;
        return $this->api;
    }

    public function disconnect()
    {
        if ($this->connected) $this->api->disconnect();
    }

    public function testConnection($router)
    {
        try {
            $this->connect($router->ip_address, $router->username, $router->password, $router->port);
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
     * [FIX] Ambil stats traffic dari Simple Queue per username.
     * Simple Queue menyimpan bytes aktual, bukan /ppp/active.
     */
    public function getQueueStats()
    {
        try {
            $result = $this->api->comm('/queue/simple/print');
            $stats  = [];

            foreach ($result as $q) {
                $name = $q['name'] ?? '';
                if (!$name) continue;

                // RouterOS v6 & v7: field bytes-in / bytes-out
                $bytesIn  = (int) ($q['bytes-in']  ?? 0);
                $bytesOut = (int) ($q['bytes-out'] ?? 0);

                // Fallback: beberapa versi pakai format "in/out" dalam satu field
                if ($bytesIn === 0 && $bytesOut === 0 && !empty($q['bytes'])) {
                    $parts    = explode('/', $q['bytes']);
                    $bytesIn  = (int) ($parts[0] ?? 0);
                    $bytesOut = (int) ($parts[1] ?? 0);
                }

                $stats[$name] = [
                    'bytes_in'  => $bytesIn,
                    'bytes_out' => $bytesOut,
                ];
            }

            return ['status' => true, 'data' => $stats];
        } catch (Exception $e) {
            return ['status' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * [FIX] Ambil active sessions + merge traffic bytes dari Simple Queue.
     * /ppp/active/print tidak mengembalikan bytes traffic yang akurat,
     * sehingga data diambil dari Simple Queue berdasarkan username.
     */
    public function getActiveSessions()
    {
        try {
            $sessions   = $this->api->comm('/ppp/active/print');
            $queueStats = $this->getQueueStats();
            $queues     = $queueStats['data'] ?? [];

            foreach ($sessions as &$s) {
                $name = $s['name'] ?? '';
                if (isset($queues[$name])) {
                    // Override bytes dengan data dari Simple Queue
                    $s['rx-byte'] = $queues[$name]['bytes_in'];
                    $s['tx-byte'] = $queues[$name]['bytes_out'];
                } else {
                    // Kalau tidak ada di queue, set ke 0 (jangan biarkan null)
                    $s['rx-byte'] = $s['rx-byte'] ?? $s['bytes-in'] ?? 0;
                    $s['tx-byte'] = $s['tx-byte'] ?? $s['bytes-out'] ?? 0;
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