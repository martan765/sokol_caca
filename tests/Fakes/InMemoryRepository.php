<?php

namespace Tests\Fakes;

use ReflectionProperty;
use Sokol\Domain\Exceptions\EntityNotFoundException;
use Sokol\Domain\Pagination\Page;

abstract class InMemoryRepository
{
    protected array $items = [];

    private int $nextId = 1;

    abstract protected function entityName(): string;

    protected function findEntity(int $id): ?object
    {
        return isset($this->items[$id]) ? clone $this->items[$id] : null;
    }

    protected function getEntity(int $id): object
    {
        return $this->findEntity($id) ?? throw EntityNotFoundException::for($this->entityName(), $id);
    }

    public function exists(int $id): bool
    {
        return isset($this->items[$id]);
    }

    public function delete(int $id): void
    {
        unset($this->items[$id]);
    }

    protected function store(object $entity): object
    {
        $id = $entity->id();

        if ($id === null) {
            $id = $this->nextId++;
            $property = new ReflectionProperty($entity, 'id');
            $property->setValue($entity, $id);
        } else {
            $this->nextId = max($this->nextId, $id + 1);
        }

        $this->items[$id] = clone $entity;

        return clone $entity;
    }

    protected function values(): array
    {
        return array_values(array_map(fn (object $item) => clone $item, $this->items));
    }

    protected function pageOf(array $sorted, int $page, int $perPage): Page
    {
        return new Page(array_slice($sorted, Page::offsetFor($page, $perPage), $perPage), count($sorted), max(1, $page), $perPage);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
