<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\BundleImages;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(BundleImages::class)]
final class BundleImagesTest extends ResourceTestCase
{
    private BundleImages $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(BundleImages::class);
    }

    #[Test]
    public function listSendsGetRequestToBundleImagesEndpoint(): void
    {
        $this->expectRequest('GET', '/bundles/8/images', $this->paginatedBody());

        $result = $this->resource->list(8);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bundles/8/images', ['page' => '1'], $this->paginatedBody());

        $result = $this->resource->list(8, ['page' => '1']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function uploadSendsPostRequestWithBody(): void
    {
        $body = ['url' => 'https://example.com/image.jpg', 'caption' => 'Main image'];
        $this->expectRequestWithBody('POST', '/bundles/8/images', $body, ['id' => 1, 'url' => 'https://example.com/image.jpg']);

        $result = $this->resource->upload(8, $body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function removeSendsDeleteRequestWithProductIdAndImageId(): void
    {
        $this->expectRequest('DELETE', '/bundles/8/images/22', ['removed' => true]);

        $result = $this->resource->remove(8, 22);

        $this->assertIsArray($result);
    }
}
