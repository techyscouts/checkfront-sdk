<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\CustomRates;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(CustomRates::class)]
final class CustomRatesTest extends ResourceTestCase
{
    private CustomRates $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(CustomRates::class);
    }

    #[Test]
    public function listSendsGetRequestToCustomRatesEndpoint(): void
    {
        $this->expectRequest('GET', '/custom-rates', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/custom-rates', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Weekend Rate', 'multiplier' => 1.5];
        $this->expectRequestWithBody('POST', '/custom-rates', $body, ['id' => 1, 'name' => 'Weekend Rate']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithCustomRateId(): void
    {
        $this->expectRequest('GET', '/custom-rates/8', ['id' => 8, 'name' => 'Weekend Rate']);

        $result = $this->resource->fetch(8);

        $this->assertIsArray($result);
        $this->assertSame(8, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/custom-rates/8', ['include' => 'items'], ['id' => 8]);

        $result = $this->resource->fetch(8, ['include' => 'items']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function disableSendsDeleteRequestWithCustomRateId(): void
    {
        $this->expectRequest('DELETE', '/custom-rates/8', ['disabled' => true]);

        $result = $this->resource->disable(8);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Rate'];
        $this->expectRequestWithBody('PATCH', '/custom-rates/8', $body, ['id' => 8, 'name' => 'Updated Rate']);

        $result = $this->resource->update(8, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Rate', $result['name']);
    }
}
