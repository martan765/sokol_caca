<?php

namespace Sokol\Application\Ports;

use Sokol\Application\Dto\PasswordResetResult;

interface PasswordResetBroker
{
    public function sendResetLink(string $email): PasswordResetResult;

    public function reset(string $email, string $token, string $password, callable $onReset): PasswordResetResult;
}
