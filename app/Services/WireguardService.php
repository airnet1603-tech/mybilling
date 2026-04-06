<?php
namespace App\Services;

class WireguardService
{
    protected string $configPath = '/etc/wireguard/wg0.conf';
    protected string $interface  = 'wg0';

    public function generateKeypair(): array
    {
        $privateKey = trim(shell_exec('wg genkey'));
        $publicKey  = trim(shell_exec("echo '$privateKey' | wg pubkey"));
        return ['private' => $privateKey, 'public' => $publicKey];
    }

    public function getVpsPublicKey(): string
    {
        return trim(shell_exec("sudo wg show $this->interface public-key"));
    }

    public function getNextAvailableIp(): string
    {
        $used = [];
        $conf = file_get_contents($this->configPath);
        preg_match_all('/AllowedIPs\s*=\s*([\d\.]+)\/32/', $conf, $matches);
        foreach ($matches[1] as $ip) $used[] = $ip;
        for ($i = 10; $i <= 254; $i++) {
            $ip = "10.10.10.$i";
            if (!in_array($ip, $used)) return $ip;
        }
        return '10.10.10.10';
    }

    public function addPeer(string $publicKey, string $allowedIp, int $keepalive = 25): bool
    {
        $conf = file_get_contents($this->configPath);
        if (str_contains($conf, $publicKey)) return true;
        $peer = "\n[Peer]\nPublicKey = $publicKey\nAllowedIPs = $allowedIp/32\nPersistentKeepalive = $keepalive\n";
        file_put_contents($this->configPath, $conf . $peer);
        shell_exec("wg set $this->interface peer $publicKey allowed-ips $allowedIp/32 persistent-keepalive $keepalive 2>/dev/null");
        return true;
    }

    public function removePeer(string $publicKey): bool
    {
        $conf    = file_get_contents($this->configPath);
        $pattern = '/\n\[Peer\][^\[]*' . preg_quote($publicKey, '/') . '[^\[]*/s';
        $new     = preg_replace($pattern, '', $conf);
        file_put_contents($this->configPath, $new);
        shell_exec("wg set $this->interface peer $publicKey remove 2>/dev/null");
        return true;
    }

    public function getMikrotikConfig(string $privateKey, string $wgIp, string $vpsPublicKey, string $vpsEndpoint = '163.61.58.172'): string
    {
        return "/interface wireguard add name=wg-billing listen-port=51820 private-key=\"$privateKey\"
/interface wireguard peers add interface=wg-billing public-key=\"$vpsPublicKey\" endpoint-address=$vpsEndpoint endpoint-port=51820 allowed-address=10.10.10.1/32 persistent-keepalive=25
/ip address add address=$wgIp/24 interface=wg-billing";
    }
}
