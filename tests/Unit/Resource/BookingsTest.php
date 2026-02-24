<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Bookings;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Bookings::class)]
final class BookingsTest extends ResourceTestCase
{
    private Bookings $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Bookings::class);
    }

    #[Test]
    public function listSendsGetRequestToBookingsEndpoint(): void
    {
        $this->expectRequest('GET', '/bookings', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings', ['status' => 'confirmed', 'page' => '1'], $this->paginatedBody());

        $result = $this->resource->list(['status' => 'confirmed', 'page' => '1']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function fetchSendsGetRequestWithBookingCode(): void
    {
        $this->expectRequest('GET', '/bookings/BK-3003', ['code' => 'BK-3003', 'status' => 'confirmed']);

        $result = $this->resource->fetch('BK-3003');

        $this->assertIsArray($result);
        $this->assertSame('BK-3003', $result['code']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings/BK-3003', ['include' => 'items'], ['code' => 'BK-3003']);

        $result = $this->resource->fetch('BK-3003', ['include' => 'items']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['status' => 'cancelled'];
        $this->expectRequestWithBody('PATCH', '/bookings/BK-3003', $body, ['code' => 'BK-3003', 'status' => 'cancelled']);

        $result = $this->resource->update('BK-3003', $body);

        $this->assertIsArray($result);
        $this->assertSame('cancelled', $result['status']);
    }

    #[Test]
    public function listForCustomerSendsGetRequestToCustomerBookingsEndpoint(): void
    {
        $this->expectRequest('GET', '/customers/100/bookings', $this->paginatedBody());

        $result = $this->resource->listForCustomer(100);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForCustomerForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/customers/100/bookings', ['page' => '2'], $this->paginatedBody());

        $result = $this->resource->listForCustomer(100, ['page' => '2']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function checkinSendsPostRequestToCheckinEndpoint(): void
    {
        $this->expectRequest('POST', '/bookings/BK-3003/checkin', ['checked_in' => true]);

        $result = $this->resource->checkin('BK-3003');

        $this->assertIsArray($result);
    }

    #[Test]
    public function checkinForwardsBody(): void
    {
        $body = ['note' => 'Arrived early'];
        $this->expectRequestWithBody('POST', '/bookings/BK-3003/checkin', $body, ['checked_in' => true]);

        $result = $this->resource->checkin('BK-3003', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function checkoutSendsPostRequestToCheckoutEndpoint(): void
    {
        $this->expectRequest('POST', '/bookings/BK-3003/checkout', ['checked_out' => true]);

        $result = $this->resource->checkout('BK-3003');

        $this->assertIsArray($result);
    }

    #[Test]
    public function checkoutForwardsBody(): void
    {
        $body = ['note' => 'Left on time'];
        $this->expectRequestWithBody('POST', '/bookings/BK-3003/checkout', $body, ['checked_out' => true]);

        $result = $this->resource->checkout('BK-3003', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function checkinResetSendsPostRequestToCheckinResetEndpoint(): void
    {
        $this->expectRequest('POST', '/bookings/BK-3003/checkin-reset', ['reset' => true]);

        $result = $this->resource->checkinReset('BK-3003');

        $this->assertIsArray($result);
    }

    #[Test]
    public function checkinResetForwardsBody(): void
    {
        $body = ['reason' => 'Mistake'];
        $this->expectRequestWithBody('POST', '/bookings/BK-3003/checkin-reset', $body, ['reset' => true]);

        $result = $this->resource->checkinReset('BK-3003', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function changeStatusSendsPostRequestWithBody(): void
    {
        $body = ['status' => 'cancelled', 'reason' => 'Customer request'];
        $this->expectRequestWithBody('POST', '/bookings/BK-3003/changeStatus', $body, ['code' => 'BK-3003', 'status' => 'cancelled']);

        $result = $this->resource->changeStatus('BK-3003', $body);

        $this->assertIsArray($result);
        $this->assertSame('cancelled', $result['status']);
    }
}
