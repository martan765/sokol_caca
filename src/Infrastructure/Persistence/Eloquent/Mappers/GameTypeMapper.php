<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Mappers;

use Sokol\Domain\Entities\GameType;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameTypeModel;

final class GameTypeMapper
{
    public static function toEntity(GameTypeModel $model): GameType
    {
        return new GameType((int) $model->id, (string) $model->name);
    }

    public static function fill(GameTypeModel $model, GameType $type): GameTypeModel
    {
        return $model->fill(['name' => $type->name()]);
    }
}
