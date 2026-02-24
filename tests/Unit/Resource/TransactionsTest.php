<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Transactions;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(Transactions::class)]
final class TransactionsTest extends ResourceTestCase
{
    private Transactions $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Transactions::class);
    }

    #[Test]
    public function listSendsGetRequestToTransactionsEndpoint(): void
    {
        $this->expectRequest('GET', '/transactions', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/transactions', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function fetchSendsGetRequestWithTransactionId(): void
    {
        $this->expectRequest('GET', '/transactions/TXN-001', ['id' => 'TXN-001', 'amount' => 100]);

        $result = $this->resource->fetch('TXN-001');

        $this->assertIsArray($result);
        $this->assertSame('TXN-001', $result['id']);
    }

    #[Test]
    public function listForBookingSendsGetRequestToBookingTransactionsEndpoint(): void
    {
        $this->expectRequest('GET', '/bookings/BK-1001/transactions', $this->paginatedBody());

        $result = $this->resource->listForBooking('BK-1001');

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForBookingForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/bookings/BK-1001/transactions', ['page' => '2'], $this->paginatedBody());

        $result = $this->resource->listForBooking('BK-1001', ['page' => '2']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createPosPaymentSendsPostRequestWithBody(): void
    {
        $body = ['amount' => 50, 'method' => 'cash'];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/transactions', $body, ['id' => 'TXN-002', 'amount' => 50]);

        $result = $this->resource->createPosPayment('BK-1001', $body);

        $this->assertIsArray($result);
        $this->assertSame('TXN-002', $result['id']);
    }

    #[Test]
    public function fetchForBookingSendsGetRequestWithBookingCodeAndTransactionId(): void
    {
        $this->expectRequest('GET', '/bookings/BK-1001/transactions/TXN-001', ['id' => 'TXN-001', 'amount' => 100]);

        $result = $this->resource->fetchForBooking('BK-1001', 'TXN-001');

        $this->assertIsArray($result);
        $this->assertSame('TXN-001', $result['id']);
    }

    #[Test]
    public function updatePosTransactionSendsPatchRequestWithBody(): void
    {
        $body = ['amount' => 75];
        $this->expectRequestWithBody('PATCH', '/bookings/BK-1001/transactions/TXN-001', $body, ['id' => 'TXN-001', 'amount' => 75]);

        $result = $this->resource->updatePosTransaction('BK-1001', 'TXN-001', $body);

        $this->assertIsArray($result);
        $this->assertSame(75, $result['amount']);
    }

    #[Test]
    public function createGiftCertificatePaymentSendsPostRequestWithBody(): void
    {
        $body = ['amount' => 25];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/transactions/giftcert/GC-100', $body, ['id' => 'TXN-003', 'amount' => 25]);

        $result = $this->resource->createGiftCertificatePayment('BK-1001', 'GC-100', $body);

        $this->assertIsArray($result);
        $this->assertSame('TXN-003', $result['id']);
    }

    #[Test]
    public function capturePendingGiftCertificatePaymentsSendsPostRequestWithBody(): void
    {
        $body = ['capture_all' => true];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/transactions/pending/giftcert', $body, ['captured' => true]);

        $result = $this->resource->capturePendingGiftCertificatePayments('BK-1001', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function removePendingGiftCertificatePaymentsSendsDeleteRequest(): void
    {
        $this->expectRequest('DELETE', '/bookings/BK-1001/transactions/pending/giftcert', ['removed' => true]);

        $result = $this->resource->removePendingGiftCertificatePayments('BK-1001');

        $this->assertIsArray($result);
    }

    #[Test]
    public function removePendingGiftCertificatePaymentSendsDeleteRequestWithGiftcertCode(): void
    {
        $this->expectRequest('DELETE', '/bookings/BK-1001/transactions/pending/giftcert/GC-100', ['removed' => true]);

        $result = $this->resource->removePendingGiftCertificatePayment('BK-1001', 'GC-100');

        $this->assertIsArray($result);
    }

    #[Test]
    public function importTransactionSendsPostRequestWithBody(): void
    {
        $body = ['external_id' => 'EXT-001', 'amount' => 200];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/transactions/import', $body, ['id' => 'TXN-004', 'imported' => true]);

        $result = $this->resource->importTransaction('BK-1001', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function refundSendsPostRequestWithBody(): void
    {
        $body = ['amount' => 50, 'reason' => 'Customer request'];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/transactions/TXN-001/refund', $body, ['id' => 'TXN-005', 'refunded' => true]);

        $result = $this->resource->refund('BK-1001', 'TXN-001', $body);

        $this->assertIsArray($result);
    }

    #[Test]
    public function refundToGiftCertificateSendsPostRequestWithBody(): void
    {
        $body = ['amount' => 30];
        $this->expectRequestWithBody('POST', '/bookings/BK-1001/transactions/TXN-001/refund/giftcert/GC-100', $body, ['id' => 'TXN-006', 'refunded' => true]);

        $result = $this->resource->refundToGiftCertificate('BK-1001', 'TXN-001', 'GC-100', $body);

        $this->assertIsArray($result);
    }
}
