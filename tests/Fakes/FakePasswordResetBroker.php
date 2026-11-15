<?php

namespace Tests\Fakes;

use Sokol\Application\Dto\PasswordResetResult;
use Sokol\Application\Ports\PasswordResetBroker;

final class FakePasswordResetBroker implements PasswordResetBroker
{
    public function __construct(
        private readonly int $userId = 1,
    ) {}

    public function sendResetLink(string $email): PasswordResetResult
    {
        return PasswordResetResult::LinkSent;
    }

    public function reset(string $email, string $token, string $password, callable $onReset): PasswordResetResult
    {
        if ($token !== 'valid') {
            return PasswordResetResult::InvalidToken;
        }

        $onReset($this->userId);

        return PasswordResetResult::Reset;
    }
}
