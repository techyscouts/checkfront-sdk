<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Products;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Products::class)]
final class ProductsTest extends ResourceTestCase
{
    private Products $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Products::class);
    }

    #[Test]
    public function listSendsGetRequestToProductsEndpoint(): void
    {
        $this->expectRequest('GET', '/products', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Test Product', 'price' => 100];
        $this->expectRequestWithBody('POST', '/products', $body, ['id' => 1, 'name' => 'Test Product']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithProductId(): void
    {
        $this->expectRequest('GET', '/products/42', ['id' => 42, 'name' => 'Test Product']);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products/42', ['include' => 'details'], ['id' => 42]);

        $result = $this->resource->fetch(42, ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithProductId(): void
    {
        $this->expectRequest('DELETE', '/products/42', ['archived' => true]);

        $result = $this->resource->archive(42);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Product'];
        $this->expectRequestWithBody('PATCH', '/products/42', $body, ['id' => 42, 'name' => 'Updated Product']);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Product', $result['name']);
    }

    #[Test]
    public function copySendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Copied Product'];
        $this->expectRequestWithBody('POST', '/products/42/copy', $body, ['id' => 43, 'name' => 'Copied Product']);

        $result = $this->resource->copy(42, $body);

        $this->assertIsArray($result);
        $this->assertSame(43, $result['id']);
    }
}
