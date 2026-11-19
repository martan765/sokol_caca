<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Sokol\Infrastructure\Database\Seeders\ScheduleSeeder;
use Sokol\Infrastructure\Database\Seeders\UserSeeder;

/**
 * Ukázková data. Celou databázi vytvoříš příkazem:
 *   php artisan migrate:fresh --seed
 *
 * Číselníky (stavy účasti, typ "Mistrovské utkání") plní už migrace,
 * protože je aplikace potřebuje i bez ukázkových dat.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ScheduleSeeder::class,
        ]);
    }
}
