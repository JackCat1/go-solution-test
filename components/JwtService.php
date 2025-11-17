<?php

namespace app\components;

use app\models\User;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * JwtService handles simple HS256 JWT issuing & validation without extra deps.
 */
class JwtService extends Component
{
    public string $secret = '';
    public string $issuer = 'library-api';
    public int $ttl = 3600;

    public function init(): void
    {
        parent::init();

        if ($this->secret === '') {
            throw new InvalidConfigException('JWT secret must be configured.');
        }
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function generateToken(User $user): string
    {
        $issuedAt = time();
        $payload = [
            'iss' => $this->issuer,
            'aud' => $this->issuer,
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $issuedAt + $this->ttl,
            'sub' => $user->id,
        ];

        return $this->encode($payload);
    }

    public function getIdentity(string $token): ?User
    {
        $payload = $this->validate($token);
        if ($payload === null) {
            return null;
        }

        $userId = $payload['sub'] ?? null;
        return $userId ? User::findOne((int) $userId) : null;
    }

    public function validate(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerB64, $payloadB64, $signatureB64] = $parts;
        $signingInput = $headerB64 . '.' . $payloadB64;
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', $signingInput, $this->secret, true)
        );

        if (!hash_equals($expectedSignature, $signatureB64)) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($payloadB64), true);
        if (!is_array($payload)) {
            return null;
        }

        if (($payload['nbf'] ?? 0) > time()) {
            return null;
        }

        if (($payload['exp'] ?? 0) < time()) {
            return null;
        }

        return $payload;
    }

    private function encode(array $payload): string
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $segments = [
            $this->base64UrlEncode(json_encode($header)),
            $this->base64UrlEncode(json_encode($payload)),
        ];
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', implode('.', $segments), $this->secret, true)
        );

        $segments[] = $signature;
        return implode('.', $segments);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return base64_decode(strtr($data, '-_', '+/'));
    }
}
