<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Účast hráče na zápase (pivot mezi users a games).
 */
class GameAttendance extends Pivot
{
    protected $table = 'game_attendance';

    public $incrementing = true;

    public $timestamps = false;

    public function status(): BelongsTo
    {
        return $this->belongsTo(AttendanceStatus::class, 'status_id');
    }
}
