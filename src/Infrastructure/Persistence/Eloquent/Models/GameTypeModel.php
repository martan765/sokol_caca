<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTypeModel extends Model
{
    protected $table = 'game_types';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function games(): HasMany
    {
        return $this->hasMany(GameModel::class, 'match_type_id');
    }
}
