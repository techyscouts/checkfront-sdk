<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use TechyScouts\Checkfront\Auth\AuthenticationInterface;
use TechyScouts\Checkfront\CheckfrontClient;
use TechyScouts\Checkfront\Configuration;
use TechyScouts\Checkfront\Resource\Accounts;
use TechyScouts\Checkfront\Resource\AssetEvents;
use TechyScouts\Checkfront\Resource\AssetPoolCategories;
use TechyScouts\Checkfront\Resource\AssetPools;
use TechyScouts\Checkfront\Resource\BookingGuests;
use TechyScouts\Checkfront\Resource\BookingNotes;
use TechyScouts\Checkfront\Resource\Bookings;
use TechyScouts\Checkfront\Resource\BundleImages;
use TechyScouts\Checkfront\Resource\Bundles;
use TechyScouts\Checkfront\Resource\Categories;
use TechyScouts\Checkfront\Resource\ClassicDiscounts;
use TechyScouts\Checkfront\Resource\ClassicItems;
use TechyScouts\Checkfront\Resource\ClassicRuleSets;
use TechyScouts\Checkfront\Resource\Company;
use TechyScouts\Checkfront\Resource\CustomRates;
use TechyScouts\Checkfront\Resource\Customers;
use TechyScouts\Checkfront\Resource\DocumentTemplates;
use TechyScouts\Checkfront\Resource\FormFields;
use TechyScouts\Checkfront\Resource\GiftCertificates;
use TechyScouts\Checkfront\Resource\GuestTypes;
use TechyScouts\Checkfront\Resource\ProductAvailability;
use TechyScouts\Checkfront\Resource\ProductClosures;
use TechyScouts\Checkfront\Resource\ProductDefaultPricing;
use TechyScouts\Checkfront\Resource\ProductDiscounts;
use TechyScouts\Checkfront\Resource\ProductEvents;
use TechyScouts\Checkfront\Resource\ProductGuestTypeMaps;
use TechyScouts\Checkfront\Resource\ProductImages;
use TechyScouts\Checkfront\Resource\ProductInventoryAllotments;
use TechyScouts\Checkfront\Resource\ProductResources;
use TechyScouts\Checkfront\Resource\Products;
use TechyScouts\Checkfront\Resource\ProductUpsells;
use TechyScouts\Checkfront\Resource\Statuses;
use TechyScouts\Checkfront\Resource\Tags;
use TechyScouts\Checkfront\Resource\Taxes;
use TechyScouts\Checkfront\Resource\Tokens;
use TechyScouts\Checkfront\Resource\Transactions;

#[CoversClass(CheckfrontClient::class)]
final class CheckfrontClientTest extends TestCase
{
    private CheckfrontClient $client;

    protected function setUp(): void
    {
        $auth = $this->createMock(AuthenticationInterface::class);
        $httpClient = $this->createMock(ClientInterface::class);

        $config = new Configuration(
            'https://api.checkfront.com',
            $auth,
            $httpClient,
        );

        $this->client = new CheckfrontClient($config);
    }

    /**
     * @return array<string, array{string, class-string}>
     */
    public static function resourceMethodProvider(): array
    {
        return [
            'accounts' => ['accounts', Accounts::class],
            'assetEvents' => ['assetEvents', AssetEvents::class],
            'assetPoolCategories' => ['assetPoolCategories', AssetPoolCategories::class],
            'assetPools' => ['assetPools', AssetPools::class],
            'bookings' => ['bookings', Bookings::class],
            'bookingGuests' => ['bookingGuests', BookingGuests::class],
            'bookingNotes' => ['bookingNotes', BookingNotes::class],
            'bundles' => ['bundles', Bundles::class],
            'bundleImages' => ['bundleImages', BundleImages::class],
            'categories' => ['categories', Categories::class],
            'classicDiscounts' => ['classicDiscounts', ClassicDiscounts::class],
            'classicItems' => ['classicItems', ClassicItems::class],
            'classicRuleSets' => ['classicRuleSets', ClassicRuleSets::class],
            'company' => ['company', Company::class],
            'customRates' => ['customRates', CustomRates::class],
            'customers' => ['customers', Customers::class],
            'documentTemplates' => ['documentTemplates', DocumentTemplates::class],
            'formFields' => ['formFields', FormFields::class],
            'giftCertificates' => ['giftCertificates', GiftCertificates::class],
            'guestTypes' => ['guestTypes', GuestTypes::class],
            'productAvailability' => ['productAvailability', ProductAvailability::class],
            'productClosures' => ['productClosures', ProductClosures::class],
            'productDefaultPricing' => ['productDefaultPricing', ProductDefaultPricing::class],
            'productDiscounts' => ['productDiscounts', ProductDiscounts::class],
            'productEvents' => ['productEvents', ProductEvents::class],
            'productGuestTypeMaps' => ['productGuestTypeMaps', ProductGuestTypeMaps::class],
            'productImages' => ['productImages', ProductImages::class],
            'productInventoryAllotments' => ['productInventoryAllotments', ProductInventoryAllotments::class],
            'productResources' => ['productResources', ProductResources::class],
            'productUpsells' => ['productUpsells', ProductUpsells::class],
            'products' => ['products', Products::class],
            'statuses' => ['statuses', Statuses::class],
            'tags' => ['tags', Tags::class],
            'taxes' => ['taxes', Taxes::class],
            'tokens' => ['tokens', Tokens::class],
            'transactions' => ['transactions', Transactions::class],
        ];
    }

    #[Test]
    #[DataProvider('resourceMethodProvider')]
    public function resourceMethodReturnsCorrectType(string $method, string $expectedClass): void
    {
        $resource = $this->client->$method();

        $this->assertInstanceOf($expectedClass, $resource);
    }

    #[Test]
    #[DataProvider('resourceMethodProvider')]
    public function resourceMethodReturnsSameInstanceOnSubsequentCalls(string $method, string $expectedClass): void
    {
        $first = $this->client->$method();
        $second = $this->client->$method();

        $this->assertSame($first, $second, "Expected {$method}() to return the same cached instance on repeated calls.");
    }

    #[Test]
    public function allThirtySixResourceMethodsExist(): void
    {
        $expectedMethods = [
            'accounts', 'assetEvents', 'assetPoolCategories', 'assetPools',
            'bookings', 'bookingGuests', 'bookingNotes',
            'bundles', 'bundleImages',
            'categories',
            'classicDiscounts', 'classicItems', 'classicRuleSets',
            'company', 'customRates', 'customers',
            'documentTemplates',
            'formFields',
            'giftCertificates', 'guestTypes',
            'productAvailability', 'productClosures', 'productDefaultPricing',
            'productDiscounts', 'productEvents', 'productGuestTypeMaps',
            'productImages', 'productInventoryAllotments', 'productResources',
            'productUpsells', 'products',
            'statuses', 'tags', 'taxes', 'tokens', 'transactions',
        ];

        $this->assertCount(36, $expectedMethods);

        foreach ($expectedMethods as $method) {
            $this->assertTrue(
                method_exists($this->client, $method),
                "Expected method {$method}() to exist on CheckfrontClient."
            );
        }
    }

    #[Test]
    public function differentResourceMethodsReturnDifferentInstances(): void
    {
        $bookings = $this->client->bookings();
        $customers = $this->client->customers();

        $this->assertNotSame($bookings, $customers);
    }
}
