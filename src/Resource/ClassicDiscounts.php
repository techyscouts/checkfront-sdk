<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class ClassicDiscounts extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/classic/discounts', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/classic/discounts', $body);
    }

    public function fetch(int $id, array $query = []): array
    {
        return $this->get("/classic/discounts/{$id}", $query);
    }

    public function disable(int $id): array
    {
        return $this->delete("/classic/discounts/{$id}");
    }

    public function update(int $id, array $body): array
    {
        return $this->patch("/classic/discounts/{$id}", $body);
    }

    public function addItems(int $id, array $body): array
    {
        return $this->post("/classic/discounts/{$id}/items", $body);
    }

    public function removeItems(int $id, array $body): array
    {
        return $this->sendRequest('DELETE', "/classic/discounts/{$id}/items", [], $body);
    }

    public function addVouchers(int $id, array $body): array
    {
        return $this->post("/classic/discounts/{$id}/vouchers", $body);
    }
}
