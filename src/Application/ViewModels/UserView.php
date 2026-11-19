<?php

namespace Sokol\Application\ViewModels;

use Sokol\Domain\Entities\User;
use Sokol\Domain\Enums\Role;

final readonly class UserView
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public Role $role,
        public bool $isAdmin,
        public bool $emailVerified,
    ) {}

    public static function fromEntity(User $user): self
    {
        return new self(
            (int) $user->id(),
            $user->name(),
            $user->email(),
            $user->role(),
            $user->isAdmin(),
            $user->isEmailVerified(),
        );
    }
}
