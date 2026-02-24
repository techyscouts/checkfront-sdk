<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductDefaultPricing;

#[CoversClass(ProductDefaultPricing::class)]
final class ProductDefaultPricingTest extends ResourceTestCase
{
    private ProductDefaultPricing $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductDefaultPricing::class);
    }

    #[Test]
    public function fetchSendsGetRequestWithProductId(): void
    {
        $this->expectRequest('GET', '/products/42/pricing', ['product_id' => 42, 'price' => 99.99]);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['product_id']);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['price' => 149.99];
        $this->expectRequestWithBody('PATCH', '/products/42/pricing', $body, ['product_id' => 42, 'price' => 149.99]);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame(149.99, $result['price']);
    }

    #[Test]
    public function copySendsPostRequestWithSourceAndTarget(): void
    {
        $this->expectRequest('POST', '/products/42/pricing/copy/99', ['copied' => true]);

        $result = $this->resource->copy(42, 99);

        $this->assertIsArray($result);
    }
}
