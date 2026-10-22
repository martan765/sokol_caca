<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Starší databáze mají schéma v mírně odlišných variantách,
 * proto se každá změna aplikuje jen tehdy, když v DB ještě není.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Sloupce, které vyžaduje Laravel auth ("zapamatovat si mě", změna e-mailu v profilu)
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken();
            }
        });

        // game_attendance neměl FK na users – po smazání hráče zůstávaly osiřelé záznamy
        DB::table('game_attendance')
            ->whereNotIn('user_id', DB::table('users')->select('id'))
            ->delete();

        // Odstranění duplicitních hlasů (ponecháme nejnovější), jinak nelze přidat unique index
        $this->removeDuplicates('game_attendance', 'game_id');
        $this->removeDuplicates('training_attendance', 'training_id');

        Schema::table('game_attendance', function (Blueprint $table) {
            if (! $this->hasForeignKey('game_attendance', 'user_id', 'users')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
            if (! Schema::hasIndex('game_attendance', ['user_id', 'game_id'], 'unique')) {
                $table->unique(['user_id', 'game_id']);
            }
        });

        Schema::table('training_attendance', function (Blueprint $table) {
            if (! Schema::hasIndex('training_attendance', ['user_id', 'training_id'], 'unique')) {
                $table->unique(['user_id', 'training_id']);
            }
        });

        // Výpisy filtrují a řadí podle data
        foreach (['trainings' => 'training_date', 'games' => 'game_date'] as $tableName => $column) {
            if (! Schema::hasIndex($tableName, [$column])) {
                Schema::table($tableName, fn (Blueprint $table) => $table->index($column));
            }
        }
    }

    public function down(): void
    {
        foreach (['trainings' => 'training_date', 'games' => 'game_date'] as $tableName => $column) {
            if (Schema::hasIndex($tableName, [$column])) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropIndex([$column]));
            }
        }

        // FK na users záměrně ponecháváme – v části databází existoval už před touto migrací.
        // MySQL mohl po přidání unique indexu zahodit automatický index pro FK na user_id;
        // bez náhrady by unique index nešel smazat.
        foreach (['game_attendance' => 'game_id', 'training_attendance' => 'training_id'] as $tableName => $eventColumn) {
            if (! Schema::hasIndex($tableName, ['user_id', $eventColumn], 'unique')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $eventColumn) {
                if (! Schema::hasIndex($tableName, ['user_id'])) {
                    $table->index('user_id');
                }
                $table->dropUnique(['user_id', $eventColumn]);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verified_at', 'remember_token']);
        });
    }

    private function removeDuplicates(string $table, string $eventColumn): void
    {
        $keepIds = DB::table($table)
            ->get(['id', 'user_id', $eventColumn])
            ->groupBy(fn (object $row) => $row->user_id . '-' . $row->{$eventColumn})
            ->map(fn ($rows) => $rows->max('id'))
            ->values();

        DB::table($table)->whereNotIn('id', $keepIds)->delete();
    }

    private function hasForeignKey(string $table, string $column, string $foreignTable): bool
    {
        return collect(Schema::getForeignKeys($table))->contains(
            fn (array $fk) => $fk['columns'] === [$column] && $fk['foreign_table'] === $foreignTable
        );
    }
};
