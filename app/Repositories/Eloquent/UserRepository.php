<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function players(): Collection
    {
        return User::where('role', User::ROLE_PLAYER)->orderBy('name')->get();
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        // 'admin' < 'player' abecedně, takže trenéři jsou nahoře
        return User::orderBy('role')->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function save(User $user): void
    {
        $user->save();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
