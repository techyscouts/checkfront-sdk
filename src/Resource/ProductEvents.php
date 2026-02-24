<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductEvents extends AbstractResource
{
    public function list(int $productId): PaginatedResponse
    {
        return $this->paginate("/products/{$productId}/events");
    }

    public function create(int $productId, array $body): array
    {
        return $this->post("/products/{$productId}/events", $body);
    }

    public function fetch(int $productId, int $eventId): array
    {
        return $this->get("/products/{$productId}/events/{$eventId}");
    }

    public function remove(int $productId, int $eventId): array
    {
        return $this->delete("/products/{$productId}/events/{$eventId}");
    }

    public function update(int $productId, int $eventId, array $body): array
    {
        return $this->patch("/products/{$productId}/events/{$eventId}", $body);
    }

    public function copy(int $sourceProductId, int $targetProductId): array
    {
        return $this->post("/products/{$sourceProductId}/events/copy/{$targetProductId}");
    }
}
