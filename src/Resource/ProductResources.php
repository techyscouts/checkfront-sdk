<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

final class ProductResources extends AbstractResource
{
    public function list(int $productId): array
    {
        return $this->get("/products/{$productId}/resources");
    }

    public function update(int $productId, array $body): array
    {
        return $this->patch("/products/{$productId}/resources", $body);
    }

    public function copy(int $sourceProductId, int $targetProductId): array
    {
        return $this->post("/products/{$sourceProductId}/resources/copy/{$targetProductId}");
    }
}
