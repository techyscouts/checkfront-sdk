<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Categories;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Categories::class)]
final class CategoriesTest extends ResourceTestCase
{
    private Categories $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Categories::class);
    }

    #[Test]
    public function listSendsGetRequestToCategoriesEndpoint(): void
    {
        $this->expectRequest('GET', '/categories', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/categories', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Test Category'];
        $this->expectRequestWithBody('POST', '/categories', $body, ['id' => 1, 'name' => 'Test Category']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/categories/42', ['id' => 42, 'name' => 'Test Category']);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/categories/42', ['include' => 'details'], ['id' => 42]);

        $result = $this->resource->fetch(42, ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function disableSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/categories/42', ['disabled' => true]);

        $result = $this->resource->disable(42);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Category'];
        $this->expectRequestWithBody('PATCH', '/categories/42', $body, ['id' => 42, 'name' => 'Updated Category']);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Category', $result['name']);
    }
}
