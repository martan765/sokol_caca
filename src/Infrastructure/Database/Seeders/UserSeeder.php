<?php

namespace Sokol\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Sokol\Domain\Enums\Role;
use Sokol\Infrastructure\Persistence\Eloquent\Models\UserModel;

/**
 * Trenér a hráči kádru. Všichni mají heslo "password".
 */
class UserSeeder extends Seeder
{
    public const PASSWORD = 'password';

    /** login (část e-mailu před @) => [jméno, e-mail, role] */
    private const USERS = [
        'admin'     => ['admin', 'admin@admin.cz', Role::Admin],
        'novak'     => ['Petr Novák', 'novak@caca.cz', Role::Player],
        'kucera'    => ['Jaroslav Kučera', 'kucera@caca.cz', Role::Player],
        'dvorak'    => ['Martin Dvořák', 'dvorak@caca.cz', Role::Player],
        'svoboda'   => ['Michal Svoboda', 'svoboda@caca.cz', Role::Player],
        'marek'     => ['Tomáš Marek', 'marek@caca.cz', Role::Player],
        'benes'     => ['Pavel Beneš', 'benes@caca.cz', Role::Player],
        'cerny'     => ['Lukáš Černý', 'cerny@caca.cz', Role::Player],
        'prochazka' => ['Zdeněk Procházka', 'prochazka@caca.cz', Role::Player],
        'vesely'    => ['Jiří Veselý', 'vesely@caca.cz', Role::Player],
    ];

    public function run(): void
    {
        $password = Hash::make(self::PASSWORD);

        foreach (self::USERS as [$name, $email, $role]) {
            // role není ve $fillable (nesmí jít nastavit z registračního formuláře), proto forceFill
            UserModel::firstOrNew(['email' => $email])
                ->forceFill([
                    'name'              => $name,
                    'password'          => $password,
                    'role'              => $role->value,
                    'email_verified_at' => now(),
                ])
                ->save();
        }
    }
}
