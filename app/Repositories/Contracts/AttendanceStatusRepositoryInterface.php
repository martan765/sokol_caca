<?php

namespace App\Repositories\Contracts;

use App\Models\AttendanceStatus;
use Illuminate\Support\Collection;

interface AttendanceStatusRepositoryInterface
{
    /** @return Collection<int, AttendanceStatus> seřazené podle ID */
    public function all(): Collection;

    public function update(AttendanceStatus $status, array $data): void;
}
