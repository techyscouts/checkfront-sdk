<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class CustomRates extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/custom-rates', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/custom-rates', $body);
    }

    public function fetch(int $customRateId, array $query = []): array
    {
        return $this->get("/custom-rates/{$customRateId}", $query);
    }

    public function disable(int $customRateId): array
    {
        return $this->delete("/custom-rates/{$customRateId}");
    }

    public function update(int $customRateId, array $body): array
    {
        return $this->patch("/custom-rates/{$customRateId}", $body);
    }
}
