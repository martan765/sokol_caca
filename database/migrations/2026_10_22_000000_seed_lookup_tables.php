<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Číselníky, bez kterých aplikace nefunguje (FK z docházky a zápasů).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('attendance_statuses')->insertOrIgnore([
            ['id' => 1, 'name' => 'Budu'],
            ['id' => 2, 'name' => 'Nebudu'],
        ]);

        DB::table('game_types')->insertOrIgnore([
            ['id' => 1, 'name' => 'Mistrovské utkání'],
        ]);
    }

    public function down(): void
    {
        // Číselníky nemažeme – mohou na ně odkazovat existující data
    }
};
