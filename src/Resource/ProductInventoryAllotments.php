<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductInventoryAllotments extends AbstractResource
{
    public function list(int $productId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/products/{$productId}/allotments", $query);
    }

    public function remove(int $productId, array $body): array
    {
        return $this->sendRequest('DELETE', "/products/{$productId}/allotments", [], $body);
    }

    public function create(int $productId, array $body): array
    {
        return $this->put("/products/{$productId}/allotment", $body);
    }

    public function update(int $productId, array $body): array
    {
        return $this->patch("/products/{$productId}/allotment", $body);
    }
}
