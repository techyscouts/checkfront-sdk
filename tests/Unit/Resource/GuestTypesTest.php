<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\GuestTypes;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(GuestTypes::class)]
final class GuestTypesTest extends ResourceTestCase
{
    private GuestTypes $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(GuestTypes::class);
    }

    #[Test]
    public function listSendsGetRequestToGuestTypesEndpoint(): void
    {
        $this->expectRequest('GET', '/guest-types', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/guest-types', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Adult', 'label' => 'Adults'];
        $this->expectRequestWithBody('POST', '/guest-types', $body, ['id' => 'gt-1', 'name' => 'Adult']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame('gt-1', $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/guest-types/gt-1', ['id' => 'gt-1', 'name' => 'Adult']);

        $result = $this->resource->fetch('gt-1');

        $this->assertIsArray($result);
        $this->assertSame('gt-1', $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/guest-types/gt-1', ['include' => 'details'], ['id' => 'gt-1']);

        $result = $this->resource->fetch('gt-1', ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/guest-types/gt-1', ['deleted' => true]);

        $result = $this->resource->remove('gt-1');

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Senior'];
        $this->expectRequestWithBody('PATCH', '/guest-types/gt-1', $body, ['id' => 'gt-1', 'name' => 'Senior']);

        $result = $this->resource->update('gt-1', $body);

        $this->assertIsArray($result);
        $this->assertSame('Senior', $result['name']);
    }
}
