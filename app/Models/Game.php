<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    public $timestamps = false;

    protected $fillable = ['match_type_id', 'home_team', 'away_team', 'game_date', 'location', 'result'];

    protected function casts(): array
    {
        return [
            'game_date' => 'datetime',
        ];
    }

    /**
     * Hrajeme doma? Náš klub je domácí tým (název klubu je v config/club.php).
     */
    public function isHome(): bool
    {
        return str_contains($this->home_team, config('club.name'));
    }

    public function isOurTeam(string $team): bool
    {
        return str_contains($team, config('club.name'));
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(GameType::class, 'match_type_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'game_attendance', 'game_id', 'user_id')
                    ->using(GameAttendance::class)
                    ->withPivot('id', 'status_id', 'note');
    }
}
