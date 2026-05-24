<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_attendance', 'game_id', 'user_id')
                    ->withPivot('status_id', 'note');
    }
}
