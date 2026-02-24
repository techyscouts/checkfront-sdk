<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Statuses extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/statuses', $query);
    }

    public function create(array $body = []): array
    {
        return $this->post('/statuses', $body);
    }

    public function fetch(string|int $id): array
    {
        return $this->get("/statuses/{$id}");
    }

    public function update(string|int $id, array $body = []): array
    {
        return $this->patch("/statuses/{$id}", $body);
    }
}
