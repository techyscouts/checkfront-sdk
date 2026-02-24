<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductImages extends AbstractResource
{
    public function list(int $productId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/products/{$productId}/images", $query);
    }

    public function upload(int $productId, array $body): array
    {
        return $this->post("/products/{$productId}/images", $body);
    }

    public function remove(int $productId, string $imageId): array
    {
        return $this->delete("/products/{$productId}/images/{$imageId}");
    }

    public function copy(int $sourceProductId, int $targetProductId): array
    {
        return $this->post("/products/{$sourceProductId}/images/copy/{$targetProductId}");
    }
}
