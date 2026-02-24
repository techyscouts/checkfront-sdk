<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ClassicDiscounts;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ClassicDiscounts::class)]
final class ClassicDiscountsTest extends ResourceTestCase
{
    private ClassicDiscounts $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ClassicDiscounts::class);
    }

    #[Test]
    public function listSendsGetRequestToClassicDiscountsEndpoint(): void
    {
        $this->expectRequest('GET', '/classic/discounts', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/classic/discounts', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Test Discount', 'amount' => 10];
        $this->expectRequestWithBody('POST', '/classic/discounts', $body, ['id' => 1, 'name' => 'Test Discount']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/classic/discounts/5', ['id' => 5, 'name' => 'Test Discount']);

        $result = $this->resource->fetch(5);

        $this->assertIsArray($result);
        $this->assertSame(5, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/classic/discounts/5', ['include' => 'items'], ['id' => 5]);

        $result = $this->resource->fetch(5, ['include' => 'items']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function disableSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/classic/discounts/5', ['disabled' => true]);

        $result = $this->resource->disable(5);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Discount'];
        $this->expectRequestWithBody('PATCH', '/classic/discounts/5', $body, ['id' => 5, 'name' => 'Updated Discount']);

        $result = $this->resource->update(5, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Discount', $result['name']);
    }

    #[Test]
    public function addItemsSendsPostRequestWithBody(): void
    {
        $body = ['item_ids' => [1, 2, 3]];
        $this->expectRequestWithBody('POST', '/classic/discounts/5/items', $body, ['success' => true]);

        $result = $this->resource->addItems(5, $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeItemsSendsDeleteRequestToItemsEndpoint(): void
    {
        $body = ['item_ids' => [1, 2]];
        $this->expectRequestWithBody('DELETE', '/classic/discounts/5/items', $body, ['success' => true]);

        $result = $this->resource->removeItems(5, $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function addVouchersSendsPostRequestWithBody(): void
    {
        $body = ['voucher_codes' => ['ABC123', 'DEF456']];
        $this->expectRequestWithBody('POST', '/classic/discounts/5/vouchers', $body, ['success' => true]);

        $result = $this->resource->addVouchers(5, $body);

        $this->assertIsArray($result);
    }
}
