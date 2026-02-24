<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http\Factory;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use TechyScouts\Checkfront\Http\Message\Uri;

final class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
