<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Tokens;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Tokens::class)]
final class TokensTest extends ResourceTestCase
{
    private Tokens $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Tokens::class);
    }

    #[Test]
    public function listSendsGetRequestToTokensEndpoint(): void
    {
        $this->expectRequest('GET', '/tokens', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/tokens', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'API Token', 'scopes' => ['read', 'write']];
        $this->expectRequestWithBody('POST', '/tokens', $body, ['client_id' => 'abc-123', 'name' => 'API Token']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame('abc-123', $result['client_id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithClientId(): void
    {
        $this->expectRequest('GET', '/tokens/abc-123', ['client_id' => 'abc-123', 'name' => 'API Token']);

        $result = $this->resource->fetch('abc-123');

        $this->assertIsArray($result);
        $this->assertSame('abc-123', $result['client_id']);
    }

    #[Test]
    public function revokeSendsDeleteRequestWithClientId(): void
    {
        $this->expectRequest('DELETE', '/tokens/abc-123', ['revoked' => true]);

        $result = $this->resource->revoke('abc-123');

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Token'];
        $this->expectRequestWithBody('PATCH', '/tokens/abc-123', $body, ['client_id' => 'abc-123', 'name' => 'Updated Token']);

        $result = $this->resource->update('abc-123', $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Token', $result['name']);
    }

    #[Test]
    public function refreshSendsPostRequestToRefreshEndpoint(): void
    {
        $this->expectRequest('POST', '/tokens/abc-123/refresh', ['client_id' => 'abc-123', 'refreshed' => true]);

        $result = $this->resource->refresh('abc-123');

        $this->assertIsArray($result);
    }
}
