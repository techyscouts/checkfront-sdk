<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use TechyScouts\Checkfront\Response\PaginatedResponse;

final class CategoriesTest extends FeatureTestCase
{
    #[Test]
    public function listReturnsPaginatedResponse(): void
    {
        $response = self::$client->categories()->list();

        $this->assertInstanceOf(PaginatedResponse::class, $response);
        $this->assertIsArray($response->getItems());
        $this->assertIsArray($response->getMeta());
    }

    #[Test]
    public function paginationMetadataIsAccessible(): void
    {
        $response = self::$client->categories()->list();

        $this->assertIsInt($response->getCurrentPage());
        $this->assertIsBool($response->hasNextPage());

        $total = $response->getTotalCount();
        $this->assertTrue($total === null || is_int($total));
    }
}
