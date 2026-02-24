<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Bundles extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/bundles', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/bundles', $body);
    }

    public function fetch(int $productId, array $query = []): array
    {
        return $this->get("/bundles/{$productId}", $query);
    }

    public function archive(int $productId): array
    {
        return $this->delete("/bundles/{$productId}");
    }

    public function update(int $productId, array $body): array
    {
        return $this->patch("/bundles/{$productId}", $body);
    }
}
