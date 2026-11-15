<?php

namespace Tests\Fakes;

use DateTimeImmutable;
use Sokol\Domain\Entities\Game;
use Sokol\Domain\Pagination\Page;
use Sokol\Domain\Repositories\GameRepositoryInterface;

final class InMemoryGameRepository extends InMemoryRepository implements GameRepositoryInterface
{
    protected function entityName(): string
    {
        return 'Zápas';
    }

    public function findById(int $id): ?Game
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): Game
    {
        return $this->getEntity($id);
    }

    public function upcoming(DateTimeImmutable $from): array
    {
        $upcoming = array_values(array_filter($this->values(), fn (Game $g) => $g->date() >= $from));
        usort($upcoming, fn (Game $a, Game $b) => $a->date() <=> $b->date());

        return $upcoming;
    }

    public function all(): array
    {
        return $this->values();
    }

    public function paginate(int $page, int $perPage): Page
    {
        $all = $this->values();
        usort($all, fn (Game $a, Game $b) => $b->date() <=> $a->date());

        return $this->pageOf($all, $page, $perPage);
    }

    public function save(Game $game): Game
    {
        return $this->store($game);
    }

    public function existsBetween(DateTimeImmutable $from, DateTimeImmutable $to, ?int $exceptId = null): bool
    {
        foreach ($this->items as $id => $game) {
            if ($id !== $exceptId && $game->date() >= $from && $game->date() <= $to) {
                return true;
            }
        }

        return false;
    }

    public function games(): array
    {
        return $this->values();
    }
}
