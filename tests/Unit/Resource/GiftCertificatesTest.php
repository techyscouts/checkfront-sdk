<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\GiftCertificates;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(GiftCertificates::class)]
final class GiftCertificatesTest extends ResourceTestCase
{
    private GiftCertificates $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(GiftCertificates::class);
    }

    #[Test]
    public function listSendsGetRequestToGiftCertificatesEndpoint(): void
    {
        $this->expectRequest('GET', '/gift-certificates', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/gift-certificates', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function createSendsPostRequestWithBody(): void
    {
        $body = ['code' => 'GIFT100', 'value' => 100];
        $this->expectRequestWithBody('POST', '/gift-certificates', $body, ['id' => 1, 'code' => 'GIFT100']);

        $result = $this->resource->create($body);

        $this->assertIsArray($result);
        $this->assertSame(1, $result['id']);
    }

    #[Test]
    public function fetchSendsGetRequestWithIdentifier(): void
    {
        $this->expectRequest('GET', '/gift-certificates/GC-ABC123', ['id' => 1, 'code' => 'GC-ABC123']);

        $result = $this->resource->fetch('GC-ABC123');

        $this->assertIsArray($result);
        $this->assertSame('GC-ABC123', $result['code']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/gift-certificates/GC-ABC123', ['include' => 'details'], ['id' => 1]);

        $result = $this->resource->fetch('GC-ABC123', ['include' => 'details']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['value' => 200];
        $this->expectRequestWithBody('PATCH', '/gift-certificates/GC-ABC123', $body, ['id' => 1, 'value' => 200]);

        $result = $this->resource->update('GC-ABC123', $body);

        $this->assertIsArray($result);
        $this->assertSame(200, $result['value']);
    }

    #[Test]
    public function sendNotificationsSendsPostRequest(): void
    {
        $this->expectRequest('POST', '/gift-certificates/GC-ABC123/send-notifications', ['sent' => true]);

        $result = $this->resource->sendNotifications('GC-ABC123');

        $this->assertIsArray($result);
    }

    #[Test]
    public function voidSendsPostRequest(): void
    {
        $this->expectRequest('POST', '/gift-certificates/GC-ABC123/void', ['voided' => true]);

        $result = $this->resource->void('GC-ABC123');

        $this->assertIsArray($result);
    }

    #[Test]
    public function activateSendsPostRequest(): void
    {
        $this->expectRequest('POST', '/gift-certificates/GC-ABC123/activate', ['activated' => true]);

        $result = $this->resource->activate('GC-ABC123');

        $this->assertIsArray($result);
    }

    #[Test]
    public function addValueSendsPostRequestWithBody(): void
    {
        $body = ['amount' => 50];
        $this->expectRequestWithBody('POST', '/gift-certificates/GC-ABC123/add-value', $body, ['id' => 1, 'value' => 150]);

        $result = $this->resource->addValue('GC-ABC123', $body);

        $this->assertIsArray($result);
        $this->assertSame(150, $result['value']);
    }
}
