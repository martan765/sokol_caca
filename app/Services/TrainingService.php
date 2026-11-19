<?php

namespace App\Services;

use App\Models\Training;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TrainingService
{
    private const PER_PAGE = 15;

    public function __construct(
        private readonly TrainingRepositoryInterface $trainings,
    ) {}

    public function upcoming(): Collection
    {
        return $this->trainings->upcomingWithAttendance();
    }

    public function paginated(): LengthAwarePaginator
    {
        return $this->trainings->paginate(self::PER_PAGE);
    }

    public function plan(array $data): Training
    {
        return $this->trainings->create($data);
    }

    public function update(Training $training, array $data): void
    {
        $this->trainings->update($training, $data);
    }

    public function cancel(Training $training): void
    {
        $this->trainings->delete($training);
    }
}
