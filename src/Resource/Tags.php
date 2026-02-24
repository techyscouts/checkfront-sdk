<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Tags extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/tags', $query);
    }

    public function create(array $body = []): array
    {
        return $this->post('/tags', $body);
    }

    public function fetch(string|int $id): array
    {
        return $this->get("/tags/{$id}");
    }

    public function archive(string|int $id): array
    {
        return $this->delete("/tags/{$id}");
    }

    public function update(string|int $id, array $body = []): array
    {
        return $this->patch("/tags/{$id}", $body);
    }
}
