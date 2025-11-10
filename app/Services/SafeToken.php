<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class SafeToken
{
    private static string $salt = 'KSLQsPu1LyO8p52QBc9br3tNU5w';
    private const METHOD = 'AES-256-CBC';

    public static function encode(array $data): ?string
    {
        try {
            $json = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($json === false) {
                throw new Exception('Failed to encode JSON');
            }

            // Kunci enkripsi di-generate dari salt dengan SHA-256 (32 bytes binary)
            $key = hash('sha256', self::$salt, true);

            // Generate random IV sesuai panjang cipher
            $ivLength = openssl_cipher_iv_length(self::METHOD);
            $iv = random_bytes($ivLength);

            // Enkripsi data JSON dengan key dan IV random
            $encrypted = openssl_encrypt($json, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);
            if ($encrypted === false) {
                throw new Exception('Encryption failed');
            }

            // Gabungkan IV + ciphertext lalu encode base64 URL-safe
            $combined = base64_encode($iv . $encrypted);
            $urlSafe = rtrim(strtr($combined, '+/', '-_'), '=');

            return $urlSafe;

        } catch (Exception $e) {
            Log::error('SafeToken encode error: ' . $e->getMessage());
            return null;
        }
    }

    public static function decode(string $token): ?array
    {
        try {
            // Restore padding dan decode base64 URL-safe
            $padded = $token . str_repeat('=', (4 - strlen($token) % 4) % 4);
            $decoded = base64_decode(strtr($padded, '-_', '+/'));
            if ($decoded === false) {
                throw new Exception('Base64 decode failed');
            }

            $ivLength = openssl_cipher_iv_length(self::METHOD);
            if (strlen($decoded) < $ivLength) {
                return null;
                // throw new Exception('Data too short. Decoded length '.strlen($decoded).' < ivLength '.$ivLength);
            }

            // Pisahkan IV dan ciphertext
            $iv = substr($decoded, 0, $ivLength);
            $encrypted = substr($decoded, $ivLength);

            $key = hash('sha256', self::$salt, true);

            // Dekripsi
            $decrypted = openssl_decrypt($encrypted, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);
            if ($decrypted === false) {
                throw new Exception('Decryption failed');
            }

            $data = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('JSON decode error: ' . json_last_error_msg());
            }

            return $data;

        } catch (Exception $e) {
            Log::error('SafeToken decode error: ' . $e->getMessage());
            return null;
        }
    }

    public static function isValid(string $token): bool
    {
        return self::decode($token) !== null;
    }

    public static function generateUrl(string $route, array $data, array $additionalParams = []): ?string
    {
        $token = self::encode($data);
        if (!$token) {
            return null;
        }

        $params = array_merge($additionalParams, ['token' => $token]);
        return route($route, $params);
    }

    public static function setSalt(string $salt): void
    {
        self::$salt = $salt;
    }

    public static function getSalt(): string
    {
        return self::$salt;
    }

    public static function generateSalt(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}