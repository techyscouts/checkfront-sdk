<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Categories extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/categories', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/categories', $body);
    }

    public function fetch(int $id, array $query = []): array
    {
        return $this->get("/categories/{$id}", $query);
    }

    public function disable(int $id): array
    {
        return $this->delete("/categories/{$id}");
    }

    public function update(int $id, array $body): array
    {
        return $this->patch("/categories/{$id}", $body);
    }
}
