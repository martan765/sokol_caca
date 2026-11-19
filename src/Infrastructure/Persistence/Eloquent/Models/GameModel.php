<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GameModel extends Model
{
    protected $table = 'games';

    public $timestamps = false;

    protected $fillable = ['match_type_id', 'home_team', 'away_team', 'game_date', 'location', 'result'];

    protected function casts(): array
    {
        return [
            'game_date' => 'datetime',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(GameTypeModel::class, 'match_type_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(UserModel::class, 'game_attendance', 'game_id', 'user_id')
                    ->using(GameAttendanceModel::class)
                    ->withPivot('id', 'status_id', 'note');
    }
}
