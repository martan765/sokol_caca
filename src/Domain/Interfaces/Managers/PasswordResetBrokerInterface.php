<?php

namespace Sokol\Domain\Interfaces\Managers;

use Sokol\Domain\Enums\PasswordResetResult;

interface PasswordResetBrokerInterface
{
    public function sendResetLink(string $email): PasswordResetResult;

    public function reset(string $email, string $token, string $password, callable $onReset): PasswordResetResult;
}
