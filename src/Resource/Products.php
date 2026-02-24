<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Products extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/products', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/products', $body);
    }

    public function fetch(int $productId, array $query = []): array
    {
        return $this->get("/products/{$productId}", $query);
    }

    public function archive(int $productId): array
    {
        return $this->delete("/products/{$productId}");
    }

    public function update(int $productId, array $body): array
    {
        return $this->patch("/products/{$productId}", $body);
    }

    public function copy(int $productId, array $body): array
    {
        return $this->post("/products/{$productId}/copy", $body);
    }
}
