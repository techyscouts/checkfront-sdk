<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductGuestTypeMaps;

#[CoversClass(ProductGuestTypeMaps::class)]
final class ProductGuestTypeMapsTest extends ResourceTestCase
{
    private ProductGuestTypeMaps $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductGuestTypeMaps::class);
    }

    #[Test]
    public function fetchSendsGetRequestWithParentProductId(): void
    {
        $this->expectRequest('GET', '/products/42/guestMaps', ['product_id' => 42, 'maps' => []]);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['product_id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products/42/guestMaps', ['include' => 'details'], ['product_id' => 42]);

        $result = $this->resource->fetch(42, ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeSendsDeleteRequestWithParentProductId(): void
    {
        $this->expectRequest('DELETE', '/products/42/guestMaps', ['deleted' => true]);

        $result = $this->resource->remove(42);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('DELETE', '/products/42/guestMaps', ['guest_type_id' => 'gt-1'], ['deleted' => true]);

        $result = $this->resource->remove(42, ['guest_type_id' => 'gt-1']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function replaceSendsPatchRequestWithBody(): void
    {
        $body = ['maps' => [['guest_type_id' => 'gt-1', 'min' => 1, 'max' => 10]]];
        $this->expectRequestWithBody('PATCH', '/products/42/guestMaps', $body, ['product_id' => 42, 'maps' => $body['maps']]);

        $result = $this->resource->replace(42, $body);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['product_id']);
    }
}
