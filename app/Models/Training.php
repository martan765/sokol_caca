<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
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
        return $this->belongsToMany(User::class, 'training_attendance', 'training_id', 'user_id')
                    ->using(TrainingAttendance::class)
                    ->withPivot('id', 'status_id', 'note');
    }
}
