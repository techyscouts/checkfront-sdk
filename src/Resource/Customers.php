<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Customers extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/customers/', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/customers', $body);
    }

    public function fetch(int $customerId, array $query = []): array
    {
        return $this->get("/customers/{$customerId}", $query);
    }

    public function archive(int $customerId): array
    {
        return $this->delete("/customers/{$customerId}");
    }

    public function update(int $customerId, array $body): array
    {
        return $this->patch("/customers/{$customerId}", $body);
    }

    public function redact(int $customerId, array $body = []): array
    {
        return $this->post("/customers/{$customerId}/redact", $body);
    }

    public function changePassword(int $customerId, array $body): array
    {
        return $this->patch("/customers/{$customerId}/change-password", $body);
    }
}
