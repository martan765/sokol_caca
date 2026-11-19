<?php

namespace App\Repositories\Eloquent;

use App\Models\GameType;
use App\Repositories\Contracts\GameTypeRepositoryInterface;
use Illuminate\Support\Collection;

class GameTypeRepository implements GameTypeRepositoryInterface
{
    public function all(): Collection
    {
        return GameType::withCount('games')->orderBy('name')->get();
    }

    public function create(array $data): GameType
    {
        return GameType::create($data);
    }

    public function update(GameType $type, array $data): void
    {
        $type->update($data);
    }

    public function delete(GameType $type): void
    {
        $type->delete();
    }

    public function isUsed(GameType $type): bool
    {
        return $type->games()->exists();
    }
}
