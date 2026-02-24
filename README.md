# Checkfront PHP SDK

A modern PHP SDK for the [Checkfront API v4.0](https://api.checkfront.com/).

Requires PHP 8.2+ and the `curl` extension.

## Installation

```bash
composer require techyscouts/checkfront-sdk
```

## Usage

```php
use TechyScouts\Checkfront\Auth\TokenAuthentication;
use TechyScouts\Checkfront\CheckfrontClient;
use TechyScouts\Checkfront\Configuration;

$config = new Configuration(
    host: 'https://your-company.checkfront.com',
    auth: new TokenAuthentication(apiKey: '...', apiSecret: '...'),
);

$client = new CheckfrontClient($config);

// List bookings
$bookings = $client->bookings()->list();

// Fetch a single booking
$booking = $client->bookings()->fetch('BK-1234');

// List products
$products = $client->products()->list();
```

### OAuth2

```php
use TechyScouts\Checkfront\Auth\OAuth2Authentication;

$auth = new OAuth2Authentication(accessToken: '...');
```

### Laravel

The package auto-discovers its service provider. Publish the config:

```bash
php artisan vendor:publish --provider="TechyScouts\Checkfront\Laravel\CheckfrontServiceProvider"
```

Then set `CHECKFRONT_HOST`, `CHECKFRONT_API_KEY`, and `CHECKFRONT_API_SECRET` in your `.env`.

## License

[MIT](LICENSE.md)
