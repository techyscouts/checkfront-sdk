<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class AssetEvents extends AbstractResource
{
    public function list(int $assetPoolId, int $assetId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/assetpools/{$assetPoolId}/assets/{$assetId}/events", $query);
    }

    public function create(int $assetPoolId, int $assetId, array $body): array
    {
        return $this->post("/assetpools/{$assetPoolId}/assets/{$assetId}/events", $body);
    }

    public function fetch(int $assetPoolId, int $assetId, int $assetEventId, array $query = []): array
    {
        return $this->get("/assetpools/{$assetPoolId}/assets/{$assetId}/events/{$assetEventId}", $query);
    }

    public function archive(int $assetPoolId, int $assetId, int $assetEventId): array
    {
        return $this->delete("/assetpools/{$assetPoolId}/assets/{$assetId}/events/{$assetEventId}");
    }

    public function update(int $assetPoolId, int $assetId, int $assetEventId, array $body): array
    {
        return $this->patch("/assetpools/{$assetPoolId}/assets/{$assetId}/events/{$assetEventId}", $body);
    }

    public function listForPool(int $assetPoolId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/assetpools/{$assetPoolId}/events", $query);
    }
}
