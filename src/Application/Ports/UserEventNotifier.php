<?php

namespace Sokol\Application\Ports;

interface UserEventNotifier
{
    public function registered(int $userId): void;

    public function passwordReset(int $userId): void;

    public function emailVerified(int $userId): void;

    public function sendEmailVerification(int $userId): void;
}
