<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\BookingNotes;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(BookingNotes::class)]
final class BookingNotesTest extends ResourceTestCase
{
    private BookingNotes $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(BookingNotes::class);
    }

    #[Test]
    public function listSendsGetRequestToBookingNotesEndpoint(): void
    {
        $this->expectRequest('GET', '/bookings/BK-2002/notes/', $this->paginatedBody());

        $result = $this->resource->list('BK-2002');

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings/BK-2002/notes/', ['page' => '1'], $this->paginatedBody());

        $result = $this->resource->list('BK-2002', ['page' => '1']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['content' => 'Important note about booking'];
        $this->expectRequestWithBody('POST', '/bookings/BK-2002/notes', $body, ['id' => 1, 'content' => 'Important note about booking']);

        $result = $this->resource->create('BK-2002', $body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithBookingCodeAndNoteId(): void
    {
        $this->expectRequest('GET', '/bookings/BK-2002/notes/55', ['id' => 55, 'content' => 'A note']);

        $result = $this->resource->fetch('BK-2002', 55);

        $this->assertIsArray($result);
        $this->assertSame(55, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings/BK-2002/notes/55', ['include' => 'author'], ['id' => 55]);

        $result = $this->resource->fetch('BK-2002', 55, ['include' => 'author']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removeSendsDeleteRequestWithBookingCodeAndNoteId(): void
    {
        $this->expectRequest('DELETE', '/bookings/BK-2002/notes/55', ['removed' => true]);

        $result = $this->resource->remove('BK-2002', 55);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['content' => 'Updated note content'];
        $this->expectRequestWithBody('PATCH', '/bookings/BK-2002/notes/55', $body, ['id' => 55, 'content' => 'Updated note content']);

        $result = $this->resource->update('BK-2002', 55, $body);

        $this->assertIsArray($result);
        $this->assertSame('Updated note content', $result['content']);
    }
}
