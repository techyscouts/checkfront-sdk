<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Taxes;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Taxes::class)]
final class TaxesTest extends ResourceTestCase
{
    private Taxes $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Taxes::class);
    }

    #[Test]
    public function listSendsGetRequestToTaxesEndpoint(): void
    {
        $this->expectRequest('GET', '/taxes', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/taxes', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Sales Tax', 'rate' => 13];
        $this->expectRequestWithBody('POST', '/taxes', $body, ['id' => 1, 'name' => 'Sales Tax']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/taxes/42', ['id' => 42, 'name' => 'Sales Tax']);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/taxes/42', ['archived' => true]);

        $result = $this->resource->archive(42);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Tax', 'rate' => 15];
        $this->expectRequestWithBody('PATCH', '/taxes/42', $body, ['id' => 42, 'name' => 'Updated Tax']);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Tax', $result['name']);
    }
}
