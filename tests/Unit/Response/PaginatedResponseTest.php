<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Response\PaginatedResponse;

#[CoversClass(PaginatedResponse::class)]
final class PaginatedResponseTest extends TestCase
{
    #[Test]
    public function getItemsReturnsItemsByKey(): void
    {
        $data = [
            'data' => [
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
            ],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertSame($data['data'], $response->getItems());
    }

    #[Test]
    public function getItemsReturnsEmptyArrayWhenKeyMissing(): void
    {
        $response = new PaginatedResponse([], 'data', fn () => []);

        $this->assertSame([], $response->getItems());
    }

    #[Test]
    public function getItemsWithCustomKey(): void
    {
        $data = [
            'bookings' => [
                ['id' => 1],
            ],
        ];

        $response = new PaginatedResponse($data, 'bookings', fn () => []);

        $this->assertSame($data['bookings'], $response->getItems());
    }

    #[Test]
    public function getTotalCountReturnsTotalFromMeta(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['total' => 42]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertSame(42, $response->getTotalCount());
    }

    #[Test]
    public function getTotalCountReturnsNullWhenMetaMissing(): void
    {
        $response = new PaginatedResponse(['data' => []], 'data', fn () => []);

        $this->assertNull($response->getTotalCount());
    }

    #[Test]
    public function getTotalCountReturnsNullWhenTotalMissing(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['offset' => 0, 'limit' => 25]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertNull($response->getTotalCount());
    }

    #[Test]
    public function getCurrentPageReturnsPageFromMeta(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['offset' => 50, 'limit' => 25]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertSame(3, $response->getCurrentPage());
    }

    #[Test]
    public function getCurrentPageReturns1WhenMetaMissing(): void
    {
        $response = new PaginatedResponse(['data' => []], 'data', fn () => []);

        $this->assertSame(1, $response->getCurrentPage());
    }

    #[Test]
    public function getCurrentPageReturns1WhenOffsetIsZero(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['offset' => 0, 'limit' => 25]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertSame(1, $response->getCurrentPage());
    }

    #[Test]
    public function hasNextPageReturnsTrueWhenNextPageUrlExists(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['nextPageUrl' => 'https://api.example.com/bookings?page=2']],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertTrue($response->hasNextPage());
    }

    #[Test]
    public function hasNextPageReturnsFalseWhenNoNextPageUrl(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['nextPageUrl' => null]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertFalse($response->hasNextPage());
    }

    #[Test]
    public function hasNextPageReturnsFalseWhenMetaMissing(): void
    {
        $response = new PaginatedResponse(['data' => []], 'data', fn () => []);

        $this->assertFalse($response->hasNextPage());
    }

    #[Test]
    public function getNextPageUrlReturnsUrl(): void
    {
        $data = [
            'data' => [],
            'meta' => ['records' => ['nextPageUrl' => 'https://api.example.com/bookings?page=2']],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertSame('https://api.example.com/bookings?page=2', $response->getNextPageUrl());
    }

    #[Test]
    public function getNextPageUrlReturnsNullWhenNotAvailable(): void
    {
        $response = new PaginatedResponse(['data' => []], 'data', fn () => []);

        $this->assertNull($response->getNextPageUrl());
    }

    #[Test]
    public function getMetaReturnsMeta(): void
    {
        $meta = ['records' => ['total' => 100, 'offset' => 0, 'limit' => 25]];
        $data = [
            'data' => [],
            'meta' => $meta,
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertSame($meta, $response->getMeta());
    }

    #[Test]
    public function getMetaReturnsEmptyArrayWhenMissing(): void
    {
        $response = new PaginatedResponse(['data' => []], 'data', fn () => []);

        $this->assertSame([], $response->getMeta());
    }

    #[Test]
    public function countReturnsTotalCountWhenAvailable(): void
    {
        $data = [
            'data' => [['id' => 1], ['id' => 2]],
            'meta' => ['records' => ['total' => 50]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertCount(50, $response);
    }

    #[Test]
    public function countReturnsItemCountWhenTotalNotAvailable(): void
    {
        $data = [
            'data' => [['id' => 1], ['id' => 2], ['id' => 3]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $this->assertCount(3, $response);
    }

    #[Test]
    public function iterationYieldsCurrentPageItems(): void
    {
        $items = [['id' => 1], ['id' => 2]];
        $data = [
            'data' => $items,
            'meta' => ['records' => ['nextPageUrl' => null]],
        ];

        $response = new PaginatedResponse($data, 'data', fn () => []);

        $collected = [];
        foreach ($response as $item) {
            $collected[] = $item;
        }

        $this->assertSame($items, $collected);
    }

    #[Test]
    public function iterationFetchesNextPages(): void
    {
        $page1Data = [
            'data' => [['id' => 1], ['id' => 2]],
            'meta' => [
                'records' => [
                    'offset' => 0,
                    'limit' => 2,
                    'nextPageUrl' => 'https://api.example.com/bookings?page=2',
                ],
            ],
        ];

        $page2Data = [
            'data' => [['id' => 3], ['id' => 4]],
            'meta' => [
                'records' => [
                    'offset' => 2,
                    'limit' => 2,
                    'nextPageUrl' => null,
                ],
            ],
        ];

        $fetcher = function (string $url) use ($page2Data): array {
            $this->assertSame('https://api.example.com/bookings?page=2', $url);
            return $page2Data;
        };

        $response = new PaginatedResponse($page1Data, 'data', $fetcher);

        $collected = [];
        foreach ($response as $item) {
            $collected[] = $item;
        }

        $this->assertCount(4, $collected);
        $this->assertSame(['id' => 1], $collected[0]);
        $this->assertSame(['id' => 2], $collected[1]);
        $this->assertSame(['id' => 3], $collected[2]);
        $this->assertSame(['id' => 4], $collected[3]);
    }

    #[Test]
    public function iterationHandlesThreePages(): void
    {
        $page3Data = [
            'data' => [['id' => 5]],
            'meta' => ['records' => ['offset' => 4, 'limit' => 2, 'nextPageUrl' => null]],
        ];

        $page2Data = [
            'data' => [['id' => 3], ['id' => 4]],
            'meta' => [
                'records' => [
                    'offset' => 2,
                    'limit' => 2,
                    'nextPageUrl' => 'https://api.example.com/bookings?page=3',
                ],
            ],
        ];

        $page1Data = [
            'data' => [['id' => 1], ['id' => 2]],
            'meta' => [
                'records' => [
                    'offset' => 0,
                    'limit' => 2,
                    'nextPageUrl' => 'https://api.example.com/bookings?page=2',
                ],
            ],
        ];

        $pages = [
            'https://api.example.com/bookings?page=2' => $page2Data,
            'https://api.example.com/bookings?page=3' => $page3Data,
        ];

        $fetcher = function (string $url) use ($pages): array {
            return $pages[$url];
        };

        $response = new PaginatedResponse($page1Data, 'data', $fetcher);

        $collected = [];
        foreach ($response as $item) {
            $collected[] = $item;
        }

        $this->assertCount(5, $collected);
        $this->assertSame(5, $collected[4]['id']);
    }

    #[Test]
    public function emptyPaginatedResponseIteratesNothing(): void
    {
        $response = new PaginatedResponse(
            ['data' => [], 'meta' => ['records' => ['nextPageUrl' => null]]],
            'data',
            fn () => [],
        );

        $collected = [];
        foreach ($response as $item) {
            $collected[] = $item;
        }

        $this->assertSame([], $collected);
    }

    #[Test]
    public function implementsCountable(): void
    {
        $response = new PaginatedResponse(
            ['data' => [['id' => 1]], 'meta' => ['records' => ['total' => 100]]],
            'data',
            fn () => [],
        );

        $this->assertInstanceOf(\Countable::class, $response);
    }

    #[Test]
    public function implementsIteratorAggregate(): void
    {
        $response = new PaginatedResponse(
            ['data' => []],
            'data',
            fn () => [],
        );

        $this->assertInstanceOf(\IteratorAggregate::class, $response);
    }
}
