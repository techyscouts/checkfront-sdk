<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http\Factory;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use TechyScouts\Checkfront\Http\Message\Request;
use TechyScouts\Checkfront\Http\Message\Uri;

final class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }

        return new Request($method, $uri);
    }
}
