<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Účast hráče na tréninku (pivot mezi users a trainings).
 */
class TrainingAttendance extends Pivot
{
    protected $table = 'training_attendance';

    public $incrementing = true;

    public $timestamps = false;

    public function status(): BelongsTo
    {
        return $this->belongsTo(AttendanceStatus::class, 'status_id');
    }
}
