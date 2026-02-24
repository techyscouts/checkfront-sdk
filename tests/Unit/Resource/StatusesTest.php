<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Statuses;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Statuses::class)]
final class StatusesTest extends ResourceTestCase
{
    private Statuses $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Statuses::class);
    }

    #[Test]
    public function listSendsGetRequestToStatusesEndpoint(): void
    {
        $this->expectRequest('GET', '/statuses', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/statuses', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Confirmed', 'color' => '#00ff00'];
        $this->expectRequestWithBody('POST', '/statuses', $body, ['id' => 1, 'name' => 'Confirmed']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/statuses/42', ['id' => 42, 'name' => 'Confirmed']);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Cancelled'];
        $this->expectRequestWithBody('PATCH', '/statuses/42', $body, ['id' => 42, 'name' => 'Cancelled']);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('Cancelled', $result['name']);
    }
}
