<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use TechyScouts\Checkfront\CheckfrontClient;

/**
 * @mixin CheckfrontClient
 */
final class Checkfront extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CheckfrontClient::class;
    }
}
