<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\BookingGuests;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(BookingGuests::class)]
final class BookingGuestsTest extends ResourceTestCase
{
    private BookingGuests $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(BookingGuests::class);
    }

    #[Test]
    public function listSendsGetRequestToBookingGuestsEndpoint(): void
    {
        $this->expectRequest('GET', '/bookings/BK-1001/guests/', $this->paginatedBody());

        $result = $this->resource->list('BK-1001');

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings/BK-1001/guests/', ['page' => '2'], $this->paginatedBody());

        $result = $this->resource->list('BK-1001', ['page' => '2']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['name' => 'John Doe', 'email' => 'john@example.com'];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/guests/', $body, ['uuid' => 'abc-123', 'name' => 'John Doe']);

        $result = $this->resource->create('BK-1001', $body);

        $this->assertIsArray($result);
        $this->assertSame('abc-123', $result['uuid']);
    }

    #[Test]
    public function fetchSendsGetRequestWithBookingCodeAndUuid(): void
    {
        $this->expectRequest('GET', '/bookings/BK-1001/guests/abc-123', ['uuid' => 'abc-123', 'name' => 'John Doe']);

        $result = $this->resource->fetch('BK-1001', 'abc-123');

        $this->assertIsArray($result);
        $this->assertSame('abc-123', $result['uuid']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings/BK-1001/guests/abc-123', ['include' => 'details'], ['uuid' => 'abc-123']);

        $result = $this->resource->fetch('BK-1001', 'abc-123', ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeSendsDeleteRequestWithBookingCodeAndUuid(): void
    {
        $this->expectRequest('DELETE', '/bookings/BK-1001/guests/abc-123', ['removed' => true]);

        $result = $this->resource->remove('BK-1001', 'abc-123');

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Jane Doe'];
        $this->expectRequestWithBody('PATCH', '/bookings/BK-1001/guests/abc-123', $body, ['uuid' => 'abc-123', 'name' => 'Jane Doe']);

        $result = $this->resource->update('BK-1001', 'abc-123', $body);

        $this->assertIsArray($result);
        $this->assertSame('Jane Doe', $result['name']);
    }
}
