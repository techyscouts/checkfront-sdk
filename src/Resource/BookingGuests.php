<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class BookingGuests extends AbstractResource
{
    public function list(string $bookingCode, array $query = []): PaginatedResponse
    {
        return $this->paginate("/bookings/{$bookingCode}/guests/", $query);
    }

    public function create(string $bookingCode, array $body): array
    {
        return $this->post("/bookings/{$bookingCode}/guests/", $body);
    }

    public function fetch(string $bookingCode, string $uuid, array $query = []): array
    {
        return $this->get("/bookings/{$bookingCode}/guests/{$uuid}", $query);
    }

    public function remove(string $bookingCode, string $uuid): array
    {
        return $this->sendRequest('DELETE', "/bookings/{$bookingCode}/guests/{$uuid}");
    }

    public function update(string $bookingCode, string $uuid, array $body): array
    {
        return $this->patch("/bookings/{$bookingCode}/guests/{$uuid}", $body);
    }
}
