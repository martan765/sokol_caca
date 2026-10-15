<?php

namespace App\Repositories\Eloquent;

use App\Models\Training;
use App\Repositories\Contracts\TrainingRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TrainingRepository implements TrainingRepositoryInterface
{
    public function upcomingWithAttendance(): Collection
    {
        return Training::with('users')
            ->where('training_date', '>=', now())
            ->orderBy('training_date')
            ->get();
    }

    public function all(): Collection
    {
        return Training::all();
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Training::orderByDesc('training_date')->paginate($perPage);
    }

    public function create(array $data): Training
    {
        return Training::create($data);
    }

    public function update(Training $training, array $data): void
    {
        $training->update($data);
    }

    public function delete(Training $training): void
    {
        $training->delete();
    }

    public function existsBetween(CarbonInterface $from, CarbonInterface $to, ?int $exceptId = null): bool
    {
        return Training::whereBetween('training_date', [$from, $to])
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }
}
