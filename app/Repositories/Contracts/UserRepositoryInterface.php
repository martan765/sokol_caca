<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /** @return Collection<int, User> hráči seřazení podle jména */
    public function players(): Collection;

    /** Všichni uživatelé (nejdřív trenéři, pak hráči podle jména), po stránkách. */
    public function paginate(int $perPage): LengthAwarePaginator;

    public function create(array $data): User;

    public function save(User $user): void;

    public function delete(User $user): void;
}
