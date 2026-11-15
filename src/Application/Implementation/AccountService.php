<?php

namespace Sokol\Application\Implementation;

use Sokol\Application\Abstraction\AccountServiceInterface;
use Sokol\Domain\Interfaces\Managers\AccountManagerInterface;

class AccountService implements AccountServiceInterface
{
    public function __construct(
        private readonly AccountManagerInterface $accountManager,
    ) {}

    public function login(string $email, string $password, bool $remember): bool
    {
        return $this->accountManager->login($email, $password, $remember);
    }

    public function loginById(int $userId): void
    {
        $this->accountManager->loginById($userId);
    }

    public function logout(): void
    {
        $this->accountManager->logout();
    }
}
