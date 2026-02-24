<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\AssetEvents;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(AssetEvents::class)]
final class AssetEventsTest extends ResourceTestCase
{
    private AssetEvents $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(AssetEvents::class);
    }

    #[Test]
    public function listSendsGetRequestToAssetEventsEndpoint(): void
    {
        $this->expectRequest('GET', '/assetpools/5/assets/10/events', $this->paginatedBody());

        $result = $this->resource->list(5, 10);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpools/5/assets/10/events', ['page' => '2'], $this->paginatedBody());

        $result = $this->resource->list(5, 10, ['page' => '2']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'New Event', 'date' => '2026-03-01'];
        $this->expectRequestWithBody('POST', '/assetpools/5/assets/10/events', $body, ['id' => 1]);

        $result = $this->resource->create(5, 10, $body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithAllIds(): void
    {
        $this->expectRequest('GET', '/assetpools/5/assets/10/events/99', ['id' => 99]);

        $result = $this->resource->fetch(5, 10, 99);

        $this->assertIsArray($result);
        $this->assertSame(99, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpools/5/assets/10/events/99', ['include' => 'details'], ['id' => 99]);

        $result = $this->resource->fetch(5, 10, 99, ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithAllIds(): void
    {
        $this->expectRequest('DELETE', '/assetpools/5/assets/10/events/99', ['archived' => true]);

        $result = $this->resource->archive(5, 10, 99);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Event'];
        $this->expectRequestWithBody('PATCH', '/assetpools/5/assets/10/events/99', $body, ['id' => 99, 'name' => 'Updated Event']);

        $result = $this->resource->update(5, 10, 99, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Event', $result['name']);
    }

    #[Test]
    public function listForPoolSendsGetRequestToPoolEventsEndpoint(): void
    {
        $this->expectRequest('GET', '/assetpools/5/events', $this->paginatedBody());

        $result = $this->resource->listForPool(5);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForPoolForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpools/5/events', ['page' => '3'], $this->paginatedBody());

        $result = $this->resource->listForPool(5, ['page' => '3']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }
}
