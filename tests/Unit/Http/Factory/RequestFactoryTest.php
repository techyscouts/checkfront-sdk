<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http\Factory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use TechyScouts\Checkfront\Http\Factory\RequestFactory;
use TechyScouts\Checkfront\Http\Message\Uri;

#[CoversClass(RequestFactory::class)]
final class RequestFactoryTest extends TestCase
{
    private RequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RequestFactory();
    }

    #[Test]
    public function createRequestWithStringUri(): void
    {
        $request = $this->factory->createRequest('GET', 'https://example.com/path');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://example.com/path', (string) $request->getUri());
    }

    #[Test]
    public function createRequestWithUriInterface(): void
    {
        $uri = new Uri('https://example.com/path');
        $request = $this->factory->createRequest('POST', $uri);

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://example.com/path', (string) $request->getUri());
    }

    #[Test]
    public function createRequestSetsHostHeader(): void
    {
        $request = $this->factory->createRequest('GET', 'https://example.com/path');

        $this->assertTrue($request->hasHeader('Host'));
        $this->assertSame('example.com', $request->getHeaderLine('Host'));
    }

    #[Test]
    public function createRequestWithDifferentMethods(): void
    {
        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

        foreach ($methods as $method) {
            $request = $this->factory->createRequest($method, 'https://example.com');
            $this->assertSame($method, $request->getMethod());
        }
    }

    #[Test]
    public function createRequestWithEmptyBody(): void
    {
        $request = $this->factory->createRequest('GET', 'https://example.com');

        $this->assertSame('', (string) $request->getBody());
    }
}
