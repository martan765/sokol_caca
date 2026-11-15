<?php

namespace Sokol\Domain\Pagination;

final class Page
{
    public function __construct(
        public readonly array $items,
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
    ) {}

    public static function offsetFor(int $page, int $perPage): int
    {
        return (max(1, $page) - 1) * $perPage;
    }

    public function map(callable $map): self
    {
        return new self(array_values(array_map($map, $this->items)), $this->total, $this->page, $this->perPage);
    }
}
