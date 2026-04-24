<?php

class RouterosAPI
{
    private $socket;
    private $debug = false;

    public function connect($ip, $login, $password, $port = 8728)
    {
        $this->socket = @fsockopen($ip, $port, $errno, $errstr, 10);
        if (!$this->socket) {
            throw new Exception("Gagal konek ke $ip:$port - $errstr");
        }
        $this->login($login, $password);
        return true;
    }

    public function disconnect()
    {
        if ($this->socket) fclose($this->socket);
    }

    private function login($login, $password)
    {
        // Coba plain login dulu (RouterOS v7)
        $result = $this->comm('/login', ['name' => $login, 'password' => $password]);

        if (isset($result[0]['!trap'])) {
            throw new Exception("Login gagal: " . ($result[0]['message'] ?? 'Unknown error'));
        }

        // Jika ada challenge (RouterOS v6), pakai MD5
        if (isset($result[0]['ret'])) {
            $challenge = pack('H*', $result[0]['ret']);
            $hash = md5(chr(0) . $password . $challenge);
            $result2 = $this->comm('/login', [
                'name'     => $login,
                'response' => '00' . $hash,
            ]);
            if (isset($result2[0]['!trap'])) {
                throw new Exception("Login gagal: " . ($result2[0]['message'] ?? 'Unknown error'));
            }
        }
    }

    public function comm($command, $attr = [])
    {
        $this->writeWord($command);
        foreach ($attr as $k => $v) {
            // Key diawali ? = filter query, tulis tanpa = di depan
            if (str_starts_with($k, '?')) {
                $this->writeWord("$k=$v");
            } else {
                $this->writeWord("=$k=$v");
            }
        }
        $this->writeWord('');
        return $this->readResponse();
    }

    private function writeWord($word)
    {
        $len = strlen($word);
        if ($len < 0x80) {
            fwrite($this->socket, chr($len));
        } elseif ($len < 0x4000) {
            $len |= 0x8000;
            fwrite($this->socket, chr(($len >> 8) & 0xFF) . chr($len & 0xFF));
        } else {
            $len |= 0xC00000;
            fwrite($this->socket, chr(($len >> 16) & 0xFF) . chr(($len >> 8) & 0xFF) . chr($len & 0xFF));
        }
        fwrite($this->socket, $word);
    }

    private function readWord()
    {
        $byte = ord(fread($this->socket, 1));
        if ($byte & 0x80) {
            if ($byte & 0x40) {
                $byte &= ~0xC0;
                $byte = ($byte << 8) | ord(fread($this->socket, 1));
                $byte = ($byte << 8) | ord(fread($this->socket, 1));
            } else {
                $byte &= ~0x80;
                $byte = ($byte << 8) | ord(fread($this->socket, 1));
            }
        }
        if ($byte == 0) return '';
        return fread($this->socket, $byte);
    }

    private function readResponse()
    {
        $result = [];
        $current = [];
        while (true) {
            $word = $this->readWord();
            if ($word === '!done') {
                if (!empty($current)) $result[] = $current;
                break;
            } elseif ($word === '!re') {
                if (!empty($current)) $result[] = $current;
                $current = [];
            } elseif ($word === '!trap' || $word === '!fatal') {
                $current['!trap'] = true;
            } elseif (strpos($word, '=') === 0) {
                $parts = explode('=', substr($word, 1), 2);
                if (count($parts) == 2) $current[$parts[0]] = $parts[1];
            }
        }
        if (!empty($current)) $result[] = $current;
        return $result;
    }
}
