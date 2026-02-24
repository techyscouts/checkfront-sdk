<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Tags;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Tags::class)]
final class TagsTest extends ResourceTestCase
{
    private Tags $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Tags::class);
    }

    #[Test]
    public function listSendsGetRequestToTagsEndpoint(): void
    {
        $this->expectRequest('GET', '/tags', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/tags', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'VIP'];
        $this->expectRequestWithBody('POST', '/tags', $body, ['id' => 1, 'name' => 'VIP']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/tags/42', ['id' => 42, 'name' => 'VIP']);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/tags/42', ['archived' => true]);

        $result = $this->resource->archive(42);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Premium'];
        $this->expectRequestWithBody('PATCH', '/tags/42', $body, ['id' => 42, 'name' => 'Premium']);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('Premium', $result['name']);
    }
}
