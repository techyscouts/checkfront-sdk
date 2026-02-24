<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Response;

/** @implements \IteratorAggregate<int, mixed> */
class PaginatedResponse implements \IteratorAggregate, \Countable
{
    public function __construct(
        private readonly array $data,
        private readonly string $itemsKey,
        private readonly \Closure $nextPageFetcher,
    ) {
    }

    public function getItems(): array
    {
        return $this->data[$this->itemsKey] ?? [];
    }

    public function getTotalCount(): ?int
    {
        return isset($this->data['meta']['records']['total'])
            ? (int) $this->data['meta']['records']['total']
            : null;
    }

    public function getCurrentPage(): int
    {
        $offset = (int) ($this->data['meta']['records']['offset'] ?? 0);
        $limit = (int) ($this->data['meta']['records']['limit'] ?? 1);

        return $limit > 0 ? (int) floor($offset / $limit) + 1 : 1;
    }

    public function hasNextPage(): bool
    {
        return ($this->data['meta']['records']['nextPageUrl'] ?? null) !== null;
    }

    public function getNextPageUrl(): ?string
    {
        return $this->data['meta']['records']['nextPageUrl'] ?? null;
    }

    public function getMeta(): array
    {
        return $this->data['meta'] ?? [];
    }

    public function count(): int
    {
        return $this->getTotalCount() ?? count($this->getItems());
    }

    public function getIterator(): \Traversable
    {
        yield from $this->getItems();

        if ($this->hasNextPage()) {
            $nextPageData = ($this->nextPageFetcher)($this->getNextPageUrl());
            $nextPage = new self($nextPageData, $this->itemsKey, $this->nextPageFetcher);

            yield from $nextPage;
        }
    }
}
