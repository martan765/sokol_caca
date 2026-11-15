<?php

namespace Sokol\Application\Implementation;

use Sokol\Application\Abstraction\GameServiceInterface;
use Sokol\Application\Dto\GameData;
use Sokol\Application\Support\Changes;
use Sokol\Application\ViewModels\GameTypeView;
use Sokol\Application\ViewModels\GameView;
use Sokol\Domain\Entities\Game;
use Sokol\Domain\Entities\GameType;
use Sokol\Domain\Enums\EventType;
use Sokol\Domain\Interfaces\Managers\ClockInterface;
use Sokol\Domain\Interfaces\Managers\ClubSettingsInterface;
use Sokol\Domain\Interfaces\Repositories\GameRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\GameTypeRepositoryInterface;
use Sokol\Domain\Pagination\Page;

class GameService implements GameServiceInterface
{
    public const PER_PAGE = 15;

    public function __construct(
        private readonly GameRepositoryInterface $games,
        private readonly GameTypeRepositoryInterface $types,
        private readonly VoterListBuilder $voters,
        private readonly ClubSettingsInterface $club,
        private readonly ClockInterface $clock,
    ) {}

    public function upcoming(?int $viewerId): array
    {
        $games = $this->games->upcoming($this->clock->now());
        $votes = $this->voters->forEvents(
            EventType::Game,
            array_map(fn (Game $g) => (int) $g->id(), $games),
            $viewerId,
        );
        $typeNames = $this->typeNames();

        return array_map(fn (Game $g) => $this->toView($g, $typeNames, $votes[$g->id()] ?? null), $games);
    }

    public function paginated(int $page): Page
    {
        $typeNames = $this->typeNames();

        return $this->games->paginate($page, self::PER_PAGE)->map(fn (Game $g) => $this->toView($g, $typeNames));
    }

    public function get(int $id): GameView
    {
        return $this->toView($this->games->getById($id), $this->typeNames());
    }

    public function exists(int $id): bool
    {
        return $this->games->exists($id);
    }

    public function types(): array
    {
        return array_map(fn (GameType $t) => new GameTypeView((int) $t->id(), $t->name()), $this->types->all());
    }

    public function schedule(GameData $data): GameView
    {
        $game = $this->games->save(Game::schedule(
            $data->gameTypeId,
            $data->homeTeam,
            $data->awayTeam,
            $data->date,
            $data->location,
            $data->result,
        ));

        return $this->toView($game, $this->typeNames());
    }

    public function update(int $id, GameData $data): array
    {
        $game   = $this->games->getById($id);
        $before = $this->snapshot($game);

        $game->update($data->gameTypeId, $data->homeTeam, $data->awayTeam, $data->date, $data->location, $data->result);
        $this->games->save($game);

        return Changes::between($before, $this->snapshot($game));
    }

    public function cancel(int $id): GameView
    {
        $view = $this->get($id);

        $this->games->delete($id);

        return $view;
    }

    private function typeNames(): array
    {
        $names = [];
        foreach ($this->types->all() as $type) {
            $names[(int) $type->id()] = $type->name();
        }

        return $names;
    }

    private function snapshot(Game $game): array
    {
        return [
            'match_type_id' => $game->gameTypeId(),
            'home_team'     => $game->homeTeam(),
            'away_team'     => $game->awayTeam(),
            'game_date'     => $game->date(),
            'location'      => $game->location(),
            'result'        => $game->result(),
        ];
    }

    private function toView(Game $game, array $typeNames, ?array $votes = null): GameView
    {
        $club = $this->club->clubName();

        return new GameView(
            (int) $game->id(),
            $game->gameTypeId(),
            $typeNames[$game->gameTypeId()] ?? null,
            $game->homeTeam(),
            $game->awayTeam(),
            $game->label(),
            $game->date(),
            $game->location(),
            $game->result(),
            $game->isHomeFor($club),
            $game->isOurTeam($game->homeTeam(), $club),
            $game->isOurTeam($game->awayTeam(), $club),
            $game->isPastAt($this->clock->now()),
            $votes['voters'] ?? [],
            $votes['myStatusId'] ?? null,
        );
    }
}
