<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductAvailability;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ProductAvailability::class)]
final class ProductAvailabilityTest extends ResourceTestCase
{
    private ProductAvailability $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductAvailability::class);
    }

    #[Test]
    public function listAllSendsGetRequestToProductsInventoryEndpoint(): void
    {
        $this->expectRequest('GET', '/products-inventory/', $this->paginatedBody());

        $result = $this->resource->listAll();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listAllForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products-inventory/', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->listAll(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function fetchForProductSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/products-inventory/42', ['id' => 42, 'available' => true]);

        $result = $this->resource->fetchForProduct(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function fetchForProductForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products-inventory/42', ['date' => '2026-03-01'], ['id' => 42]);

        $result = $this->resource->fetchForProduct(42, ['date' => '2026-03-01']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function fetchInventorySendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/inventory/42', ['id' => 42, 'slots' => 10]);

        $result = $this->resource->fetchInventory(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function fetchInventoryForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/inventory/42', ['date' => '2026-03-01'], ['id' => 42]);

        $result = $this->resource->fetchInventory(42, ['date' => '2026-03-01']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function fetchBookingCountsSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/inventory/42/booking-counts', ['id' => 42, 'count' => 5]);

        $result = $this->resource->fetchBookingCounts(42);

        $this->assertIsArray($result);
        $this->assertSame(5, $result['count']);
    }

    #[Test]
    public function fetchBookingCountsForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/inventory/42/booking-counts', ['date' => '2026-03-01'], ['id' => 42]);

        $result = $this->resource->fetchBookingCounts(42, ['date' => '2026-03-01']);

        $this->assertIsArray($result);
    }
}
