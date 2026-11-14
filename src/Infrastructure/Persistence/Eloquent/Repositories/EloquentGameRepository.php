<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Entities\Game;
use Sokol\Domain\Pagination\Page;
use Sokol\Domain\Repositories\GameRepositoryInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Mappers\GameMapper;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameModel;

final class EloquentGameRepository extends EloquentRepository implements GameRepositoryInterface
{
    protected function modelClass(): string
    {
        return GameModel::class;
    }

    protected function entityName(): string
    {
        return 'Zápas';
    }

    protected function toEntity(Model $model): Game
    {
        return GameMapper::toEntity($model);
    }

    public function findById(int $id): ?Game
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): Game
    {
        return $this->getEntity($id);
    }

    public function upcoming(DateTimeImmutable $from): array
    {
        return $this->toEntities($this->query()->where('game_date', '>=', $from)->orderBy('game_date'));
    }

    public function all(): array
    {
        return $this->toEntities($this->query());
    }

    public function paginate(int $page, int $perPage): Page
    {
        return $this->paginateQuery($this->query()->orderByDesc('game_date'), $page, $perPage);
    }

    public function save(Game $game): Game
    {
        $model = $this->modelFor($game->id());

        GameMapper::fill($model, $game)->save();

        return GameMapper::toEntity($model);
    }

    public function existsBetween(DateTimeImmutable $from, DateTimeImmutable $to, ?int $exceptId = null): bool
    {
        return $this->query()
            ->whereBetween('game_date', [$from, $to])
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }
}
