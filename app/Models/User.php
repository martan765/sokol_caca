<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        return $this->belongsToMany(Training::class, 'training_attendance', 'user_id', 'training_id')
                    ->withPivot('status_id', 'note');
    }

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_attendance', 'user_id', 'game_id')
                    ->withPivot('status_id', 'note');
    }
}
