<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\Configuration;
use TechyScouts\Checkfront\Exception\AuthenticationException;
use TechyScouts\Checkfront\Exception\CheckfrontException;
use TechyScouts\Checkfront\Exception\ConflictException;
use TechyScouts\Checkfront\Exception\ForbiddenException;
use TechyScouts\Checkfront\Exception\NotFoundException;
use TechyScouts\Checkfront\Exception\RateLimitException;
use TechyScouts\Checkfront\Exception\ValidationException;
use TechyScouts\Checkfront\Response\PaginatedResponse;

abstract class AbstractResource
{
    public function __construct(
        protected readonly ClientInterface $httpClient,
        protected readonly AuthenticationInterface $auth,
        protected readonly Configuration $configuration,
        protected readonly RequestFactoryInterface $requestFactory,
        protected readonly StreamFactoryInterface $streamFactory,
        protected readonly UriFactoryInterface $uriFactory,
    ) {
    }

    protected function get(string $path, array $query = []): array
    {
        return $this->sendRequest('GET', $path, $query);
    }

    protected function post(string $path, array $body = []): array
    {
        return $this->sendRequest('POST', $path, [], $body);
    }

    protected function put(string $path, array $body = []): array
    {
        return $this->sendRequest('PUT', $path, [], $body);
    }

    protected function patch(string $path, array $body = []): array
    {
        return $this->sendRequest('PATCH', $path, [], $body);
    }

    protected function delete(string $path, array $query = []): array
    {
        return $this->sendRequest('DELETE', $path, $query);
    }

    protected function paginate(string $path, array $query = [], string $itemsKey = 'data'): PaginatedResponse
    {
        $data = $this->get($path, $query);

        $nextPageFetcher = function (string $url): array {
            $request = $this->requestFactory->createRequest('GET', $url);
            $request = $request->withHeader('Accept', 'application/json');
            $request = $this->auth->authenticate($request);

            $response = $this->httpClient->sendRequest($request);
            $body = (string) $response->getBody();
            $decoded = json_decode($body, true) ?? [];

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 400) {
                $this->throwForStatusCode($statusCode, $response, $decoded);
            }

            return $decoded;
        };

        return new PaginatedResponse($data, $itemsKey, $nextPageFetcher);
    }

    protected function sendRequest(string $method, string $path, array $query = [], array $body = []): array
    {
        $url = $this->configuration->getHost() . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $request = $this->requestFactory->createRequest($method, $url);
        $request = $request->withHeader('Accept', 'application/json');

        if (!empty($body)) {
            $stream = $this->streamFactory->createStream(json_encode($body));
            $request = $request->withBody($stream);
            $request = $request->withHeader('Content-Type', 'application/json');
        }

        $request = $this->auth->authenticate($request);

        $response = $this->httpClient->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $decoded = json_decode($responseBody, true) ?? [];

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 400) {
            $this->throwForStatusCode($statusCode, $response, $decoded);
        }

        return $decoded;
    }

    /**
     * @throws CheckfrontException
     */
    private function throwForStatusCode(int $statusCode, \Psr\Http\Message\ResponseInterface $response, array $decoded): never
    {
        $message = $decoded['message'] ?? $decoded['error'] ?? "HTTP {$statusCode} error";

        match ($statusCode) {
            400 => throw new ValidationException(
                $message,
                $statusCode,
                $decoded,
                $decoded['errors'] ?? [],
            ),
            401 => throw new AuthenticationException($message, $statusCode, $decoded),
            403 => throw new ForbiddenException($message, $statusCode, $decoded),
            404 => throw new NotFoundException($message, $statusCode, $decoded),
            409 => throw new ConflictException($message, $statusCode, $decoded),
            429 => throw new RateLimitException(
                $message,
                $statusCode,
                $decoded,
                $this->parseRetryAfter($response),
            ),
            default => throw new CheckfrontException($message, $statusCode, $decoded),
        };
    }

    private function parseRetryAfter(\Psr\Http\Message\ResponseInterface $response): ?int
    {
        $retryAfter = $response->getHeaderLine('Retry-After');

        if ($retryAfter === '') {
            return null;
        }

        return (int) $retryAfter;
    }
}
