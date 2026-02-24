<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Accounts;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Accounts::class)]
final class AccountsTest extends ResourceTestCase
{
    private Accounts $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Accounts::class);
    }

    #[Test]
    public function listSendsGetRequestToAccountsEndpoint(): void
    {
        $this->expectRequest('GET', '/accounts/', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/accounts/', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'Test Account', 'email' => 'test@example.com'];
        $this->expectRequestWithBody('POST', '/accounts', $body, ['id' => 1, 'name' => 'Test Account']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/accounts/42', ['id' => 42, 'name' => 'Test Account']);

        $result = $this->resource->fetch(42);

        $this->assertIsArray($result);
        $this->assertSame(42, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/accounts/42', ['include' => 'details'], ['id' => 42]);

        $result = $this->resource->fetch(42, ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithId(): void
    {
        $this->expectRequest('DELETE', '/accounts/42', ['archived' => true]);

        $result = $this->resource->archive(42);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Account'];
        $this->expectRequestWithBody('PATCH', '/accounts/42', $body, ['id' => 42, 'name' => 'Updated Account']);

        $result = $this->resource->update(42, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Account', $result['name']);
    }
}
