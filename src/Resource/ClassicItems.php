<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ClassicItems extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/classic/items', $query);
    }

    public function fetch(int $id, array $query = []): array
    {
        return $this->get("/classic/items/{$id}", $query);
    }
}
