<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductsTest extends FeatureTestCase
{
    #[Test]
    public function listReturnsPaginatedResponse(): void
    {
        $response = self::$client->products()->list();

        $this->assertInstanceOf(PaginatedResponse::class, $response);
        $this->assertIsArray($response->getItems());
        $this->assertIsArray($response->getMeta());
    }

    #[Test]
    public function listItemsHaveExpectedKeys(): void
    {
        $response = self::$client->products()->list();
        $items = $response->getItems();

        if (count($items) === 0) {
            $this->markTestSkipped('No products available to verify structure');
        }

        $first = $items[0];
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('sku', $first);
    }

    #[Test]
    public function fetchReturnsSingleProduct(): void
    {
        $response = self::$client->products()->list();
        $items = $response->getItems();

        if (count($items) === 0) {
            $this->markTestSkipped('No products available to fetch');
        }

        $productId = $items[0]['id'];
        $product = self::$client->products()->fetch((int) $productId);

        $this->assertIsArray($product);
        $this->assertArrayHasKey('data', $product);
    }

    #[Test]
    public function paginationMetadataIsAccessible(): void
    {
        $response = self::$client->products()->list();

        $this->assertIsInt($response->getCurrentPage());
        $this->assertIsBool($response->hasNextPage());

        $total = $response->getTotalCount();
        $this->assertTrue($total === null || is_int($total));
    }
}
