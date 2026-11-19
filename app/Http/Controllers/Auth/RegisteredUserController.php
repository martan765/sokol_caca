<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Sokol\Application\Abstraction\AccountServiceInterface;
use Sokol\Application\Abstraction\UserServiceInterface;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $users,
        private readonly AccountServiceInterface $accounts,
    ) {}

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = $this->users->register($request->toData());

        $this->accounts->loginById($user->id);

        return redirect(route('dashboard', absolute: false));
    }
}
