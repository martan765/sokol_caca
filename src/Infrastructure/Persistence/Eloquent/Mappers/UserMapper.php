<?php

namespace Sokol\Infrastructure\Persistence\Eloquent\Mappers;

use DateTimeImmutable;
use Sokol\Domain\Entities\User;
use Sokol\Domain\Enums\Role;
use Sokol\Infrastructure\Persistence\Eloquent\Models\UserModel;

final class UserMapper
{
    public static function toEntity(UserModel $model): User
    {
        return new User(
            (int) $model->id,
            (string) $model->name,
            (string) $model->email,
            (string) $model->password,
            Role::from((string) $model->role),
            $model->email_verified_at ? DateTimeImmutable::createFromInterface($model->email_verified_at) : null,
        );
    }

    public static function fill(UserModel $model, User $user): UserModel
    {
        $model->forceFill([
            'name'              => $user->name(),
            'email'             => $user->email(),
            'role'              => $user->role()->value,
            'email_verified_at' => $user->emailVerifiedAt(),
        ]);

        if ($model->password !== $user->passwordHash()) {
            $model->password = $user->passwordHash();
        }

        return $model;
    }
}
