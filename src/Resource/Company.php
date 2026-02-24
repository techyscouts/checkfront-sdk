<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

final class Company extends AbstractResource
{
    public function list(array $query = []): array
    {
        return $this->get('/company', $query);
    }

    public function update(array $body): array
    {
        return $this->patch('/company', $body);
    }
}
