<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Mappers;

use DateTimeImmutable;
use Sokol\Domain\Entities\Training;
use Sokol\Infrastructure\Persistence\Eloquent\Models\TrainingModel;

final class TrainingMapper
{
    public static function toEntity(TrainingModel $model): Training
    {
        return new Training(
            (int) $model->id,
            DateTimeImmutable::createFromInterface($model->training_date),
            (string) $model->location,
            $model->description,
        );
    }

    public static function fill(TrainingModel $model, Training $training): TrainingModel
    {
        return $model->fill([
            'training_date' => $training->date(),
            'location'      => $training->location(),
            'description'   => $training->description(),
        ]);
    }
}
