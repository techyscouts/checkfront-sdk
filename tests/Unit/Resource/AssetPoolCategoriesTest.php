<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\AssetPoolCategories;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(AssetPoolCategories::class)]
final class AssetPoolCategoriesTest extends ResourceTestCase
{
    private AssetPoolCategories $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(AssetPoolCategories::class);
    }

    #[Test]
    public function listSendsGetRequestToAssetPoolCategoriesEndpoint(): void
    {
        $this->expectRequest('GET', '/assetpool-categories', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpool-categories', ['page' => '1', 'limit' => '25'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '25']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'New Category'];
        $this->expectRequestWithBody('POST', '/assetpool-categories', $body, ['id' => 1, 'name' => 'New Category']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/assetpool-categories/7', ['id' => 7, 'name' => 'Category A']);

        $result = $this->resource->fetch(7);

        $this->assertIsArray($result);
        $this->assertSame(7, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpool-categories/7', ['include' => 'pools'], ['id' => 7]);

        $result = $this->resource->fetch(7, ['include' => 'pools']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Category'];
        $this->expectRequestWithBody('PATCH', '/assetpool-categories/7', $body, ['id' => 7, 'name' => 'Updated Category']);

        $result = $this->resource->update(7, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Category', $result['name']);
    }
}
