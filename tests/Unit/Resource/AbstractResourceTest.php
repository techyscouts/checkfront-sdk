<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\Configuration;
use TechyScouts\Checkfront\Exception\AuthenticationException;
use TechyScouts\Checkfront\Exception\CheckfrontException;
use TechyScouts\Checkfront\Exception\ConflictException;
use TechyScouts\Checkfront\Exception\ForbiddenException;
use TechyScouts\Checkfront\Exception\NotFoundException;
use TechyScouts\Checkfront\Exception\RateLimitException;
use TechyScouts\Checkfront\Exception\ValidationException;
use TechyScouts\Checkfront\Http\Factory\RequestFactory;
use TechyScouts\Checkfront\Http\Factory\StreamFactory;
use TechyScouts\Checkfront\Http\Factory\UriFactory;
use TechyScouts\Checkfront\Http\Message\Response;
use TechyScouts\Checkfront\Http\Message\Stream;
use TechyScouts\Checkfront\Resource\AbstractResource;
use TechyScouts\Checkfront\Response\PaginatedResponse;

/**
 * Concrete test double extending AbstractResource to expose protected methods.
 */
final class ConcreteResource extends AbstractResource
{
    public function doGet(string $path, array $query = []): array
    {
        return $this->get($path, $query);
    }

    public function doPost(string $path, array $body = []): array
    {
        return $this->post($path, $body);
    }

    public function doPut(string $path, array $body = []): array
    {
        return $this->put($path, $body);
    }

    public function doPatch(string $path, array $body = []): array
    {
        return $this->patch($path, $body);
    }

    public function doDelete(string $path, array $query = []): array
    {
        return $this->delete($path, $query);
    }

    public function doPaginate(string $path, array $query = [], string $itemsKey = 'data'): PaginatedResponse
    {
        return $this->paginate($path, $query, $itemsKey);
    }
}

#[CoversClass(AbstractResource::class)]
final class AbstractResourceTest extends TestCase
{
    private ClientInterface $httpClient;
    private AuthenticationInterface $auth;
    private ConcreteResource $resource;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);

        $this->auth = $this->createMock(AuthenticationInterface::class);
        $this->auth->method('authenticate')
            ->willReturnCallback(function (RequestInterface $request): RequestInterface {
                return $request->withHeader('Authorization', 'Bearer test-token');
            });

        $config = new Configuration('https://api.checkfront.com', $this->auth);

        $this->resource = new ConcreteResource(
            $this->httpClient,
            $this->auth,
            $config,
            new RequestFactory(),
            new StreamFactory(),
            new UriFactory(),
        );
    }

    private function createJsonResponse(int $statusCode, array $body, array $headers = []): ResponseInterface
    {
        return new Response(
            $statusCode,
            $headers,
            Stream::create(json_encode($body)),
        );
    }

    #[Test]
    public function getBuildsCorrectUrl(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('GET', $request->getMethod());
                $this->assertSame('https://api.checkfront.com/bookings', (string) $request->getUri());
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, ['data' => []]));

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function getAppendsQueryParameters(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();
                $this->assertStringContainsString('page=1', $uri);
                $this->assertStringContainsString('limit=10', $uri);
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, ['data' => []]));

        $this->resource->doGet('/bookings', ['page' => 1, 'limit' => 10]);
    }

    #[Test]
    public function getSetsAcceptHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('application/json', $request->getHeaderLine('Accept'));
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function getSetsAuthorizationHeader(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('Bearer test-token', $request->getHeaderLine('Authorization'));
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function getReturnsDecodedJsonResponse(): void
    {
        $expectedData = ['id' => 1, 'name' => 'Test Booking'];
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(200, $expectedData));

        $result = $this->resource->doGet('/bookings/1');

        $this->assertSame($expectedData, $result);
    }

    #[Test]
    public function postSendsJsonBody(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('POST', $request->getMethod());
                $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
                $body = json_decode((string) $request->getBody(), true);
                $this->assertSame('value', $body['key']);
                return true;
            }))
            ->willReturn($this->createJsonResponse(201, ['id' => 1]));

        $this->resource->doPost('/bookings', ['key' => 'value']);
    }

    #[Test]
    public function postWithEmptyBodyDoesNotSetContentType(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('POST', $request->getMethod());
                $this->assertFalse($request->hasHeader('Content-Type'));
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doPost('/bookings');
    }

    #[Test]
    public function putSendsCorrectMethod(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('PUT', $request->getMethod());
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doPut('/bookings/1', ['status' => 'confirmed']);
    }

    #[Test]
    public function patchSendsCorrectMethod(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('PATCH', $request->getMethod());
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doPatch('/bookings/1', ['name' => 'Updated']);
    }

    #[Test]
    public function deleteSendsCorrectMethod(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $this->assertSame('DELETE', $request->getMethod());
                $this->assertSame('https://api.checkfront.com/bookings/1', (string) $request->getUri());
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doDelete('/bookings/1');
    }

    #[Test]
    public function deleteAppendsQueryParameters(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();
                $this->assertStringContainsString('force=true', $uri);
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doDelete('/bookings/1', ['force' => 'true']);
    }

    #[Test]
    public function throws400ValidationException(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(400, [
                'message' => 'Validation failed',
                'errors' => ['name' => ['Name is required']],
            ]));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $this->resource->doPost('/bookings', ['incomplete' => true]);
    }

    #[Test]
    public function validationExceptionContainsErrors(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(400, [
                'message' => 'Validation failed',
                'errors' => ['name' => ['Name is required'], 'email' => ['Email is invalid']],
            ]));

        try {
            $this->resource->doPost('/bookings', []);
        } catch (ValidationException $e) {
            $this->assertSame(['name' => ['Name is required'], 'email' => ['Email is invalid']], $e->getErrors());
            $this->assertSame(400, $e->getStatusCode());
            return;
        }

        $this->fail('ValidationException was not thrown.');
    }

    #[Test]
    public function throws401AuthenticationException(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(401, ['message' => 'Invalid credentials']));

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function throws403ForbiddenException(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(403, ['message' => 'Access denied']));

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Access denied');

        $this->resource->doGet('/admin/settings');
    }

    #[Test]
    public function throws404NotFoundException(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(404, ['message' => 'Booking not found']));

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Booking not found');

        $this->resource->doGet('/bookings/nonexistent');
    }

    #[Test]
    public function throws409ConflictException(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(409, ['message' => 'Conflict detected']));

        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Conflict detected');

        $this->resource->doPost('/bookings', ['id' => 'duplicate']);
    }

    #[Test]
    public function throws429RateLimitException(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(429, ['message' => 'Too many requests'], [
                'Retry-After' => '60',
            ]));

        try {
            $this->resource->doGet('/bookings');
        } catch (RateLimitException $e) {
            $this->assertSame('Too many requests', $e->getMessage());
            $this->assertSame(60, $e->getRetryAfter());
            $this->assertSame(429, $e->getStatusCode());
            return;
        }

        $this->fail('RateLimitException was not thrown.');
    }

    #[Test]
    public function throws429WithoutRetryAfterHeader(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(429, ['message' => 'Too many requests']));

        try {
            $this->resource->doGet('/bookings');
        } catch (RateLimitException $e) {
            $this->assertNull($e->getRetryAfter());
            return;
        }

        $this->fail('RateLimitException was not thrown.');
    }

    #[Test]
    public function throwsGenericCheckfrontExceptionForUnknownErrorCodes(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(500, ['message' => 'Internal server error']));

        $this->expectException(CheckfrontException::class);
        $this->expectExceptionMessage('Internal server error');

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function throwsGenericExceptionFor502(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(502, ['error' => 'Bad Gateway']));

        $this->expectException(CheckfrontException::class);
        $this->expectExceptionMessage('Bad Gateway');

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function errorMessageFallsBackToHttpStatus(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(500, []));

        $this->expectException(CheckfrontException::class);
        $this->expectExceptionMessage('HTTP 500 error');

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function errorMessagePrefersMessageOverError(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(500, [
                'message' => 'Specific message',
                'error' => 'Generic error',
            ]));

        $this->expectException(CheckfrontException::class);
        $this->expectExceptionMessage('Specific message');

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function errorMessageFallsToErrorKey(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(500, [
                'error' => 'Something went wrong',
            ]));

        $this->expectException(CheckfrontException::class);
        $this->expectExceptionMessage('Something went wrong');

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function paginateReturnsPaginatedResponse(): void
    {
        $this->httpClient
            ->method('sendRequest')
            ->willReturn($this->createJsonResponse(200, [
                'data' => [['id' => 1], ['id' => 2]],
                'meta' => ['total' => 10, 'current_page' => 1],
            ]));

        $result = $this->resource->doPaginate('/bookings');

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    #[Test]
    public function getWithNoQueryDoesNotAppendQuestionMark(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request): bool {
                $uri = (string) $request->getUri();
                $this->assertStringNotContainsString('?', $uri);
                return true;
            }))
            ->willReturn($this->createJsonResponse(200, []));

        $this->resource->doGet('/bookings');
    }

    #[Test]
    public function getReturnsEmptyArrayWhenResponseBodyIsNotJson(): void
    {
        $response = new Response(
            200,
            [],
            Stream::create('not valid json'),
        );

        $this->httpClient
            ->method('sendRequest')
            ->willReturn($response);

        $result = $this->resource->doGet('/bookings');

        $this->assertSame([], $result);
    }
}
