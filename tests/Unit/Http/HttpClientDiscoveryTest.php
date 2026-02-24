<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use TechyScouts\Checkfront\Http\CurlHttpClient;
use TechyScouts\Checkfront\Http\HttpClientDiscovery;

#[CoversClass(HttpClientDiscovery::class)]
final class HttpClientDiscoveryTest extends TestCase
{
    #[Test]
    public function discoverReturnsClientInterface(): void
    {
        $client = HttpClientDiscovery::discover();

        $this->assertInstanceOf(ClientInterface::class, $client);
    }

    #[Test]
    public function discoverReturnsCurlHttpClientWhenNoOtherClientsAvailable(): void
    {
        // When neither Guzzle nor Symfony HTTP client are installed,
        // the fallback is CurlHttpClient. Since this is a standalone SDK
        // test suite without Guzzle or Symfony, we expect CurlHttpClient.
        if (class_exists(\GuzzleHttp\Client::class) || class_exists(\Symfony\Component\HttpClient\Psr18Client::class)) {
            $this->markTestSkipped('Guzzle or Symfony HTTP client is installed; cannot test CurlHttpClient fallback.');
        }

        $client = HttpClientDiscovery::discover();

        $this->assertInstanceOf(CurlHttpClient::class, $client);
    }

    #[Test]
    public function discoverAlwaysReturnsNewInstance(): void
    {
        $client1 = HttpClientDiscovery::discover();
        $client2 = HttpClientDiscovery::discover();

        $this->assertNotSame($client1, $client2);
    }
}
