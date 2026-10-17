<?php

namespace App\Services;

use App\Models\GameType;
use App\Repositories\Contracts\GameTypeRepositoryInterface;
use Illuminate\Support\Collection;

class GameTypeService
{
    public function __construct(
        private readonly GameTypeRepositoryInterface $types,
    ) {}

    public function all(): Collection
    {
        return $this->types->all();
    }

    public function create(array $data): GameType
    {
        return $this->types->create($data);
    }

    public function update(GameType $type, array $data): void
    {
        $this->types->update($type, $data);
    }

    /**
     * Typ, který už používá nějaký zápas, smazat nelze (zápas by ztratil typ).
     *
     * @return bool  false, pokud se typ nesmazal, protože je používaný
     */
    public function delete(GameType $type): bool
    {
        if ($this->types->isUsed($type)) {
            return false;
        }

        $this->types->delete($type);

        return true;
    }
}
