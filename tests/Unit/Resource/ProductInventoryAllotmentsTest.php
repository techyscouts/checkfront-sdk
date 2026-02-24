<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductInventoryAllotments;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ProductInventoryAllotments::class)]
final class ProductInventoryAllotmentsTest extends ResourceTestCase
{
    private ProductInventoryAllotments $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductInventoryAllotments::class);
    }

    #[Test]
    public function listSendsGetRequestToAllotmentsEndpoint(): void
    {
        $this->expectRequest('GET', '/products/5/allotments', $this->paginatedBody());

        $result = $this->resource->list(5);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products/5/allotments', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(5, ['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function removeSendsDeleteRequestWithBody(): void
    {
        $body = ['date' => '2026-03-01'];
        $this->expectRequestWithBody('DELETE', '/products/5/allotments', $body, ['removed' => true]);

        $result = $this->resource->remove(5, $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function createSendsPutRequestWithBody(): void
    {
        $body = ['date' => '2026-03-01', 'quantity' => 10];
        $this->expectRequestWithBody('PUT', '/products/5/allotment', $body, ['id' => 1, 'quantity' => 10]);

        $result = $this->resource->create(5, $body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['quantity' => 20];
        $this->expectRequestWithBody('PATCH', '/products/5/allotment', $body, ['id' => 1, 'quantity' => 20]);

        $result = $this->resource->update(5, $body);

        $this->assertIsArray($result);
        $this->assertSame(20, $result['quantity']);
    }
}
