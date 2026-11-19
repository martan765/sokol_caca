<?php

namespace App\Repositories\Contracts;

use App\Models\GameType;
use Illuminate\Support\Collection;

interface GameTypeRepositoryInterface
{
    /** @return Collection<int, GameType> seřazené podle názvu, s počtem zápasů (games_count) */
    public function all(): Collection;

    public function create(array $data): GameType;

    public function update(GameType $type, array $data): void;

    public function delete(GameType $type): void;

    /** Používá typ nějaký zápas? */
    public function isUsed(GameType $type): bool;
}
