<?php

namespace Tests\Fakes;

use Sokol\Domain\Enums\PasswordResetResult;
use Sokol\Domain\Interfaces\Managers\PasswordResetBrokerInterface;

final class FakePasswordResetBroker implements PasswordResetBrokerInterface
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
