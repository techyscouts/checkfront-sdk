<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Resource;

use TechyScouts\Checkfront\Response\PaginatedResponse;

final class Transactions extends AbstractResource
{
    public function list(array $query = []): PaginatedResponse
    {
        return $this->paginate('/transactions', $query);
    }

    public function fetch(string $transactionId): array
    {
        return $this->get("/transactions/{$transactionId}");
    }

    public function listForBooking(string $bookingCode, array $query = []): PaginatedResponse
    {
        return $this->paginate("/bookings/{$bookingCode}/transactions", $query);
    }

    public function createPosPayment(string $bookingCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/transactions", $body);
    }

    public function fetchForBooking(string $bookingCode, string $transactionId): array
    {
        return $this->get("/bookings/{$bookingCode}/transactions/{$transactionId}");
    }

    public function updatePosTransaction(string $bookingCode, string $transactionId, array $body = []): array
    {
        return $this->patch("/bookings/{$bookingCode}/transactions/{$transactionId}", $body);
    }

    public function createGiftCertificatePayment(string $bookingCode, string $giftcertCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/transactions/giftcert/{$giftcertCode}", $body);
    }

    public function capturePendingGiftCertificatePayments(string $bookingCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/transactions/pending/giftcert", $body);
    }

    public function removePendingGiftCertificatePayments(string $bookingCode): array
    {
        return $this->delete("/bookings/{$bookingCode}/transactions/pending/giftcert");
    }

    public function removePendingGiftCertificatePayment(string $bookingCode, string $giftcertCode): array
    {
        return $this->delete("/bookings/{$bookingCode}/transactions/pending/giftcert/{$giftcertCode}");
    }

    public function importTransaction(string $bookingCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/transactions/import", $body);
    }

    public function refund(string $bookingCode, string $transactionId, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/transactions/{$transactionId}/refund", $body);
    }

    public function refundToGiftCertificate(string $bookingCode, string $transactionId, string $giftcertCode, array $body = []): array
    {
        return $this->post("/bookings/{$bookingCode}/transactions/{$transactionId}/refund/giftcert/{$giftcertCode}", $body);
    }
}
