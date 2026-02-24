<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Auth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\Auth\TokenAuthentication;
use TechyScouts\Checkfront\Http\Message\Request;

#[CoversClass(TokenAuthentication::class)]
final class TokenAuthenticationTest extends TestCase
{
    #[Test]
    public function implementsAuthenticationInterface(): void
    {
        $auth = new TokenAuthentication('key', 'secret');

        $this->assertInstanceOf(AuthenticationInterface::class, $auth);
    }

    #[Test]
    public function authenticateAddsBasicAuthHeader(): void
    {
        $auth = new TokenAuthentication('myApiKey', 'myApiSecret');
        $request = new Request('GET', 'https://example.com/api/v4/bookings');

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertTrue($authenticatedRequest->hasHeader('Authorization'));

        $expectedCredentials = base64_encode('myApiKey:myApiSecret');
        $this->assertSame(
            "Basic {$expectedCredentials}",
            $authenticatedRequest->getHeaderLine('Authorization')
        );
    }

    #[Test]
    public function authenticateReturnsNewRequestInstance(): void
    {
        $auth = new TokenAuthentication('key', 'secret');
        $request = new Request('GET', 'https://example.com');

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertNotSame($request, $authenticatedRequest);
        $this->assertFalse($request->hasHeader('Authorization'));
    }

    #[Test]
    public function authenticateUsesCorrectBase64Encoding(): void
    {
        $auth = new TokenAuthentication('user', 'pass');
        $request = new Request('GET', 'https://example.com');

        $authenticatedRequest = $auth->authenticate($request);

        $header = $authenticatedRequest->getHeaderLine('Authorization');
        $this->assertStringStartsWith('Basic ', $header);

        $encoded = substr($header, 6);
        $decoded = base64_decode($encoded, true);

        $this->assertSame('user:pass', $decoded);
    }

    #[Test]
    public function authenticatePreservesExistingHeaders(): void
    {
        $auth = new TokenAuthentication('key', 'secret');
        $request = new Request('POST', 'https://example.com', [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        $authenticatedRequest = $auth->authenticate($request);

        $this->assertSame('application/json', $authenticatedRequest->getHeaderLine('Content-Type'));
        $this->assertSame('application/json', $authenticatedRequest->getHeaderLine('Accept'));
        $this->assertTrue($authenticatedRequest->hasHeader('Authorization'));
    }

    #[Test]
    public function authenticateWithSpecialCharactersInCredentials(): void
    {
        $auth = new TokenAuthentication('key:with:colons', 'secret/with+special=chars');
        $request = new Request('GET', 'https://example.com');

        $authenticatedRequest = $auth->authenticate($request);
        $header = $authenticatedRequest->getHeaderLine('Authorization');

        $encoded = substr($header, 6);
        $decoded = base64_decode($encoded, true);

        $this->assertSame('key:with:colons:secret/with+special=chars', $decoded);
    }

    #[Test]
    public function authenticateWithEmptyCredentials(): void
    {
        $auth = new TokenAuthentication('', '');
        $request = new Request('GET', 'https://example.com');

        $authenticatedRequest = $auth->authenticate($request);

        $header = $authenticatedRequest->getHeaderLine('Authorization');
        $encoded = substr($header, 6);
        $decoded = base64_decode($encoded, true);

        $this->assertSame(':', $decoded);
    }
}
