<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductEvents;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ProductEvents::class)]
final class ProductEventsTest extends ResourceTestCase
{
    private ProductEvents $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductEvents::class);
    }

    #[Test]
    public function listSendsGetRequestToProductEventsEndpoint(): void
    {
        $this->expectRequest('GET', '/products/42/events', $this->paginatedBody());

        $result = $this->resource->list(42);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Morning Session', 'start_time' => '09:00'];
        $this->expectRequestWithBody('POST', '/products/42/events', $body, ['id' => 1, 'name' => 'Morning Session']);

        $result = $this->resource->create(42, $body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithProductAndEventId(): void
    {
        $this->expectRequest('GET', '/products/42/events/7', ['id' => 7, 'name' => 'Morning Session']);

        $result = $this->resource->fetch(42, 7);

        $this->assertIsArray($result);
        $this->assertSame(7, $result['id']);
    }

    #[Test]
    public function removeSendsDeleteRequestWithProductAndEventId(): void
    {
        $this->expectRequest('DELETE', '/products/42/events/7', ['deleted' => true]);

        $result = $this->resource->remove(42, 7);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Afternoon Session'];
        $this->expectRequestWithBody('PATCH', '/products/42/events/7', $body, ['id' => 7, 'name' => 'Afternoon Session']);

        $result = $this->resource->update(42, 7, $body);

        $this->assertIsArray($result);
        $this->assertSame('Afternoon Session', $result['name']);
    }

    #[Test]
    public function copySendsPostRequestWithSourceAndTarget(): void
    {
        $this->expectRequest('POST', '/products/42/events/copy/99', ['copied' => true]);

        $result = $this->resource->copy(42, 99);

        $this->assertIsArray($result);
    }
}
