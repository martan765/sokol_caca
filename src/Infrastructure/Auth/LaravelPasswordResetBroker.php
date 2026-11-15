<?php

namespace Sokol\Infrastructure\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Sokol\Domain\Enums\PasswordResetResult;
use Sokol\Domain\Interfaces\Managers\PasswordResetBrokerInterface;
use Sokol\Infrastructure\Persistence\Eloquent\Models\UserModel;

final class LaravelPasswordResetBroker implements PasswordResetBrokerInterface
{
    public function sendResetLink(string $email): PasswordResetResult
    {
        return $this->result(Password::sendResetLink(['email' => $email]));
    }

    public function reset(string $email, string $token, string $password, callable $onReset): PasswordResetResult
    {
        $status = Password::reset(
            [
                'email'                 => $email,
                'password'              => $password,
                'password_confirmation' => $password,
                'token'                 => $token,
            ],
            function (UserModel $user) use ($onReset): void {
                $user->setRememberToken(Str::random(60));
                $user->save();

                $onReset((int) $user->getKey());
            },
        );

        return $this->result($status);
    }

    private function result(string $status): PasswordResetResult
    {
        return match ($status) {
            Password::RESET_LINK_SENT => PasswordResetResult::LinkSent,
            Password::PASSWORD_RESET  => PasswordResetResult::Reset,
            Password::INVALID_TOKEN   => PasswordResetResult::InvalidToken,
            Password::RESET_THROTTLED => PasswordResetResult::Throttled,
            default                   => PasswordResetResult::InvalidUser,
        };
    }
}
