<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Taxes extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/taxes', $query);
    }

    public function create(array $body = []): array
    {
        return $this->post('/taxes', $body);
    }

    public function fetch(string|int $id): array
    {
        return $this->get("/taxes/{$id}");
    }

    public function archive(string|int $id): array
    {
        return $this->delete("/taxes/{$id}");
    }

    public function update(string|int $id, array $body = []): array
    {
        return $this->patch("/taxes/{$id}", $body);
    }
}
