<?php

namespace Sokol\Domain\Entities;

use DateTimeImmutable;
use Sokol\Domain\Enums\Role;

final class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $passwordHash,
        private Role $role = Role::Player,
        private ?DateTimeImmutable $emailVerifiedAt = null,
    ) {}

    public static function register(string $name, string $email, string $passwordHash): self
    {
        return new self(null, $name, $email, $passwordHash, Role::Player, null);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function emailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isPlayer(): bool
    {
        return $this->role === Role::Player;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(string $email): void
    {
        if ($email === $this->email) {
            return;
        }

        $this->email = $email;
        $this->emailVerifiedAt = null;
    }

    public function changeRole(Role $role): void
    {
        $this->role = $role;
    }

    public function changePasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function markEmailVerified(DateTimeImmutable $at): bool
    {
        if ($this->isEmailVerified()) {
            return false;
        }

        $this->emailVerifiedAt = $at;

        return true;
    }

    public function isSameAs(User $other): bool
    {
        return $this->id !== null && $this->id === $other->id;
    }

    public function canDelete(User $target): bool
    {
        return $this->isAdmin() && ! $this->isSameAs($target);
    }
}
