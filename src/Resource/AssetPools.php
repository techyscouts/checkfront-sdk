<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class AssetPools extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/assetpools', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/assetpools', $body);
    }

    public function fetch(int $assetPoolId, array $query = []): array
    {
        return $this->get("/assetpools/{$assetPoolId}", $query);
    }

    public function archive(int $assetPoolId): array
    {
        return $this->delete("/assetpools/{$assetPoolId}");
    }

    public function update(int $assetPoolId, array $body): array
    {
        return $this->patch("/assetpools/{$assetPoolId}", $body);
    }

    public function addAssets(int $assetPoolId, array $body): array
    {
        return $this->post("/assetpools/{$assetPoolId}/assets", $body);
    }

    public function fetchAsset(int $assetPoolId, int $assetId, array $query = []): array
    {
        return $this->get("/assetpools/{$assetPoolId}/assets/{$assetId}", $query);
    }

    public function archiveAsset(int $assetPoolId, int $assetId): array
    {
        return $this->delete("/assetpools/{$assetPoolId}/assets/{$assetId}");
    }

    public function updateAsset(int $assetPoolId, int $assetId, array $body): array
    {
        return $this->patch("/assetpools/{$assetPoolId}/assets/{$assetId}", $body);
    }
}
