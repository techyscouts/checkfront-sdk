<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

final class ProductGuestTypeMaps extends AbstractResource
{
    public function fetch(int $parentProductId, array $query = []): array
    {
        return $this->get("/products/{$parentProductId}/guestMaps", $query);
    }

    public function remove(int $parentProductId, array $query = []): array
    {
        return $this->delete("/products/{$parentProductId}/guestMaps", $query);
    }

    public function replace(int $parentProductId, array $body): array
    {
        return $this->patch("/products/{$parentProductId}/guestMaps", $body);
    }
}
