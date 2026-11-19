<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Sokol\Application\Abstraction\UserServiceInterface;

class PasswordController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
    ) {}

    /**
     * Update the user's password.
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->users->changePassword($this->currentUserId(), $request->validated('password'));

        return back()->with('status', 'password-updated');
    }
}
