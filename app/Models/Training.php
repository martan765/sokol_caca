<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    public $timestamps = false;

    protected $fillable = ['training_date', 'location', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'training_attendance', 'training_id', 'user_id')
                    ->withPivot('status_id', 'note');
    }
}
