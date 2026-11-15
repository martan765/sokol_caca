<?php

namespace Sokol\Infrastructure\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Sokol\Domain\Exceptions\EntityNotFoundException;
use Sokol\Domain\Interfaces\Managers\UserEventNotifierInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Models\UserModel;

final class LaravelUserEventNotifier implements UserEventNotifierInterface
{
    public function registered(int $userId): void
    {
        event(new Registered($this->model($userId)));
    }

    public function passwordReset(int $userId): void
    {
        event(new PasswordReset($this->model($userId)));
    }

    public function emailVerified(int $userId): void
    {
        event(new Verified($this->model($userId)));
    }

    public function sendEmailVerification(int $userId): void
    {
        $this->model($userId)->sendEmailVerificationNotification();
    }

    private function model(int $userId): UserModel
    {
        return UserModel::query()->find($userId) ?? throw EntityNotFoundException::for('Uživatel', $userId);
    }
}
