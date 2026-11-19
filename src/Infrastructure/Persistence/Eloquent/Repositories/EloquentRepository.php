<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Exceptions\EntityNotFoundException;
use Sokol\Domain\Pagination\Page;

abstract class EloquentRepository
{
    abstract protected function modelClass(): string;

    abstract protected function entityName(): string;

    abstract protected function toEntity(Model $model): object;

    protected function findEntity(int $id): ?object
    {
        $model = $this->query()->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    protected function getEntity(int $id): object
    {
        return $this->toEntity($this->findModelOrFail($id));
    }

    public function exists(int $id): bool
    {
        return $this->query()->whereKey($id)->exists();
    }

    public function delete(int $id): void
    {
        $this->query()->find($id)?->delete();
    }

    protected function query(): Builder
    {
        $class = $this->modelClass();

        return $class::query();
    }

    protected function findModelOrFail(int $id): Model
    {
        return $this->query()->find($id) ?? throw EntityNotFoundException::for($this->entityName(), $id);
    }

    protected function modelFor(?int $id): Model
    {
        if ($id === null) {
            $class = $this->modelClass();

            return new $class();
        }

        return $this->findModelOrFail($id);
    }

    protected function toEntities(Builder $query): array
    {
        return $query->get()->map(fn (Model $model) => $this->toEntity($model))->values()->all();
    }

    protected function paginateQuery(Builder $query, int $page, int $perPage): Page
    {
        $page  = max(1, $page);
        $total = (clone $query)->count();
        $items = $this->toEntities($query->forPage($page, $perPage));

        return new Page($items, $total, $page, $perPage);
    }
}
