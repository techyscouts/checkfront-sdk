<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\Configuration;
use TechyScouts\Checkfront\Http\Factory\RequestFactory;
use TechyScouts\Checkfront\Http\Factory\StreamFactory;
use TechyScouts\Checkfront\Http\Factory\UriFactory;
use TechyScouts\Checkfront\Http\Message\Response;
use TechyScouts\Checkfront\Http\Message\Stream;
use TechyScouts\Checkfront\Resource\AbstractResource;
use TechyScouts\Checkfront\Response\PaginatedResponse;

abstract class ResourceTestCase extends TestCase
{
    protected ClientInterface $httpClient;
    protected AuthenticationInterface $auth;
    protected Configuration $config;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);

        $this->auth = $this->createMock(AuthenticationInterface::class);
        $this->auth->method('authenticate')
            ->willReturnCallback(fn (RequestInterface $r) => $r->withHeader('Authorization', 'Bearer test'));

        $this->config = new Configuration('https://api.checkfront.com', $this->auth);
    }

    protected function createResource(string $class): AbstractResource
    {
        return new $class(
            $this->httpClient,
            $this->auth,
            $this->config,
            new RequestFactory(),
            new StreamFactory(),
            new UriFactory(),
        );
    }

    protected function jsonResponse(int $status = 200, array $body = []): Response
    {
        return new Response($status, [], Stream::create(json_encode($body)));
    }

    protected function expectRequest(string $method, string $path, array $responseBody = []): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($method, $path): bool {
                $this->assertSame($method, $request->getMethod());
                $uri = (string) $request->getUri();
                $this->assertStringContainsString($path, $uri);
                return true;
            }))
            ->willReturn($this->jsonResponse(200, $responseBody));
    }

    protected function expectRequestWithBody(string $method, string $path, array $expectedBody, array $responseBody = []): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($method, $path, $expectedBody): bool {
                $this->assertSame($method, $request->getMethod());
                $this->assertStringContainsString($path, (string) $request->getUri());
                $body = json_decode((string) $request->getBody(), true);
                $this->assertSame($expectedBody, $body);
                return true;
            }))
            ->willReturn($this->jsonResponse(200, $responseBody));
    }

    protected function expectRequestWithQuery(string $method, string $path, array $queryParams, array $responseBody = []): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($method, $path, $queryParams): bool {
                $this->assertSame($method, $request->getMethod());
                $uri = (string) $request->getUri();
                $this->assertStringContainsString($path, $uri);
                foreach ($queryParams as $key => $value) {
                    $this->assertStringContainsString("{$key}={$value}", $uri);
                }
                return true;
            }))
            ->willReturn($this->jsonResponse(200, $responseBody));
    }

    protected function paginatedBody(array $items = []): array
    {
        return [
            'data' => $items,
            'meta' => ['records' => ['total' => count($items), 'offset' => 0, 'limit' => 25]],
        ];
    }
}
