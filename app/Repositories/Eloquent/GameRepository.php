<?php

namespace App\Repositories\Eloquent;

use App\Models\Game;
use App\Repositories\Contracts\GameRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GameRepository implements GameRepositoryInterface
{
    public function upcomingWithAttendance(): Collection
    {
        return Game::with(['users', 'type'])
            ->where('game_date', '>=', now())
            ->orderBy('game_date')
            ->get();
    }

    public function all(): Collection
    {
        return Game::all();
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Game::with('type')->orderByDesc('game_date')->paginate($perPage);
    }

    public function create(array $data): Game
    {
        return Game::create($data);
    }

    public function update(Game $game, array $data): void
    {
        $game->update($data);
    }

    public function delete(Game $game): void
    {
        $game->delete();
    }

    public function existsBetween(CarbonInterface $from, CarbonInterface $to, ?int $exceptId = null): bool
    {
        return Game::whereBetween('game_date', [$from, $to])
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }
}
