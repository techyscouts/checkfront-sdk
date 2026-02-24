<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class GuestTypes extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/guest-types', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/guest-types', $body);
    }

    public function fetch(string $id, array $query = []): array
    {
        return $this->get("/guest-types/{$id}", $query);
    }

    public function remove(string $id): array
    {
        return $this->delete("/guest-types/{$id}");
    }

    public function update(string $id, array $body): array
    {
        return $this->patch("/guest-types/{$id}", $body);
    }
}
