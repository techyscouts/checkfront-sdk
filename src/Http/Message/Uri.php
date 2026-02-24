<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http\Message;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
    private const array DEFAULT_PORTS = [
        'http' => 80,
        'https' => 443,
    ];

    private string $scheme = '';
    private string $userInfo = '';
    private string $host = '';
    private ?int $port = null;
    private string $path = '';
    private string $query = '';
    private string $fragment = '';

    public function __construct(string $uri = '')
    {
        if ($uri === '') {
            return;
        }

        $parts = parse_url($uri);

        if ($parts === false) {
            throw new InvalidArgumentException("Unable to parse URI: {$uri}");
        }

        $this->scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        $this->host = isset($parts['host']) ? strtolower($parts['host']) : '';
        $this->port = isset($parts['port']) ? $this->filterPort($parts['port']) : null;
        $this->path = isset($parts['path']) ? $this->filterPath($parts['path']) : '';
        $this->query = isset($parts['query']) ? $this->filterQueryOrFragment($parts['query']) : '';
        $this->fragment = isset($parts['fragment']) ? $this->filterQueryOrFragment($parts['fragment']) : '';

        $user = $parts['user'] ?? '';
        $password = $parts['pass'] ?? null;

        if ($user !== '') {
            $this->userInfo = $password !== null ? "{$user}:{$password}" : $user;
        }
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        if ($this->host === '') {
            return '';
        }

        $authority = $this->host;

        if ($this->userInfo !== '') {
            $authority = $this->userInfo . '@' . $authority;
        }

        if ($this->port !== null && !$this->isDefaultPort()) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        if ($this->port !== null && $this->isDefaultPort()) {
            return null;
        }

        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): UriInterface
    {
        $scheme = strtolower($scheme);

        if ($this->scheme === $scheme) {
            return $this;
        }

        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $userInfo = $user;

        if ($user !== '' && $password !== null && $password !== '') {
            $userInfo .= ':' . $password;
        }

        if ($this->userInfo === $userInfo) {
            return $this;
        }

        $clone = clone $this;
        $clone->userInfo = $userInfo;

        return $clone;
    }

    public function withHost(string $host): UriInterface
    {
        $host = strtolower($host);

        if ($this->host === $host) {
            return $this;
        }

        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    public function withPort(?int $port): UriInterface
    {
        if ($port !== null) {
            $port = $this->filterPort($port);
        }

        if ($this->port === $port) {
            return $this;
        }

        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    public function withPath(string $path): UriInterface
    {
        $path = $this->filterPath($path);

        if ($this->path === $path) {
            return $this;
        }

        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function withQuery(string $query): UriInterface
    {
        $query = $this->filterQueryOrFragment($query);

        if ($this->query === $query) {
            return $this;
        }

        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $fragment = $this->filterQueryOrFragment($fragment);

        if ($this->fragment === $fragment) {
            return $this;
        }

        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    public function __toString(): string
    {
        $uri = '';

        if ($this->scheme !== '') {
            $uri .= $this->scheme . ':';
        }

        $authority = $this->getAuthority();

        if ($authority !== '') {
            $uri .= '//' . $authority;
        }

        $path = $this->path;

        if ($authority !== '' && ($path === '' || $path[0] !== '/')) {
            // If authority is present, path must be empty or begin with "/"
            $path = '/' . $path;
        } elseif ($authority === '' && str_starts_with($path, '//')) {
            // If no authority, path must not start with "//"
            $path = '/' . ltrim($path, '/');
        }

        $uri .= $path;

        if ($this->query !== '') {
            $uri .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uri .= '#' . $this->fragment;
        }

        return $uri;
    }

    private function isDefaultPort(): bool
    {
        if ($this->scheme === '' || $this->port === null) {
            return false;
        }

        return (self::DEFAULT_PORTS[$this->scheme] ?? null) === $this->port;
    }

    private function filterPort(int $port): int
    {
        if ($port < 0 || $port > 65535) {
            throw new InvalidArgumentException(
                "Invalid port: {$port}. Must be between 0 and 65535."
            );
        }

        return $port;
    }

    private function filterPath(string $path): string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-.~!$&\'()*+,;=:@\/%]|%(?![A-Fa-f0-9]{2}))/',
            static fn (array $match): string => rawurlencode($match[0]),
            $path,
        );
    }

    private function filterQueryOrFragment(string $value): string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-.~!$&\'()*+,;=:@\/?%]|%(?![A-Fa-f0-9]{2}))/',
            static fn (array $match): string => rawurlencode($match[0]),
            $value,
        );
    }
}
