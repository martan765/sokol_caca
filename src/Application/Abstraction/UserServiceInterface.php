<?php

namespace Sokol\Application\Abstraction;

use Sokol\Application\Dto\AdminUpdateUserData;
use Sokol\Application\Dto\RegisterUserData;
use Sokol\Application\Dto\UpdateProfileData;
use Sokol\Application\ViewModels\UserView;
use Sokol\Domain\Enums\PasswordResetResult;
use Sokol\Domain\Pagination\Page;

interface UserServiceInterface
{
    public function register(RegisterUserData $data): UserView;

    public function current(?int $id): ?UserView;

    public function get(int $id): UserView;

    public function isAdmin(?int $id): bool;

    public function isEmailTaken(string $email, ?int $exceptId = null): bool;

    public function paginated(int $page): Page;

    public function updateProfile(int $id, UpdateProfileData $data): array;

    public function updateByAdmin(int $id, AdminUpdateUserData $data): array;

    public function checkPassword(int $id, string $plainPassword): bool;

    public function changePassword(int $id, string $newPassword): void;

    public function sendPasswordResetLink(string $email): PasswordResetResult;

    public function resetPassword(string $email, string $token, string $newPassword): PasswordResetResult;

    public function hasVerifiedEmail(int $id): bool;

    public function verifyEmail(int $id): bool;

    public function sendEmailVerification(int $id): void;

    public function canDelete(int $actorId, int $targetId): bool;

    public function delete(int $id): UserView;
}
