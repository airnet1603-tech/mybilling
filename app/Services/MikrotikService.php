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

    public function getActiveSessions()
    {
        try {
            $result = $this->api->comm('/ppp/active/print');
            return ['status' => true, 'data' => $result];
        } catch (Exception $e) {
            return ['status' => false, 'data' => []];
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
     * $router = object Router (bisa null jika tidak ada)
     */
    public function syncProfile($nama, $download, $upload, $router = null)
    {
        try {
            $rateLimit = $upload . "M/" . $download . "M";

            $params = [
                "name"       => $nama,
                "rate-limit" => $rateLimit,
            ];

            // Tambahkan local-address jika ada di router
            if ($router && !empty($router->local_address)) {
                $params['local-address'] = $router->local_address;
            }

            // Tambahkan remote-address (pool) jika ada di router
            if ($router && !empty($router->remote_address)) {
                $params['remote-address'] = $router->remote_address;
            }

            // Tambahkan DNS server jika ada di router
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

    /**
     * Ambil semua IP Pool dari RB
     */
    public function getIpPools()
    {
        try {
            $result = $this->api->comm('/ip/pool/print');
            $pools = [];
            foreach ($result as $p) {
                $pools[] = [
                    'name'      => $p['name']    ?? '-',
                    'ranges'    => $p['ranges']  ?? '-',
                ];
            }
            return ['status' => true, 'data' => $pools];
        } catch (Exception $e) {
            return ['status' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    /**
     * Ambil DNS Server dari RB (IP ? DNS)
     */
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

    /**
     * Ambil Local Address PPPoE dari RB (dari profile default atau interface PPPoE server)
     */
    public function getPppoeLocalAddress()
    {
        try {
            $result = $this->api->comm('/ppp/profile/print');
            // Cari profile default dulu
            foreach ($result as $p) {
                if (($p['name'] ?? '') === 'default' && !empty($p['local-address'])) {
                    return ['status' => true, 'local_address' => $p['local-address']];
                }
            }
            // Kalau default kosong, cari profile lain yang ada local-address
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
        $bytes = (int)$bytes;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}