<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

final class CompanyTest extends FeatureTestCase
{
    #[Test]
    public function listReturnsCompanySettings(): void
    {
        $response = self::$client->company()->list();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);
    }
}
