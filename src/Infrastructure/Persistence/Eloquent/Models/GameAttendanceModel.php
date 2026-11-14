<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GameAttendanceModel extends Pivot
{
    protected $table = 'game_attendance';

    public $incrementing = true;

    public $timestamps = false;

    public function status(): BelongsTo
    {
        return $this->belongsTo(AttendanceStatusModel::class, 'status_id');
    }
}
