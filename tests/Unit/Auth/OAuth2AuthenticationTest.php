<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\Auth\OAuth2Authentication;
use TechyScouts\Checkfront\Http\Message\Request;

#[CoversClass(OAuth2Authentication::class)]
final class OAuth2AuthenticationTest extends TestCase
{
    #[Test]
    public function implementsAuthenticationInterface(): void
    {
        $auth = new OAuth2Authentication('access-token');

        $this->assertInstanceOf(AuthenticationInterface::class, $auth);
    }

    #[Test]
    public function authenticateAddsBearerHeader(): void
    {
        $auth = new OAuth2Authentication('my-access-token');
        $request = new Request('GET', 'https://example.com/api/v4/bookings');

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertTrue($authenticatedRequest->hasHeader('Authorization'));
        $this->assertSame(
            'Bearer my-access-token',
            $authenticatedRequest->getHeaderLine('Authorization')
        );
    }

    #[Test]
    public function authenticateReturnsNewRequestInstance(): void
    {
        $auth = new OAuth2Authentication('token');
        $request = new Request('GET', 'https://example.com');

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertNotSame($request, $authenticatedRequest);
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    #[Test]
    public function getAccessTokenReturnsToken(): void
    {
        $auth = new OAuth2Authentication('my-token');

        $this->assertSame('my-token', $auth->getAccessToken());
    }

    #[Test]
    public function getRefreshTokenReturnsRefreshToken(): void
    {
        $auth = new OAuth2Authentication('access', 'refresh');

        $this->assertSame('refresh', $auth->getRefreshToken());
    }

    #[Test]
    public function getRefreshTokenReturnsNullWhenNotSet(): void
    {
        $auth = new OAuth2Authentication('access');

        $this->assertNull($auth->getRefreshToken());
    }

    #[Test]
    public function getExpiresAtReturnsTimestamp(): void
    {
        $expiresAt = time() + 3600;
        $auth = new OAuth2Authentication('access', null, $expiresAt);

        $this->assertSame($expiresAt, $auth->getExpiresAt());
    }

    #[Test]
    public function getExpiresAtReturnsNullWhenNotSet(): void
    {
        $auth = new OAuth2Authentication('access');

        $this->assertNull($auth->getExpiresAt());
    }

    #[Test]
    public function isExpiredReturnsFalseWhenNoExpiresAt(): void
    {
        $auth = new OAuth2Authentication('access');

        $this->assertFalse($auth->isExpired());
    }

    #[Test]
    public function isExpiredReturnsFalseWhenNotYetExpired(): void
    {
        $auth = new OAuth2Authentication('access', null, time() + 3600);

        $this->assertFalse($auth->isExpired());
    }

    #[Test]
    public function isExpiredReturnsTrueWhenExpired(): void
    {
        $auth = new OAuth2Authentication('access', null, time() - 1);

        $this->assertTrue($auth->isExpired());
    }

    #[Test]
    public function isExpiredReturnsTrueAtExactExpiry(): void
    {
        $auth = new OAuth2Authentication('access', null, time());

        $this->assertTrue($auth->isExpired());
    }

    #[Test]
    public function loadsCachedTokensFromCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')
            ->with('checkfront_oauth2_tokens')
            ->willReturn([
                'access_token' => 'cached-access-token',
                'refresh_token' => 'cached-refresh-token',
                'expires_at' => time() + 7200,
            ]);

        $auth = new OAuth2Authentication(
            'original-token',
            'original-refresh',
            null,
            $cache,
        );

        $this->assertSame('cached-access-token', $auth->getAccessToken());
        $this->assertSame('cached-refresh-token', $auth->getRefreshToken());
    }

    #[Test]
    public function skipsLoadingWhenCacheReturnsNull(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')
            ->with('checkfront_oauth2_tokens')
            ->willReturn(null);

        $auth = new OAuth2Authentication(
            'original-token',
            'original-refresh',
            null,
            $cache,
        );

        $this->assertSame('original-token', $auth->getAccessToken());
        $this->assertSame('original-refresh', $auth->getRefreshToken());
    }

    #[Test]
    public function skipsLoadingWhenNoCacheProvided(): void
    {
        $auth = new OAuth2Authentication('my-token', 'my-refresh');

        $this->assertSame('my-token', $auth->getAccessToken());
        $this->assertSame('my-refresh', $auth->getRefreshToken());
    }

    #[Test]
    public function authenticatePreservesExistingHeaders(): void
    {
        $auth = new OAuth2Authentication('token');
        $request = new Request('POST', 'https://example.com', [
            'Content-Type' => 'application/json',
        ]);

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertSame('application/json', $authenticatedRequest->getHeaderLine('Content-Type'));
        $this->assertTrue($authenticatedRequest->hasHeader('Authorization'));
    }

    #[Test]
    public function cachedTokensOverrideConstructorValues(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')
            ->with('checkfront_oauth2_tokens')
            ->willReturn([
                'access_token' => 'cached-token',
            ]);

        $auth = new OAuth2Authentication(
            'constructor-token',
            null,
            null,
            $cache,
        );

        $this->assertSame('cached-token', $auth->getAccessToken());
    }

    #[Test]
    public function cachedRefreshTokenFallsBackToConstructorValue(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('get')
            ->with('checkfront_oauth2_tokens')
            ->willReturn([
                'access_token' => 'cached-access',
                // No refresh_token in cache
            ]);

        $auth = new OAuth2Authentication(
            'constructor-token',
            'constructor-refresh',
            null,
            $cache,
        );

        $this->assertSame('constructor-refresh', $auth->getRefreshToken());
    }

    #[Test]
    public function authenticateUsesTokenEvenWhenExpiredWithoutRefreshCapability(): void
    {
        // When expired but no refresh token or endpoint, just use the current token
        $auth = new OAuth2Authentication('expired-token', null, time() - 100);
        $request = new Request('GET', 'https://example.com');

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertSame('Bearer expired-token', $authenticatedRequest->getHeaderLine('Authorization'));
    }
}
