<?php

namespace App\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Sokol\Application\Abstraction\UserServiceInterface;

class UserPolicy
{
    public function __construct(
        private readonly UserServiceInterface $users,
    ) {}

    /**
     * Trenér nesmí smazat sám sebe – tým by mohl zůstat bez admina.
     */
    public function delete(Authenticatable $actor, int|string $targetId): bool
    {
        return $this->users->canDelete((int) $actor->getAuthIdentifier(), (int) $targetId);
    }
}
