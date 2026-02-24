<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class AssetPoolCategories extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/assetpool-categories', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/assetpool-categories', $body);
    }

    public function fetch(int $id, array $query = []): array
    {
        return $this->get("/assetpool-categories/{$id}", $query);
    }

    public function update(int $id, array $body): array
    {
        return $this->patch("/assetpool-categories/{$id}", $body);
    }
}
