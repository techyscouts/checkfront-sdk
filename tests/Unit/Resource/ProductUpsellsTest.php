<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductUpsells;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ProductUpsells::class)]
final class ProductUpsellsTest extends ResourceTestCase
{
    private ProductUpsells $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductUpsells::class);
    }

    #[Test]
    public function listSendsGetRequestToUpsellsEndpoint(): void
    {
        $this->expectRequest('GET', '/products/5/upsells', $this->paginatedBody());

        $result = $this->resource->list('5');

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/products/5/upsells', ['page' => '1'], $this->paginatedBody());

        $result = $this->resource->list('5', ['page' => '1']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function replaceSendsPutRequestWithBody(): void
    {
        $body = ['addon_ids' => ['10', '20']];
        $this->expectRequestWithBody('PUT', '/products/5/upsells', $body, ['replaced' => true]);

        $result = $this->resource->replace('5', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function fetchSendsGetRequestWithProductIdAndAddonId(): void
    {
        $this->expectRequest('GET', '/products/5/upsells/10', ['id' => '10', 'name' => 'Test Addon']);

        $result = $this->resource->fetch('5', '10');

        $this->assertIsArray($result);
        $this->assertSame('10', $result['id']);
    }

    #[Test]
    public function assignSendsPostRequestWithBody(): void
    {
        $body = ['priority' => 1];
        $this->expectRequestWithBody('POST', '/products/5/upsells/10', $body, ['id' => '10', 'assigned' => true]);

        $result = $this->resource->assign('5', '10', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeSendsDeleteRequestWithProductIdAndAddonId(): void
    {
        $this->expectRequest('DELETE', '/products/5/upsells/10', ['removed' => true]);

        $result = $this->resource->remove('5', '10');

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['priority' => 2];
        $this->expectRequestWithBody('PATCH', '/products/5/upsells/10', $body, ['id' => '10', 'priority' => 2]);

        $result = $this->resource->update('5', '10', $body);

        $this->assertIsArray($result);
        $this->assertSame(2, $result['priority']);
    }

    #[Test]
    public function copySendsPostRequestToCorrectPath(): void
    {
        $this->expectRequest('POST', '/products/5/upsells/copy/10', ['copied' => true]);

        $result = $this->resource->copy('5', '10');

        $this->assertIsArray($result);
    }
}
