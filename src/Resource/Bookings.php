<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Bookings extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/bookings', $query);
    }

    public function fetch(string $bookingCode, array $query = []): array
    {
        return $this->get("/bookings/{$bookingCode}", $query);
    }

    public function update(string $bookingCode, array $body): array
    {
        return $this->patch("/bookings/{$bookingCode}", $body);
    }

    public function listForCustomer(int $customerId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/customers/{$customerId}/bookings", $query);
    }

    public function checkin(string $bookingCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/checkin", $body);
    }

    public function checkout(string $bookingCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/checkout", $body);
    }

    public function checkinReset(string $bookingCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/checkin-reset", $body);
    }

    public function changeStatus(string $bookingCode, array $body): array
    {
        return $this->post("/bookings/{$bookingCode}/changeStatus", $body);
    }
}
