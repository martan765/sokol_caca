<?php

namespace Sokol\Domain\Interfaces\Managers;

interface UserEventNotifierInterface
{
    public function registered(int $userId): void;

    public function passwordReset(int $userId): void;

    public function emailVerified(int $userId): void;

    public function sendEmailVerification(int $userId): void;
}
