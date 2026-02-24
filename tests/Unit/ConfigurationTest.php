<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\Configuration;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    #[Test]
    public function constructorSetsHost(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertSame('https://api.checkfront.com', $config->getHost());
    }

    #[Test]
    public function constructorStripsTrailingSlashFromHost(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com/', $auth);

        $this->assertSame('https://api.checkfront.com', $config->getHost());
    }

    #[Test]
    public function constructorStripsMultipleTrailingSlashes(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com///', $auth);

        $this->assertSame('https://api.checkfront.com', $config->getHost());
    }

    #[Test]
    public function getAuthReturnsAuthInstance(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertSame($auth, $config->getAuth());
    }

    #[Test]
    public function getHttpClientReturnsNullByDefault(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertNull($config->getHttpClient());
    }

    #[Test]
    public function getHttpClientReturnsProvidedClient(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $client = $this->createMock(ClientInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth, $client);

        $this->assertSame($client, $config->getHttpClient());
    }

    #[Test]
    public function getRequestFactoryReturnsNullByDefault(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertNull($config->getRequestFactory());
    }

    #[Test]
    public function getRequestFactoryReturnsProvidedFactory(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $factory = $this->createMock(RequestFactoryInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth, null, $factory);

        $this->assertSame($factory, $config->getRequestFactory());
    }

    #[Test]
    public function getStreamFactoryReturnsNullByDefault(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertNull($config->getStreamFactory());
    }

    #[Test]
    public function getStreamFactoryReturnsProvidedFactory(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $factory = $this->createMock(StreamFactoryInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth, null, null, $factory);

        $this->assertSame($factory, $config->getStreamFactory());
    }

    #[Test]
    public function getUriFactoryReturnsNullByDefault(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertNull($config->getUriFactory());
    }

    #[Test]
    public function getUriFactoryReturnsProvidedFactory(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $factory = $this->createMock(UriFactoryInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth, null, null, null, $factory);

        $this->assertSame($factory, $config->getUriFactory());
    }

    #[Test]
    public function getTimeoutReturnsDefaultOf30(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth);

        $this->assertSame(30, $config->getTimeout());
    }

    #[Test]
    public function getTimeoutReturnsCustomValue(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $config = new Configuration('https://api.checkfront.com', $auth, null, null, null, null, 60);

        $this->assertSame(60, $config->getTimeout());
    }
}
