<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use TechyScouts\Checkfront\Http\Factory\RequestFactory;
use TechyScouts\Checkfront\Http\Factory\StreamFactory;
use TechyScouts\Checkfront\Http\Factory\UriFactory;
use TechyScouts\Checkfront\Http\HttpClientDiscovery;
use TechyScouts\Checkfront\Resource\Accounts;
use TechyScouts\Checkfront\Resource\AssetEvents;
use TechyScouts\Checkfront\Resource\AssetPoolCategories;
use TechyScouts\Checkfront\Resource\AssetPools;
use TechyScouts\Checkfront\Resource\Bookings;
use TechyScouts\Checkfront\Resource\BookingGuests;
use TechyScouts\Checkfront\Resource\BookingNotes;
use TechyScouts\Checkfront\Resource\Bundles;
use TechyScouts\Checkfront\Resource\BundleImages;
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
use TechyScouts\Checkfront\Resource\ProductUpsells;
use TechyScouts\Checkfront\Resource\Products;
use TechyScouts\Checkfront\Resource\Statuses;
use TechyScouts\Checkfront\Resource\Tags;
use TechyScouts\Checkfront\Resource\Taxes;
use TechyScouts\Checkfront\Resource\Tokens;
use TechyScouts\Checkfront\Resource\Transactions;

final class CheckfrontClient
{
    private readonly ClientInterface $httpClient;
    private readonly RequestFactoryInterface $requestFactory;
    private readonly StreamFactoryInterface $streamFactory;
    private readonly UriFactoryInterface $uriFactory;

    /** @var array<class-string, object> */
    private array $resources = [];

    public function __construct(
        private readonly Configuration $configuration,
    ) {
        $this->httpClient = $configuration->getHttpClient() ?? HttpClientDiscovery::discover();
        $this->requestFactory = $configuration->getRequestFactory() ?? new RequestFactory();
        $this->streamFactory = $configuration->getStreamFactory() ?? new StreamFactory();
        $this->uriFactory = $configuration->getUriFactory() ?? new UriFactory();
    }

    public function accounts(): Accounts
    {
        return $this->resolve(Accounts::class);
    }

    public function assetEvents(): AssetEvents
    {
        return $this->resolve(AssetEvents::class);
    }

    public function assetPoolCategories(): AssetPoolCategories
    {
        return $this->resolve(AssetPoolCategories::class);
    }

    public function assetPools(): AssetPools
    {
        return $this->resolve(AssetPools::class);
    }

    public function bookings(): Bookings
    {
        return $this->resolve(Bookings::class);
    }

    public function bookingGuests(): BookingGuests
    {
        return $this->resolve(BookingGuests::class);
    }

    public function bookingNotes(): BookingNotes
    {
        return $this->resolve(BookingNotes::class);
    }

    public function bundles(): Bundles
    {
        return $this->resolve(Bundles::class);
    }

    public function bundleImages(): BundleImages
    {
        return $this->resolve(BundleImages::class);
    }

    public function categories(): Categories
    {
        return $this->resolve(Categories::class);
    }

    public function classicDiscounts(): ClassicDiscounts
    {
        return $this->resolve(ClassicDiscounts::class);
    }

    public function classicItems(): ClassicItems
    {
        return $this->resolve(ClassicItems::class);
    }

    public function classicRuleSets(): ClassicRuleSets
    {
        return $this->resolve(ClassicRuleSets::class);
    }

    public function company(): Company
    {
        return $this->resolve(Company::class);
    }

    public function customRates(): CustomRates
    {
        return $this->resolve(CustomRates::class);
    }

    public function customers(): Customers
    {
        return $this->resolve(Customers::class);
    }

    public function documentTemplates(): DocumentTemplates
    {
        return $this->resolve(DocumentTemplates::class);
    }

    public function formFields(): FormFields
    {
        return $this->resolve(FormFields::class);
    }

    public function giftCertificates(): GiftCertificates
    {
        return $this->resolve(GiftCertificates::class);
    }

    public function guestTypes(): GuestTypes
    {
        return $this->resolve(GuestTypes::class);
    }

    public function productAvailability(): ProductAvailability
    {
        return $this->resolve(ProductAvailability::class);
    }

    public function productClosures(): ProductClosures
    {
        return $this->resolve(ProductClosures::class);
    }

    public function productDefaultPricing(): ProductDefaultPricing
    {
        return $this->resolve(ProductDefaultPricing::class);
    }

    public function productDiscounts(): ProductDiscounts
    {
        return $this->resolve(ProductDiscounts::class);
    }

    public function productEvents(): ProductEvents
    {
        return $this->resolve(ProductEvents::class);
    }

    public function productGuestTypeMaps(): ProductGuestTypeMaps
    {
        return $this->resolve(ProductGuestTypeMaps::class);
    }

    public function productImages(): ProductImages
    {
        return $this->resolve(ProductImages::class);
    }

    public function productInventoryAllotments(): ProductInventoryAllotments
    {
        return $this->resolve(ProductInventoryAllotments::class);
    }

    public function productResources(): ProductResources
    {
        return $this->resolve(ProductResources::class);
    }

    public function productUpsells(): ProductUpsells
    {
        return $this->resolve(ProductUpsells::class);
    }

    public function products(): Products
    {
        return $this->resolve(Products::class);
    }

    public function statuses(): Statuses
    {
        return $this->resolve(Statuses::class);
    }

    public function tags(): Tags
    {
        return $this->resolve(Tags::class);
    }

    public function taxes(): Taxes
    {
        return $this->resolve(Taxes::class);
    }

    public function tokens(): Tokens
    {
        return $this->resolve(Tokens::class);
    }

    public function transactions(): Transactions
    {
        return $this->resolve(Transactions::class);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    private function resolve(string $class): object
    {
        return $this->resources[$class] ??= new $class(
            $this->httpClient,
            $this->configuration->getAuth(),
            $this->configuration,
            $this->requestFactory,
            $this->streamFactory,
            $this->uriFactory,
        );
    }
}
