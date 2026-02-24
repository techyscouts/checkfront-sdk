<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Accounts extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/accounts/', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/accounts', $body);
    }

    public function fetch(int $id, array $query = []): array
    {
        return $this->get("/accounts/{$id}", $query);
    }

    public function archive(int $id): array
    {
        return $this->delete("/accounts/{$id}");
    }

    public function update(int $id, array $body): array
    {
        return $this->patch("/accounts/{$id}", $body);
    }
}
