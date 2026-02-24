<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductDiscounts extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/discounts', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/discounts', $body);
    }

    public function fetch(int $id): array
    {
        return $this->get("/discounts/{$id}");
    }

    public function disable(int $id): array
    {
        return $this->delete("/discounts/{$id}");
    }

    public function update(int $id, array $body): array
    {
        return $this->patch("/discounts/{$id}", $body);
    }
}
