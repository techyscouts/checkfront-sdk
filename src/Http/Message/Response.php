<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Response implements ResponseInterface
{
    /** @var array<int, string> */
    private const array REASON_PHRASES = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        204 => 'No Content',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        409 => 'Conflict',
        429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
    ];

    private int $statusCode;
    private string $reasonPhrase;
    private string $protocolVersion;
    private StreamInterface $body;

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
        int $statusCode = 200,
        array $headers = [],
        ?StreamInterface $body = null,
        string $reasonPhrase = '',
        string $protocolVersion = '1.1',
    ) {
        $this->statusCode = $this->filterStatusCode($statusCode);
        $this->reasonPhrase = $reasonPhrase !== '' ? $reasonPhrase : (self::REASON_PHRASES[$statusCode] ?? '');
        $this->protocolVersion = $protocolVersion;
        $this->body = $body ?? Stream::create('');

        foreach ($headers as $name => $value) {
            $this->setHeaderInternal($name, $value);
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

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $code = $this->filterStatusCode($code);

        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase !== '' ? $reasonPhrase : (self::REASON_PHRASES[$code] ?? '');

        return $clone;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    private function filterStatusCode(int $code): int
    {
        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException(
                "Invalid HTTP status code: {$code}. Must be between 100 and 599."
            );
        }

        return $code;
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
}
