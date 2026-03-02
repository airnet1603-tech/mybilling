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
        return $this;
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

    public function syncProfile($nama, $download, $upload)
    {
        try {
            $rateLimit = $upload . "M/" . $download . "M";
            $all = $this->api->comm("/ppp/profile/print");
            $existing = null;
            foreach ($all as $p) {
                if (($p["name"] ?? "") === $nama) { $existing = $p; break; }
            }
            if ($existing) {
                $this->api->comm("/ppp/profile/set", [".id" => $existing[".id"], "rate-limit" => $rateLimit]);
            } else {
                $this->api->comm("/ppp/profile/add", ["name" => $nama, "rate-limit" => $rateLimit]);
            }
            return ["status" => true, "message" => "Profile $nama berhasil disync"];
        } catch (\Exception $e) {
            return ["status" => false, "message" => $e->getMessage()];
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

    private function formatBytes($bytes)
    {
        $bytes = (int)$bytes;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
