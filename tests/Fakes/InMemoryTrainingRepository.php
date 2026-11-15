<?php

namespace Tests\Fakes;

use DateTimeImmutable;
use Sokol\Domain\Entities\Training;
use Sokol\Domain\Pagination\Page;
use Sokol\Domain\Repositories\TrainingRepositoryInterface;

final class InMemoryTrainingRepository extends InMemoryRepository implements TrainingRepositoryInterface
{
    protected function entityName(): string
    {
        return 'Trénink';
    }

    public function findById(int $id): ?Training
    {
        return $this->findEntity($id);
    }

    public function getById(int $id): Training
    {
        return $this->getEntity($id);
    }

    public function upcoming(DateTimeImmutable $from): array
    {
        $upcoming = array_values(array_filter($this->values(), fn (Training $t) => $t->date() >= $from));
        usort($upcoming, fn (Training $a, Training $b) => $a->date() <=> $b->date());

        return $upcoming;
    }

    public function all(): array
    {
        return $this->values();
    }

    public function paginate(int $page, int $perPage): Page
    {
        $all = $this->values();
        usort($all, fn (Training $a, Training $b) => $b->date() <=> $a->date());

        return $this->pageOf($all, $page, $perPage);
    }

    public function save(Training $training): Training
    {
        return $this->store($training);
    }

    public function existsBetween(DateTimeImmutable $from, DateTimeImmutable $to, ?int $exceptId = null): bool
    {
        foreach ($this->items as $id => $training) {
            if ($id !== $exceptId && $training->date() >= $from && $training->date() <= $to) {
                return true;
            }
        }

        return false;
    }
}
