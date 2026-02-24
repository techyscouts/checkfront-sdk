<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http;

use Psr\Http\Client\ClientInterface;

final class HttpClientDiscovery
{
    public static function discover(): ClientInterface
    {
        if (class_exists(\GuzzleHttp\Client::class)) {
            /** @phpstan-ignore return.type */
            return new \GuzzleHttp\Client();
        }

        if (class_exists(\Symfony\Component\HttpClient\Psr18Client::class)) {
            /** @phpstan-ignore return.type */
            return new \Symfony\Component\HttpClient\Psr18Client();
        }

        return new CurlHttpClient();
    }
}
