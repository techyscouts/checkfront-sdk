<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ClassicItems;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ClassicItems::class)]
final class ClassicItemsTest extends ResourceTestCase
{
    private ClassicItems $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ClassicItems::class);
    }

    #[Test]
    public function listSendsGetRequestToClassicItemsEndpoint(): void
    {
        $this->expectRequest('GET', '/classic/items', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/classic/items', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/classic/items/7', ['id' => 7, 'name' => 'Test Item']);

        $result = $this->resource->fetch(7);

        $this->assertIsArray($result);
        $this->assertSame(7, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/classic/items/7', ['include' => 'details'], ['id' => 7]);

        $result = $this->resource->fetch(7, ['include' => 'details']);

        $this->assertIsArray($result);
    }
}
