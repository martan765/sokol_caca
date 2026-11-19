<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Mappers;

use DateTimeImmutable;
use Sokol\Domain\Entities\Game;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameModel;

final class GameMapper
{
    public static function toEntity(GameModel $model): Game
    {
        return new Game(
            (int) $model->id,
            (int) $model->match_type_id,
            (string) $model->home_team,
            (string) $model->away_team,
            DateTimeImmutable::createFromInterface($model->game_date),
            $model->location,
            $model->result,
        );
    }

    public static function fill(GameModel $model, Game $game): GameModel
    {
        return $model->fill([
            'match_type_id' => $game->gameTypeId(),
            'home_team'     => $game->homeTeam(),
            'away_team'     => $game->awayTeam(),
            'game_date'     => $game->date(),
            'location'      => $game->location(),
            'result'        => $game->result(),
        ]);
    }
}
