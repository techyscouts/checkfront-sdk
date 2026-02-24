<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

final class ProductClosures extends AbstractResource
{
    public function create(array $body): array
    {
        return $this->post('/closures', $body);
    }

    public function disable(int $id): array
    {
        return $this->delete("/closures/{$id}");
    }

    public function update(int $id, array $body): array
    {
        return $this->patch("/closures/{$id}", $body);
    }
}
