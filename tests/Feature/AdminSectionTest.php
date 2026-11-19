<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Sokol\Domain\Enums\AttendanceStatusId;
use Sokol\Domain\Enums\Role;
use Sokol\Infrastructure\Persistence\Eloquent\Models\AttendanceStatusModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\GameTypeModel;
use Sokol\Infrastructure\Persistence\Eloquent\Models\TrainingModel;
use Tests\TestCase;

class AdminSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_player_cannot_access_admin_section(): void
    {
        $player = UserFactory::new()->create();
        $other  = UserFactory::new()->create();

        foreach (['admin.users.index', 'admin.game-types.index', 'admin.attendance-statuses.index'] as $route) {
            $this->actingAs($player)->get(route($route))->assertRedirect(route('dashboard'));
        }

        $this->actingAs($player)->put(route('admin.users.update', $other), [
            'name' => 'Hacker', 'email' => $other->email, 'role' => Role::Admin->value,
        ]);

        $this->assertDatabaseHas('users', ['id' => $other->id, 'role' => Role::Player->value]);
    }

    public function test_admin_pages_render(): void
    {
        $admin    = UserFactory::new()->admin()->create();
        $training = TrainingModel::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);
        $game     = GameModel::create(['match_type_id' => 1, 'home_team' => 'A', 'away_team' => 'B', 'game_date' => now()->addDays(3)]);

        $routes = [
            route('admin.users.index'),
            route('admin.users.edit', $admin),
            route('admin.game-types.index'),
            route('admin.game-types.create'),
            route('admin.game-types.edit', GameTypeModel::first()),
            route('admin.attendance-statuses.index'),
            route('admin.attendance-statuses.edit', AttendanceStatusModel::first()),
            route('admin.trainings.attendance', $training),
            route('admin.games.attendance', $game),
        ];

        foreach ($routes as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admin_can_edit_user_but_not_password(): void
    {
        $admin  = UserFactory::new()->admin()->create();
        $player = UserFactory::new()->create();
        $hash   = $player->password;

        $this->actingAs($admin)->put(route('admin.users.update', $player), [
            'name'     => 'Nové Jméno',
            'email'    => 'novy@example.com',
            'role'     => Role::Admin->value,
            'password' => 'heslo-ktere-se-nema-ulozit',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.users.index'));

        $player->refresh();
        $this->assertSame('Nové Jméno', $player->name);
        $this->assertSame('novy@example.com', $player->email);
        $this->assertSame(Role::Admin->value, $player->role);
        $this->assertSame($hash, $player->password);
    }

    public function test_admin_cannot_demote_or_delete_himself(): void
    {
        $admin = UserFactory::new()->admin()->create();

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'role' => Role::Player->value,
        ])->assertSessionHasErrors('role');

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => Role::Admin->value]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin  = UserFactory::new()->admin()->create();
        $player = UserFactory::new()->create();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $player))->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $player->id]);
    }

    public function test_game_type_crud_and_used_type_cannot_be_deleted(): void
    {
        $admin = UserFactory::new()->admin()->create();

        $this->actingAs($admin)->post(route('admin.game-types.store'), ['name' => 'Přátelák'])
            ->assertRedirect(route('admin.game-types.index'));
        $type = GameTypeModel::where('name', 'Přátelák')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.game-types.update', $type), ['name' => 'Přátelské utkání'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('game_types', ['id' => $type->id, 'name' => 'Přátelské utkání']);

        // Duplicitní název
        $this->actingAs($admin)->post(route('admin.game-types.store'), ['name' => 'Přátelské utkání'])
            ->assertSessionHasErrors('name');

        GameModel::create(['match_type_id' => $type->id, 'home_team' => 'A', 'away_team' => 'B', 'game_date' => now()->addDay()]);
        $this->actingAs($admin)->delete(route('admin.game-types.destroy', $type))->assertSessionHas('error');
        $this->assertDatabaseHas('game_types', ['id' => $type->id]);

        $unused = GameTypeModel::create(['name' => 'Turnaj']);
        $this->actingAs($admin)->delete(route('admin.game-types.destroy', $unused))->assertSessionHas('success');
        $this->assertDatabaseMissing('game_types', ['id' => $unused->id]);
    }

    public function test_renamed_status_is_shown_on_vote_buttons(): void
    {
        $admin = UserFactory::new()->admin()->create();
        TrainingModel::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);

        $this->actingAs($admin)->put(route('admin.attendance-statuses.update', AttendanceStatusId::Attending->value), ['name' => 'Přijdu'])
            ->assertRedirect(route('admin.attendance-statuses.index'));

        $this->actingAs($admin)->get(route('trainings.index'))->assertSee('Přijdu');
    }

    public function test_admin_fills_in_attendance_for_players(): void
    {
        $admin    = UserFactory::new()->admin()->create();
        [$a, $b]  = UserFactory::new()->count(2)->create();
        $training = TrainingModel::create(['training_date' => now()->addDay(), 'location' => 'Hřiště']);

        // Hráč B už hlasoval sám, trenér jeho hlas maže; trenérův vlastní hlas zůstává
        $b->trainings()->attach($training->id, ['status_id' => AttendanceStatusId::Attending->value]);
        $admin->trainings()->attach($training->id, ['status_id' => AttendanceStatusId::Attending->value]);

        $this->actingAs($admin)->put(route('admin.trainings.attendance.update', $training), [
            'attendance' => [
                $a->id => ['status_id' => AttendanceStatusId::NotAttending->value, 'note' => 'nemocný'],
                $b->id => ['status_id' => ''],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.trainings.index'));

        $this->assertDatabaseHas('training_attendance', [
            'user_id' => $a->id, 'training_id' => $training->id, 'status_id' => AttendanceStatusId::NotAttending->value, 'note' => 'nemocný',
        ]);
        $this->assertDatabaseMissing('training_attendance', ['user_id' => $b->id]);
        $this->assertDatabaseHas('training_attendance', ['user_id' => $admin->id]);
    }

    public function test_admin_fills_in_game_attendance(): void
    {
        $admin  = UserFactory::new()->admin()->create();
        $player = UserFactory::new()->create();
        $game   = GameModel::create(['match_type_id' => 1, 'home_team' => 'A', 'away_team' => 'B', 'game_date' => now()->addDay()]);

        $this->actingAs($admin)->put(route('admin.games.attendance.update', $game), [
            'attendance' => [$player->id => ['status_id' => AttendanceStatusId::Attending->value]],
        ])->assertRedirect(route('admin.games.index'));

        $this->assertDatabaseHas('game_attendance', ['user_id' => $player->id, 'game_id' => $game->id, 'status_id' => AttendanceStatusId::Attending->value]);
    }
}
