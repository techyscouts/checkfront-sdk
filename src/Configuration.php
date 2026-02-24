<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;

final class Configuration
{
    private readonly string $host;

    public function __construct(
        string $host,
        private readonly AuthenticationInterface $auth,
        private readonly ?ClientInterface $httpClient = null,
        private readonly ?RequestFactoryInterface $requestFactory = null,
        private readonly ?StreamFactoryInterface $streamFactory = null,
        private readonly ?UriFactoryInterface $uriFactory = null,
        private readonly int $timeout = 30,
    ) {
        $this->host = rtrim($host, '/');
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getAuth(): AuthenticationInterface
    {
        return $this->auth;
    }

    public function getHttpClient(): ?ClientInterface
    {
        return $this->httpClient;
    }

    public function getRequestFactory(): ?RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    public function getStreamFactory(): ?StreamFactoryInterface
    {
        return $this->streamFactory;
    }

    public function getUriFactory(): ?UriFactoryInterface
    {
        return $this->uriFactory;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
