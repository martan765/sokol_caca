<?php

namespace Tests\Fakes;

use Sokol\Domain\Entities\GameType;
use Sokol\Domain\Interfaces\Repositories\GameRepositoryInterface;
use Sokol\Domain\Interfaces\Repositories\GameTypeRepositoryInterface;

final class InMemoryGameTypeRepository extends InMemoryRepository implements GameTypeRepositoryInterface
{
    public function __construct(
        private readonly ?GameRepositoryInterface $games = null,
    ) {}

    protected function entityName(): string
    {
        return 'Typ zápasu';
    }

    public function findById(int $id): ?GameType
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): GameType
    {
        return $this->getEntity($id);
    }

    public function all(): array
    {
        $all = $this->values();
        usort($all, fn (GameType $a, GameType $b) => strcmp($a->name(), $b->name()));

        return $all;
    }

    public function gameCounts(): array
    {
        $counts = [];
        foreach ($this->games?->all() ?? [] as $game) {
            $counts[$game->gameTypeId()] = ($counts[$game->gameTypeId()] ?? 0) + 1;
        }

        return $counts;
    }

    public function isUsed(int $id): bool
    {
        return ($this->gameCounts()[$id] ?? 0) > 0;
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        foreach ($this->items as $id => $type) {
            if ($type->name() === $name && $id !== $exceptId) {
                return true;
            }
        }

        return false;
    }

    public function save(GameType $type): GameType
    {
        return $this->store($type);
    }
}
