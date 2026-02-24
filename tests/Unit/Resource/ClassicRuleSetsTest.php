<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\ClassicRuleSets;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(ClassicRuleSets::class)]
final class ClassicRuleSetsTest extends ResourceTestCase
{
    private ClassicRuleSets $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(ClassicRuleSets::class);
    }

    #[Test]
    public function listSendsGetRequestToClassicRuleSetsEndpoint(): void
    {
        $this->expectRequest('GET', '/classic/rulesets', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/classic/rulesets', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/classic/rulesets/3', ['id' => 3, 'name' => 'Test RuleSet']);

        $result = $this->resource->fetch(3);

        $this->assertIsArray($result);
        $this->assertSame(3, $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/classic/rulesets/3', ['include' => 'rules'], ['id' => 3]);

        $result = $this->resource->fetch(3, ['include' => 'rules']);

        $this->assertIsArray($result);
    }
}
