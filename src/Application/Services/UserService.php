<?php

namespace Sokol\Application\Services;

use Sokol\Application\Dto\AdminUpdateUserData;
use Sokol\Application\Dto\PasswordResetResult;
use Sokol\Application\Dto\RegisterUserData;
use Sokol\Application\Dto\UpdateProfileData;
use Sokol\Application\Ports\Clock;
use Sokol\Application\Ports\PasswordHasher;
use Sokol\Application\Ports\PasswordResetBroker;
use Sokol\Application\Ports\UserEventNotifier;
use Sokol\Application\Support\Changes;
use Sokol\Application\ViewModels\UserView;
use Sokol\Domain\Entities\User;
use Sokol\Domain\Pagination\Page;
use Sokol\Domain\Repositories\UserRepositoryInterface;

class UserService
{
    public const PER_PAGE = 20;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly PasswordHasher $hasher,
        private readonly UserEventNotifier $notifier,
        private readonly PasswordResetBroker $passwordReset,
        private readonly Clock $clock,
    ) {}

    public function register(RegisterUserData $data): UserView
    {
        $user = $this->users->save(User::register($data->name, $data->email, $this->hasher->hash($data->password)));

        $this->notifier->registered((int) $user->id());

        return UserView::fromEntity($user);
    }

    public function current(?int $id): ?UserView
    {
        $user = $id === null ? null : $this->users->findById($id);

        return $user ? UserView::fromEntity($user) : null;
    }

    public function get(int $id): UserView
    {
        return UserView::fromEntity($this->users->getById($id));
    }

    public function isAdmin(?int $id): bool
    {
        return (bool) $this->current($id)?->isAdmin;
    }

    public function isEmailTaken(string $email, ?int $exceptId = null): bool
    {
        return $this->users->emailExists($email, $exceptId);
    }

    public function paginated(int $page): Page
    {
        return $this->users->paginate($page, self::PER_PAGE)->map(fn (User $u) => UserView::fromEntity($u));
    }

    public function updateProfile(int $id, UpdateProfileData $data): array
    {
        $user   = $this->users->getById($id);
        $before = $this->snapshot($user);

        $user->rename($data->name);
        $user->changeEmail($data->email);
        $this->users->save($user);

        return Changes::between($before, $this->snapshot($user));
    }

    public function updateByAdmin(int $id, AdminUpdateUserData $data): array
    {
        $user   = $this->users->getById($id);
        $before = $this->snapshot($user);

        $user->changeRole($data->role);
        $user->rename($data->name);
        $user->changeEmail($data->email);
        $this->users->save($user);

        return Changes::between($before, $this->snapshot($user));
    }

    public function checkPassword(int $id, string $plainPassword): bool
    {
        $user = $this->users->findById($id);

        return $user !== null && $this->hasher->check($plainPassword, $user->passwordHash());
    }

    public function changePassword(int $id, string $newPassword): void
    {
        $user = $this->users->getById($id);

        $user->changePasswordHash($this->hasher->hash($newPassword));
        $this->users->save($user);
    }

    public function sendPasswordResetLink(string $email): PasswordResetResult
    {
        return $this->passwordReset->sendResetLink($email);
    }

    public function resetPassword(string $email, string $token, string $newPassword): PasswordResetResult
    {
        return $this->passwordReset->reset($email, $token, $newPassword, function (int $userId) use ($newPassword): void {
            $this->changePassword($userId, $newPassword);
            $this->notifier->passwordReset($userId);
        });
    }

    public function hasVerifiedEmail(int $id): bool
    {
        return $this->users->getById($id)->isEmailVerified();
    }

    public function verifyEmail(int $id): bool
    {
        $user = $this->users->getById($id);

        if (! $user->markEmailVerified($this->clock->now())) {
            return false;
        }

        $this->users->save($user);
        $this->notifier->emailVerified($id);

        return true;
    }

    public function sendEmailVerification(int $id): void
    {
        $this->notifier->sendEmailVerification($id);
    }

    public function canDelete(int $actorId, int $targetId): bool
    {
        $actor  = $this->users->findById($actorId);
        $target = $this->users->findById($targetId);

        return $actor !== null && $target !== null && $actor->canDelete($target);
    }

    public function delete(int $id): UserView
    {
        $view = $this->get($id);

        $this->users->delete($id);

        return $view;
    }

    private function snapshot(User $user): array
    {
        return [
            'name'              => $user->name(),
            'email'             => $user->email(),
            'role'              => $user->role()->value,
            'email_verified_at' => $user->emailVerifiedAt(),
        ];
    }
}
