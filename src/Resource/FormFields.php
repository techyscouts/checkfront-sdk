<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class FormFields extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/form-fields', $query);
    }

    public function fetch(string $id, array $query = []): array
    {
        return $this->get("/form-fields/{$id}", $query);
    }
}
