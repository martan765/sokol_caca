<?php

namespace App\Services;

use App\Models\Game;
use App\Repositories\Contracts\GameRepositoryInterface;
use App\Repositories\Contracts\GameTypeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GameService
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly GameRepositoryInterface $games,
        private readonly GameTypeRepositoryInterface $types,
    ) {}

    public function upcoming(): Collection
    {
        return $this->games->upcomingWithAttendance();
    }

    public function paginated(): LengthAwarePaginator
    {
        return $this->games->paginate(self::PER_PAGE);
    }

    public function types(): Collection
    {
        return $this->types->all();
    }

    public function schedule(array $data): Game
    {
        return $this->games->create($data);
    }

    public function update(Game $game, array $data): void
    {
        $this->games->update($game, $data);
    }

    public function cancel(Game $game): void
    {
        $this->games->delete($game);
    }
}
