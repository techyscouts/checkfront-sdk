<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Resource;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Resource\FormFields;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(FormFields::class)]
final class FormFieldsTest extends ResourceTestCase
{
    private FormFields $resource;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = $this->createResource(FormFields::class);
    }

    #[Test]
    public function listSendsGetRequestToFormFieldsEndpoint(): void
    {
        $this->expectRequest('GET', '/form-fields', $this->paginatedBody());

        $result = $this->resource->list();

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function listForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/form-fields', ['page' => '1', 'limit' => '10'], $this->paginatedBody());

        $result = $this->resource->list(['page' => '1', 'limit' => '10']);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
    }

    #[Test]
    public function fetchSendsGetRequestWithId(): void
    {
        $this->expectRequest('GET', '/form-fields/custom_field_1', ['id' => 'custom_field_1', 'label' => 'Custom Field']);

        $result = $this->resource->fetch('custom_field_1');

        $this->assertIsArray($result);
        $this->assertSame('custom_field_1', $result['id']);
    }

    #[Test]
    public function fetchForwardsQueryParameters(): void
    {
        $this->expectRequestWithQuery('GET', '/form-fields/custom_field_1', ['include' => 'options'], ['id' => 'custom_field_1']);

        $result = $this->resource->fetch('custom_field_1', ['include' => 'options']);

        $this->assertIsArray($result);
    }
}
