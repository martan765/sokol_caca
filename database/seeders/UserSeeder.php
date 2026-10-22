<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Trenér a hráči kádru. Všichni mají heslo "password".
 */
class UserSeeder extends Seeder
{
    public const PASSWORD = 'password';

    /** login (část e-mailu před @) => [jméno, e-mail, role] */
    private const USERS = [
        'admin'     => ['admin', 'admin@admin.cz', User::ROLE_ADMIN],
        'novak'     => ['Petr Novák', 'novak@caca.cz', User::ROLE_PLAYER],
        'kucera'    => ['Jaroslav Kučera', 'kucera@caca.cz', User::ROLE_PLAYER],
        'dvorak'    => ['Martin Dvořák', 'dvorak@caca.cz', User::ROLE_PLAYER],
        'svoboda'   => ['Michal Svoboda', 'svoboda@caca.cz', User::ROLE_PLAYER],
        'marek'     => ['Tomáš Marek', 'marek@caca.cz', User::ROLE_PLAYER],
        'benes'     => ['Pavel Beneš', 'benes@caca.cz', User::ROLE_PLAYER],
        'cerny'     => ['Lukáš Černý', 'cerny@caca.cz', User::ROLE_PLAYER],
        'prochazka' => ['Zdeněk Procházka', 'prochazka@caca.cz', User::ROLE_PLAYER],
        'vesely'    => ['Jiří Veselý', 'vesely@caca.cz', User::ROLE_PLAYER],
    ];

    public function run(): void
    {
        $password = Hash::make(self::PASSWORD);

        foreach (self::USERS as [$name, $email, $role]) {
            // role není ve $fillable (nesmí jít nastavit z registračního formuláře), proto forceFill
            User::firstOrNew(['email' => $email])
                ->forceFill([
                    'name'              => $name,
                    'password'          => $password,
                    'role'              => $role,
                    'email_verified_at' => now(),
                ])
                ->save();
        }
    }
}
