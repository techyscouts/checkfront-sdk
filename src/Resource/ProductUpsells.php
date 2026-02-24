<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductUpsells extends AbstractResource
{
    public function list(string $productId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/products/{$productId}/upsells", $query);
    }

    public function replace(string $productId, array $body = []): array
    {
        return $this->put("/products/{$productId}/upsells", $body);
    }

    public function fetch(string $productId, string $addonId): array
    {
        return $this->get("/products/{$productId}/upsells/{$addonId}");
    }

    public function assign(string $productId, string $addonId, array $body = []): array
    {
        return $this->post("/products/{$productId}/upsells/{$addonId}", $body);
    }

    public function remove(string $productId, string $addonId): array
    {
        return $this->delete("/products/{$productId}/upsells/{$addonId}");
    }

    public function update(string $productId, string $addonId, array $body = []): array
    {
        return $this->patch("/products/{$productId}/upsells/{$addonId}", $body);
    }

    public function copy(string $sourceProductId, string $targetProductId): array
    {
        return $this->post("/products/{$sourceProductId}/upsells/copy/{$targetProductId}");
    }
}
