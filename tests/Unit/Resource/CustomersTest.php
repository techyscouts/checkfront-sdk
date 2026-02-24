<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Customers;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Customers::class)]
final class CustomersTest extends ResourceTestCase
{
    private Customers $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Customers::class);
    }

    #[Test]
    public function listSendsGetRequestToCustomersEndpoint(): void
    {
        $this->expectRequest('GET', '/customers/', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/customers/', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $this->expectRequestWithBody('POST', '/customers', $body, ['id' => 1, 'name' => 'John Doe']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithCustomerId(): void
    {
        $this->expectRequest('GET', '/customers/10', ['id' => 10, 'name' => 'John Doe']);

        $result = $this->resource->fetch(10);

        $this->assertIsArray($result);
        $this->assertSame(10, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/customers/10', ['include' => 'bookings'], ['id' => 10]);

        $result = $this->resource->fetch(10, ['include' => 'bookings']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function archiveSendsDeleteRequestWithCustomerId(): void
    {
        $this->expectRequest('DELETE', '/customers/10', ['archived' => true]);

        $result = $this->resource->archive(10);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Jane Doe'];
        $this->expectRequestWithBody('PATCH', '/customers/10', $body, ['id' => 10, 'name' => 'Jane Doe']);

        $result = $this->resource->update(10, $body);

        $this->assertIsArray($result);
        $this->assertSame('Jane Doe', $result['name']);
    }

    #[Test]
    public function redactSendsPostRequestToRedactEndpoint(): void
    {
        $body = ['reason' => 'GDPR request'];
        $this->expectRequestWithBody('POST', '/customers/10/redact', $body, ['redacted' => true]);

        $result = $this->resource->redact(10, $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function redactSendsPostRequestWithEmptyBodyByDefault(): void
    {
        $this->expectRequest('POST', '/customers/10/redact', ['redacted' => true]);

        $result = $this->resource->redact(10);

        $this->assertIsArray($result);
    }

    #[Test]
    public function changePasswordSendsPatchRequestWithBody(): void
    {
        $body = ['password' => 'newSecurePassword123'];
        $this->expectRequestWithBody('PATCH', '/customers/10/change-password', $body, ['success' => true]);

        $result = $this->resource->changePassword(10, $body);

        $this->assertIsArray($result);
    }
}
