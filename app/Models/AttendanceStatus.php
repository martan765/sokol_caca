<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Systémový číselník (Budu / Nebudu). Aplikace pracuje s pevnými ID,
 * proto lze stavy jen přejmenovat, ne přidávat ani mazat.
 */
class AttendanceStatus extends Model
{
    public const ATTENDING     = 1;
    public const NOT_ATTENDING = 2;

    public $timestamps = false;

    protected $fillable = ['name'];
}
