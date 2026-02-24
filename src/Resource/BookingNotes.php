<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class BookingNotes extends AbstractResource
{
    public function list(string $bookingCode, array $query = []): PaginatedResponse
    {
        return $this->paginate("/bookings/{$bookingCode}/notes/", $query);
    }

    public function create(string $bookingCode, array $body): array
    {
        return $this->post("/bookings/{$bookingCode}/notes", $body);
    }

    public function fetch(string $bookingCode, int $noteId, array $query = []): array
    {
        return $this->get("/bookings/{$bookingCode}/notes/{$noteId}", $query);
    }

    public function remove(string $bookingCode, int $noteId): array
    {
        return $this->sendRequest('DELETE', "/bookings/{$bookingCode}/notes/{$noteId}");
    }

    public function update(string $bookingCode, int $noteId, array $body): array
    {
        return $this->patch("/bookings/{$bookingCode}/notes/{$noteId}", $body);
    }
}
