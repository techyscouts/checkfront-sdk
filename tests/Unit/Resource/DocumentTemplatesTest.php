<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\DocumentTemplates;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(DocumentTemplates::class)]
final class DocumentTemplatesTest extends ResourceTestCase
{
    private DocumentTemplates $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(DocumentTemplates::class);
    }

    #[Test]
    public function listSendsGetRequestToDocumentTemplatesEndpoint(): void
    {
        $this->expectRequest('GET', '/document-templates', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/document-templates', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }
}
