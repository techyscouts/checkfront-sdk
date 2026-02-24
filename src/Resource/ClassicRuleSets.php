<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ClassicRuleSets extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/classic/rulesets', $query);
    }

    public function fetch(int $id, array $query = []): array
    {
        return $this->get("/classic/rulesets/{$id}", $query);
    }
}
