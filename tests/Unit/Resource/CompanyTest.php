<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\Company;

#[CoversClass(Company::class)]
final class CompanyTest extends ResourceTestCase
{
    private Company $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(Company::class);
    }

    #[Test]
    public function listSendsGetRequestToCompanyEndpoint(): void
    {
        $this->expectRequest('GET', '/company', ['name' => 'Test Company', 'timezone' => 'UTC']);

        $result = $this->resource->list();

        $this->assertIsArray($result);
        $this->assertSame('Test Company', $result['name']);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/company', ['include' => 'settings'], ['name' => 'Test Company']);

        $result = $this->resource->list(['include' => 'settings']);

        $this->assertIsArray($result);
    }

    #[Test]
    public function updateSendsPatchRequestWithBody(): void
    {
        $body = ['name' => 'Updated Company'];
        $this->expectRequestWithBody('PATCH', '/company', $body, ['name' => 'Updated Company']);

        $result = $this->resource->update($body);

        $this->assertIsArray($result);
        $this->assertSame('Updated Company', $result['name']);
    }
}
