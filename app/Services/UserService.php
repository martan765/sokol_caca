<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    private const PER_PAGE = 20;

    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function register(array $data): User
    {
        $user = $this->users->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        return $user;
    }

    public function updateProfile(User $user, array $data): void
    {
        $user->fill($data);

        // Změněný e-mail je potřeba znovu ověřit
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $this->users->save($user);
    }

    public function paginated(): LengthAwarePaginator
    {
        return $this->users->paginate(self::PER_PAGE);
    }

    /**
     * Úprava uživatele trenérem – jméno, e-mail a role. Heslo admin měnit nesmí.
     */
    public function updateByAdmin(User $user, array $data): void
    {
        $user->role = $data['role'];

        $this->updateProfile($user, Arr::only($data, ['name', 'email']));
    }

    public function changePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);

        $this->users->save($user);
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->forceFill([
            'password'       => Hash::make($password),
            'remember_token' => Str::random(60),
        ]);

        $this->users->save($user);

        event(new PasswordReset($user));
    }

    public function delete(User $user): void
    {
        $this->users->delete($user);
    }
}
