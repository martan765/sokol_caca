<?php

namespace App\Repositories\Contracts;

use App\Models\Training;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TrainingRepositoryInterface
{
    /**
     * Nadcházející tréninky (od nejbližšího) včetně hlasování hráčů.
     *
     * @return Collection<int, Training>
     */
    public function upcomingWithAttendance(): Collection;

    /** @return Collection<int, Training> */
    public function all(): Collection;

    /** Všechny tréninky od nejnovějšího, po stránkách. */
    public function paginate(int $perPage): LengthAwarePaginator;

    public function create(array $data): Training;

    public function update(Training $training, array $data): void;

    public function delete(Training $training): void;

    public function existsBetween(CarbonInterface $from, CarbonInterface $to, ?int $exceptId = null): bool;
}
