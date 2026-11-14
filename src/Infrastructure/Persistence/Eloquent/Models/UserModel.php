<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(TrainingModel::class, 'training_attendance', 'user_id', 'training_id')
                    ->using(TrainingAttendanceModel::class)
                    ->withPivot('id', 'status_id', 'note');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(GameModel::class, 'game_attendance', 'user_id', 'game_id')
                    ->using(GameAttendanceModel::class)
                    ->withPivot('id', 'status_id', 'note');
    }
}
