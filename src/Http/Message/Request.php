<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class Request implements RequestInterface
{
    private string $method;
    private UriInterface $uri;
    private string $protocolVersion;
    private StreamInterface $body;
    private string $requestTarget = '';

    /**
     * Map of lowercase header name => original-case header name.
     *
     * @var array<string, string>
     */
    private array $headerNames = [];

    /**
     * Map of original-case header name => list of values.
     *
     * @var array<string, string[]>
     */
    private array $headers = [];

    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(
        string $method,
        UriInterface|string $uri,
        array $headers = [],
        ?StreamInterface $body = null,
        string $protocolVersion = '1.1',
    ) {
        $this->method = $method;
        $this->uri = is_string($uri) ? new Uri($uri) : $uri;
        $this->protocolVersion = $protocolVersion;
        $this->body = $body ?? Stream::create('');

        foreach ($headers as $name => $value) {
            $this->setHeaderInternal($name, $value);
        }

        // Auto-set Host header from URI if not provided
        if (!$this->hasHeader('Host') && $this->uri->getHost() !== '') {
            $this->updateHostFromUri();
        }
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        if ($this->protocolVersion === $version) {
            return $this;
        }

        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headerNames[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        $normalized = strtolower($name);

        if (!isset($this->headerNames[$normalized])) {
            return [];
        }

        return $this->headers[$this->headerNames[$normalized]];
    }

    public function getHeaderLine(string $name): string
    {
        $values = $this->getHeader($name);

        if ($values === []) {
            return '';
        }

        return implode(', ', $values);
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $values = $this->normalizeHeaderValue($value);
        $normalized = strtolower($name);

        $clone = clone $this;

        // Remove existing header with same normalized name but possibly different case
        if (isset($clone->headerNames[$normalized])) {
            unset($clone->headers[$clone->headerNames[$normalized]]);
        }

        $clone->headerNames[$normalized] = $name;
        $clone->headers[$name] = $values;

        return $clone;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $values = $this->normalizeHeaderValue($value);
        $normalized = strtolower($name);

        $clone = clone $this;

        if (isset($clone->headerNames[$normalized])) {
            $originalName = $clone->headerNames[$normalized];
            $clone->headers[$originalName] = array_merge($clone->headers[$originalName], $values);
        } else {
            $clone->headerNames[$normalized] = $name;
            $clone->headers[$name] = $values;
        }

        return $clone;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $normalized = strtolower($name);

        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $clone = clone $this;
        $originalName = $clone->headerNames[$normalized];
        unset($clone->headers[$originalName], $clone->headerNames[$normalized]);

        return $clone;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        if ($body === $this->body) {
            return $this;
        }

        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== '') {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();

        if ($target === '') {
            $target = '/';
        }

        $query = $this->uri->getQuery();

        if ($query !== '') {
            $target .= '?' . $query;
        }

        return $target;
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        if ($this->requestTarget === $requestTarget) {
            return $this;
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        if ($method === '') {
            throw new InvalidArgumentException('HTTP method must not be empty.');
        }

        if ($this->method === $method) {
            return $this;
        }

        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        if ($uri === $this->uri) {
            return $this;
        }

        $clone = clone $this;
        $clone->uri = $uri;

        if (!$preserveHost || !$clone->hasHeader('Host')) {
            $clone->updateHostFromUri();
        }

        return $clone;
    }

    /**
     * @param string|string[] $value
     * @return string[]
     */
    private function normalizeHeaderValue(string|array $value): array
    {
        $values = is_array($value) ? $value : [$value];

        if ($values === []) {
            throw new InvalidArgumentException('Header value must not be an empty array.');
        }

        return array_map(static fn (string $v): string => trim($v, " \t"), $values);
    }

    private function setHeaderInternal(string $name, string|array $value): void
    {
        $values = $this->normalizeHeaderValue($value);
        $normalized = strtolower($name);

        if (isset($this->headerNames[$normalized])) {
            $originalName = $this->headerNames[$normalized];
            $this->headers[$originalName] = array_merge($this->headers[$originalName], $values);
        } else {
            $this->headerNames[$normalized] = $name;
            $this->headers[$name] = $values;
        }
    }

    private function updateHostFromUri(): void
    {
        $host = $this->uri->getHost();

        if ($host === '') {
            return;
        }

        $port = $this->uri->getPort();

        if ($port !== null) {
            $host .= ':' . $port;
        }

        // Remove existing Host header (preserving case-insensitive lookup)
        $normalized = 'host';

        if (isset($this->headerNames[$normalized])) {
            unset($this->headers[$this->headerNames[$normalized]]);
        }

        $this->headerNames[$normalized] = 'Host';

        // Host header should be the first header; rebuild the array with Host first
        $this->headers = ['Host' => [$host]] + $this->headers;
    }
}
