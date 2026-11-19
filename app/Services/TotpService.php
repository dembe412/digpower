<?php

namespace App\Services;

/** RFC 6238 TOTP (Google Authenticator compatible), dependency-free. */
class TotpService
{
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $bytes = 20): string
    {
        $bin = random_bytes($bytes);
        $bits = '';
        foreach (str_split($bin) as $c) {
            $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 5) as $chunk) {
            $out .= self::ALPHABET[bindec(str_pad($chunk, 5, '0'))];
        }

        return $out;
    }

    public function uri(string $secret, string $account): string
    {
        $issuer = rawurlencode(config('platform.name'));

        return "otpauth://totp/{$issuer}:".rawurlencode($account)."?secret={$secret}&issuer={$issuer}&digits=6&period=30";
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        $slice = (int) floor(time() / 30);
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->at($secret, $slice + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    private function at(string $secret, int $slice): string
    {
        $key = $this->base32Decode($secret);
        $hash = hash_hmac('sha1', pack('N*', 0).pack('N*', $slice), $key, true);
        $offset = ord($hash[19]) & 0xF;
        $val = ((ord($hash[$offset]) & 0x7F) << 24) | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8) | (ord($hash[$offset + 3]) & 0xFF);

        return str_pad((string) ($val % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $s): string
    {
        $bits = '';
        foreach (str_split(strtoupper($s)) as $c) {
            $p = strpos(self::ALPHABET, $c);
            if ($p !== false) {
                $bits .= str_pad(decbin($p), 5, '0', STR_PAD_LEFT);
            }
        }
        $out = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $out .= chr(bindec($byte));
            }
        }

        return $out;
    }
}
