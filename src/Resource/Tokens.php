<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Tokens extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/tokens', $query);
    }

    public function create(array $body = []): array
    {
        return $this->post('/tokens', $body);
    }

    public function fetch(string $clientId): array
    {
        return $this->get("/tokens/{$clientId}");
    }

    public function revoke(string $clientId): array
    {
        return $this->sendRequest('DELETE', "/tokens/{$clientId}");
    }

    public function update(string $clientId, array $body = []): array
    {
        return $this->patch("/tokens/{$clientId}", $body);
    }

    public function refresh(string $clientId): array
    {
        return $this->post("/tokens/{$clientId}/refresh");
    }
}
