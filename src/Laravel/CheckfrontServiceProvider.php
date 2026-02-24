<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Laravel;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use TechyScouts\Checkfront\Auth\OAuth2Authentication;
use TechyScouts\Checkfront\Auth\TokenAuthentication;
use TechyScouts\Checkfront\CheckfrontClient;
use TechyScouts\Checkfront\Configuration;
use TechyScouts\Checkfront\Laravel\Facades\Checkfront;

final class CheckfrontServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/checkfront.php', 'checkfront');

        $this->app->singleton(CheckfrontClient::class, function (Application $app): CheckfrontClient {
            /** @var array{host: string, auth: array{type: string, basic: array{api_key: string, api_secret: string}, oauth2: array{access_token: string, refresh_token: ?string, expires_at: ?int}}} $config */
            $config = $app->make('config')->get('checkfront');

            $auth = match ($config['auth']['type']) {
                'oauth2' => new OAuth2Authentication(
                    accessToken: $config['auth']['oauth2']['access_token'],
                    refreshToken: $config['auth']['oauth2']['refresh_token'] ?? null,
                    expiresAt: $config['auth']['oauth2']['expires_at'] ? (int) $config['auth']['oauth2']['expires_at'] : null,
                ),
                default => new TokenAuthentication(
                    apiKey: $config['auth']['basic']['api_key'],
                    apiSecret: $config['auth']['basic']['api_secret'],
                ),
            };

            return new CheckfrontClient(new Configuration(
                host: $config['host'],
                auth: $auth,
            ));
        });

        $this->app->alias(CheckfrontClient::class, 'checkfront');

        AliasLoader::getInstance()->alias('Checkfront', Checkfront::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/checkfront.php' => $this->app->configPath('checkfront.php'),
            ], 'checkfront-config');
        }
    }
}
