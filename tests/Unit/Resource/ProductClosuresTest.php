<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductClosures;

#[CoversClass(ProductClosures::class)]
final class ProductClosuresTest extends ResourceTestCase
{
    private ProductClosures $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductClosures::class);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['product_id' => 42, 'start_date' => '2026-03-01', 'end_date' => '2026-03-05'];
        $this->expectRequestWithBody('POST', '/closures', $body, ['id' => 1, 'product_id' => 42]);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function disableSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/closures/7', ['disabled' => true]);

        $result = $this->resource->disable(7);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['end_date' => '2026-03-10'];
        $this->expectRequestWithBody('PATCH', '/closures/7', $body, ['id' => 7, 'end_date' => '2026-03-10']);

        $result = $this->resource->update(7, $body);

        $this->assertIsArray($result);
        $this->assertSame(7, $result['id']);
    }
}
