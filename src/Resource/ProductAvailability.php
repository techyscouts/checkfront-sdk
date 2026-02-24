<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ProductAvailability extends AbstractResource
{
    public function listAll(array $query = []): PaginatedResponse
    {
        return $this->paginate('/products-inventory/', $query);
    }

    public function fetchForProduct(int $id, array $query = []): array
    {
        return $this->get("/products-inventory/{$id}", $query);
    }

    public function fetchInventory(int $id, array $query = []): array
    {
        return $this->get("/inventory/{$id}", $query);
    }

    public function fetchBookingCounts(int $id, array $query = []): array
    {
        return $this->get("/inventory/{$id}/booking-counts", $query);
    }
}
