<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Response\PaginatedResponse;

final class BookingsTest extends FeatureTestCase
{
    #[Test]
    public function listReturnsPaginatedResponse(): void
    {
        $response = self::$client->bookings()->list();

        $this->assertInstanceOf(PaginatedResponse::class, $response);
        $this->assertIsArray($response->getItems());
        $this->assertIsArray($response->getMeta());
    }

    #[Test]
    public function listItemsHaveExpectedKeys(): void
    {
        $response = self::$client->bookings()->list();
        $items = $response->getItems();

        if (count($items) === 0) {
            $this->markTestSkipped('No bookings available to verify structure');
        }

        $first = $items[0];
        $this->assertArrayHasKey('code', $first);
    }

    #[Test]
    public function fetchReturnsSingleBooking(): void
    {
        $response = self::$client->bookings()->list();
        $items = $response->getItems();

        if (count($items) === 0) {
            $this->markTestSkipped('No bookings available to fetch');
        }

        $code = $items[0]['code'];
        $booking = self::$client->bookings()->fetch($code);

        $this->assertIsArray($booking);
        $this->assertArrayHasKey('data', $booking);
    }

    #[Test]
    public function paginationMetadataIsAccessible(): void
    {
        $response = self::$client->bookings()->list();

        $this->assertIsInt($response->getCurrentPage());
        $this->assertIsBool($response->hasNextPage());

        $total = $response->getTotalCount();
        $this->assertTrue($total === null || is_int($total));
    }
}
