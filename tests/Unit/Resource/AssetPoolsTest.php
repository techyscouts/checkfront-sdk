<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\AssetPools;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(AssetPools::class)]
final class AssetPoolsTest extends ResourceTestCase
{
    private AssetPools $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(AssetPools::class);
    }

    #[Test]
    public function listSendsGetRequestToAssetPoolsEndpoint(): void
    {
        $this->expectRequest('GET', '/assetpools', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpools', ['page' => '1'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'New Pool', 'capacity' => 10];
        $this->expectRequestWithBody('POST', '/assetpools', $body, ['id' => 1, 'name' => 'New Pool']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithAssetPoolId(): void
    {
        $this->expectRequest('GET', '/assetpools/3', ['id' => 3, 'name' => 'Pool A']);

        $result = $this->resource->fetch(3);

        $this->assertIsArray($result);
        $this->assertSame(3, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpools/3', ['include' => 'assets'], ['id' => 3]);

        $result = $this->resource->fetch(3, ['include' => 'assets']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithAssetPoolId(): void
    {
        $this->expectRequest('DELETE', '/assetpools/3', ['archived' => true]);

        $result = $this->resource->archive(3);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Pool'];
        $this->expectRequestWithBody('PATCH', '/assetpools/3', $body, ['id' => 3, 'name' => 'Updated Pool']);

        $result = $this->resource->update(3, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Pool', $result['name']);
    }

    #[Test]
    public function addAssetsSendsPostRequestWithBody(): void
    {
        $body = ['asset_ids' => [10, 20, 30]];
        $this->expectRequestWithBody('POST', '/assetpools/3/assets', $body, ['added' => 3]);

        $result = $this->resource->addAssets(3, $body);

        $this->assertIsArray($result);
        $this->assertSame(3, $result['added']);
    }

    #[Test]
    public function fetchAssetSendsGetRequestWithBothIds(): void
    {
        $this->expectRequest('GET', '/assetpools/3/assets/15', ['id' => 15, 'pool_id' => 3]);

        $result = $this->resource->fetchAsset(3, 15);

        $this->assertIsArray($result);
        $this->assertSame(15, $result['id']);
    }

    #[Test]
    public function fetchAssetForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/assetpools/3/assets/15', ['include' => 'events'], ['id' => 15]);

        $result = $this->resource->fetchAsset(3, 15, ['include' => 'events']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveAssetSendsDeleteRequestWithBothIds(): void
    {
        $this->expectRequest('DELETE', '/assetpools/3/assets/15', ['archived' => true]);

        $result = $this->resource->archiveAsset(3, 15);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateAssetSendsPatchRequestWithBody(): void
    {
        $body = ['status' => 'active'];
        $this->expectRequestWithBody('PATCH', '/assetpools/3/assets/15', $body, ['id' => 15, 'status' => 'active']);

        $result = $this->resource->updateAsset(3, 15, $body);

        $this->assertIsArray($result);
        $this->assertSame('active', $result['status']);
    }
}
