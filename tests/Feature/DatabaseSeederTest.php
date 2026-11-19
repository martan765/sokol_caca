<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Training;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_demo_data(): void
    {
        $this->seed();

        $this->assertSame(1, User::where('role', User::ROLE_ADMIN)->count());
        $this->assertSame(9, User::where('role', User::ROLE_PLAYER)->count());
        $this->assertTrue(Hash::check(UserSeeder::PASSWORD, User::where('email', 'admin@admin.cz')->value('password')));

        // 15 odehraných + 2 nadcházející tréninky, 9 odehraných + 1 nadcházející zápas
        $this->assertSame(17, Training::count());
        $this->assertSame(10, Game::count());
        $this->assertSame(2, Training::where('training_date', '>=', now())->count());

        $this->assertDatabaseCount('training_attendance', 127);
        $this->assertDatabaseCount('game_attendance', 72);
        $this->assertDatabaseHas('games', ['home_team' => 'Hradčovice', 'result' => '6:0']);
    }

    public function test_seeded_admin_can_log_in_and_see_dashboard(): void
    {
        $this->seed();

        $this->post('/login', ['email' => 'admin@admin.cz', 'password' => UserSeeder::PASSWORD])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('dashboard'))->assertOk()->assertSee('Petr Novák');
    }
}
