<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameType extends Model
{
    public $timestamps = false;

    protected $fillable = ['name'];

    public function games(): HasMany
    {
        return $this->hasMany(Game::class, 'match_type_id');
    }
}
