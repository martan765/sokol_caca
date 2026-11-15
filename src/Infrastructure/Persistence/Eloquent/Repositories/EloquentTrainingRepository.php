<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Repositories;

use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Interfaces\Repositories\TrainingRepositoryInterface;
use Sokol\Domain\Pagination\Page;
use Sokol\Infrastructure\Persistence\Eloquent\Mappers\TrainingMapper;
use Sokol\Infrastructure\Persistence\Eloquent\Models\TrainingModel;

final class EloquentTrainingRepository extends EloquentRepository implements TrainingRepositoryInterface
{
    protected function modelClass(): string
    {
        return TrainingModel::class;
    }

    protected function entityName(): string
    {
        return 'Trénink';
    }

    protected function toEntity(Model $model): Training
    {
        return TrainingMapper::toEntity($model);
    }

    public function findById(int $id): ?Training
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): Training
    {
        return $this->getEntity($id);
    }

    public function upcoming(DateTimeImmutable $from): array
    {
        return $this->toEntities($this->query()->where('training_date', '>=', $from)->orderBy('training_date'));
    }

    public function all(): array
    {
        return $this->toEntities($this->query());
    }

    public function paginate(int $page, int $perPage): Page
    {
        return $this->paginateQuery($this->query()->orderByDesc('training_date'), $page, $perPage);
    }

    public function save(Training $training): Training
    {
        $model = $this->modelFor($training->id());

        TrainingMapper::fill($model, $training)->save();

        return TrainingMapper::toEntity($model);
    }

    public function existsBetween(DateTimeImmutable $from, DateTimeImmutable $to, ?int $exceptId = null): bool
    {
        return $this->query()
            ->whereBetween('training_date', [$from, $to])
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }
}
