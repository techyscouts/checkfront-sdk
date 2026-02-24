<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class BundleImages extends AbstractResource
{
    public function list(int $productId, array $query = []): PaginatedResponse
    {
        return $this->paginate("/bundles/{$productId}/images", $query);
    }

    public function upload(int $productId, array $body): array
    {
        return $this->post("/bundles/{$productId}/images", $body);
    }

    public function remove(int $productId, int $imageId): array
    {
        return $this->sendRequest('DELETE', "/bundles/{$productId}/images/{$imageId}");
    }
}
