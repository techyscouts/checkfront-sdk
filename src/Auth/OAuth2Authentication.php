<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Auth;

use Psr\Http\Message\RequestInterface;
use Psr\SimpleCache\CacheInterface;
use TechyScouts\Checkfront\Exception\AuthenticationException;

final class OAuth2Authentication implements AuthenticationInterface
{
    private const CACHE_KEY = 'checkfront_oauth2_tokens';

    public function __construct(
        private string $accessToken,
        private ?string $refreshToken = null,
        private ?int $expiresAt = null,
        private ?CacheInterface $cache = null,
        private ?string $tokenEndpoint = null,
    ) {
        $this->loadCachedTokens();
    }

    public function authenticate(RequestInterface $request): RequestInterface
    {
        if ($this->isExpired() && $this->refreshToken !== null && $this->tokenEndpoint !== null) {
            $this->refreshAccessToken();
        }

        return $request->withHeader('Authorization', "Bearer {$this->accessToken}");
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return time() >= $this->expiresAt;
    }

    private function refreshAccessToken(): void
    {
        if ($this->tokenEndpoint === null || $this->refreshToken === null) {
            throw new AuthenticationException('Cannot refresh token: missing token endpoint or refresh token.');
        }

        $ch = curl_init($this->tokenEndpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            throw new AuthenticationException(
                'Failed to refresh OAuth2 access token.',
                $httpCode,
            );
        }

        /** @var array{access_token?: string, refresh_token?: string, expires_in?: int} $data */
        $data = json_decode((string) $response, true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['access_token'])) {
            throw new AuthenticationException('Invalid token refresh response: missing access_token.');
        }

        $this->accessToken = $data['access_token'];

        if (isset($data['refresh_token'])) {
            $this->refreshToken = $data['refresh_token'];
        }

        if (isset($data['expires_in'])) {
            $this->expiresAt = time() + (int) $data['expires_in'];
        }

        $this->saveCachedTokens($data['expires_in'] ?? null);
    }

    private function loadCachedTokens(): void
    {
        if ($this->cache === null) {
            return;
        }

        /** @var array{access_token: string, refresh_token: ?string, expires_at: ?int}|null $cached */
        $cached = $this->cache->get(self::CACHE_KEY);

        if ($cached === null) {
            return;
        }

        $this->accessToken = $cached['access_token'];
        $this->refreshToken = $cached['refresh_token'] ?? $this->refreshToken;
        $this->expiresAt = $cached['expires_at'] ?? $this->expiresAt;
    }

    private function saveCachedTokens(?int $expiresIn): void
    {
        if ($this->cache === null) {
            return;
        }

        $this->cache->set(self::CACHE_KEY, [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'expires_at' => $this->expiresAt,
        ], $expiresIn);
    }
}
