<?php

namespace App\Repositories\Eloquent;

use App\Models\AttendanceStatus;
use App\Repositories\Contracts\AttendanceStatusRepositoryInterface;
use Illuminate\Support\Collection;

class AttendanceStatusRepository implements AttendanceStatusRepositoryInterface
{
    public function all(): Collection
    {
        return AttendanceStatus::orderBy('id')->get();
    }

    public function update(AttendanceStatus $status, array $data): void
    {
        $status->update($data);
    }
}
