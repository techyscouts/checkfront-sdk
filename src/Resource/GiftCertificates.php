<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class GiftCertificates extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/gift-certificates', $query);
    }

    public function create(array $body): array
    {
        return $this->post('/gift-certificates', $body);
    }

    public function fetch(string $giftCertificateIdentifier, array $query = []): array
    {
        return $this->get("/gift-certificates/{$giftCertificateIdentifier}", $query);
    }

    public function update(string $giftCertificateIdentifier, array $body): array
    {
        return $this->patch("/gift-certificates/{$giftCertificateIdentifier}", $body);
    }

    public function sendNotifications(string $giftCertificateIdentifier): array
    {
        return $this->post("/gift-certificates/{$giftCertificateIdentifier}/send-notifications");
    }

    public function void(string $giftCertificateIdentifier): array
    {
        return $this->post("/gift-certificates/{$giftCertificateIdentifier}/void");
    }

    public function activate(string $giftCertificateIdentifier): array
    {
        return $this->post("/gift-certificates/{$giftCertificateIdentifier}/activate");
    }

    public function addValue(string $giftCertificateIdentifier, array $body): array
    {
        return $this->post("/gift-certificates/{$giftCertificateIdentifier}/add-value", $body);
    }
}
