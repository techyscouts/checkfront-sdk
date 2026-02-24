<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Feature;

use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Auth\TokenAuthentication;
use TechyScouts\Checkfront\CheckfrontClient;
use TechyScouts\Checkfront\Configuration;

abstract class FeatureTestCase extends TestCase
{
    protected static ?CheckfrontClient $client = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$client !== null) {
            return;
        }

        $env = $this->loadEnv();

        $apiKey = $env['CHECKFRONT_API_KEY'] ?? '';
        $apiSecret = $env['CHECKFRONT_API_SECRET'] ?? '';
        $host = $env['CHECKFRONT_HOST'] ?? '';

        if ($apiKey === '' || $apiSecret === '' || $host === '') {
            $this->markTestSkipped('Missing CHECKFRONT_API_KEY, CHECKFRONT_API_SECRET, or CHECKFRONT_HOST in .env');
        }

        $auth = new TokenAuthentication($apiKey, $apiSecret);
        $configuration = new Configuration(
            host: "https://{$host}/api/4.0",
            auth: $auth,
        );

        self::$client = new CheckfrontClient($configuration);
    }

    private function loadEnv(): array
    {
        $path = dirname(__DIR__, 2) . '/.env';

        if (!is_file($path)) {
            return [];
        }

        $vars = [];

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $vars[trim($key)] = trim($value);
        }

        return $vars;
    }
}
