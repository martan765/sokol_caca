<?php

namespace Tests\Unit\Application;

use PHPUnit\Framework\TestCase;
use Sokol\Application\Dto\AdminUpdateUserData;
use Sokol\Application\Dto\PasswordResetResult;
use Sokol\Application\Dto\RegisterUserData;
use Sokol\Application\Dto\UpdateProfileData;
use Sokol\Application\Services\UserService;
use Sokol\Domain\Enums\Role;
use Tests\Fakes\FakePasswordResetBroker;
use Tests\Fakes\FixedClock;
use Tests\Fakes\InMemoryUserRepository;
use Tests\Fakes\PlainPasswordHasher;
use Tests\Fakes\RecordingUserEventNotifier;

class UserServiceTest extends TestCase
{
    private InMemoryUserRepository $users;

    private RecordingUserEventNotifier $notifier;

    private UserService $service;

    protected function setUp(): void
    {
        $this->users    = new InMemoryUserRepository();
        $this->notifier = new RecordingUserEventNotifier();
        $this->service  = new UserService($this->users, new PlainPasswordHasher(), $this->notifier, new FakePasswordResetBroker(1), new FixedClock());
    }

    public function test_registered_user_is_player_with_hashed_password(): void
    {
        $user = $this->service->register(new RegisterUserData('Petr', 'petr@example.com', 'tajne-heslo'));

        $this->assertSame(Role::Player, $user->role);
        $this->assertSame('hashed:tajne-heslo', $this->users->getById($user->id)->passwordHash());
        $this->assertSame([['registered', $user->id]], $this->notifier->events);
    }

    public function test_admin_update_changes_role_but_not_password(): void
    {
        $user = $this->service->register(new RegisterUserData('Petr', 'petr@example.com', 'heslo'));
        $hash = $this->users->getById($user->id)->passwordHash();

        $changes = $this->service->updateByAdmin($user->id, new AdminUpdateUserData('Petr Novák', 'petr@example.com', Role::Admin));

        $this->assertSame(['name' => 'Petr Novák', 'role' => 'admin'], $changes);
        $this->assertTrue($this->service->isAdmin($user->id));
        $this->assertSame($hash, $this->users->getById($user->id)->passwordHash());
    }

    public function test_changed_email_must_be_verified_again(): void
    {
        $user = $this->service->register(new RegisterUserData('Petr', 'petr@example.com', 'heslo'));
        $this->service->verifyEmail($user->id);

        $this->service->updateProfile($user->id, new UpdateProfileData('Petr', 'novy@example.com'));

        $this->assertFalse($this->service->hasVerifiedEmail($user->id));
    }

    public function test_admin_cannot_delete_himself(): void
    {
        $admin  = $this->service->register(new RegisterUserData('Trenér', 'admin@example.com', 'heslo'));
        $player = $this->service->register(new RegisterUserData('Hráč', 'hrac@example.com', 'heslo'));
        $this->service->updateByAdmin($admin->id, new AdminUpdateUserData('Trenér', 'admin@example.com', Role::Admin));

        $this->assertFalse($this->service->canDelete($admin->id, $admin->id));
        $this->assertTrue($this->service->canDelete($admin->id, $player->id));
        $this->assertFalse($this->service->canDelete($player->id, $admin->id));
    }

    public function test_password_reset_stores_new_hash(): void
    {
        $user = $this->service->register(new RegisterUserData('Petr', 'petr@example.com', 'stare'));

        $this->assertSame(PasswordResetResult::InvalidToken, $this->service->resetPassword('petr@example.com', 'spatny', 'nove'));
        $this->assertSame(PasswordResetResult::Reset, $this->service->resetPassword('petr@example.com', 'valid', 'nove'));
        $this->assertTrue($this->service->checkPassword($user->id, 'nove'));
    }
}
