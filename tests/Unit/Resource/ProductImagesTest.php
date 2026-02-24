<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductImages;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ProductImages::class)]
final class ProductImagesTest extends ResourceTestCase
{
    private ProductImages $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductImages::class);
    }

    #[Test]
    public function listSendsGetRequestToProductImagesEndpoint(): void
    {
        $this->expectRequest('GET', '/products/42/images', $this->paginatedBody());

        $result = $this->resource->list(42);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products/42/images', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(42, ['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function uploadSendsPostRequestWithBody(): void
    {
        $body = ['url' => 'https://example.com/image.jpg', 'caption' => 'Product photo'];
        $this->expectRequestWithBody('POST', '/products/42/images', $body, ['id' => 'img-1', 'url' => 'https://example.com/image.jpg']);

        $result = $this->resource->upload(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('img-1', $result['id']);
    }

    #[Test]
    public function removeSendsDeleteRequestWithProductAndImageId(): void
    {
        $this->expectRequest('DELETE', '/products/42/images/img-1', ['deleted' => true]);

        $result = $this->resource->remove(42, 'img-1');

        $this->assertIsArray($result);
    }

    #[Test]
    public function copySendsPostRequestWithSourceAndTarget(): void
    {
        $this->expectRequest('POST', '/products/42/images/copy/99', ['copied' => true]);

        $result = $this->resource->copy(42, 99);

        $this->assertIsArray($result);
    }
}
