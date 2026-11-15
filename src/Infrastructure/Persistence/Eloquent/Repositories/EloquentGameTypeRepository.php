<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Entities\GameType;
use Sokol\Domain\Interfaces\Repositories\GameTypeRepositoryInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Mappers\GameTypeMapper;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameTypeModel;

final class EloquentGameTypeRepository extends EloquentRepository implements GameTypeRepositoryInterface
{
    protected function modelClass(): string
    {
        return GameTypeModel::class;
    }

    protected function entityName(): string
    {
        return 'Typ zápasu';
    }

    protected function toEntity(Model $model): GameType
    {
        return GameTypeMapper::toEntity($model);
    }

    public function findById(int $id): ?GameType
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): GameType
    {
        return $this->getEntity($id);
    }

    public function all(): array
    {
        return $this->toEntities($this->query()->orderBy('name'));
    }

    public function gameCounts(): array
    {
        return GameTypeModel::query()
            ->withCount('games')
            ->get()
            ->mapWithKeys(fn (GameTypeModel $type) => [(int) $type->id => (int) $type->games_count])
            ->all();
    }

    public function isUsed(int $id): bool
    {
        return GameModel::query()->where('match_type_id', $id)->exists();
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        return $this->query()
            ->where('name', $name)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }

    public function save(GameType $type): GameType
    {
        $model = $this->modelFor($type->id());

        GameTypeMapper::fill($model, $type)->save();

        return GameTypeMapper::toEntity($model);
    }
}
