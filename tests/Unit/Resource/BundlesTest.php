<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Bundles;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Bundles::class)]
final class BundlesTest extends ResourceTestCase
{
    private Bundles $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Bundles::class);
    }

    #[Test]
    public function listSendsGetRequestToBundlesEndpoint(): void
    {
        $this->expectRequest('GET', '/bundles', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bundles', ['page' => '1', 'limit' => '50'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '50']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Weekend Bundle', 'price' => 199];
        $this->expectRequestWithBody('POST', '/bundles', $body, ['id' => 1, 'name' => 'Weekend Bundle']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithProductId(): void
    {
        $this->expectRequest('GET', '/bundles/12', ['id' => 12, 'name' => 'Weekend Bundle']);

        $result = $this->resource->fetch(12);

        $this->assertIsArray($result);
        $this->assertSame(12, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bundles/12', ['include' => 'items'], ['id' => 12]);

        $result = $this->resource->fetch(12, ['include' => 'items']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithProductId(): void
    {
        $this->expectRequest('DELETE', '/bundles/12', ['archived' => true]);

        $result = $this->resource->archive(12);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Bundle', 'price' => 249];
        $this->expectRequestWithBody('PATCH', '/bundles/12', $body, ['id' => 12, 'name' => 'Updated Bundle']);

        $result = $this->resource->update(12, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Bundle', $result['name']);
    }
}
