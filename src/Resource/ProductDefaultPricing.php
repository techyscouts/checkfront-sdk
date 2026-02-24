<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

final class ProductDefaultPricing extends AbstractResource
{
    public function fetch(int $productId): array
    {
        return $this->get("/products/{$productId}/pricing");
    }

    public function update(int $productId, array $body): array
    {
        return $this->patch("/products/{$productId}/pricing", $body);
    }

    public function copy(int $sourceProductId, int $targetProductId): array
    {
        return $this->post("/products/{$sourceProductId}/pricing/copy/{$targetProductId}");
    }
}
