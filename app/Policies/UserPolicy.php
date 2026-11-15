<?php

namespace App\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Sokol\Application\Services\UserService;

class UserPolicy
{
    public function __construct(
        private readonly UserService $users,
    ) {}

    /**
     * Trenér nesmí smazat sám sebe – tým by mohl zůstat bez admina.
     */
    public function delete(Authenticatable $actor, int|string $targetId): bool
    {
        return $this->users->canDelete((int) $actor->getAuthIdentifier(), (int) $targetId);
    }
}
