<?php

namespace App\Http\Support;

use Sokol\Domain\Enums\PasswordResetResult;

final class PasswordResetMessages
{
    public static function key(PasswordResetResult $result): string
    {
        return match ($result) {
            PasswordResetResult::LinkSent     => 'passwords.sent',
            PasswordResetResult::Reset        => 'passwords.reset',
            PasswordResetResult::InvalidToken => 'passwords.token',
            PasswordResetResult::Throttled    => 'passwords.throttled',
            PasswordResetResult::InvalidUser  => 'passwords.user',
        };
    }
}
