<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductResources;

#[CoversClass(ProductResources::class)]
final class ProductResourcesTest extends ResourceTestCase
{
    private ProductResources $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductResources::class);
    }

    #[Test]
    public function listSendsGetRequestToResourcesEndpoint(): void
    {
        $this->expectRequest('GET', '/products/5/resources', ['resources' => []]);

        $result = $this->resource->list(5);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['resource_ids' => [1, 2, 3]];
        $this->expectRequestWithBody('PATCH', '/products/5/resources', $body, ['updated' => true]);

        $result = $this->resource->update(5, $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function copySendsPostRequestToCorrectPath(): void
    {
        $this->expectRequest('POST', '/products/5/resources/copy/10', ['copied' => true]);

        $result = $this->resource->copy(5, 10);

        $this->assertIsArray($result);
    }
}
