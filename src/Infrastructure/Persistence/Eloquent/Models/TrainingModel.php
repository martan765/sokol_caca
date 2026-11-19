<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingModel extends Model
{
    protected $table = 'trainings';

    public $timestamps = false;

    protected $fillable = ['training_date', 'location', 'description'];

    protected function casts(): array
    {
        return [
            'training_date' => 'datetime',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserModel::class, 'training_attendance', 'training_id', 'user_id')
                    ->using(TrainingAttendanceModel::class)
                    ->withPivot('id', 'status_id', 'note');
    }
}
