<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ProductDiscounts;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ProductDiscounts::class)]
final class ProductDiscountsTest extends ResourceTestCase
{
    private ProductDiscounts $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ProductDiscounts::class);
    }

    #[Test]
    public function listSendsGetRequestToDiscountsEndpoint(): void
    {
        $this->expectRequest('GET', '/discounts', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/discounts', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Early Bird', 'percentage' => 10];
        $this->expectRequestWithBody('POST', '/discounts', $body, ['id' => 1, 'name' => 'Early Bird']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/discounts/5', ['id' => 5, 'name' => 'Early Bird']);

        $result = $this->resource->fetch(5);

        $this->assertIsArray($result);
        $this->assertSame(5, $result['id']);
    }

    #[Test]
    public function disableSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/discounts/5', ['disabled' => true]);

        $result = $this->resource->disable(5);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['percentage' => 15];
        $this->expectRequestWithBody('PATCH', '/discounts/5', $body, ['id' => 5, 'percentage' => 15]);

        $result = $this->resource->update(5, $body);

        $this->assertIsArray($result);
        $this->assertSame(15, $result['percentage']);
    }
}
