<?php

namespace Database\Seeders;

use App\Models\AttendanceStatus;
use App\Models\Game;
use App\Models\GameType;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Jarní část sezóny 2026 (tréninky, mistrovské zápasy a docházka hráčů)
 * a pár nadcházejících akcí, aby bylo na stránkách co hlasovat.
 *
 * Hráči se v docházce uvádějí loginem = částí e-mailu před @ (viz UserSeeder).
 */
class ScheduleSeeder extends Seeder
{
    /** [datum, místo, přijdou, nepřijdou] */
    private const TRAININGS = [
        ['2026-02-17 17:30', 'Umělka Zlámanec', ['novak', 'dvorak', 'svoboda', 'marek', 'cerny'], ['kucera', 'benes', 'prochazka', 'vesely']],
        ['2026-02-20 17:30', 'Umělka Zlámanec', ['novak', 'dvorak', 'marek', 'benes', 'vesely'], ['kucera', 'svoboda', 'cerny', 'prochazka']],
        ['2026-03-06 17:00', 'Hřiště Částkov', ['novak', 'dvorak', 'svoboda', 'marek', 'cerny'], ['kucera', 'benes', 'prochazka', 'vesely']],
        ['2026-03-13 17:00', 'Hřiště Částkov', ['novak', 'dvorak', 'marek', 'benes', 'vesely'], ['kucera', 'svoboda', 'cerny', 'prochazka']],
        ['2026-03-20 17:00', 'Hřiště Částkov', ['novak', 'svoboda', 'marek', 'benes', 'cerny'], ['kucera', 'dvorak', 'prochazka', 'vesely']],
        ['2026-03-27 17:00', 'Hřiště Částkov', ['novak', 'dvorak', 'svoboda', 'marek', 'prochazka'], ['kucera', 'benes', 'cerny', 'vesely']],
        ['2026-04-03 17:30', 'Hřiště Částkov', ['novak', 'dvorak', 'marek', 'benes', 'cerny'], ['kucera', 'svoboda', 'prochazka', 'vesely']],
        ['2026-04-10 17:30', 'Hřiště Částkov', ['novak', 'dvorak', 'svoboda', 'marek', 'benes', 'vesely'], ['kucera', 'cerny', 'prochazka']],
        ['2026-04-17 17:30', 'Hřiště Částkov', ['novak', 'dvorak', 'marek', 'cerny'], ['kucera', 'svoboda', 'benes', 'prochazka', 'vesely']],
        ['2026-04-24 17:30', 'Hřiště Částkov', ['novak', 'dvorak', 'svoboda', 'marek', 'benes', 'prochazka'], ['kucera', 'cerny', 'vesely']],
        ['2026-05-01 18:00', 'Hřiště Částkov', ['novak', 'dvorak', 'marek', 'benes', 'cerny'], ['kucera', 'svoboda', 'prochazka', 'vesely']],
        ['2026-05-08 18:00', 'Hřiště Částkov', ['novak', 'dvorak', 'svoboda', 'marek', 'vesely'], ['kucera', 'benes', 'cerny', 'prochazka']],
        ['2026-05-15 18:00', 'Hřiště Částkov', ['novak', 'svoboda', 'marek', 'benes', 'cerny'], ['kucera', 'dvorak', 'prochazka', 'vesely']],
        ['2026-05-22 18:00', 'Hřiště Částkov', ['novak', 'dvorak', 'marek', 'benes'], ['kucera', 'svoboda', 'cerny', 'prochazka', 'vesely']],
        ['2026-05-29 18:00', 'Hřiště Částkov', ['admin'], []],
    ];

    /** [datum, domácí, hosté, hřiště, výsledek, přijdou, nepřijdou] */
    private const GAMES = [
        ['2026-04-05 15:30', 'Hradčovice', 'Částkov', 'Hradčovice', '6:0', ['novak', 'dvorak', 'svoboda', 'marek', 'cerny'], ['kucera', 'benes', 'vesely']],
        ['2026-04-12 10:15', 'Částkov', 'Popovice', 'Částkov', '0:3', ['novak', 'dvorak', 'marek', 'benes', 'vesely'], ['kucera', 'svoboda', 'cerny', 'prochazka']],
        ['2026-04-19 16:00', 'Javořina B', 'Částkov', 'Horní Němčí', '1:1', ['novak', 'svoboda', 'marek', 'benes'], ['kucera', 'dvorak', 'cerny', 'prochazka', 'vesely']],
        ['2026-04-26 10:15', 'Částkov', 'Březolupy', 'Částkov', '2:3', ['novak', 'dvorak', 'marek', 'cerny'], ['kucera', 'svoboda', 'benes', 'prochazka', 'vesely']],
        ['2026-05-03 16:30', 'Bílovice', 'Částkov', 'Bílovice', '3:1', ['novak', 'dvorak', 'svoboda', 'marek', 'benes', 'cerny'], ['kucera', 'prochazka', 'vesely']],
        ['2026-05-10 10:15', 'Částkov', 'Havřice', 'Částkov', null, ['novak', 'dvorak', 'marek', 'benes', 'vesely'], ['kucera', 'svoboda', 'cerny', 'prochazka']],
        ['2026-05-17 17:00', 'Boršice u Bl./D. Němčí B', 'Částkov', 'Boršice u Blatnice', '4:2', ['novak', 'dvorak', 'svoboda', 'marek', 'cerny', 'prochazka'], ['kucera', 'benes', 'vesely']],
        ['2026-05-24 10:15', 'Částkov', 'Drslavice', 'Částkov', null, ['admin', 'dvorak', 'svoboda', 'marek', 'benes'], ['kucera', 'cerny', 'prochazka', 'vesely']],
        ['2026-05-31 10:15', 'Částkov', 'Uh. Ostroh B', 'Částkov', null, ['admin'], []],
    ];

    /** login => user_id */
    private Collection $userIds;

    public function run(): void
    {
        $this->userIds = User::pluck('id', 'email')->mapWithKeys(fn (int $id, string $email) => [Str::before($email, '@') => $id]);

        $gameTypeId = GameType::firstOrCreate(['name' => 'Mistrovské utkání'])->id;

        foreach (self::TRAININGS as [$date, $location, $attending, $notAttending]) {
            $training = Training::create(['training_date' => $date, 'location' => $location]);
            $this->attendance($training->users(), $attending, $notAttending);
        }

        foreach (self::GAMES as [$date, $home, $away, $location, $result, $attending, $notAttending]) {
            $game = Game::create([
                'match_type_id' => $gameTypeId,
                'game_date'     => $date,
                'home_team'     => $home,
                'away_team'     => $away,
                'location'      => $location,
                'result'        => $result,
            ]);
            $this->attendance($game->users(), $attending, $notAttending);
        }

        $this->upcoming($gameTypeId);
    }

    /**
     * Nadcházející akce relativně k datu seedování – bez hlasů, hráči hlasují sami.
     */
    private function upcoming(int $gameTypeId): void
    {
        $friday = now()->next('Friday');

        Training::create(['training_date' => $friday->copy()->setTime(17, 30), 'location' => 'Hřiště Částkov', 'description' => 'Kondice + standardky']);
        Training::create(['training_date' => $friday->copy()->addWeek()->setTime(17, 30), 'location' => 'Hřiště Částkov']);

        Game::create([
            'match_type_id' => $gameTypeId,
            'game_date'     => $friday->copy()->addDays(2)->setTime(10, 15),
            'home_team'     => 'Částkov',
            'away_team'     => 'Havřice',
            'location'      => 'Částkov',
        ]);
    }

    private function attendance(BelongsToMany $users, array $attending, array $notAttending): void
    {
        $users->attach($this->ids($attending), ['status_id' => AttendanceStatus::ATTENDING]);
        $users->attach($this->ids($notAttending), ['status_id' => AttendanceStatus::NOT_ATTENDING]);
    }

    private function ids(array $logins): array
    {
        return array_map(fn (string $login) => $this->userIds[$login], $logins);
    }
}
