<?php

namespace App\Policies;

use App\Models\User;

/**
 * Oprávnění při správě uživatelů v administraci.
 * Laravel policy najde automaticky podle názvu (User -> UserPolicy).
 */
class UserPolicy
{
    /**
     * Trenér nesmí smazat sám sebe – tým by mohl zůstat bez admina.
     */
    public function delete(User $actor, User $target): bool
    {
        return $actor->isAdmin() && ! $actor->is($target);
    }
}
