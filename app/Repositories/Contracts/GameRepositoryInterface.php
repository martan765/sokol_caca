<?php

namespace App\Repositories\Contracts;

use App\Models\Game;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GameRepositoryInterface
{
    /**
     * Nadcházející zápasy (od nejbližšího) včetně hlasování hráčů.
     *
     * @return Collection<int, Game>
     */
    public function upcomingWithAttendance(): Collection;

    /** @return Collection<int, Game> */
    public function all(): Collection;

    /** Všechny zápasy od nejnovějšího včetně typu utkání, po stránkách. */
    public function paginate(int $perPage): LengthAwarePaginator;

    public function create(array $data): Game;

    public function update(Game $game, array $data): void;

    public function delete(Game $game): void;

    public function existsBetween(CarbonInterface $from, CarbonInterface $to, ?int $exceptId = null): bool;
}
